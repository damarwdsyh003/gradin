<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierApiTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso', 'phone' => '+62 812-0000-0001',
            'email' => 'budi@example.com', 'address' => 'Jakarta', 'level' => 3,
            'joined_at' => '2026-01-15', 'is_active' => true,
        ], $overrides);
    }

    public function test_store_persists_a_courier(): void
    {
        $response = $this->postJson('/api/couriers', $this->payload());

        $response->assertCreated()->assertJsonPath('name', 'Budi Santoso');
        $this->assertDatabaseHas('couriers', ['phone' => '+62 812-0000-0001', 'level' => 3]);
    }

    public function test_store_validates_required_unique_and_level_fields(): void
    {
        Courier::create($this->payload());

        $this->postJson('/api/couriers', $this->payload(['name' => 'A', 'level' => 6]))
            ->assertUnprocessable()->assertJsonValidationErrors(['name', 'phone', 'email', 'level']);
    }

    public function test_index_searches_each_search_word_filters_levels_and_paginates(): void
    {
        Courier::create($this->payload(['name' => 'Budiono Hadi Agung', 'phone' => '081200000002', 'email' => 'agung@example.com', 'level' => 2]));
        Courier::create($this->payload(['name' => 'Budi Santoso', 'phone' => '081200000003', 'email' => 'santoso@example.com', 'level' => 4]));
        Courier::create($this->payload(['name' => 'Agung Pratama', 'phone' => '081200000004', 'email' => 'pratama@example.com', 'level' => 3]));

        $this->getJson('/api/couriers?search=budi+agung&level=2,3&per_page=1')
            ->assertOk()->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.name', 'Budiono Hadi Agung')->assertJsonPath('per_page', 1);
    }

    public function test_index_defaults_to_name_and_can_sort_by_registration_date(): void
    {
        $older = Courier::create($this->payload(['name' => 'Alpha', 'phone' => '081200000005', 'email' => 'alpha@example.com']));
        Courier::create($this->payload(['name' => 'Zeta', 'phone' => '081200000006', 'email' => 'zeta@example.com']));
        $older->created_at = now()->subDay();
        $older->saveQuietly();

        $this->getJson('/api/couriers')->assertJsonPath('data.0.name', 'Alpha');
        $this->getJson('/api/couriers?sort=registered_at')->assertJsonPath('data.0.name', 'Zeta');
    }

    public function test_show_update_and_destroy_work_with_database(): void
    {
        $courier = Courier::create($this->payload());

        $this->getJson('/api/couriers/'.$courier->id)->assertOk()->assertJsonPath('email', 'budi@example.com');
        $this->putJson('/api/couriers/'.$courier->id, $this->payload(['name' => 'Budi Agung', 'level' => 5]))
            ->assertOk()->assertJsonPath('name', 'Budi Agung');
        $this->assertDatabaseHas('couriers', ['id' => $courier->id, 'name' => 'Budi Agung', 'level' => 5]);

        $this->deleteJson('/api/couriers/'.$courier->id)->assertNoContent();
        $this->assertDatabaseMissing('couriers', ['id' => $courier->id]);
    }
}
