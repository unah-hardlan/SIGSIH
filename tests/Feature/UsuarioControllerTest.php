<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use Firebase\JWT\JWT;

class UsuarioControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate()
    {
        $usuario = Usuario::factory()->create();
        $payload = [
            'sub' => $usuario->id_usuario_pk,
            'name' => $usuario->nombre_usuario,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    public function test_index_returns_all_users()
    {
        Usuario::factory()->count(3)->create();

        $token = $this->authenticate();
        $response = $this->getJson('/api/usuarios', ['Authorization' => "Bearer $token"]);

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_store_creates_a_user()
    {
        $data = [
            'usuario' => 'testuser',
            'nombre_usuario' => 'Test User',
            'correo_electronico' => 'test@example.com',
            'contrasena' => 'password123',
        ];

        $token = $this->authenticate();
        $response = $this->postJson('/api/usuarios', $data, ['Authorization' => "Bearer $token"]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tbl_ms_usuario', ['usuario' => 'testuser']);
    }

    public function test_show_returns_a_user()
    {
        $usuario = Usuario::factory()->create();

        $token = $this->authenticate();
        $response = $this->getJson("/api/usuarios/{$usuario->id_usuario_pk}", ['Authorization' => "Bearer $token"]);

        $response->assertStatus(200);
        $response->assertJson(['usuario' => $usuario->usuario]);
    }

    public function test_update_modifies_a_user()
    {
        $usuario = Usuario::factory()->create();

        $data = ['nombre_usuario' => 'Updated Name'];

        $token = $this->authenticate();
        $response = $this->putJson("/api/usuarios/{$usuario->id_usuario_pk}", $data, ['Authorization' => "Bearer $token"]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tbl_ms_usuario', ['nombre_usuario' => 'Updated Name']);
    }

    public function test_destroy_deletes_a_user()
    {
        $usuario = Usuario::factory()->create();

        $token = $this->authenticate();
        $response = $this->deleteJson("/api/usuarios/{$usuario->id_usuario_pk}", [], ['Authorization' => "Bearer $token"]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tbl_ms_usuario', ['id_usuario_pk' => $usuario->id_usuario_pk]);
    }
}
