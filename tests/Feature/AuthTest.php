<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_petugas_can_login(): void
    {
        $user = User::factory()->create(['role' => 'petugas', 'password' => bcrypt('password')]);
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $response->assertRedirect('/petugas/dashboard');
    }

    public function test_guru_can_login(): void
    {
        $user = User::factory()->create(['role' => 'guru', 'password' => bcrypt('password')]);
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $response->assertRedirect('/guru/dashboard');
    }

    public function test_orangtua_can_login(): void
    {
        $user = User::factory()->create(['role' => 'orangtua', 'password' => bcrypt('password')]);
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $response->assertRedirect('/orangtua/dashboard');
    }

    public function test_invalid_login_fails(): void
    {
        $response = $this->post('/login', ['email' => 'wrong@test.com', 'password' => 'wrongpass']);
        $response->assertSessionHasErrors('email');
    }

    public function test_role_access_control(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $response = $this->actingAs($petugas)->get('/guru/dashboard');
        $response->assertStatus(403);
    }
}
