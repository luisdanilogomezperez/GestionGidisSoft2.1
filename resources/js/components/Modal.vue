<script setup>
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  show: { type: Boolean, default: false },
  maxWidth: { type: String, default: '2xl' },
  closeable: { type: Boolean, default: true },
});

const emit = defineEmits(['close']);
const showSlot = ref(props.show);

watch(() => props.show, (newVal) => {
  if (newVal) {
    document.body.style.overflow = 'hidden';
    showSlot.value = true;
  } else {
    document.body.style.overflow = null;
    setTimeout(() => (showSlot.value = false), 200);
  }
});

const close = () => {
  if (props.closeable) emit('close');
};

const closeOnEscape = (e) => {
  if (e.key === 'Escape' && props.show) {
    e.preventDefault();
    close();
  }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
  document.removeEventListener('keydown', closeOnEscape);
  document.body.style.overflow = null;
});

const maxWidthClass = computed(() => ({
  sm: 'sm:max-w-sm',
  md: 'sm:max-w-md',
  lg: 'sm:max-w-lg',
  xl: 'sm:max-w-xl',
  '2xl': 'sm:max-w-2xl',
  '4xl': 'sm:max-w-4xl', 
  'full': 'sm:max-w-full',
  '30': 'sm:max-w-[30%]',
  '40': 'sm:max-w-[40%]',
  '50': 'sm:max-w-[50%]',
  '70': 'sm:max-w-[70%]',
  '80': 'sm:max-w-[80%]',
  '90': 'sm:max-w-[90%]',
  '96': 'sm:max-w-[96%]',
  '100': 'sm:max-w-[100%]',
}[props.maxWidth]));
</script>

<template>
  <div v-show="showSlot" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay -->
    <transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-show="show"
        class="absolute inset-0 bg-black bg-opacity-60 z-40"
        @click="close"
        style="background-color: rgba(87, 79, 79, 0.8);"
      />
    </transition>

    <!-- Modal content -->
    <transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      enter-to-class="opacity-100 translate-y-0 sm:scale-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0 sm:scale-100"
      leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
      <div
        v-show="show"
        class="relative bg-white dark:bg-gray-900 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto p-6 z-50"
        :class="maxWidthClass"
      >
        <slot v-if="showSlot" />
      </div>
    </transition>
  </div>
</template>
