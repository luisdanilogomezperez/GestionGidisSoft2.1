<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue'
import { router, usePage  } from '@inertiajs/vue3'
import CollapsibleCard from '@/components/CollapsibleCard.vue';
import UserRolesModal from '@/components/UserRolesModal.vue'
import PrimaryButton from '@/components/PrimaryButton.vue';
import PermissionsRolesModal from '@/components/PermissionsRolesModal.vue';
import { useToast } from "vue-toastification"
import axios from 'axios'
import RolesModal from '@/components/RolesModal.vue';
import Pagination from '@/components/Pagination.vue';
import ViewUsersInfoModal from '../Partials/Modals/UsersModal/ViewUsersInfoModal.vue';
import Swal from 'sweetalert2'

const page = usePage()
const toast = useToast()
const isRoleModalOpen = ref(false)
const isGestionRolesModalOpen = ref(false)
const isPermissionsModalOpen = ref(false)
const selectedUserId = ref<number | string | null>(null)
const maxWidth = 'xl'
const maxWidthPermissions = 'x1'
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gestión de Usuarios',
        href: '/users-management',
    },
];

const props = defineProps({
    users: {
        type: Object,
        required: true
    },
    authUser: {
        type: Object,
        required: true
    },
});

// Filtros individuales
const filterName = ref('')
const filterDocumentType = ref('')
const filterDocumentNumber = ref('')
const userInfoSelected = ref(null)
const isUserInfoModalOpen = ref('cerrar')
// Opciones de tipo de documento (puedes cargarlas dinámicamente si quieres)
const documentTypes = ref([
    { value: '', text: 'Todos' },
    { value: 'CC', text: 'Cédula de Ciudadanía' },
    { value: 'TI', text: 'Tarjeta de Identidad' },
    { value: 'CE', text: 'Cédula de Extranjería' },
    { value: 'PS', text: 'Pasaporte' },
])

// Computed para filtrar la tabla
const filteredUsers = computed(() => {
  return props.users.data.filter((user: any) => {
    // Evitar errores si algún campo es null o numérico
    const name = (user.name || "").toString().toLowerCase();
    const documentType = (user.document_type || "").toString();
    const documentNumber = (user.document_number || "").toString().toLowerCase();

    const matchesName = name.includes(filterName.value.toLowerCase());
    const matchesDocumentType = filterDocumentType.value
      ? documentType === filterDocumentType.value
      : true;
    const matchesDocumentNumber = documentNumber.includes(filterDocumentNumber.value.toLowerCase());

    return (
      ///user.id !== props.authUser.id && // excluir usuario logueado
      matchesName &&
      matchesDocumentType &&
      matchesDocumentNumber
    );
  });
});

// Acciones disponibles
const usersActions = ref([
  { value: '', text: 'Seleccione una acción', disabled: true },
  { value: 'delete', text: 'Eliminar', route: (id: number | string) => `/users-management/${id}/delete` },
  { value: 'watch', text: 'Ver Información' },
  { value: 'gestion_roles', text: 'Asignar Roles' },
])


// Objeto para guardar el valor seleccionado por usuario
const userActionsSelected = ref({})
const modalOrigin = ref({ x: 0, y: 0 });

