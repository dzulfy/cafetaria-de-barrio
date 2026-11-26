<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\User;
use Database\Seeders\BahanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertSee('Biji kopi arabica');
        $response->assertSee('Susu UHT');
        $response->assertSee('3 bahan berada di bawah batas minimum: Biji kopi arabica, Gula aren, Pisang.');
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

        $responseSearch = $this->get(route('bahan.index', ['search' => 'Pisang']));
        $responseSearch->assertStatus(200);
        $this->assertTrue($responseSearch->viewData('bahanList')->contains('nama', 'Pisang'));
        $this->assertFalse($responseSearch->viewData('bahanList')->contains('nama', 'Susu UHT'));

        $responseFilter = $this->get(route('bahan.index', ['status' => 'menipis']));
        $responseFilter->assertStatus(200);
        $this->assertTrue($responseFilter->viewData('bahanList')->contains('nama', 'Biji kopi arabica'));
        $this->assertTrue($responseFilter->viewData('bahanList')->contains('nama', 'Gula aren'));
        $this->assertTrue($responseFilter->viewData('bahanList')->contains('nama', 'Pisang'));
        $this->assertFalse($responseFilter->viewData('bahanList')->contains('nama', 'Susu UHT'));
    }
}
