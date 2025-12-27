# Invoice Management System - Setup Guide

This document provides complete instructions for setting up and running the Invoice Management System built with Laravel 12.

## 📋 Prerequisites

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js and NPM (for asset compilation, optional)

## 🚀 Installation Steps

### 1. Install Dependencies

The required packages have already been installed:
- `yajra/laravel-datatables-oracle` - For DataTables integration
- `barryvdh/laravel-dompdf` - For PDF generation

If you need to reinstall:
```bash
composer install
```

### 2. Database Configuration

Update your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Create Database

Create a new MySQL database:

```sql
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Run Migrations

Run the migrations to create all necessary tables:

```bash
php artisan migrate
```

This will create the following tables:
- `customers` - Stores customer information
- `invoices` - Stores invoice headers
- `invoice_items` - Stores invoice line items

### 5. Seed Sample Data (Optional)

To populate the database with sample customers and invoices:

```bash
php artisan db:seed
```

Or seed only the invoice system data:

```bash
php artisan db:seed --class=CustomerSeeder
php artisan db:seed --class=InvoiceSeeder
```

### 6. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## 📁 Project Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── CustomerController.php    # Customer CRUD operations
│       └── InvoiceController.php     # Invoice CRUD + PDF export
├── Models/
    ├── Customer.php                  # Customer model
    ├── Invoice.php                   # Invoice model
    └── InvoiceItem.php               # Invoice item model

database/
├── migrations/
│   ├── *_create_customers_table.php
│   ├── *_create_invoices_table.php
│   └── *_create_invoice_items_table.php
└── seeders/
    ├── CustomerSeeder.php
    └── InvoiceSeeder.php

resources/
└── views/
    ├── layouts/
    │   └── app.blade.php             # Main layout with Bootstrap 5
    ├── customers/
    │   ├── index.blade.php          # Customer list with DataTables
    │   ├── create.blade.php         # Create customer form
    │   ├── edit.blade.php            # Edit customer form
    │   └── show.blade.php            # Customer details
    └── invoices/
        ├── index.blade.php           # Invoice list with DataTables
        ├── create.blade.php          # Create invoice form (with dynamic items)
        ├── edit.blade.php            # Edit invoice form
        ├── show.blade.php            # Invoice details
        └── pdf.blade.php             # PDF template

routes/
└── web.php                           # All application routes
```

## 🎯 Features

### Customers Module
- ✅ Create, Read, Update, Delete customers
- ✅ DataTables integration for listing
- ✅ Form validation
- ✅ Bootstrap 5 UI

### Invoice Module
- ✅ Auto-generated invoice numbers (INV-0001 format)
- ✅ Create invoices with multiple items
- ✅ Dynamic invoice item rows (add/remove with jQuery)
- ✅ Auto-calculation of subtotals and total
- ✅ Status management (Draft, Paid, Unpaid)
- ✅ DataTables integration
- ✅ PDF export functionality
- ✅ Full CRUD operations

### PDF Export
- ✅ Professional invoice PDF layout
- ✅ Company information placeholder
- ✅ Customer details
- ✅ Invoice items table
- ✅ Total amount calculation
- ✅ Status badges

## 🔗 Routes

### Customer Routes
- `GET /customers` - List all customers (DataTables)
- `GET /customers/create` - Show create form
- `POST /customers` - Store new customer
- `GET /customers/{id}` - Show customer details
- `GET /customers/{id}/edit` - Show edit form
- `PUT /customers/{id}` - Update customer
- `DELETE /customers/{id}` - Delete customer

### Invoice Routes
- `GET /invoices` - List all invoices (DataTables)
- `GET /invoices/create` - Show create form
- `POST /invoices` - Store new invoice
- `GET /invoices/{id}` - Show invoice details
- `GET /invoices/{id}/edit` - Show edit form
- `PUT /invoices/{id}` - Update invoice
- `DELETE /invoices/{id}` - Delete invoice
- `GET /invoices/{id}/download-pdf` - Download invoice PDF

## 💡 Usage Examples

### Creating a Customer
1. Navigate to Customers → Add New Customer
2. Fill in Name, Email, and Phone
3. Click "Create Customer"

### Creating an Invoice
1. Navigate to Invoices → Create New Invoice
2. Select a customer from dropdown
3. Set date, due date, and status
4. Add invoice items:
   - Click "Add Item" to add more rows
   - Fill in description, quantity, and unit price
   - Subtotal is auto-calculated
   - Total amount updates automatically
5. Click "Create Invoice"

### Generating PDF
1. Go to invoice list or view an invoice
2. Click "Download PDF" button
3. PDF will be generated and downloaded

## 🛠️ Technologies Used

- **Laravel 12** - PHP Framework
- **MySQL** - Database
- **Bootstrap 5** - CSS Framework
- **jQuery** - JavaScript Library
- **Yajra DataTables** - Server-side DataTables
- **barryvdh/laravel-dompdf** - PDF Generation

## 📝 Notes

- Invoice numbers are auto-generated in format: `INV-0001`, `INV-0002`, etc.
- At least one invoice item is required when creating/editing invoices
- Invoice items can be dynamically added/removed using jQuery
- All calculations (subtotals, totals) are done client-side and server-side
- PDF generation uses DomPDF library

## 🔧 Troubleshooting

### DataTables not loading
- Ensure jQuery is loaded before DataTables scripts
- Check browser console for JavaScript errors

### PDF not generating
- Ensure `barryvdh/laravel-dompdf` is installed
- Check storage permissions
- Verify DomPDF configuration

### Migration errors
- Ensure database exists and credentials are correct
- Check if tables already exist (drop them first if needed)

## 📄 License

This project is open-sourced software licensed under the MIT license.

