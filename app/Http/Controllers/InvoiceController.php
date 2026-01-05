<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Challan;
use App\Models\Invoice;
use App\Models\Party;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with('party');

        // Filter by party
        if ($request->filled('party_id')) {
            $query->where('party_id', $request->party_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // Search by invoice number
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%");
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15);
        $parties = Party::orderBy('name')->get();

        return view('invoices.index', compact('invoices', 'parties'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $parties = Party::orderBy('name')->get();
        $invoiceNumber = Invoice::generateInvoiceNumber();

        return view('invoices.create', compact('parties', 'invoiceNumber'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(InvoiceRequest $request)
    {
        DB::beginTransaction();

        try {
            // Validate that selected challans belong to the party (allow both invoiced and non-invoiced)
            $challans = Challan::whereIn('id', $request->challan_ids)
                ->where('party_id', $request->party_id)
                ->get();

            if ($challans->count() !== count($request->challan_ids)) {
                return back()
                    ->withInput()
                    ->with('error', 'One or more selected challans are invalid.');
            }

            // Calculate subtotal from challans (server-side)
            $subtotal = $challans->sum('subtotal');

            // Calculate all amounts
            $amounts = Invoice::calculateAmounts(
                $subtotal,
                $request->gst_percent ?? 0,
                $request->tds_percent ?? 0,
                $request->discount_type ?? 'fixed',
                $request->discount_value ?? 0
            );

            // Create invoice
            $invoice = Invoice::create([
                'party_id' => $request->party_id,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date' => $request->invoice_date,
                'subtotal' => $amounts['subtotal'],
                'gst_percent' => $request->gst_percent ?? 0,
                'gst_amount' => $amounts['gst_amount'],
                'tds_percent' => $request->tds_percent ?? 0,
                'tds_amount' => $amounts['tds_amount'],
                'discount_type' => $request->discount_type ?? 'fixed',
                'discount_value' => $request->discount_value ?? 0,
                'discount_amount' => $amounts['discount_amount'],
                'final_amount' => $amounts['final_amount'],
                'notes' => $request->notes,
            ]);

            // Attach challans to invoice
            $invoice->challans()->attach($request->challan_ids);

            // Mark challans as invoiced
            Challan::whereIn('id', $request->challan_ids)->update(['is_invoiced' => true]);

            DB::commit();

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create invoice. Please try again.');
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['party', 'challans.items']);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        DB::beginTransaction();

        try {
            // Mark challans as not invoiced
            $invoice->challans()->update(['is_invoiced' => false]);

            // Detach challans
            $invoice->challans()->detach();

            // Delete invoice
            $invoice->delete();

            DB::commit();

            return redirect()
                ->route('invoices.index')
                ->with('success', 'Invoice deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete invoice. Please try again.');
        }
    }

    /**
     * Download invoice as PDF.
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['party', 'challans.items']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Print invoice (HTML view).
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['party', 'challans.items']);
        return view('invoices.print', compact('invoice'));
    }

    /**
     * Calculate invoice amounts (AJAX endpoint).
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'challan_ids' => 'required|array|min:1',
            'gst_percent' => 'nullable|numeric|min:0|max:100',
            'tds_percent' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        // Get challans and calculate subtotal (allow both invoiced and non-invoiced)
        $challans = Challan::whereIn('id', $request->challan_ids)
            ->get();

        $subtotal = $challans->sum('subtotal');

        // Calculate amounts
        $amounts = Invoice::calculateAmounts(
            $subtotal,
            $request->gst_percent ?? 0,
            $request->tds_percent ?? 0,
            $request->discount_type ?? 'fixed',
            $request->discount_value ?? 0
        );

        return response()->json([
            'subtotal' => number_format($amounts['subtotal'], 2),
            'gst_amount' => number_format($amounts['gst_amount'], 2),
            'tds_amount' => number_format($amounts['tds_amount'], 2),
            'discount_amount' => number_format($amounts['discount_amount'], 2),
            'final_amount' => number_format($amounts['final_amount'], 2),
            'subtotal_raw' => $amounts['subtotal'],
            'gst_amount_raw' => $amounts['gst_amount'],
            'tds_amount_raw' => $amounts['tds_amount'],
            'discount_amount_raw' => $amounts['discount_amount'],
            'final_amount_raw' => $amounts['final_amount'],
        ]);
    }
}
