<template>
  <div class="p-8 max-w-md mx-auto">
    <button @click="goBack" class="mb-4 text-blue-600 hover:underline">
      &larr; Voltar
    </button>

    <div v-if="loading" class="text-center">
      <span>Carregando detalhes...</span>
    </div>

    <div
      v-else
      class="bg-white rounded-2xl shadow p-6 flex flex-col items-center"
    >
      <img :src="pokemon.photo" :alt="pokemon.name" class="w-32 h-32 mb-4" />
      <h1 class="text-2xl font-bold capitalize mb-2">
        {{ pokemon.name }}
      </h1>
      <p class="mb-1">
        <strong>Tipos:</strong>
        {{ pokemon.types.map((t) => t.type_name).join(", ") }}
      </p>
      <p class="mb-1"><strong>Altura:</strong> {{ pokemon.height }} cm</p>
      <p class="mb-1"><strong>Peso:</strong> {{ pokemon.weight }} kg</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const loading = ref(true);
const pokemon = ref(null);

// extrai o nome do Pokémon da URL, ex: "/pikachu" → "pikachu"
const name = window.location.pathname.split("/").filter(Boolean)[0] || "";

const fetchDetail = async () => {
  try {
    const res = await fetch(`/api/${name}`);
    if (!res.ok) throw new Error("Erro na resposta da API");
    pokemon.value = await res.json();
  } catch (err) {
    console.error("Erro ao buscar detalhes:", err);
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  window.history.back();
};

const formatDate = (iso) => new Date(iso).toLocaleString();

onMounted(fetchDetail);
</script>

<style scoped>
/* ajuste se quiser centralizar melhor */
</style>
