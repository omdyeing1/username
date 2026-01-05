<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartyRequest;
use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    /**
     * Display a listing of parties.
     */
    public function index(Request $request)
    {
        $query = Party::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%");
            });
        }

        $parties = $query->orderBy('name')->paginate(15);

        return view('parties.index', compact('parties'));
    }

    /**
     * Show the form for creating a new party.
     */
    public function create()
    {
        return view('parties.create');
    }

    /**
     * Store a newly created party in storage.
     */
    public function store(PartyRequest $request)
    {
        Party::create($request->validated());

        return redirect()
            ->route('parties.index')
            ->with('success', 'Party created successfully.');
    }

    /**
     * Display the specified party.
     */
    public function show(Party $party)
    {
        $party->load(['challans' => function ($query) {
            $query->latest('challan_date')->limit(10);
        }, 'invoices' => function ($query) {
            $query->latest('invoice_date')->limit(10);
        }]);

        return view('parties.show', compact('party'));
    }

    /**
     * Show the form for editing the specified party.
     */
    public function edit(Party $party)
    {
        return view('parties.edit', compact('party'));
    }

    /**
     * Update the specified party in storage.
     */
    public function update(PartyRequest $request, Party $party)
    {
        $party->update($request->validated());

        return redirect()
            ->route('parties.index')
            ->with('success', 'Party updated successfully.');
    }

    /**
     * Remove the specified party from storage.
     */
    public function destroy(Party $party)
    {
        // Check if party has any challans or invoices
        if ($party->challans()->exists() || $party->invoices()->exists()) {
            return redirect()
                ->route('parties.index')
                ->with('error', 'Cannot delete party with existing challans or invoices.');
        }

        $party->delete();

        return redirect()
            ->route('parties.index')
            ->with('success', 'Party deleted successfully.');
    }

    /**
     * Get party details for AJAX requests.
     */
    public function getDetails(Party $party)
    {
        return response()->json([
            'id' => $party->id,
            'name' => $party->name,
            'address' => $party->address,
            'contact_number' => $party->contact_number,
            'gst_number' => $party->gst_number,
        ]);
    }
}
