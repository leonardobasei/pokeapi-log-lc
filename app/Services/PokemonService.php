<?php

namespace App\Services;

use App\Models\Pokemon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PokemonService
{
    protected PokemonIntegrationService $integration;

    public function __construct(PokemonIntegrationService $integration)
    {
        $this->integration = $integration;
    }

    /**
     * Retorna todos os Pokémons do banco, aplicando filtros opcionais de nome e tipo.
     * Se não houver nenhum registro, sincroniza do serviço externo antes.
     *
     * @param  string|null  $name  Filtro por nome (parcial, LIKE)
     * @param  string|null  $type  Filtro por tipo exato (type_name)
     * @return Collection<Pokemon>
     */
    public function getAll(
        ?string $name = null,
        ?string $type = null,
        ?int    $perPage = null,
        ?int    $page    = null
    ): LengthAwarePaginator|array {

        if (Pokemon::count() === 0) {
            $this->syncFromApi();
        }

        $query = Pokemon::with('types');

        if ($name !== null) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($type !== null) {
            $query->whereHas('types', function ($q) use ($type) {
                $q->where('type_name', $type);
            });
        }

        $perPage = $perPage ?? 15;
        $page = $page ?? 1;

        return $query->paginate(
            $perPage,      // itens por página
            ['*'],         // colunas
            'page',        // nome do parâmetro de página
            $page          // valor da página
        );
    }

    /**
     * Se a tabela estiver vazia, busca todos via API e persiste localmente.
     */
    protected function syncFromApi(): void
    {
        $remoteList = $this->integration->getAll();

        DB::transaction(function () use ($remoteList) {
            foreach ($remoteList as $item) {
                $this->createFromApiData($item);
            }
        });
    }

    /**
     * Retorna um único Pokémon pelo nome.
     * Se não estiver no banco, busca na API, persiste e retorna.
     */
    public function getByName(string $name): array
    {
        $pokemon = Pokemon::with('types')
            ->where('name', $name)
            ->first();

        if (! $pokemon) {
            $data = $this->integration->getByName($name);

            if (empty($data['name'])) {
                throw new RuntimeException("Pokémon '{$name}' não encontrado na API.");
            }

            $pokemon = DB::transaction(fn() => $this->createFromApiData($data));
            $pokemon->load('types');
        }

        return $pokemon->toArray();
    }

    /**
     * Cria um registro de Pokémon (e seus tipos) a partir
     * dos dados retornados pela API.
     */
    private function createFromApiData(array $data): Pokemon
    {
        $pokemon = Pokemon::create([
            'name'   => $data['name'],
            'height' => $data['height'],
            'weight' => $data['weight'],
            'photo'  => $data['photo'],
        ]);

        foreach ($data['types'] as $typeName) {
            $pokemon->types()->create([
                'type_name' => $typeName,
            ]);
        }

        return $pokemon;
    }
}
