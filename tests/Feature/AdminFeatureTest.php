<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_registration_screen_can_be_rendered()
    {
        $response = $this->get('/admin/register');
        $response->assertStatus(200);
    }

    #[Test]
    public function new_admin_can_register()
    {
        $response = $this->post('/admin/register', [
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/admin/login');
        $this->assertDatabaseHas('admins', [
            'email' => 'admin@example.com',
        ]);
    }

    #[Test]
    public function admin_login_screen_can_be_rendered()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_login_with_valid_credentials()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    #[Test]
    public function admin_cannot_login_with_invalid_credentials()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('admin');
    }

    #[Test]
    public function admin_can_update_profile_information()
    {
        $admin = Admin::factory()->create([
            'name' => 'Old Name',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin, 'admin')->patch('/admin/profile', [
            'name' => 'New Name',
            'email' => 'admin@example.com',
        ]);

        $this->assertDatabaseHas('admins', [
            'name' => 'New Name',
            'email' => 'admin@example.com',
        ]);
    }

    #[Test]
    public function admin_can_delete_their_account()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $this->actingAs($admin, 'admin')->delete('/admin/profile', [
            'password' => 'password',
        ]);

        $this->assertDatabaseMissing('admins', [
            'email' => 'admin@example.com',
        ]);
        $this->assertGuest('admin');
    }

    #[Test]
    public function admin_can_request_password_reset()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $response = $this->post('/admin/forgot-password', [
            'email' => 'admin@example.com',
        ]);

        $response->assertSessionHas('status');
    }

    #[Test]
    public function admin_can_reset_password_with_valid_token()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $token = app('auth.password.broker')->createToken($admin);

        $response = $this->post('/admin/reset-password', [
            'token' => $token,
            'email' => 'admin@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect('/admin/login');
        $this->assertTrue(Hash::check('newpassword', $admin->fresh()->password));
    }
}
