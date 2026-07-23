<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPeriodFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_defaults_to_todays_transactions(): void
    {
        Carbon::setTestNow('2026-07-23 12:00:00');

        $product = $this->createProduct();
        $this->createTransaction($product, '2026-07-23 09:00:00', 25000, 2);
        $this->createTransaction($product, '2026-07-22 09:00:00', 10000, 1);

        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('period', 'hari_ini')
            ->assertViewHas('summary', [
                'revenue' => 25000,
                'count' => 1,
                'items' => 2,
            ])
            ->assertSee('Ringkasan penjualan hari ini');
    }

    public function test_dashboard_can_filter_the_last_seven_days(): void
    {
        Carbon::setTestNow('2026-07-23 12:00:00');

        $product = $this->createProduct();
        $this->createTransaction($product, '2026-07-23 09:00:00', 25000, 2);
        $this->createTransaction($product, '2026-07-18 09:00:00', 10000, 1);
        $this->createTransaction($product, '2026-07-16 09:00:00', 50000, 4);

        $response = $this->get(route('dashboard', ['period' => '7_hari']));

        $response->assertOk()
            ->assertViewHas('period', '7_hari')
            ->assertViewHas('summary', [
                'revenue' => 35000,
                'count' => 2,
                'items' => 3,
            ])
            ->assertViewHas('chartLabels', fn ($labels) => $labels->count() === 7);
    }

    public function test_dashboard_can_filter_a_custom_period(): void
    {
        Carbon::setTestNow('2026-07-23 12:00:00');

        $product = $this->createProduct();
        $included = $this->createTransaction($product, '2026-07-10 09:00:00', 30000, 3);
        $excluded = $this->createTransaction($product, '2026-07-20 09:00:00', 50000, 5);

        $response = $this->get(route('dashboard', [
            'period' => 'kustom',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-15',
        ]));

        $response->assertOk()
            ->assertViewHas('summary', [
                'revenue' => 30000,
                'count' => 1,
                'items' => 3,
            ])
            ->assertViewHas('recent', function ($recent) use ($included, $excluded) {
                return $recent->pluck('code')->contains($this->transactionCode($included))
                    && ! $recent->pluck('code')->contains($this->transactionCode($excluded));
            });
    }

    public function test_custom_period_requires_a_valid_date_range(): void
    {
        $response = $this->from(route('dashboard'))->get(route('dashboard', [
            'period' => 'kustom',
            'start_date' => '2026-07-20',
            'end_date' => '2026-07-10',
        ]));

        $response->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('end_date');
    }

    private function createProduct(): Product
    {
        return Product::create([
            'sku' => 'DASHBOARD-001',
            'name' => 'Produk Dashboard',
            'selling_price' => 10000,
            'stock_quantity' => 20,
            'min_stock' => 2,
            'is_active' => true,
        ]);
    }

    private function createTransaction(
        Product $product,
        string $createdAt,
        int $total,
        int $quantity
    ): Transaction {
        $transaction = Transaction::create([
            'total' => $total,
            'transaction_date' => $createdAt,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $total / $quantity,
            'amount' => $total,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        return $transaction;
    }

    private function transactionCode(Transaction $transaction): string
    {
        return 'TRX-'.str_pad((string) $transaction->id, 4, '0', STR_PAD_LEFT);
    }
}
