<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_report_displays_local_product_and_sales_summary(): void
    {
        $category = Category::create(['name' => 'Minuman']);
        $product = Product::create([
            'sku' => 'MIN-001',
            'name' => 'Kopi Susu',
            'selling_price' => 15_000,
            'stock_quantity' => 5,
            'min_stock' => 5,
            'category_id' => $category->id,
        ]);
        $transaction = Transaction::create([
            'total' => 30_000,
            'transaction_date' => now(),
        ]);
        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 15_000,
            'amount' => 30_000,
        ]);

        $response = $this->get(route('reports.products', ['filter' => 'today']));

        $response->assertOk()
            ->assertViewIs('reports.products')
            ->assertSee('Kopi Susu')
            ->assertSee('30.000')
            ->assertViewHas('summary', function (array $summary) {
                return $summary['total_products'] === 1
                    && $summary['low_stock_products'] === 1
                    && $summary['quantity_sold'] === 2
                    && $summary['revenue'] === 30_000;
            });
    }

    public function test_product_report_can_filter_by_category_and_stock_status(): void
    {
        $food = Category::create(['name' => 'Makanan']);
        $drink = Category::create(['name' => 'Minuman']);

        Product::create([
            'sku' => 'FOOD-001',
            'name' => 'Roti Bakar',
            'selling_price' => 12_000,
            'stock_quantity' => 0,
            'min_stock' => 3,
            'category_id' => $food->id,
        ]);
        Product::create([
            'sku' => 'DRINK-001',
            'name' => 'Es Teh',
            'selling_price' => 5_000,
            'stock_quantity' => 20,
            'min_stock' => 5,
            'category_id' => $drink->id,
        ]);

        $response = $this->get(route('reports.products', [
            'category_id' => $food->id,
            'stock_status' => 'habis',
        ]));

        $response->assertOk()
            ->assertSee('Roti Bakar')
            ->assertDontSee('Es Teh');
    }

    public function test_custom_period_requires_valid_dates(): void
    {
        $this->get(route('reports.products', ['filter' => 'custom']))
            ->assertSessionHasErrors(['start_date', 'end_date']);
    }
}
