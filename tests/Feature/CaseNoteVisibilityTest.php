<?php

namespace Tests\Feature;

use App\Models\TaxCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CaseNoteVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $accountant;

    private TaxCase $case;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.key' => 'base64:'.base64_encode(str_repeat('t', 32))]);
        $this->owner = User::factory()->create(['role' => 'business_owner']);
        $this->accountant = User::factory()->create(['role' => 'accountant']);
        $business = $this->owner->businesses()->create([
            'registered_name' => 'Visibility Test Business',
            'business_type' => 'single_proprietor',
        ]);
        $this->case = TaxCase::create([
            'user_id' => $this->owner->id,
            'business_id' => $business->id,
            'assigned_accountant_id' => $this->accountant->id,
            'case_type' => 'open_case_review',
            'status' => TaxCase::STATUS_ASSIGNED,
        ]);
        foreach (['internal' => 'INTERNAL_ONLY_SENTINEL', 'client' => 'CLIENT_VISIBLE_SENTINEL'] as $visibility => $note) {
            $this->case->notes()->create([
                'user_id' => $this->accountant->id,
                'visibility' => $visibility,
                'note' => $note,
            ]);
        }
    }

    public function test_owner_initial_page_excludes_internal_notes_and_keeps_client_notes(): void
    {
        $this->actingAs($this->owner)
            ->get("/cases/{$this->case->id}")
            ->assertOk()
            ->assertDontSee('INTERNAL_ONLY_SENTINEL', false)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Owner/Cases/show')
                ->missing('caseRecord.notes')
                ->has('clientNotes', 1)
                ->where('clientNotes.0.note', 'CLIENT_VISIBLE_SENTINEL')
                ->where('clientNotes.0.user.id', $this->accountant->id)
            );
    }

    public function test_owner_inertia_responses_do_not_serialize_internal_notes(): void
    {
        foreach ([
            ['X-Inertia' => 'true'],
            [
                'X-Inertia' => 'true',
                'X-Inertia-Partial-Component' => 'Owner/Cases/show',
                'X-Inertia-Partial-Data' => 'caseRecord,clientNotes',
            ],
        ] as $headers) {
            $this->actingAs($this->owner)
                ->get("/cases/{$this->case->id}", $headers)
                ->assertOk()
                ->assertJsonMissingPath('props.caseRecord.notes')
                ->assertJsonCount(1, 'props.clientNotes')
                ->assertJsonPath('props.clientNotes.0.note', 'CLIENT_VISIBLE_SENTINEL')
                ->assertDontSee('INTERNAL_ONLY_SENTINEL', false);
        }
    }

    public function test_authorized_staff_can_still_read_internal_and_client_notes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        foreach (['admin' => $admin, 'accountant' => $this->accountant] as $prefix => $user) {
            $response = $this->actingAs($user)
                ->get("/{$prefix}/cases/{$this->case->id}", ['X-Inertia' => 'true'])
                ->assertOk()
                ->assertJsonCount(2, 'props.caseRecord.notes');

            $this->assertEqualsCanonicalizing(
                ['INTERNAL_ONLY_SENTINEL', 'CLIENT_VISIBLE_SENTINEL'],
                array_column($response->json('props.caseRecord.notes'), 'note'),
            );
        }
    }

    public function test_other_owner_and_unassigned_accountant_cannot_read_case_notes(): void
    {
        foreach (['business_owner' => '/cases/', 'accountant' => '/accountant/cases/'] as $role => $prefix) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)
                ->get($prefix.$this->case->id, ['X-Inertia' => 'true'])
                ->assertForbidden()
                ->assertDontSee('INTERNAL_ONLY_SENTINEL', false)
                ->assertDontSee('CLIENT_VISIBLE_SENTINEL', false);
        }
    }
}
