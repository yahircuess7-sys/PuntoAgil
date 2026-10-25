<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessInvitation;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BusinessAndUserManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    private function createBusinessWithHierarchy(): array
    {
        $business = Business::create([
            'name' => 'Empresa Gestion Test ' . uniqid(),
            'slug' => 'empresa-gestion-' . uniqid(),
            'status' => 'active',
        ]);

        $owner = User::create([
            'name' => 'Propietario Test',
            'email' => 'owner_' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'status' => 'active',
            'current_business_id' => $business->id,
        ]);
        $owner->businesses()->attach($business->id, ['role' => 'owner', 'is_default' => true]);

        $admin = User::create([
            'name' => 'Administrador Test',
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'current_business_id' => $business->id,
        ]);
        $admin->businesses()->attach($business->id, ['role' => 'admin', 'is_default' => true]);

        $cajero = User::create([
            'name' => 'Cajero Test',
            'email' => 'cajero_' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'role' => 'cajero',
            'status' => 'active',
            'current_business_id' => $business->id,
        ]);
        $cajero->businesses()->attach($business->id, ['role' => 'cajero', 'is_default' => true]);

        return [$business, $owner, $admin, $cajero];
    }

    public function test_owner_can_configure_business_and_view_audit_logs_while_admin_and_cajero_cannot(): void
    {
        [$business, $owner, $admin, $cajero] = $this->createBusinessWithHierarchy();
        app(TenantManager::class)->setTenant($business);

        // Cajero -> 403
        $this->actingAs($cajero)->get(route('admin.business.settings'))->assertStatus(403);
        $this->actingAs($cajero)->get(route('admin.audit.index'))->assertStatus(403);

        // Admin -> 403 on business configuration and audit logs
        $this->actingAs($admin)->get(route('admin.business.settings'))->assertStatus(403);
        $this->actingAs($admin)->put(route('admin.business.update'), [
            'name' => 'Intento Admin',
        ])->assertStatus(403);
        $this->actingAs($admin)->get(route('admin.audit.index'))->assertStatus(403);

        // Owner -> 200 and can update settings
        $this->actingAs($owner)->get(route('admin.business.settings'))->assertStatus(200);
        $this->actingAs($owner)->put(route('admin.business.update'), [
            'name' => 'Empresa Renombrada Por Owner',
            'tax_id' => 'RFC999888777',
            'email' => 'contacto@renombrada.com',
            'phone' => '5551234567',
            'address' => 'Calle Reforma 100',
            'currency' => 'MXN',
            'tax_percentage' => 16,
            'timezone' => 'America/Mexico_City',
        ])->assertRedirect(route('admin.business.settings'));

        $this->assertDatabaseHas('businesses', [
            'id' => $business->id,
            'name' => 'Empresa Renombrada Por Owner',
            'tax_id' => 'RFC999888777',
        ]);

        // Verify AuditLog was written and Owner can view it
        $this->assertDatabaseHas('audit_logs', [
            'business_id' => $business->id,
            'user_id' => $owner->id,
            'action' => 'business.updated',
        ]);

        $this->actingAs($owner)->get(route('admin.audit.index'))->assertStatus(200);
    }

    public function test_invitation_flow_creates_and_accepts_invitation_with_role_protection(): void
    {
        [$business, $owner, $admin] = $this->createBusinessWithHierarchy();
        app(TenantManager::class)->setTenant($business);

        // Admin cannot invite another admin or owner via manipulated request
        $this->actingAs($admin)->post(route('admin.users.invite'), [
            'name' => 'Admin Escalado',
            'email' => 'escalado_' . uniqid() . '@test.com',
            'role' => 'admin',
        ])->assertStatus(403);

        // Owner can invite an admin
        $invitedEmail = 'nuevo_miembro_' . uniqid() . '@test.com';
        $this->actingAs($owner)->post(route('admin.users.invite'), [
            'name' => 'Nuevo Admin Invitado',
            'email' => $invitedEmail,
            'role' => 'admin',
        ])->assertRedirect(route('admin.users.index'));

        $invitation = BusinessInvitation::where('email', $invitedEmail)->first();
        $this->assertNotNull($invitation);
        $this->assertEquals('pending', $invitation->status);
        $this->assertEquals('admin', $invitation->role);

        // Guest can open invitation page and accept it
        auth()->logout();

        $this->get(route('invitations.show', $invitation->token))->assertStatus(200);

        $this->post(route('invitations.accept', $invitation->token), [
            'name' => 'Nuevo Admin Invitado',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $invitation->refresh();
        $this->assertEquals('accepted', $invitation->status);

        $newUser = User::where('email', $invitedEmail)->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('admin', $newUser->roleInBusiness($business->id));

        $this->assertDatabaseHas('audit_logs', [
            'business_id' => $business->id,
            'action' => 'invitation.accepted',
        ]);
    }

    public function test_account_activation_deactivation_and_revocation_enforce_hierarchy(): void
    {
        [$business, $owner, $admin, $cajero] = $this->createBusinessWithHierarchy();
        app(TenantManager::class)->setTenant($business);

        // Admin cannot deactivate Owner
        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-status', $owner->id))
            ->assertStatus(403);

        // Owner cannot deactivate themselves
        $this->actingAs($owner)
            ->patch(route('admin.users.toggle-status', $owner->id))
            ->assertStatus(403);

        // Admin can deactivate Cajero
        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-status', $cajero->id))
            ->assertRedirect(route('admin.users.index'));

        $cajero->refresh();
        $this->assertEquals('inactive', $cajero->status);

        $this->assertDatabaseHas('audit_logs', [
            'business_id' => $business->id,
            'user_id' => $admin->id,
            'action' => 'user.status_toggled',
            'target_id' => $cajero->id,
        ]);

        // Owner can revoke Admin's membership in the business
        $this->actingAs($owner)
            ->delete(route('admin.users.destroy', $admin->id))
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($admin->fresh()?->belongsToBusiness($business->id) ?? false);
        $this->assertDatabaseHas('audit_logs', [
            'business_id' => $business->id,
            'user_id' => $owner->id,
            'action' => 'user.access_revoked',
        ]);
    }

    public function test_super_admin_global_business_administration_and_business_switching(): void
    {
        [$business, $owner] = $this->createBusinessWithHierarchy();
        app(TenantManager::class)->setTenant($business);

        // Owner cannot access global Super Admin routes
        $this->actingAs($owner)->get(route('admin.businesses.index'))->assertStatus(403);
        $this->actingAs($owner)->post(route('admin.businesses.store'), [])->assertStatus(403);

        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin Global',
            'email' => 'superadmin_' . uniqid() . '@test.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_super_admin' => true,
            'status' => 'active',
            'current_business_id' => $business->id,
        ]);

        $this->actingAs($superAdmin)->get(route('admin.businesses.index'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('admin.businesses.create'))->assertStatus(200);

        $newOwnerEmail = 'nuevo_owner_' . uniqid() . '@test.com';
        $this->actingAs($superAdmin)->post(route('admin.businesses.store'), [
            'name' => 'Segunda Empresa SaaS',
            'tax_id' => 'SES240101999',
            'email' => 'info@segunda.com',
            'phone' => '5559876543',
            'address' => 'Av. Insurgentes 500',
            'owner_name' => 'Propietario Dos',
            'owner_email' => $newOwnerEmail,
            'owner_password' => 'password123',
        ])->assertRedirect(route('admin.businesses.index'));

        $secondBusiness = Business::where('name', 'Segunda Empresa SaaS')->first();
        $this->assertNotNull($secondBusiness);

        $secondOwner = User::where('email', $newOwnerEmail)->first();
        $this->assertNotNull($secondOwner);
        $this->assertEquals('owner', $secondOwner->roleInBusiness($secondBusiness->id));

        // First Owner cannot switch to Second Business (403)
        $this->actingAs($owner)->post(route('business.switch'), [
            'business_id' => $secondBusiness->id,
        ])->assertStatus(403);

        // If First Owner is also granted membership in Second Business, they CAN switch
        $owner->businesses()->attach($secondBusiness->id, ['role' => 'admin', 'is_default' => false]);
        $this->actingAs($owner)->post(route('business.switch'), [
            'business_id' => $secondBusiness->id,
        ])->assertRedirect(route('dashboard'));

        $this->assertEquals($secondBusiness->id, $owner->fresh()->current_business_id);

        // Super Admin can suspend and reactivate a business
        $this->actingAs($superAdmin)
            ->patch(route('admin.businesses.toggle-status', $secondBusiness->id))
            ->assertRedirect(route('admin.businesses.index'));

        $this->assertEquals('suspended', $secondBusiness->fresh()->status);
    }
}
