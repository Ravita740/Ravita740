@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Create New Invoice</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" id="invoice_number" 
                                       value="Auto-generated" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" 
                                        id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="unpaid" {{ old('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Invoice Items</h5>
                        <button type="button" class="btn btn-sm btn-success" id="add-item-row">
                            <i class="fas fa-plus me-1"></i>Add Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th width="40%">Description</th>
                                    <th width="15%">Quantity</th>
                                    <th width="15%">Unit Price</th>
                                    <th width="15%">Subtotal</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                                <!-- Items will be added dynamically -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td colspan="2">
                                        <strong id="total-amount">$0.00</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let itemRowCount = 0;

    $(document).ready(function() {
        // Add initial 2 rows
        addItemRow();
        addItemRow();

        // Add item row
        $('#add-item-row').click(function() {
            addItemRow();
        });

        // Remove item row
        $(document).on('click', '.remove-item-row', function() {
            if ($('#items-tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotal();
            } else {
                alert('At least one item is required.');
            }
        });

        // Calculate on input change
        $(document).on('input', '.quantity, .unit-price', function() {
            calculateRowSubtotal($(this).closest('tr'));
            calculateTotal();
        });
    });

    function addItemRow() {
        itemRowCount++;
        const row = `
            <tr>
                <td>
                    <input type="text" class="form-control description" 
                           name="items[${itemRowCount}][description]" required>
                </td>
                <td>
                    <input type="number" class="form-control quantity" 
                           name="items[${itemRowCount}][quantity]" 
                           min="1" step="1" value="1" required>
                </td>
                <td>
                    <input type="number" class="form-control unit-price" 
                           name="items[${itemRowCount}][unit_price]" 
                           min="0" step="0.01" value="0.00" required>
                </td>
                <td>
                    <input type="text" class="form-control subtotal" 
                           value="$0.00" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-item-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#items-tbody').append(row);
    }

    function calculateRowSubtotal(row) {
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
        const subtotal = quantity * unitPrice;
        row.find('.subtotal').val('$' + subtotal.toFixed(2));
    }

    function calculateTotal() {
        let total = 0;
        $('#items-tbody tr').each(function() {
            const quantity = parseFloat($(this).find('.quantity').val()) || 0;
            const unitPrice = parseFloat($(this).find('.unit-price').val()) || 0;
            total += quantity * unitPrice;
        });
        $('#total-amount').text('$' + total.toFixed(2));
    }
</script>
@endpush

