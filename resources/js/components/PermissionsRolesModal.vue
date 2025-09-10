<script setup>
import Modal from './Modal.vue';
import { ref, computed, watch } from 'vue';
import { usePage, useForm, } from '@inertiajs/vue3'
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import axios from 'axios'
import { useToast } from "vue-toastification"
import { AkTrashCan, BsPencilSquare } from '@kalimahapps/vue-icons';
import Swal from 'sweetalert2'

const toast = useToast()
const roles = ref([]);
const permissions =  ref([]);

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

const form = useForm({
    name: '',
});

const editPermission = (permission) => {
  form.name = permission.name
  editingId.value = permission.id
}
function limpiar() {
  form.name = ''
  editingId.value = ''
}

const emit = defineEmits(['close']);

const close = () => {
    selectedPermissions.value = [];
    selectedRole.value = ''
    search.value = ''
    emit('close');
};
const selectedRole = ref('');
const selectedPermissions = ref([]);

// estado del checkbox maestro
const selectAll = ref(false);
const search = ref('');
const editingId = ref('');

// 🔹 Función central para traer los roles
async function fetchRoles() {
    try {
        const { data } = await axios.get(route('roles')); 
        roles.value = data.roles;
    } catch (error) {
        toast.error("Error cargando roles:", error, { position: "top-right" });
    }
}

// 🔹 Función central para traer los roles
async function fetchPermissions() {
    try {
        const { data } = await axios.get(route('permissions'));
        permissions.value = data.permissions;
    } catch (error) {
        toast.error("Error cargando permisos:", error, { position: "top-right" });
    }
}

// 🔹 Ejecutar solo cuando se abre el modal
watch(() => props.show, async (newValue) => {
    if (newValue) {
        await fetchRoles();
        await fetchPermissions();
    }
});

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

// función que marca o desmarca todos
const toggleAll = () => {
  if (selectAll.value) {
    // selecciona todos los IDs
    selectedPermissions.value = filteredPermissions.value.map(p => p.id);
  } else {
    // limpia selección
    selectedPermissions.value = [];
  }
};

// sincronizar el checkbox maestro según los hijos
watch(selectedPermissions, (newVal) => {
  selectAll.value = newVal.length === filteredPermissions.value.length;
});

// Guardar cambios
const savePermissions = async () => {
  if (!selectedRole.value) {
    toast.error("Primero debe selecconar un rol.", { position: "top-right" });
    return  
  }

  await axios.post(`/users-management/${selectedRole.value}/permissions`, {
    permissions: selectedPermissions.value
  })
  toast.success("Permisos asignados correctamente", { position: "top-right" });
  close()
}


// 🔹 Crear o actualizar permiso
async function submit() {
    try {
        if (editingId.value) {
            await axios.put(route('permissions.update', editingId.value), { name: form.name });
            toast.success("Permiso actualizado correctamente");
        } else {
            await axios.post(route('permissions.store'), { name: form.name });
            toast.success("Permiso creado correctamente");
        }

        await fetchRoles();
        await fetchPermissions();
        form.reset();
        editingId.value = null;

    } catch (error) {
        if (error.response && error.response.data.errors) {
            const messages = Object.values(error.response.data.errors).flat();
            messages.forEach(msg => toast.error(msg, { position: "top-right" }));
        } else {
            toast.error("Error guardando rol", { position: "top-right" });
        }
    }
}

const deletePermission = (id) => {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Este permiso será eliminado permanentemente",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
    reverseButtons: true,
    showCloseButton: false,
    customClass: {
      confirmButton: "inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150 ml-2",
      cancelButton: "inline-flex items-center px-4 py-2 bg-white border border-blue-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150",
      closeButton: "text-red-500 hover:text-red-600",
      icon: "text-red-500 hover:text-red-600",
    },
    buttonsStyling: false, 
    allowOutsideClick: () => !Swal.isLoading(),
  }).then((result) => {
    if (result.isConfirmed) {
      let timerInterval;
      Swal.fire({
        title: "Procesando...",
        html: "Eliminando permiso, por favor espere.",
        timer: 1000, 
        timerProgressBar: true,
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
          const timer = Swal.getPopup().querySelector("b");
          timerInterval = setInterval(() => {
            if (timer) timer.textContent = `${Swal.getTimerLeft()}`;
          }, 100);
        },
        willClose: () => {
          clearInterval(timerInterval);
        }
      }).then(async () => {
        try {
          const response = await axios.delete(`/users-management/${id}/delete_permission`);
          Swal.fire({
            title: "Éxito",
            text: response.data.message || "Permiso eliminado correctamente",
            icon: "success",
            timer: 1500,
            showConfirmButton: false,
            timerProgressBar: true
          });
          await fetchRoles();
          await fetchPermissions();
        } catch (error) {
          Swal.fire({
            title: "Error",
            text: error.response?.data?.message || "No se pudo eliminar el permiso.",
            icon: "error",
            timer: 1500,
            showConfirmButton: false,
            timerProgressBar: true
          });
        }
      });
    }
  });
};

</script>

<template>
    <Modal :show="show" :max-width="maxWidth" @close="close">
    <div class="p-6 space-y-4 bg-white dark:bg-gray-900 rounded-xl text-gray-900 dark:text-gray-100">
      <h2 class="text-xl font-semibold">Gestionar Permisos</h2>
      <h2 class="text-xl font-semibold">
        {{ editingId ? 'Editar Permiso' : 'Crear Permiso' }}</h2>
      <form @submit.prevent="submit" class="mb-6 space-y-2">
          <input v-model="form.name" placeholder="Nombre del Rol" class="border p-2 w-full" required />
          <SecondaryButton v-if="form.name.length > 0" @click="limpiar" class="mr-2">Limpiar</SecondaryButton>
          <PrimaryButton>
            {{ editingId ? 'Actualizar' : 'Guardar' }}
          </PrimaryButton>
      </form>
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
          <thead class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 top-0 z-10 w-full left-0 right-0">
            <tr>
                <th class="p-2 text-left">Permiso</th>
                <th class="p-2 text-center">
                  <input 
                    type="checkbox" 
                    v-model="selectAll" 
                    @change="toggleAll" 
                  /> Asignado
                </th>
                <th class="p-2 text-center">Acciones</th>
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
              <td class="p-2 text-center"> 
                <!-- Botón Eliminar -->
                <button
                  type="button"
                  title="Eliminar"
                  @click="deletePermission(perm.id)"
                  class="p-1"
                >
                  <AkTrashCan class="text-red-500 w-5 h-5"/>
                </button>

                <!-- Botón Editar -->
                <button
                  type="button"
                  title="Editar"
                  @click="editPermission(perm)"
                  class="pl-2 p-1"
                >
                  <BsPencilSquare class="text-blue-500 w-5 h-5"/>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Botón Guardar -->
      <div class="flex justify-end space-x-3 mt-4">
        <SecondaryButton @click="close">Cerrar</SecondaryButton>
        <PrimaryButton @click="savePermissions">Guardar Cambios</PrimaryButton>
      </div>
    </div>
  </Modal>
</template>
<style lang="scss">
</style>