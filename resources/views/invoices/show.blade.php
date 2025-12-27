@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Invoice #{{ $invoice->invoice_number }}</h5>
                <div>
                    <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-primary me-1">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('invoices.download-pdf', $invoice->id) }}" class="btn btn-sm btn-success" target="_blank">
                        <i class="fas fa-download me-1"></i>Download PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Invoice Information</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Invoice Number:</th>
                                <td>{{ $invoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <th>Date:</th>
                                <td>{{ $invoice->date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Due Date:</th>
                                <td>{{ $invoice->due_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @php
                                        $badgeClass = match($invoice->status) {
                                            'paid' => 'success',
                                            'unpaid' => 'warning',
                                            'draft' => 'secondary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($invoice->status) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Total Amount:</th>
                                <td><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Name:</th>
                                <td>{{ $invoice->customer->name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $invoice->customer->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $invoice->customer->phone }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <h6>Invoice Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                <td><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

