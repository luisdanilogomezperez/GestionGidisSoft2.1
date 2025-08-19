<script setup>
import Modal from './Modal.vue';
import { ref, onMounted, watch } from 'vue';
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  maxWidth: { type: String, default: '2xl' },
  closeable: { type: Boolean, default: true },
  userId: [String, Number]
});

const emit = defineEmits(['close']);

const close = () => emit('close');

// datos
const roles = ref([]);        // todos los roles del sistema
const selectedRoles = ref([]) // roles que tiene este usuario

// cuando abra el modal, cargo los roles
watch(() => props.show, async (val) => {
  if (val && props.userId) {
    const { data } = await axios.get(`/users-management/${props.userId}/roles`);
    roles.value = data.roles;
    selectedRoles.value = data.userRoles;
  }
});

const saveRoles = () => {
  router.post(
    route('users.roles.update', props.userId),
    { roles: selectedRoles.value },
    {
      onSuccess: () => {
        close();
        router.reload({ only: ['users'] }); // refresca tabla de usuarios
      }
    }
  );
};
</script>

<template>
  <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
    <div class="p-6 space-y-4 bg-white dark:bg-gray-900 rounded-xl text-gray-900 dark:text-gray-100">
      <h2 class="text-xl font-semibold">Gestionar Roles para Usuario:</h2>
      <div class="space-y-2">
        <label v-for="role in roles" :key="role.id" class="flex items-center space-x-2">
          <input
            type="checkbox"
            :value="role.id"
            v-model="selectedRoles"
          />
          <span>{{ role.name }}</span>
        </label>
      </div>

      <div class="flex justify-center space-x-4 mt-6">
        <SecondaryButton @click="close">Cancelar</SecondaryButton>
        <PrimaryButton @click="saveRoles">Guardar Cambios</PrimaryButton>
      </div>
    </div>
  </Modal>
</template>