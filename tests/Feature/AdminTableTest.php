<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CafeTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CafeTable::create([
            'table_number' => '01',
            'name' => 'Meja 01',
            'token' => 'x7Kp9aLm2Q',
            'status' => 'aktif',
        ]);

        CafeTable::create([
            'table_number' => '02',
            'name' => 'Meja 02',
            'token' => 'b3Td8vNq5R',
            'status' => 'aktif',
        ]);
    }

    public function test_admin_can_view_tables_page(): void
    {
        $response = $this->get(route('admin.tables.index'));

        $response->assertStatus(200);
        $response->assertSee('Meja & QR Code', false);
        $response->assertSee('Buat QR code untuk pemesanan mandiri');
        $response->assertSee('Meja 01');
        $response->assertSee('x7Kp9aLm2Q');
        $response->assertSee('Tambah Meja');
    }

    public function test_admin_can_create_new_table(): void
    {
        $response = $this->post(route('admin.tables.store'), [
            'table_number' => '09',
            'name' => 'Meja 09 (Outdoor)',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('admin.tables.index'));

        $this->assertDatabaseHas('cafe_tables', [
            'table_number' => '09',
            'name' => 'Meja 09 (Outdoor)',
            'status' => 'aktif',
        ]);
    }

    public function test_admin_can_toggle_table_status(): void
    {
        $table = CafeTable::where('table_number', '01')->first();

        $response = $this->post(route('admin.tables.toggle', $table->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('cafe_tables', [
            'id' => $table->id,
            'status' => 'nonaktif',
        ]);

        // Toggle back to active
        $this->post(route('admin.tables.toggle', $table->id));
        $this->assertDatabaseHas('cafe_tables', [
            'id' => $table->id,
            'status' => 'aktif',
        ]);
    }

    public function test_admin_can_download_qr_svg(): void
    {
        $table = CafeTable::where('table_number', '01')->first();

        $response = $this->get(route('admin.tables.download-qr', $table->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_admin_can_view_printable_qr_card(): void
    {
        $table = CafeTable::where('table_number', '01')->first();

        $response = $this->get(route('admin.tables.print', $table->id));

        $response->assertStatus(200);
        $response->assertSee('Cafetaria De Barrio');
        $response->assertSee('MEJA 01');
    }
}
