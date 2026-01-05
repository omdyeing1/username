<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChallanRequest;
use App\Models\Challan;
use App\Models\ChallanItem;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChallanController extends Controller
{
    /**
     * Display a listing of challans.
     */
    public function index(Request $request)
    {
        $query = Challan::with('party');

        // Filter by party
        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('challan_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('challan_date', '<=', $request->to_date);
        }

        // Filter by invoice status
        if ($request->filled('invoiced')) {
            $query->where('is_invoiced', $request->invoiced === 'yes');
        }

        // Search by challan number
        if ($request->filled('search')) {
            $query->where('challan_number', 'like', "%{$request->search}%");
        }

        $challans = $query->orderBy('challan_date', 'desc')->paginate(15);
        $parties = Party::orderBy('name')->get();

        return view('challans.index', compact('challans', 'parties'));
    }

    /**
     * Show the form for creating a new challan.
     */
    public function create()
    {
        $parties = Party::orderBy('name')->get();
        $units = ChallanItem::UNITS;
        $challanNumber = Challan::generateChallanNumber();

        return view('challans.create', compact('parties', 'units', 'challanNumber'));
    }

    /**
     * Store a newly created challan in storage.
     */
    public function store(ChallanRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Create challan
            $challan = Challan::create([
                'party_id' => $request->party_id,
                'challan_number' => Challan::generateChallanNumber(),
                'challan_date' => $request->challan_date,
                'subtotal' => 0,
                'is_invoiced' => false,
            ]);

            // Server-side recalculation of item amounts
            $subtotal = 0;
            foreach ($request->items as $itemData) {
                $amount = round($itemData['quantity'] * $itemData['rate'], 2);
                $subtotal += $amount;

                ChallanItem::create([
                    'challan_id' => $challan->id,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'rate' => $itemData['rate'],
                    'amount' => $amount,
                ]);
            }

            // Update subtotal
            $challan->update(['subtotal' => $subtotal]);

            DB::commit();

            return redirect()
                ->route('challans.show', $challan)
                ->with('success', 'Challan created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create challan. Please try again.');
        }
    }

    /**
     * Display the specified challan.
     */
    public function show(Challan $challan)
    {
        $challan->load(['party', 'items']);
        return view('challans.show', compact('challan'));
    }

    /**
     * Show the form for editing the specified challan.
     */
    public function edit(Challan $challan)
    {
        if ($challan->is_invoiced) {
            return redirect()
                ->route('challans.show', $challan)
                ->with('error', 'Cannot edit a challan that has been invoiced.');
        }

        $challan->load('items');
        $parties = Party::orderBy('name')->get();
        $units = ChallanItem::UNITS;

        return view('challans.edit', compact('challan', 'parties', 'units'));
    }

    /**
     * Update the specified challan in storage.
     */
    public function update(ChallanRequest $request, Challan $challan)
    {
        if ($challan->is_invoiced) {
            return redirect()
                ->route('challans.show', $challan)
                ->with('error', 'Cannot edit a challan that has been invoiced.');
        }

        DB::beginTransaction();

        try {
            // Update challan details
            $challan->update([
                'party_id' => $request->party_id,
                'challan_date' => $request->challan_date,
            ]);

            // Delete existing items
            $challan->items()->delete();

            // Recreate items with server-side calculation
            $subtotal = 0;
            foreach ($request->items as $itemData) {
                $amount = round($itemData['quantity'] * $itemData['rate'], 2);
                $subtotal += $amount;

                ChallanItem::create([
                    'challan_id' => $challan->id,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'rate' => $itemData['rate'],
                    'amount' => $amount,
                ]);
            }

            // Update subtotal
            $challan->update(['subtotal' => $subtotal]);

            DB::commit();

            return redirect()
                ->route('challans.show', $challan)
                ->with('success', 'Challan updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update challan. Please try again.');
        }
    }

    /**
     * Remove the specified challan from storage.
     */
    public function destroy(Challan $challan)
    {
        if ($challan->is_invoiced) {
            return redirect()
                ->route('challans.index')
                ->with('error', 'Cannot delete a challan that has been invoiced.');
        }

        $challan->delete();

        return redirect()
            ->route('challans.index')
            ->with('success', 'Challan deleted successfully.');
    }

    /**
     * Get challans for a specific party (AJAX endpoint).
     */
    public function getByParty(Party $party)
    {
        $challans = $party->challans()
            ->where('is_invoiced', false)
            ->with('items')
            ->orderBy('challan_date', 'desc')
            ->get()
            ->map(function ($challan) {
                return [
                    'id' => $challan->id,
                    'challan_number' => $challan->challan_number,
                    'challan_date' => $challan->challan_date->format('d/m/Y'),
                    'subtotal' => number_format($challan->subtotal, 2),
                    'subtotal_raw' => $challan->subtotal,
                    'items_count' => $challan->items->count(),
                    'items' => $challan->items->map(function ($item) {
                        return [
                            'description' => $item->description,
                            'quantity' => $item->quantity,
                            'unit' => $item->unit,
                            'rate' => number_format($item->rate, 2),
                            'amount' => number_format($item->amount, 2),
                        ];
                    }),
                ];
            });

        return response()->json([
            'party' => [
                'id' => $party->id,
                'name' => $party->name,
                'address' => $party->address,
                'contact_number' => $party->contact_number,
                'gst_number' => $party->gst_number,
            ],
            'challans' => $challans,
        ]);
    }
}
