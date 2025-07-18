<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Route;
use App\Services\PokemonService;
use App\Http\Controllers\PokemonController;
use RuntimeException;

class PokemonControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // registramos as rotas como estão em routes/api.php
        Route::get('/api/', [PokemonController::class, 'getAll']);
        Route::get('/api/{name}', [PokemonController::class, 'getByName']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_without_filters_returns_data()
    {
        $mock = Mockery::mock(PokemonService::class);
        $mock
            ->shouldReceive('getAll')
            ->once()
            ->with(null, null, null, null)
            ->andReturn(['foo', 'bar']);

        $this->app->instance(PokemonService::class, $mock);

        $this->getJson('/api/')
            ->assertStatus(200)
            ->assertExactJson(['foo', 'bar']);
    }

    public function test_get_all_with_filters_passed_to_service()
    {
        $mock = Mockery::mock(PokemonService::class);
        $mock
            ->shouldReceive('getAll')
            ->once()
            ->with('mew', 'psychic', 5, 2)
            ->andReturn([['name' => 'mew']]);

        $this->app->instance(PokemonService::class, $mock);

        $this->getJson('/api/?name=mew&type=psychic&limit=5&page=2')
            ->assertStatus(200)
            ->assertJson([['name' => 'mew']]);
    }

    public function test_get_all_returns_error_if_service_throws()
    {
        $mock = Mockery::mock(PokemonService::class);
        $mock
            ->shouldReceive('getAll')
            ->once()
            ->andThrow(new RuntimeException('Serviço indisponível'));

        $this->app->instance(PokemonService::class, $mock);

        $this->getJson('/api/')
            ->assertStatus(400)
            ->assertJson(['error' => 'Serviço indisponível']);
    }

    public function test_get_by_name_returns_data()
    {
        $mock = Mockery::mock(PokemonService::class);
        $mock
            ->shouldReceive('getByName')
            ->once()
            ->with('ditto')
            ->andReturn(['name' => 'ditto']);

        $this->app->instance(PokemonService::class, $mock);

        $this->getJson('/api/ditto')
            ->assertStatus(200)
            ->assertJson(['name' => 'ditto']);
    }

    public function test_get_by_name_returns_error_if_service_throws()
    {
        $mock = Mockery::mock(PokemonService::class);
        $mock
            ->shouldReceive('getByName')
            ->once()
            ->with('unknown')
            ->andThrow(new RuntimeException('Pokémon não encontrado'));

        $this->app->instance(PokemonService::class, $mock);

        $this->getJson('/api/unknown')
            ->assertStatus(400)
            ->assertJson(['error' => 'Pokémon não encontrado']);
    }
}
