<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'coordinador', 'aspirante'] as $name) {
            Role::query()->create([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_quick_registration_returns_201(): void
    {
        $payload = [
            'identity_type' => 'curp',
            'identity_value' => 'gode561231hd frrn09',
            'email' => 'nuevo@example.com',
            'phone' => '9991234567',
        ];

        $register = $this->postJson('/api/v1/auth/register', $payload);

        $register->assertCreated()
            ->assertJsonPath('user.email', 'nuevo@example.com')
            ->assertJsonPath('user.folio', null)
            ->assertJsonPath('user.user_status.code', 'quick_registration');

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
            'identity_value' => 'GODE561231HDFRRN09',
            'phone' => '9991234567',
            'folio' => null,
        ]);
    }

    public function test_login_succeeds_with_correct_password(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'secreto-seguro-123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login@example.com',
            'password' => 'secreto-seguro-123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'folio', 'email', 'user_status']]);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'dup@example.com',
            'identity_type' => 'passport',
            'identity_value' => 'PASSPORTEXIST01',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'identity_type' => 'passport',
            'identity_value' => 'PASSPORTNEW0002',
            'email' => 'dup@example.com',
            'phone' => '5551112233',
        ]);

        $response->assertStatus(422);
    }
}
