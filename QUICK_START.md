# Invoice Management System - Quick Start Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoice_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 2: Create Database
```sql
CREATE DATABASE invoice_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 3: Run Migrations
```bash
php artisan migrate
```

### Step 4: (Optional) Seed Sample Data
```bash
php artisan db:seed
```

### Step 5: Start Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 📍 Main URLs

- **Home/Invoices**: `http://localhost:8000/invoices`
- **Customers**: `http://localhost:8000/customers`
- **Create Invoice**: `http://localhost:8000/invoices/create`
- **Create Customer**: `http://localhost:8000/customers/create`

## ✨ Key Features

1. **Customers Management**
   - Full CRUD operations
   - DataTables with server-side processing
   - Bootstrap 5 forms with validation

2. **Invoice Management**
   - Auto-generated invoice numbers (INV-0001, INV-0002, etc.)
   - Dynamic invoice items (add/remove rows)
   - Auto-calculation of subtotals and totals
   - Status management (Draft, Paid, Unpaid)
   - DataTables integration
   - PDF export

3. **PDF Export**
   - Professional invoice layout
   - Company info placeholder
   - Customer details
   - Itemized billing
   - Downloadable PDF files

## 🎯 How to Use

### Create a Customer
1. Go to Customers → Add New Customer
2. Fill: Name, Email, Phone
3. Click "Create Customer"

### Create an Invoice
1. Go to Invoices → Create New Invoice
2. Select customer, set dates and status
3. Add items (at least 1 required):
   - Click "Add Item" for more rows
   - Enter description, quantity, unit price
   - Subtotal auto-calculates
4. Total updates automatically
5. Click "Create Invoice"

### Download PDF
- From invoice list: Click "PDF" button
- From invoice view: Click "Download PDF"
- PDF downloads automatically

## 🔧 Troubleshooting

**DataTables not loading?**
- Check browser console for errors
- Ensure jQuery loads before DataTables

**PDF not generating?**
- Check `storage` folder permissions
- Verify DomPDF is installed: `composer show barryvdh/laravel-dompdf`

**Migration errors?**
- Drop existing tables if needed
- Check database credentials in `.env`

## 📦 Installed Packages

- `yajra/laravel-datatables-oracle` (v12.6) - DataTables
- `barryvdh/laravel-dompdf` (v3.1) - PDF generation

## 📝 Notes

- Invoice numbers auto-increment: INV-0001, INV-0002, etc.
- Minimum 1 invoice item required
- All calculations done client-side (jQuery) and server-side (PHP)
- Bootstrap 5 for responsive UI
- jQuery for dynamic form interactions

For detailed documentation, see `INVOICE_SYSTEM_SETUP.md`

