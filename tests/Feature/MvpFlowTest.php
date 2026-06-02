<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\CaseResult;
use App\Models\ChecklistItem;
use App\Models\Document;
use App\Models\Payment;
use App\Models\ServicePackage;
use App\Models\TaxCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MvpFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_register_and_reach_dashboard(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Ana',
            'last_name' => 'Santos',
            'email' => 'ana@example.com',
            'phone' => '+639170000000',
            'password' => 'Password1234',
            'password_confirmation' => 'Password1234',
            'terms' => '1',
        ]);

        $response->assertRedirect('/owner/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'ana@example.com',
            'role' => 'business_owner',
        ]);
    }

    public function test_core_case_payment_assignment_and_review_flow(): void
    {
        $this->seed();

        $owner = User::factory()->create([
            'role' => 'business_owner',
            'email_verified_at' => now(),
        ]);
        $admin = User::where('role', 'admin')->firstOrFail();
        $accountant = User::where('role', 'accountant')->firstOrFail();
        $package = ServicePackage::firstOrFail();

        $this->actingAs($owner)
            ->post('/cases', [
                'registered_name' => 'Ana Santos Trading',
                'trade_name' => 'Ana Store',
                'tin' => '123-456-789-000',
                'rdo_code' => '044',
                'business_type' => 'single_proprietor',
                'taxpayer_type' => 'individual',
                'registered_address' => 'Quezon City',
                'registration_date' => '2020-01-15',
                'business_activity' => 'Retail',
                'line_of_business' => 'Online selling',
                'business_status' => 'stopped_operating',
                'last_year_of_operation' => 2022,
                'still_operating' => 'no',
                'primary_goal' => 'business_closure',
                'case_type' => 'business_closure',
                'filed_closure' => 'no',
                'received_bir_notice' => 'yes',
                'has_cor' => 'no',
                'knows_rdo' => 'yes',
                'has_unused_receipts' => 'yes',
                'has_books' => 'not_sure',
                'last_filed_year' => 2021,
                'tax_types' => ['2551Q', '1701Q', '0605'],
                'disclaimer_accepted' => '1',
            ])
            ->assertRedirect();

        $case = TaxCase::with(['result', 'checklistItems'])->firstOrFail();

        $this->assertSame(TaxCase::STATUS_AWAITING_PAYMENT, $case->status);
        $this->assertSame('High', $case->risk_level);
        $this->assertInstanceOf(CaseResult::class, $case->result);
        $this->assertGreaterThan(0, ChecklistItem::where('tax_case_id', $case->id)->count());

        Storage::fake('local');

        $this->actingAs($owner)
            ->post("/cases/{$case->id}/documents", [
                'document_type' => 'Certificate of Registration',
                'document' => UploadedFile::fake()->create('cor.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect();

        $document = Document::firstOrFail();
        Storage::disk('local')->assertExists($document->file_path);

        $this->actingAs($owner)
            ->post("/cases/{$case->id}/package", ['service_package_id' => $package->id])
            ->assertRedirect();

        $this->actingAs($owner)
            ->post("/cases/{$case->id}/payment-reference", ['provider_reference' => 'GCASH-123456'])
            ->assertRedirect();

        $payment = Payment::firstOrFail();
        $this->assertSame('pending_review', $payment->status);

        $this->actingAs($admin)
            ->patch("/admin/payments/{$payment->id}/verify")
            ->assertRedirect();

        $this->actingAs($admin)
            ->patch("/admin/cases/{$case->id}/assign", ['assigned_accountant_id' => $accountant->id])
            ->assertRedirect();

        $case->refresh();
        $this->assertSame(TaxCase::STATUS_ASSIGNED, $case->status);
        $this->assertSame($accountant->id, $case->assigned_accountant_id);

        $this->actingAs($accountant)
            ->post("/accountant/cases/{$case->id}/notes", [
                'note' => 'Client should prepare unused invoice inventory.',
                'visibility' => 'client',
            ])
            ->assertRedirect();

        $this->actingAs($accountant)
            ->patch("/accountant/cases/{$case->id}/status", [
                'status' => TaxCase::STATUS_COMPLETED,
                'final_recommendation' => 'Ready for closure preparation after document review.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tax_cases', [
            'id' => $case->id,
            'status' => TaxCase::STATUS_COMPLETED,
        ]);
        $this->assertGreaterThan(0, AuditLog::where('tax_case_id', $case->id)->count());
    }

    public function test_accountant_cannot_open_unassigned_case(): void
    {
        $this->seed();

        $owner = User::factory()->create(['role' => 'business_owner']);
        $accountant = User::where('role', 'accountant')->firstOrFail();
        $business = $owner->businesses()->create([
            'registered_name' => 'Sample Business',
            'business_type' => 'single_proprietor',
        ]);

        $case = TaxCase::create([
            'user_id' => $owner->id,
            'business_id' => $business->id,
            'case_type' => 'open_case_review',
            'status' => TaxCase::STATUS_PAID,
        ]);

        $this->actingAs($accountant)
            ->get("/accountant/cases/{$case->id}")
            ->assertForbidden();
    }
}
