<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RiskItem;
use App\Models\RiskCause;
use App\Models\RiskMitigation;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterRiskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $manriskUser;
    private User $nonManriskUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'manrisk']);
        Role::firstOrCreate(['name' => 'kacab']);

        $this->manriskUser = User::factory()->create();
        $this->manriskUser->assignRole('manrisk');

        $this->nonManriskUser = User::factory()->create();
        $this->nonManriskUser->assignRole('kacab');
    }

    // -----------------------------------------------------------------------
    //  AUTHORIZATION — SAD PATHS
    // -----------------------------------------------------------------------

    /** @test */
    public function user_without_manrisk_role_cannot_view_the_index_page()
    {
        $this->actingAs($this->nonManriskUser)
            ->get(route('admin.risk_master.index'))
            ->assertForbidden();
    }

    /** @test */
    public function user_without_manrisk_role_cannot_store_a_cause()
    {
        $item = RiskItem::factory()->create();

        $this->actingAs($this->nonManriskUser)
            ->post(route('admin.risk_master.store_cause', $item->id), [
                'penyebab' => 'Kelalaian',
            ])
            ->assertForbidden();
    }

    /** @test */
    public function user_without_manrisk_role_cannot_store_a_mitigation()
    {
        $cause = RiskCause::factory()->create();

        $this->actingAs($this->nonManriskUser)
            ->post(route('admin.risk_master.store_mitigation', $cause->id), [
                'mitigasi' => 'Cek ulang dokumen',
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    //  VALIDATION — SAD PATHS (MANRISK USER)
    // -----------------------------------------------------------------------

    /** @test */
    public function store_cause_validates_required_fields()
    {
        $item = RiskItem::factory()->create();

        $this->actingAs($this->manriskUser)
            ->from(route('admin.risk_master.index'))
            ->post(route('admin.risk_master.store_cause', $item->id), [
                'penyebab' => '',
            ])
            ->assertRedirect(route('admin.risk_master.index'))
            ->assertSessionHasErrors('penyebab');

        $this->assertDatabaseMissing('risk_causes', [
            'risk_item_id' => $item->id,
        ]);
    }

    /** @test */
    public function store_mitigation_validates_required_fields()
    {
        $cause = RiskCause::factory()->create();

        $this->actingAs($this->manriskUser)
            ->from(route('admin.risk_master.index'))
            ->post(route('admin.risk_master.store_mitigation', $cause->id), [
                'mitigasi' => '',
            ])
            ->assertRedirect(route('admin.risk_master.index'))
            ->assertSessionHasErrors('mitigasi');

        $this->assertDatabaseMissing('risk_mitigations', [
            'risk_cause_id' => $cause->id,
        ]);
    }

    /** @test */
    public function store_cause_validates_max_string_length()
    {
        $item = RiskItem::factory()->create();

        $this->actingAs($this->manriskUser)
            ->from(route('admin.risk_master.index'))
            ->post(route('admin.risk_master.store_cause', $item->id), [
                'penyebab' => str_repeat('a', 256),
            ])
            ->assertRedirect(route('admin.risk_master.index'))
            ->assertSessionHasErrors('penyebab');
    }

    /** @test */
    public function store_mitigation_validates_max_string_length()
    {
        $cause = RiskCause::factory()->create();

        $this->actingAs($this->manriskUser)
            ->from(route('admin.risk_master.index'))
            ->post(route('admin.risk_master.store_mitigation', $cause->id), [
                'mitigasi' => str_repeat('a', 256),
            ])
            ->assertRedirect(route('admin.risk_master.index'))
            ->assertSessionHasErrors('mitigasi');
    }

    // -----------------------------------------------------------------------
    //  EXECUTION — HAPPY PATHS (MANRISK USER)
    // -----------------------------------------------------------------------

    /** @test */
    public function manrisk_user_can_access_index_page()
    {
        $response = $this->actingAs($this->manriskUser)
            ->get(route('admin.risk_master.index'));

        $response->assertOk();
    }

    /** @test */
    public function manrisk_user_can_store_a_new_cause_and_persist_to_database()
    {
        $item = RiskItem::factory()->create();

        $this->actingAs($this->manriskUser)
            ->post(route('admin.risk_master.store_cause', $item->id), [
                'penyebab' => 'Kelalaian Operasional',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('risk_causes', [
            'risk_item_id' => $item->id,
            'penyebab' => 'Kelalaian Operasional',
        ]);
    }

    /** @test */
    public function manrisk_user_can_store_a_new_mitigation_and_persist_to_database()
    {
        $cause = RiskCause::factory()->create();

        $this->actingAs($this->manriskUser)
            ->post(route('admin.risk_master.store_mitigation', $cause->id), [
                'mitigasi' => 'Double check oleh supervisor',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('risk_mitigations', [
            'risk_cause_id' => $cause->id,
            'mitigasi' => 'Double check oleh supervisor',
        ]);
    }

    /** @test */
    public function manrisk_user_can_store_multiple_causes_under_the_same_risk_item()
    {
        $item = RiskItem::factory()->create();

        $this->actingAs($this->manriskUser)
            ->post(route('admin.risk_master.store_cause', $item->id), [
                'penyebab' => 'Penyebab Pertama',
            ]);
        $this->actingAs($this->manriskUser)
            ->post(route('admin.risk_master.store_cause', $item->id), [
                'penyebab' => 'Penyebab Kedua',
            ]);

        $this->assertDatabaseHas('risk_causes', [
            'risk_item_id' => $item->id,
            'penyebab' => 'Penyebab Pertama',
        ]);
        $this->assertDatabaseHas('risk_causes', [
            'risk_item_id' => $item->id,
            'penyebab' => 'Penyebab Kedua',
        ]);
    }

    /** @test */
    public function manrisk_user_can_store_multiple_mitigations_under_the_same_cause()
    {
        $cause = RiskCause::factory()->create();

        $this->actingAs($this->manriskUser)
            ->post(route('admin.risk_master.store_mitigation', $cause->id), [
                'mitigasi' => 'Mitigasi Pertama',
            ]);

        $this->actingAs($this->manriskUser)
            ->post(route('admin.risk_master.store_mitigation', $cause->id), [
                'mitigasi' => 'Mitigasi Kedua',
            ]);

        $this->assertDatabaseHas('risk_mitigations', [
            'risk_cause_id' => $cause->id,
            'mitigasi' => 'Mitigasi Pertama',
        ]);
        $this->assertDatabaseHas('risk_mitigations', [
            'risk_cause_id' => $cause->id,
            'mitigasi' => 'Mitigasi Kedua',
        ]);
    }

    /** @test */
    public function store_cause_fails_when_risk_item_does_not_exist()
    {
        $this->actingAs($this->manriskUser)
            ->post(route('admin.risk_master.store_cause', 99999), [
                'penyebab' => 'Akan gagal karena FK constraint',
            ])
            ->assertStatus(500);
    }

    /** @test */
    public function store_mitigation_fails_when_cause_does_not_exist()
    {
        $this->actingAs($this->manriskUser)
            ->post(route('admin.risk_master.store_mitigation', 99999), [
                'mitigasi' => 'Akan gagal karena FK constraint',
            ])
            ->assertStatus(500);
    }

    /** @test */
    public function index_page_query_loads_with_eager_relationships()
    {
        $item = RiskItem::factory()
            ->has(RiskCause::factory()->has(RiskMitigation::factory(), 'mitigations'), 'causes')
            ->create(['role_target' => 'kacab']);

        $items = RiskItem::with('causes.mitigations')
            ->where('role_target', 'kacab')
            ->orderBy('kategori')
            ->get();

        $this->assertCount(1, $items);
        $this->assertTrue($items->first()->relationLoaded('causes'));
        $this->assertTrue($items->first()->causes->first()->relationLoaded('mitigations'));
    }
}
