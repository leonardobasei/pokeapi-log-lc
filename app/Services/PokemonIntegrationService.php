<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PokemonIntegrationService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config("services.pokeapi.base_url");
    }

    public function getAll(): array
    {
        $response = Http::get($this->baseUrl, [
            'limit'  => '20',
            'offset' => 0,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Erro ao consultar Pokemons');
        }

        $results = $response->json()['results'] ?? [];

        $details = [];

        foreach ($results as $pokemon) {
            try {
                $detailResp = Http::get($pokemon['url']);
                if ($detailResp->successful()) {
                    $data = $detailResp->json();

                    $details[] = [
                        'name'   => $data['name'],
                        'types'  => array_map(
                            fn($t) => $t['type']['name'],
                            $data['types']
                        ),
                        'height' => $data['height'] * 10,
                        'weight' => $data['weight'] / 10,
                        'photo'  => $data['sprites']['front_default']
                    ];
                }
            } catch (ConnectionException $e) {
                continue;
            }
        }

        return $details;
    }


    public function getByName(string $name): array
    {
        $response = Http::get($this->baseUrl . $name);

        if ($response->failed()) {
            throw new RuntimeException('Erro ao consultar Pokemon');
        }

        $data = $response->json();

        return [
            'name'   => $data['name'],
            'types'  => array_map(
                fn($t) => $t['type']['name'],
                $data['types']
            ),
            'height' => $data['height'] * 10,
            'weight' => $data['weight'] / 10,
            'photo'  => $data['sprites']['front_default'],
        ];
    }
}
