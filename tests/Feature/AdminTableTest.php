<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CafeTable;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create(['peran' => 'admin']);
        $this->actingAs($admin);

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
        $response->assertSee('Edit');
        $response->assertSee('Hapus');
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

    public function test_admin_can_update_table(): void
    {
        $table = CafeTable::where('table_number', '01')->firstOrFail();

        $response = $this->put(route('admin.tables.update', $table), [
            'table_number' => '10',
            'name' => 'Meja 10 VIP',
            'status' => 'nonaktif',
        ]);

        $response->assertRedirect(route('admin.tables.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cafe_tables', [
            'id' => $table->id,
            'table_number' => '10',
            'name' => 'Meja 10 VIP',
            'status' => 'nonaktif',
            'token' => 'x7Kp9aLm2Q',
        ]);
    }

    public function test_admin_can_update_table_keeping_same_table_number(): void
    {
        $table = CafeTable::where('table_number', '01')->firstOrFail();

        $response = $this->put(route('admin.tables.update', $table), [
            'table_number' => '01',
            'name' => 'Meja 01 Teras',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('admin.tables.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('cafe_tables', [
            'id' => $table->id,
            'table_number' => '01',
            'name' => 'Meja 01 Teras',
            'status' => 'aktif',
        ]);
    }

    public function test_admin_can_regenerate_qr_token_when_updating_table(): void
    {
        $table = CafeTable::where('table_number', '01')->firstOrFail();
        $oldToken = $table->token;

        $response = $this->put(route('admin.tables.update', $table), [
            'table_number' => '01',
            'name' => 'Meja 01',
            'status' => 'aktif',
            'regenerate_token' => '1',
        ]);

        $response->assertRedirect(route('admin.tables.index'));

        $table->refresh();
        $this->assertNotEmpty($table->token);
        $this->assertNotSame($oldToken, $table->token);
    }

    public function test_admin_cannot_update_table_to_duplicate_table_number(): void
    {
        $table = CafeTable::where('table_number', '01')->firstOrFail();

        $response = $this->from(route('admin.tables.index'))
            ->put(route('admin.tables.update', $table), [
                'table_number' => '2',
                'name' => 'Duplikat Meja 02',
                'status' => 'aktif',
            ]);

        $response->assertRedirect(route('admin.tables.index'));
        $response->assertSessionHasErrors('table_number');

        $this->assertDatabaseHas('cafe_tables', [
            'id' => $table->id,
            'table_number' => '01',
            'name' => 'Meja 01',
        ]);
    }

    public function test_admin_can_delete_table(): void
    {
        $table = CafeTable::where('table_number', '02')->firstOrFail();

        $response = $this->delete(route('admin.tables.destroy', $table));

        $response->assertRedirect(route('admin.tables.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cafe_tables', [
            'id' => $table->id,
        ]);
    }

    public function test_deleting_table_preserves_existing_orders_by_nullifying_table_id(): void
    {
        $table = CafeTable::where('table_number', '01')->firstOrFail();

        $order = Order::factory()->create([
            'table_id' => $table->id,
            'table_number' => $table->table_number,
        ]);

        $response = $this->delete(route('admin.tables.destroy', $table));

        $response->assertRedirect(route('admin.tables.index'));
        $this->assertDatabaseMissing('cafe_tables', ['id' => $table->id]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'table_id' => null,
            'table_number' => '01',
        ]);
    }

    public function test_non_admin_cannot_update_or_delete_table(): void
    {
        $kasir = User::factory()->create(['peran' => 'kasir']);
        $table = CafeTable::where('table_number', '01')->firstOrFail();

        $this->actingAs($kasir)
            ->put(route('admin.tables.update', $table), [
                'table_number' => '99',
                'name' => 'Unauthorized',
                'status' => 'aktif',
            ])
            ->assertStatus(403);

        $this->actingAs($kasir)
            ->delete(route('admin.tables.destroy', $table))
            ->assertStatus(403);

        $this->assertDatabaseHas('cafe_tables', [
            'id' => $table->id,
            'table_number' => '01',
        ]);
    }
}
