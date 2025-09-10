<script setup>
import Modal from './Modal.vue';
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3'
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import axios from 'axios'
import { AkTrashCan, BsPencilSquare } from '@kalimahapps/vue-icons';
import { useToast } from "vue-toastification"
import Swal from 'sweetalert2'

const toast = useToast()
const roles = ref([]);   

const props = defineProps({
    show: {
    type: Boolean,
    default: false
    },
    maxWidth: {
        type: String,
        default: '4xl'
    },
    closeable: {
        type: Boolean,
        default: true
    }
});

const form = useForm({
    name: '',
});

const emit = defineEmits(['close']);

const close = () => {emit('close')};
const search = ref('');
const editingId = ref('');


// 🔹 Función central para traer los roles
async function fetchRoles() {
    try {
        const { data } = await axios.get(route('roles'));
        roles.value = data.roles;
    } catch (error) {
        toast.success("Error cargando roles:", error, { position: "top-right" });
    }
}

// 🔹 Ejecutar solo cuando se abre el modal
watch(() => props.show, async (newValue) => {
    if (newValue) {
        await fetchRoles();
    }
});

// Filtro de roles
const filteredRoles = computed(() => {
  if (!search.value) return roles.value;
  return roles.value.filter(p =>
    p.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

const deleteRole = (id) => {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Este rol será eliminado permanentemente",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
    reverseButtons: true,
    showCloseButton: false,customClass: {
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
        html: "Eliminando rol, por favor espere.",
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
          const response = await axios.delete(`/users-management/${id}/delete_role`);
          Swal.fire({
            title: "Éxito",
            text: response.data.message || "Rol eliminado correctamente",
            icon: "success",
            timer: 1500,
            showConfirmButton: false,
            timerProgressBar: true
          });
          fetchRoles();
        } catch (error) {
          Swal.fire({
            title: "Error",
            text: error.response?.data?.message || "No se pudo eliminar el rol.",
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


const editRole = (role) => {
  form.name = role.name
  editingId.value = role.id
}
function limpiar() {
  form.name = ''
  editingId.value = ''
}


// 🔹 Crear o actualizar rol
async function submit() {
    try {
        if (editingId.value) {
            await axios.put(route('roles.update', editingId.value), { name: form.name });
            toast.success("Rol actualizado correctamente");
        } else {
            await axios.post(route('roles.store'), { name: form.name });
            toast.success("Rol creado correctamente");
        }

        await fetchRoles();
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
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" @close="close">
    <div class="p-6 space-y-4 bg-white dark:bg-gray-900 rounded-xl text-gray-900 dark:text-gray-100">
      <h2 class="text-xl font-semibold">Gestionar Roles</h2>
      <!-- Formulario -->
      <h2 class="text-xl font-semibold">
        {{ editingId ? 'Editar Rol' : 'Nuevo Rol' }}
      </h2>
      <form @submit.prevent="submit" class="mb-6 space-y-2">
          <input v-model="form.name" placeholder="Nombre del Rol" class="border p-2 w-full" required />
          <SecondaryButton v-if="form.name.length > 0" @click="limpiar" class="mr-2">Limpiar</SecondaryButton>
          <PrimaryButton>
            {{ editingId ? 'Actualizar' : 'Guardar' }}
          </PrimaryButton>
      </form>

      <!-- Buscador -->
      <h2 class="text-xl font-semibold">Filtro de Roles</h2>
      <input
        v-model="search"
        type="text"
        placeholder="Filtrar roles..."
        class="border rounded p-2 w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
      />

      <!-- Tabla -->
      <div class="max-h-80 overflow-y-auto border rounded bg-white dark:bg-gray-800">
        <table class="w-full text-sm">
          <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
              <th class="p-2 text-left">Rol</th>
              <th class="p-2 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="role in filteredRoles" :key="role.id"
              class="border-t hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="p-2">{{ role.name }}</td>
              <td class="p-2 text-center"> 
                <!-- Botón Eliminar -->
                <button
                  type="button"
                  title="Eliminar"
                  @click="deleteRole(role.id)"
                  class="p-1"
                >
                  <AkTrashCan class="text-red-500 w-5 h-5"/>
                </button>

                <!-- Botón Editar -->
                <button
                  type="button"
                  title="Editar"
                  @click="editRole(role)"
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
        <PrimaryButton @click="close">Cerrar</PrimaryButton>
      </div>
    </div>
  </Modal>
</template>
<style lang="scss">
</style>