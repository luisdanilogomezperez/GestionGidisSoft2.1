<template>
  <div class="border rounded-xl overflow-hidden shadow">
    <!-- Header -->
    <div
      class="bg-red-500 text-white px-4 py-2 relative flex justify-center items-center"
      :class="collapsible ? 'cursor-pointer' : ''"
      @click="collapsible ? toggle() : null"
    >
      <!-- Título centrado -->
      <span class="font-semibold">{{ title }}</span>

      <!-- Icono alineado a la derecha (solo si es colapsable) -->
      <span v-if="collapsible" class="absolute right-4">
        {{ isOpen ? "▲" : "▼" }}
      </span>
    </div>

    <!-- Contenido -->
    <transition name="fade" v-if="collapsible">
      <div v-show="isOpen" class="p-4">
        <slot></slot>
      </div>
    </transition>

    <!-- Si no es colapsable, siempre mostrar -->
    <div v-else class="p-4">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  collapsible: {
    type: Boolean,
    default: true
  }
});

const isOpen = ref(true);

function toggle() {
  isOpen.value = !isOpen.value;
}
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
