<?php

namespace Tests\Feature;

use App\Models\SyncQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_sinkron_page_loads_successfully_with_data_from_database(): void
    {
        $response = $this->get(route('kasir.sinkron'));

        $response->assertStatus(200);
        $response->assertSee('Sinkronisasi Data');
        $response->assertSee('Menunggu sinkron');
        $response->assertSee('Gagal, perlu dicoba ulang');
        $response->assertSee('Tersinkron hari ini');
        $response->assertSee('Terakhir sinkron');
        $response->assertSee('a41f...c9e2');
        $response->assertSee('7be0...12d8');
        $response->assertSee('c2d9...e771');
        $response->assertSee('Menunggu koneksi');
        $response->assertSee('Percobaan ke-3');
    }

    public function test_can_retrieve_sync_status_as_json(): void
    {
        $response = $this->getJson(route('sync.status'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'stats' => [
                'menunggu',
                'gagal',
                'synced_today',
                'terakhir_sinkron',
                'total_menunggu',
                'is_offline',
            ],
            'queue',
        ]);
        $response->assertJson([
            'success' => true,
            'stats' => [
                'menunggu' => 2,
                'gagal' => 1,
            ],
        ]);
    }

    public function test_can_sync_all_pending_and_failed_records(): void
    {
        $this->assertDatabaseHas('sync_queues', ['status' => 'pending']);

        $response = $this->postJson(route('sync.now'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(0, SyncQueue::whereIn('status', ['pending', 'failed'])->count());
    }

    public function test_can_retry_failed_records(): void
    {
        $this->assertDatabaseHas('sync_queues', ['status' => 'failed']);

        $response = $this->postJson(route('sync.retry'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(0, SyncQueue::where('status', 'failed')->count());
    }

    public function test_can_toggle_connection(): void
    {
        $response = $this->postJson(route('sync.toggle'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'is_offline', 'status_label']);
    }
}
