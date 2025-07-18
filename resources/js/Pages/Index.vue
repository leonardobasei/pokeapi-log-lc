<template>
  <div class="p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Logcomex</h1>

    <!-- filtros -->
    <div
      class="flex flex-col sm:flex-row justify-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4"
    >
      <input
        v-model="nameFilter"
        type="text"
        placeholder="Filtrar por nome"
        class="border rounded-lg p-2 flex-1"
      />
      <input
        v-model="typeFilter"
        type="text"
        placeholder="Filtrar por tipo"
        class="border rounded-lg p-2 flex-1"
      />
      <button
        @click="onSearch"
        class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700"
      >
        Buscar
      </button>
    </div>

    <div v-if="loading" class="text-center">
      <span>Carregando...</span>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="pokemon in pokemons"
        :key="pokemon.name"
        class="bg-white rounded-2xl shadow p-4 flex flex-col items-center"
        @click="goToPokemon(pokemon.name)"
      >
        <img :src="pokemon.photo" :alt="pokemon.name" class="w-24 h-24 mb-4" />
        <h2 class="text-xl font-semibold capitalize">{{ pokemon.name }}</h2>
        <p class="text-sm text-gray-600 mt-2">
          Tipos: {{ pokemon.types.map((t) => t.type_name).join(", ") }}
        </p>
      </div>
    </div>

    <!-- paginação -->
    <div class="flex justify-center items-center mt-6 space-x-4">
      <button
        @click="prevPage"
        :disabled="page === 1"
        class="px-3 py-1 rounded-lg border disabled:opacity-50"
      >
        Anterior
      </button>
      <span>Página {{ page }} de {{ Math.ceil(total / perPage) }}</span>
      <button
        @click="nextPage"
        :disabled="page === Math.ceil(total / perPage)"
        class="px-3 py-1 rounded-lg border"
      >
        Próxima
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const loading = ref(false);
const pokemons = ref([]);
const nameFilter = ref("");
const typeFilter = ref("");
const page = ref(1);
const perPage = 9;
const total = ref(0);

const fetchPokemons = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    if (nameFilter.value) params.append("name", nameFilter.value);
    if (typeFilter.value) params.append("type", typeFilter.value);
    params.append("page", page.value);
    params.append("limit", perPage);

    const query = params.toString() ? `?${params.toString()}` : "";
    const res = await fetch(`/api/${query}`);
    const data = await res.json();

    pokemons.value = data.data ?? data;
    total.value = data.total;
  } catch (error) {
    console.error("Erro ao carregar Pokémons:", error);
  } finally {
    loading.value = false;
  }
};

const onSearch = () => {
  page.value = 1;
  fetchPokemons();
};

const nextPage = () => {
  page.value++;
  fetchPokemons();
};

const prevPage = () => {
  if (page.value > 1) {
    page.value--;
    fetchPokemons();
  }
};

const goToPokemon = (name) => {
  window.location.href = `/${name}`
};

onMounted(() => {
  fetchPokemons();
});
</script>
