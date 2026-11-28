<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Database\Seeders\BahanSeeder;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BahanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'nama' => 'Muhammad Fikri Firdaus',
            'peran' => 'admin',
        ]);
    }

    public function test_halaman_stok_bahan_dapat_diakses(): void
    {
        $this->seed(BahanSeeder::class);

        $response = $this->get(route('bahan.index'));

        $response->assertStatus(200);
        $response->assertSee('Stok Bahan');
        $response->assertSee('Pantau stok dan peringatan stok menipis');
        $response->assertSee('Catat Mutasi');
        $response->assertSee('Tambah Bahan');
        $response->assertSee('Nescafe Sachet');
        $response->assertSee('Extra Joss Sachet');
        $response->assertSee('3 bahan berada di bawah batas minimum: Nescafe Sachet, Susu Kental Manis, Kentang Beku (French Fries).');
    }

    public function test_dapat_menambahkan_bahan_baru(): void
    {
        $response = $this->post(route('bahan.store'), [
            'nama' => 'Sirup Pandan',
            'satuan' => 'ml',
            'stok' => 500,
            'stok_minimum' => 200,
        ]);

        $response->assertRedirect(route('bahan.index'));
        $this->assertDatabaseHas('bahan', [
            'nama' => 'Sirup Pandan',
            'satuan' => 'ml',
            'stok' => 500,
        ]);

        $this->assertDatabaseHas('mutasi_stok', [
            'jenis' => 'masuk',
            'jumlah' => 500,
        ]);
    }

    public function test_dapat_mengubah_bahan(): void
    {
        $bahan = Bahan::create([
            'nama' => 'Kopi Robusta',
            'satuan' => 'gram',
            'stok' => 1000,
            'stok_minimum' => 500,
        ]);

        $response = $this->put(route('bahan.update', $bahan), [
            'nama' => 'Kopi Robusta Gold',
            'satuan' => 'gram',
            'stok' => 1200,
            'stok_minimum' => 600,
        ]);

        $response->assertRedirect(route('bahan.index'));
        $this->assertDatabaseHas('bahan', [
            'id' => $bahan->id,
            'nama' => 'Kopi Robusta Gold',
            'stok' => 1200,
        ]);
    }

    public function test_dapat_mencatat_mutasi_masuk(): void
    {
        $bahan = Bahan::create([
            'nama' => 'Susu Segar',
            'satuan' => 'ml',
            'stok' => 1000,
            'stok_minimum' => 500,
        ]);

        $response = $this->post(route('bahan.mutasi'), [
            'bahan_id' => $bahan->id,
            'jenis' => 'masuk',
            'jumlah' => 500,
            'catatan' => 'Restock mingguan',
        ]);

        $response->assertRedirect(route('bahan.index'));
        $this->assertEquals(1500, $bahan->fresh()->stok);
        $this->assertDatabaseHas('mutasi_stok', [
            'bahan_id' => $bahan->id,
            'jenis' => 'masuk',
            'jumlah' => 500,
        ]);
    }

    public function test_dapat_mencatat_mutasi_keluar(): void
    {
        $bahan = Bahan::create([
            'nama' => 'Susu Segar',
            'satuan' => 'ml',
            'stok' => 1000,
            'stok_minimum' => 500,
        ]);

        $response = $this->post(route('bahan.mutasi'), [
            'bahan_id' => $bahan->id,
            'jenis' => 'keluar',
            'jumlah' => 200,
            'catatan' => 'Pemakaian operasional',
        ]);

        $response->assertRedirect(route('bahan.index'));
        $this->assertEquals(800, $bahan->fresh()->stok);
    }

    public function test_dapat_menghapus_bahan(): void
    {
        $bahan = Bahan::create([
            'nama' => 'Bahan Uji Hapus',
            'satuan' => 'pcs',
            'stok' => 10,
            'stok_minimum' => 5,
        ]);

        $response = $this->delete(route('bahan.destroy', $bahan));

        $response->assertRedirect(route('bahan.index'));
        $this->assertDatabaseMissing('bahan', ['id' => $bahan->id]);
    }

    public function test_filter_dan_pencarian_bekerja(): void
    {
        $this->seed(BahanSeeder::class);

        $responseSearch = $this->get(route('bahan.index', ['search' => 'Nescafe']));
        $responseSearch->assertStatus(200);
        $this->assertTrue($responseSearch->viewData('bahanList')->contains('nama', 'Nescafe Sachet'));
        $this->assertFalse($responseSearch->viewData('bahanList')->contains('nama', 'Extra Joss Sachet'));

        $responseFilter = $this->get(route('bahan.index', ['status' => 'menipis']));
        $responseFilter->assertStatus(200);
        $this->assertTrue($responseFilter->viewData('bahanList')->contains('nama', 'Nescafe Sachet'));
        $this->assertTrue($responseFilter->viewData('bahanList')->contains('nama', 'Susu Kental Manis'));
        $this->assertTrue($responseFilter->viewData('bahanList')->contains('nama', 'Kentang Beku (French Fries)'));
        $this->assertFalse($responseFilter->viewData('bahanList')->contains('nama', 'Extra Joss Sachet'));
    }

    public function test_bahan_seeder_menyelaraskan_stok_dan_resep_dengan_menu(): void
    {
        $this->seed(MenuSeeder::class);
        $this->seed(BahanSeeder::class);

        $this->assertSame(29, Menu::count());
        $this->assertSame(32, Bahan::count());

        $menusWithoutIngredients = Menu::doesntHave('bahan')->count();
        $this->assertSame(0, $menusWithoutIngredients);
        $this->assertGreaterThan(0, DB::table('resep_menu')->count());

        $response = $this->get(route('bahan.index'));
        $response->assertStatus(200);
        $response->assertSee('Nescafe');
        $response->assertSee('French Fries');
        $response->assertSee('Mix Platter');

        $searchByMenu = $this->get(route('bahan.index', ['search' => 'French Fries']));
        $searchByMenu->assertStatus(200);
        $this->assertTrue($searchByMenu->viewData('bahanList')->contains('nama', 'Kentang Beku (French Fries)'));
    }

    public function test_dapat_menghubungkan_bahan_ke_menu_saat_tambah_dan_edit(): void
    {
        $category = Category::create(['name' => 'Mie', 'slug' => 'mie']);
        $menuA = Menu::create([
            'category_id' => $category->id,
            'name' => 'Indomie Goreng',
            'slug' => 'indomie-goreng',
            'price' => 10000,
            'is_available' => true,
        ]);
        $menuB = Menu::create([
            'category_id' => $category->id,
            'name' => 'Indomie Rendang',
            'slug' => 'indomie-rendang',
            'price' => 10000,
            'is_available' => true,
        ]);

        $this->post(route('bahan.store'), [
            'nama' => 'Bawang Goreng',
            'satuan' => 'gram',
            'stok' => 500,
            'stok_minimum' => 100,
            'menu_ids' => [$menuA->id],
        ])->assertRedirect(route('bahan.index'));

        $bahan = Bahan::where('nama', 'Bawang Goreng')->firstOrFail();
        $this->assertDatabaseHas('resep_menu', [
            'bahan_id' => $bahan->id,
            'menu_id' => $menuA->id,
        ]);

        $this->put(route('bahan.update', $bahan), [
            'nama' => 'Bawang Goreng Crispy',
            'satuan' => 'gram',
            'stok' => 500,
            'stok_minimum' => 100,
            'sync_menus' => '1',
            'menu_ids' => [$menuB->id],
        ])->assertRedirect(route('bahan.index'));

        $this->assertDatabaseMissing('resep_menu', [
            'bahan_id' => $bahan->id,
            'menu_id' => $menuA->id,
        ]);
        $this->assertDatabaseHas('resep_menu', [
            'bahan_id' => $bahan->id,
            'menu_id' => $menuB->id,
        ]);
    }
}
