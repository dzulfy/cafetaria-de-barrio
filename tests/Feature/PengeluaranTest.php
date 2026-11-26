<?php

namespace Tests\Feature;

use App\Models\Pengeluaran;
use App\Models\User;
use Database\Seeders\PengeluaranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengeluaranTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'nama' => 'Muhammad Fikri Firdaus',
            'email' => 'admin@debarrio.id',
            'peran' => 'admin',
        ]);
    }

    public function test_halaman_pengeluaran_dapat_diakses_dan_menampilkan_data_presisi(): void
    {
        $this->seed(PengeluaranSeeder::class);

        $response = $this->get(route('pengeluaran.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengeluaran');
        $response->assertSee('Catat dan pantau pengeluaran operasional');
        $response->assertSee('Total bulan ini');
        $response->assertSee('Rp 6.120.000');
        $response->assertSee('Hari ini');
        $response->assertSee('Rp 320.000');
        $response->assertSee('Jumlah catatan');
        $response->assertSee('21');

        // Memastikan 6 item halaman pertama sesuai desain acuan
        $response->assertSee('Susu UHT dan gula aren');
        $response->assertSee('Rp 245.000');
        $response->assertSee('Es batu');
        $response->assertSee('Rp 75.000');
        $response->assertSee('Biji kopi arabica 1 kg');
        $response->assertSee('Rp 150.000');
        $response->assertSee('Pisang dan telur');
        $response->assertSee('Rp 110.000');
        $response->assertSee('Token listrik');
        $response->assertSee('Rp 300.000');
        $response->assertSee('Gas LPG 3 kg');
        $response->assertSee('Rp 95.000');

        // Memastikan pencatat Owner dan Ayu Lestari tampil
        $response->assertSee('Owner');
        $response->assertSee('Ayu Lestari');
        $response->assertSee('Bahan baku');
        $response->assertSee('Operasional');
    }

    public function test_dapat_mencatat_pengeluaran_baru(): void
    {
        $response = $this->post(route('pengeluaran.store'), [
            'deskripsi' => 'Beli sirup melon',
            'jumlah' => 45000,
            'kategori' => 'Bahan baku',
            'tanggal_pengeluaran' => now()->toDateString(),
            'catatan' => 'Sirup botol kaca',
        ]);

        $response->assertRedirect(route('pengeluaran.index'));
        $this->assertDatabaseHas('pengeluaran', [
            'deskripsi' => 'Beli sirup melon',
            'jumlah' => 45000,
            'kategori' => 'Bahan baku',
        ]);
    }

    public function test_dapat_mengubah_data_pengeluaran(): void
    {
        $pengeluaran = Pengeluaran::create([
            'pengguna_id' => User::first()->id,
            'deskripsi' => 'Beli spidol papan',
            'jumlah' => 15000,
            'kategori' => 'Operasional',
            'tanggal_pengeluaran' => now()->toDateString(),
        ]);

        $response = $this->put(route('pengeluaran.update', $pengeluaran), [
            'deskripsi' => 'Beli spidol papan & penghapus',
            'jumlah' => 25000,
            'kategori' => 'Operasional',
            'tanggal_pengeluaran' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('pengeluaran.index'));
        $this->assertDatabaseHas('pengeluaran', [
            'id' => $pengeluaran->id,
            'deskripsi' => 'Beli spidol papan & penghapus',
            'jumlah' => 25000,
        ]);
    }

    public function test_dapat_menghapus_data_pengeluaran(): void
    {
        $pengeluaran = Pengeluaran::create([
            'pengguna_id' => User::first()->id,
            'deskripsi' => 'Pengeluaran Uji Hapus',
            'jumlah' => 50000,
            'kategori' => 'Operasional',
            'tanggal_pengeluaran' => now()->toDateString(),
        ]);

        $response = $this->delete(route('pengeluaran.destroy', $pengeluaran));

        $response->assertRedirect(route('pengeluaran.index'));
        $this->assertDatabaseMissing('pengeluaran', ['id' => $pengeluaran->id]);
    }

    public function test_pencarian_dan_filter_pengeluaran_bekerja(): void
    {
        $this->seed(PengeluaranSeeder::class);

        // Filter pencarian uraian
        $responseSearch = $this->get(route('pengeluaran.index', ['search' => 'Token listrik']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Token listrik');
        $responseSearch->assertDontSee('Susu UHT dan gula aren');

        // Filter pencatat Ayu Lestari
        $responsePencatat = $this->get(route('pengeluaran.index', ['pencatat' => 'Ayu Lestari']));
        $responsePencatat->assertStatus(200);
        $responsePencatat->assertSee('Pisang dan telur');
        $responsePencatat->assertSee('Gas LPG 3 kg');
    }
}
