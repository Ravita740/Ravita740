<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();

        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Please run CustomerSeeder first.');
            return;
        }

        $statuses = ['draft', 'paid', 'unpaid'];

        foreach ($customers->take(3) as $index => $customer) {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'date' => now()->subDays(rand(1, 30)),
                'due_date' => now()->addDays(rand(1, 30)),
                'customer_id' => $customer->id,
                'status' => $statuses[array_rand($statuses)],
                'total_amount' => 0,
            ]);

            // Create invoice items
            $items = [
                [
                    'description' => 'Web Development Services',
                    'quantity' => rand(10, 40),
                    'unit_price' => rand(50, 150),
                ],
                [
                    'description' => 'Consulting Hours',
                    'quantity' => rand(5, 20),
                    'unit_price' => rand(75, 200),
                ],
            ];

            $totalAmount = 0;
            foreach ($items as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['unit_price'];
                $totalAmount += $subtotal;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Update invoice total
            $invoice->update(['total_amount' => $totalAmount]);
        }
    }
}
