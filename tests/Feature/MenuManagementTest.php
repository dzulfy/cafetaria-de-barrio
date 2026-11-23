<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test customer menu page loads with seeded data.
     */
    public function test_customer_menu_page_can_be_rendered(): void
    {
        $category = Category::create([
            'name' => 'Kopi',
            'slug' => 'kopi',
        ]);

        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Kopi Susu Gula Aren',
            'description' => 'Espresso, susu, gula aren',
            'price' => 16000,
            'is_available' => true,
        ]);

        $response = $this->get(route('customer.menu'));

        $response->assertStatus(200);
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('Rp 16.000');
    }

    /**
     * Test filtering customer menu by category.
     */
    public function test_customer_menu_can_be_filtered_by_category(): void
    {
        $coffeeCategory = Category::create(['name' => 'Kopi', 'slug' => 'kopi']);
        $foodCategory = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);

        $coffee = Menu::create([
            'category_id' => $coffeeCategory->id,
            'name' => 'Americano',
            'price' => 14000,
            'is_available' => true,
        ]);

        $food = Menu::create([
            'category_id' => $foodCategory->id,
            'name' => 'Pisang Goreng',
            'price' => 10000,
            'is_available' => true,
        ]);

        $response = $this->get(route('customer.menu', ['kategori' => 'kopi']));
        $response->assertStatus(200);
        $response->assertSee('Americano');
        $response->assertDontSee('Pisang Goreng');
    }

    /**
     * Test customer checkout.
     */
    public function test_customer_can_checkout_order(): void
    {
        $category = Category::create(['name' => 'Kopi', 'slug' => 'kopi']);
        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Es Kopi Susu',
            'price' => 15000,
            'is_available' => true,
        ]);

        $payload = [
            'customer_name' => 'Budi',
            'table_number' => '05',
            'items' => [
                ['id' => $menu->id, 'quantity' => 2],
            ],
        ];

        $response = $this->postJson(route('customer.checkout'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
        ]);
        $response->assertJsonStructure(['order_id']);
    }

    /**
     * Test admin menu index page loads.
     */
    public function test_admin_menu_index_page_can_be_rendered(): void
    {
        $category = Category::create(['name' => 'Non-Kopi', 'slug' => 'non-kopi']);
        Menu::create([
            'category_id' => $category->id,
            'name' => 'Matcha Latte',
            'price' => 18000,
            'is_available' => true,
        ]);

        $response = $this->get(route('admin.menus.index'));

        $response->assertStatus(200);
        $response->assertSee('Menu &amp; Kategori', false);
        $response->assertSee('Matcha Latte');
    }

    /**
     * Test admin can store new menu.
     */
    public function test_admin_can_create_new_menu(): void
    {
        $category = Category::create(['name' => 'Kopi', 'slug' => 'kopi']);

        $payload = [
            'name' => 'Kopi Tubruk',
            'category_id' => $category->id,
            'price' => 12000,
            'description' => 'Kopi tubruk robusta',
            'is_available' => '1',
        ];

        $response = $this->post(route('admin.menus.store'), $payload);

        $response->assertRedirect(route('admin.menus.index'));
        $this->assertDatabaseHas('menus', [
            'name' => 'Kopi Tubruk',
            'price' => 12000,
            'category_id' => $category->id,
            'is_available' => 1,
        ]);
    }

    /**
     * Test admin can update menu.
     */
    public function test_admin_can_update_menu(): void
    {
        $category = Category::create(['name' => 'Kopi', 'slug' => 'kopi']);
        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Cappuccino',
            'price' => 18000,
            'is_available' => true,
        ]);

        $payload = [
            'name' => 'Cappuccino Grande',
            'category_id' => $category->id,
            'price' => 22000,
            'description' => 'Ukuran lebih besar',
            'is_available' => '1',
        ];

        $response = $this->put(route('admin.menus.update', $menu), $payload);

        $response->assertRedirect(route('admin.menus.index'));
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'name' => 'Cappuccino Grande',
            'price' => 22000,
        ]);
    }

    /**
     * Test admin can toggle menu availability status.
     */
    public function test_admin_can_toggle_menu_availability(): void
    {
        $category = Category::create(['name' => 'Kopi', 'slug' => 'kopi']);
        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'V60 Manual Brew',
            'price' => 18000,
            'is_available' => true,
        ]);

        $response = $this->patchJson(route('admin.menus.toggle', $menu));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_available' => false,
        ]);

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'is_available' => 0,
        ]);
    }

    /**
     * Test admin can delete menu.
     */
    public function test_admin_can_delete_menu(): void
    {
        $category = Category::create(['name' => 'Kopi', 'slug' => 'kopi']);
        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Menu To Delete',
            'price' => 10000,
            'is_available' => true,
        ]);

        $response = $this->delete(route('admin.menus.destroy', $menu));

        $response->assertRedirect(route('admin.menus.index'));
        $this->assertDatabaseMissing('menus', [
            'id' => $menu->id,
        ]);
    }
}
