<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    private function userWith(string $role, bool $active = true): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => $active,
            'password' => 'password',
        ]);
    }

    public function test_login_mengarah_ke_halaman_sesuai_peran(): void
    {
        foreach (['admin' => 'admin.dashboard', 'kasir' => 'kasir.index', 'pelanggan' => 'pelanggan.beranda'] as $role => $route) {
            $user = $this->userWith($role);

            $this->post('/login', ['email' => $user->email, 'password' => 'password'])
                ->assertRedirect(route($route));

            $this->post('/logout');
        }
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        $user = $this->userWith('kasir', active: false);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
    }

    public function test_kasir_tidak_boleh_membuka_kelola_pengguna(): void
    {
        $this->actingAs($this->userWith('kasir'))->get('/admin/users')->assertForbidden();
    }

    public function test_pelanggan_tidak_boleh_membuka_halaman_kasir(): void
    {
        $this->actingAs($this->userWith('pelanggan'))->get('/kasir')->assertForbidden();
    }

    public function test_admin_boleh_membuka_kelola_pengguna_dan_kasir(): void
    {
        $admin = $this->userWith('admin');

        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/kasir')->assertOk();
    }

    public function test_admin_dapat_menambah_pengguna(): void
    {
        $this->actingAs($this->userWith('admin'))
            ->post('/admin/users', [
                'name' => 'Dimas Pratama',
                'email' => 'dimas@debarrio.id',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'role' => 'kasir',
                'is_active' => 1,
            ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('pengguna', ['email' => 'dimas@debarrio.id', 'peran' => 'kasir']);
    }

    public function test_admin_and_kasir_pages_render_consistent_logout_button_and_confirmation_modal(): void
    {
        $admin = $this->userWith('admin');
        $kasir = $this->userWith('kasir');

        foreach ([
            $this->actingAs($admin)->get(route('admin.dashboard')),
            $this->actingAs($admin)->get(route('admin.tables.index')),
            $this->actingAs($kasir)->get(route('kasir.index')),
            $this->actingAs($kasir)->get(route('kasir.sinkron')),
        ] as $response) {
            $response->assertOk();
            $response->assertSee('openLogoutModal()', false);
            $response->assertSee('Konfirmasi Keluar');
            $response->assertSee('Ya, Keluar');
            $response->assertSee('Keluar');
            $response->assertSee(route('logout'), false);
        }
    }
}
