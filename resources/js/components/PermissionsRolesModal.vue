<script setup>
import Modal from './Modal.vue';
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3'
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import axios from 'axios'

const page = usePage();

const roles = computed(() => page.props.auth.all_roles);
const permissions = computed(() => page.props.auth.all_permissions);

const props = defineProps({
    show: {
    type: Boolean,
    default: false
    },
    maxWidth: {
        type: String,
        default: 'xl'
    },
    closeable: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['close']);

const close = () => {
    selectedPermissions.value = [];
    selectedRole.value = ''
    search.value = ''
    emit('close');
};
const selectedRole = ref('');
const selectedPermissions = ref([]);
const search = ref('');

// Filtro de permisos
const filteredPermissions = computed(() => {
  if (!search.value) return permissions.value;
  return permissions.value.filter(p =>
    p.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

watch(selectedRole, async (roleId) => {
  if (!roleId) {
    selectedPermissions.value = [];
    return;
  }

  
  try {
    const { data } = await axios.get(`/roles/${roleId}/permissions`);
    selectedPermissions.value = data.map(p => p.id);
  } catch (error) {
    console.error(error);
  }
});


// Guardar cambios
const savePermissions = async () => {
  if (!selectedRole.value) return

  await axios.post(`/users-management/${selectedRole.value}/permissions`, {
    permissions: selectedPermissions.value
  })

  alert('Permisos actualizados correctamente.')
  close()
}
</script>

<template>
    <Modal :show="show" max-width="4xl" @close="close">
    <div class="p-6 space-y-4 bg-white dark:bg-gray-900 rounded-xl text-gray-900 dark:text-gray-100">
      <h2 class="text-xl font-semibold">Gestionar Permisos</h2>
      <!-- Select de roles -->
      <select
        v-model="selectedRole"
        class="border rounded p-2 w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
      >
        <option disabled value="">Seleccione un rol</option>
        <option v-for="role in roles" :key="role.id" :value="role.id">
          {{ role.name }}
        </option>
      </select>

      <!-- Buscador -->
      <input
        v-model="search"
        type="text"
        placeholder="Filtrar permisos..."
        class="border rounded p-2 w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
      />

      <!-- Tabla -->
      <div class="max-h-80 overflow-y-auto border rounded bg-white dark:bg-gray-800">
        <table class="w-full text-sm">
          <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
              <th class="p-2 text-left">Permiso</th>
              <th class="p-2 text-center">Asignado</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="perm in filteredPermissions"
              :key="perm.id"
              class="border-t hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="p-2">{{ perm.name }}</td>
              <td class="p-2 text-center">
                <input 
                    type="checkbox" 
                    :value="perm.id" 
                    v-model="selectedPermissions"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Botón Guardar -->
      <div class="flex justify-end space-x-3 mt-4">
        <SecondaryButton @click="close">Cancelar</SecondaryButton>
        <PrimaryButton @click="savePermissions">Guardar Cambios</PrimaryButton>
      </div>
    </div>
  </Modal>
</template>
<style lang="scss">
</style>