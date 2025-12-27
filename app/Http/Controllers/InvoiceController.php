<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::with('customer')->select('invoices.*');
            return DataTables::of($invoices)
                ->addIndexColumn()
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->name ?? 'N/A';
                })
                ->addColumn('total_amount', function ($row) {
                    return '$' . number_format($row->total_amount, 2);
                })
                ->addColumn('status', function ($row) {
                    $badgeClass = match($row->status) {
                        'paid' => 'success',
                        'unpaid' => 'warning',
                        'draft' => 'secondary',
                        default => 'secondary'
                    };
                    return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('date', function ($row) {
                    return $row->date->format('Y-m-d');
                })
                ->addColumn('due_date', function ($row) {
                    return $row->due_date->format('Y-m-d');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('invoices.show', $row->id) . '" class="btn btn-sm btn-info me-1">View</a>';
                    $btn .= '<a href="' . route('invoices.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">Edit</a>';
                    $btn .= '<a href="' . route('invoices.download-pdf', $row->id) . '" class="btn btn-sm btn-success me-1" target="_blank">PDF</a>';
                    $btn .= '<form action="' . route('invoices.destroy', $row->id) . '" method="POST" class="d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        return view('invoices.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:draft,paid,unpaid',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate total amount
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $totalAmount += $subtotal;
        }

        // Create invoice
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'date' => $validated['date'],
            'due_date' => $validated['due_date'],
            'customer_id' => $validated['customer_id'],
            'status' => $validated['status'],
            'total_amount' => $totalAmount,
        ]);

        // Create invoice items
        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with(['customer', 'items'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $customers = Customer::all();
        return view('invoices.edit', compact('invoice', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:draft,paid,unpaid',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate total amount
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $totalAmount += $subtotal;
        }

        // Update invoice
        $invoice->update([
            'date' => $validated['date'],
            'due_date' => $validated['due_date'],
            'customer_id' => $validated['customer_id'],
            'status' => $validated['status'],
            'total_amount' => $totalAmount,
        ]);

        // Delete existing items
        $invoice->items()->delete();

        // Create new invoice items
        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Download invoice as PDF.
     */
    public function downloadPdf(string $id)
    {
        $invoice = Invoice::with(['customer', 'items'])->findOrFail($id);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
