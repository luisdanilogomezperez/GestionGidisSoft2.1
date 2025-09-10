<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  id: { type: String, default: '' },               // permite id personalizado desde el padre
  modelValue: { type: [File, Array, null], default: null },
  multiple: { type: Boolean, default: false },
  accept: { type: String, default: '' },
  placeholder: { type: String, default: 'Upload File' },
  disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'change']);

// id realmente usado (si no envías uno, generamos uno único)
const computedId = computed(() =>
  props.id || `fu-${Math.random().toString(36).slice(2, 9)}`
);

const inputRef = ref(null);

function handleChange(e) {
  const files = e.target.files || [];
  const value = props.multiple ? Array.from(files) : (files[0] || null);
  emit('update:modelValue', value);
  emit('change', value);
}

function removeFile(index = null) {
  if (props.multiple) {
    const list = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
    if (index !== null) list.splice(index, 1);
    emit('update:modelValue', list.length ? list : null);
  } else {
    emit('update:modelValue', null);
  }
  // resetear input para poder volver a seleccionar el mismo archivo
  if (inputRef.value) inputRef.value.value = '';
}

const hasFile = computed(() =>
  props.multiple ? Array.isArray(props.modelValue) && props.modelValue.length > 0
                 : !!props.modelValue
);
</script>

<template>
  <div class="w-full">
    <!-- Zona de arrastre / botón -->
    <label
      :for="computedId"
      class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-red-400 rounded-xl cursor-pointer bg-red-50 hover:bg-red-100 transition"
      :class="{ 'opacity-60 pointer-events-none': disabled }"
    >
      <svg class="w-10 h-10 text-red-500 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12V4m0 8l-4-4m4 4l4-4"/>
      </svg>
      <p class="text-red-600 font-medium">{{ placeholder }}</p>
      <input
        :id="computedId"
        ref="inputRef"
        type="file"
        class="hidden"
        :multiple="multiple"
        :accept="accept"
        :disabled="disabled"
        @change="handleChange"
      />
    </label>

    <!-- Archivo(s) seleccionado(s) -->
    <div v-if="hasFile" class="space-y-2 mt-4">
      <!-- Único -->
      <div
        v-if="!multiple"
        class="flex items-center justify-between p-3 bg-white border border-red-300 rounded-lg shadow"
      >
        <span class="text-sm text-gray-700 truncate max-w-[240px]">
          {{ (modelValue && modelValue.name) || '' }}
        </span>
        <button
          type="button"
          title="Eliminar archivo"
          @click="removeFile()"
          class="p-2 rounded-full bg-red-500 hover:bg-red-600 transition"
        >
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Múltiples -->
      <div v-else class="space-y-2">
        <div
          v-for="(f, i) in modelValue"
          :key="i"
          class="flex items-center justify-between p-3 bg-white border border-red-300 rounded-lg shadow"
        >
          <span class="text-sm text-gray-700 truncate max-w-[240px]">{{ f.name }}</span>
          <button type="button" @click="removeFile(i)" class="p-2 rounded-full bg-red-500 hover:bg-red-600 transition">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
