<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Meja;
use App\Models\Pengeluaran;
use App\Models\Pesanan;
use App\Models\User;
use Database\Seeders\DashboardSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'nama' => 'M. Fikri Firdaus',
            'email' => 'admin@debarrio.com',
            'peran' => 'admin',
        ]);
    }

    public function test_dashboard_page_can_be_rendered(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Pendapatan hari ini');
        $response->assertSee('Jumlah pesanan');
        $response->assertSee('Pengeluaran');
        $response->assertSee('Stok menipis');
        $response->assertSee('Pendapatan 7 hari terakhir');
        $response->assertSee('Menu terlaris');
        $response->assertSee('Pesanan terbaru');
    }

    public function test_dashboard_displays_seeded_data_accurately(): void
    {
        $this->seed(\Database\Seeders\BahanSeeder::class);
        $this->seed(DashboardSeeder::class);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Rp 1.248.000');
        $response->assertSee('+12% dari kemarin');
        $response->assertSee('47');
        $response->assertSee('8 menunggu diproses');
        $response->assertSee('Rp 320.000');
        $response->assertSee('3 transaksi');
        $response->assertSee('3 bahan');
        $response->assertSee('Perlu belanja');
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('86 terjual');
        $response->assertSee('DB-0124');
        $response->assertSee('Meja 05');
        $response->assertSee('Biji kopi arabica');
        $response->assertSee('Gula aren');
        $response->assertSee('Pisang');
    }
}