// Manejar la acción seleccionada
function handleUserAction(userId, event) {
  const selectedValue = userActionsSelected.value[userId];
  
  // Resetear el select después de la selección
  userActionsSelected.value[userId] = '';

  switch(selectedValue) {
    case 'delete':
        deleteUser(userId);
        break;
      
    case 'watch':
        const userSelected = props.users.data.find((user: any) => user.id === userId);

        // Capturar la posición del botón que disparó la acción
        const rect = event?.target?.getBoundingClientRect();
        if (rect) {
            modalOrigin.value = { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
        }
        
        openUserInfoModal(userSelected);
        break;  
      
    case 'disable':
    case 'enable':
        axios.post(`/users-management/${userId}/${selectedValue}`)
        .then(res => {
            toast.success(res.data.message, { position: "top-right" })
            router.reload({ only: ['users'] })
        })
        .catch(err => {
            toast.error("Ocurrió un error", { position: "top-right" })
        })
        break;
      
    case 'gestion_roles':
        selectedUserId.value = userId;
        isRoleModalOpen.value = true;
      break;

    default:
      break;
  }
}

const deleteUser = (userId: any) => {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Este usuario será eliminado permanentemente",
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
        html: "Eliminando usuario, por favor espere.",
        timer: 1000, 
        timerProgressBar: true,
        allowOutsideClick: false,
        color: "#b42529",
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
          const response = await axios.delete(`/users-management/${userId}/delete`);
          Swal.fire({
            title: "Éxito",
            text: response.data.message || "Usuario eliminado correctamente",
            icon: "success",
            timer: 1500,
            showConfirmButton: false,
            timerProgressBar: true,
            color: "#b42529",
          });
          router.reload({ only: ['users'] });
        } catch (error) {
          Swal.fire({
            title: "Error",
            text: error.response?.data?.error || "No se pudo eliminar el usuario.",
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

const permissionsModalOpen = () => {
   isPermissionsModalOpen.value = true;
};

const gestionRolesModalOpen = () => {
   isGestionRolesModalOpen.value = true;
};

const openUserInfoModal = (userSelected) => {
    userInfoSelected.value = userSelected;
    isUserInfoModalOpen.value = 'abrir';
}
</script>

<template>
    <Head title="Users Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-0 rounded-xl p-2 overflow-x-auto">
            <!-- Área de filtros --> 
            <div class="block text-sm font-medium text-white-700 dark:text-white-300 mb-3">
                <PrimaryButton
                    @click="(e) => {
                        const rect = e.currentTarget.getBoundingClientRect()
                        if (typeof window !== 'undefined') {
                        modalOrigin = {
                            x: rect.left - window.innerWidth / 2,
                            y: rect.top - window.innerHeight / 2
                        }
                        }
                        permissionsModalOpen()
                    }"
                    >
                    Gestionar Permisos
                </PrimaryButton>
                <PrimaryButton
                    class="ml-2"
                    @click="(e) => {
                        const rect = e.currentTarget.getBoundingClientRect()
                        if (typeof window !== 'undefined') {
                        modalOrigin = {
                            x: rect.left - window.innerWidth / 2,
                            y: rect.top - window.innerHeight / 2
                        }
                        }
                        gestionRolesModalOpen()
                    }"
                    >
                    Gestionar Roles
                </PrimaryButton>


            </div>
            <CollapsibleCard title="Filtro" :collapsible="true">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    
                    <!-- Filtro por nombre -->
                    <div>
                        <label for="filter-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                        <input
                            id="filter-name"
                            type="text"
                            v-model="filterName"
                            placeholder="Buscar por nombre..."
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-2"
                        />
                    </div>

                    <!-- Filtro por tipo de documento -->
                    <div>
                        <label for="filter-document-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de documento</label>
                        <select
                            id="filter-document-type"
                            v-model="filterDocumentType"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-2"
                        >
                            <option
                            v-for="type in documentTypes"
                            :key="type.value"
                            :value="type.value"
                            >
                            {{ type.text }}
                            </option>
                        </select>
                    </div>

                    <!-- Filtro por número de documento -->
                    <div>
                        <label for="filter-document-number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Número de documento
                        </label>
                        <input
                            id="filter-document-number"
                            type="text"
                            v-model="filterDocumentNumber"
                            placeholder="Buscar por número de documento..."
                            inputmode="numeric"
                            pattern="[0-9]*"
                            @input="filterDocumentNumber = filterDocumentNumber.replace(/\D/g, '')"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-2"
                        />
                    </div>
                </div>
            </CollapsibleCard>
            <!-- Tabla filtrada -->
             <CollapsibleCard title="Listado de Usuarios" :collapsible="false" class="flex-1 mt-4">
                <div
                    class="rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
                >
                    <table class="min-w-full border border-red-700 rounded-lg overflow-hidden">
                        <thead class="bg-red-500 dark:bg-red-500 shadow-md border border-red-500 dark:border-red-500">
                            <tr class="text-left text-sm font-semibold text-white dark:text-white border-b">
                                <th class="pl-2 pt-2 pb-2">Nombre Completo</th>
                                <th class="pl-2">Tipo de Documento</th>
                                <th class="pl-2">Número de Documento</th>
                                <th class="pl-2">Email</th>
                                <th class="pl-2">Estado</th>
                                <th class="pl-2">Rol</th>
                                <th class="pl-2 pr-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in filteredUsers" :key="user.id" class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="pl-2">{{ user.name }} {{ user.last_name }}</td>
                                <td class="pl-2">{{ user.document_type }}</td>
                                <td class="pl-2">{{ user.document_number }}</td>
                                <td class="pl-2">{{ user.email }}</td>
                                <td class="pl-2">
                                    <span v-if="user.is_enable === 1" class="text-green-600">Habilitado</span>
                                    <span v-else class="text-red-600">Deshabilitado</span>
                                </td>
                                <td class="pl-2">
                                    {{ user.roles.map(role => role.name).join(', ') }}
                                </td>
                                <td class="pl-2 pr-2">
                                    <select
                                    v-model="userActionsSelected[user.id]"
                                    @change="handleUserAction(user.id)"
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-1"
                                    >   
                                    <option
                                        v-for="action in usersActions"
                                        :key="action.value"
                                        :value="action.value"
                                    >
                                        {{ action.text }}
                                    </option>
                                    <option value="disable" v-if="user.is_enable === 1">
                                        Deshabilitar
                                    </option>
                                    <option value="enable" v-else>
                                        Habilitar
                                    </option>
                                    </select>
                                </td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="7" class="text-center bg-red-100 dark:bg-red-100 text-red-600 p-4">
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Pagination :links="props.users.links" />
                </div>
            </CollapsibleCard>
        </div>
        <div>
            <!-- Modal de gestión de roles -->
            <UserRolesModal
            :show="isRoleModalOpen"
            :max-width="'40'"
            :user-id="selectedUserId"
            @close="isRoleModalOpen = false"
            />
            <!-- Modal de gestión de roles -->
            <RolesModal
            :show="isGestionRolesModalOpen"
            :rol-id="selectedUserId"
            :max-width="'50'"
            @close="isGestionRolesModalOpen = false"
            />
            <!-- Modal de gestión de permisos de roles -->
            <PermissionsRolesModal
            :show="isPermissionsModalOpen"
            :rol-id="selectedUserId"
            :max-width="'50'"
            @close="isPermissionsModalOpen = false"
            />
            <!-- Modal de información del usuario -->
            <ViewUsersInfoModal
            :show="isUserInfoModalOpen === 'abrir'"
            :user-selected="userInfoSelected"
            :max-width="'70'"
            @close="() => { 
                isUserInfoModalOpen = 'cerrar'; 
                userInfoSelected = null 
            }"
            />
        </div>
    </AppLayout>
        
        
</template>
