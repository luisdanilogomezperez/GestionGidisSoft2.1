<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import CollapsibleCard from '@/components/CollapsibleCard.vue';
import DialogModal from '@/components/DialogModal.vue'
import UserRolesModal from '@/components/UserRolesModal.vue'

const isRoleModalOpen = ref(false)
const selectedUserId = ref<number | string | null>(null)
const maxWidth = '2xl'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users Management',
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
  { value: 'watch', text: 'Ver Información', route: (id: number | string) => `/users-management/${id}` },
  { value: 'gestion_roles', text: 'Gestionar Roles' },
])


// Objeto para guardar el valor seleccionado por usuario
const userActionsSelected = ref({})

// Manejar la acción seleccionada
function handleUserAction(userId) {
  const selectedValue = userActionsSelected.value[userId];
  
  // Resetear el select después de la selección
  userActionsSelected.value[userId] = '';

  switch(selectedValue) {
    case 'delete':
        if (confirm('¿Estás seguro de querer eliminar este usuario?')) {
            router.delete(`/users-management/${userId}/delete`);
        }
        break;
      
    case 'watch':
        router.get(`/users-management/${userId}`);
        break;
      
    case 'disable':
    case 'enable':
        router.post(`/users-management/${userId}/${selectedValue}`, {}, {
            onSuccess: () => router.reload()
        });
        break;
      
    case 'gestion_roles':
        alert('gestion_roles')
        selectedUserId.value = userId;
        isRoleModalOpen.value = true;
        alert(isRoleModalOpen.value)
      break;

    default:
      break;
  }
}
</script>

<template>
    <Head title="Users Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-0 rounded-xl p-2 overflow-x-auto">
            <!-- Área de filtros --> 
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
                            placeholder="Buscar por número..."
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
                            <th class="px-4 py-2">Nombre Completo</th>
                            <th class="px-4 py-2">Tipo de Documento</th>
                            <th class="px-4 py-2">Número de Documento</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Rol</th>
                            <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in filteredUsers" :key="user.id" class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 truncate">{{ user.name }} {{ user.last_name }}</td>
                            <td class="px-4 py-2 truncate">{{ user.document_type }}</td>
                            <td class="px-4 py-2 truncate">{{ user.document_number }}</td>
                            <td class="px-4 py-2 truncate">{{ user.email }}</td>
                            <td class="px-4 py-2">
                                <span v-if="user.is_enable === 1" class="text-green-600">Enable</span>
                                <span v-else class="text-red-600">Disable</span>
                            </td>
                            <td class="px-4 py-2 truncate">
                                {{ user.roles.map(role => role.name).join(', ') }}
                            </td>
                            <td class="px-4 py-2">
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
                                    Disable
                                </option>
                                <option value="enable" v-else>
                                    Enable
                                </option>
                                </select>
                            </td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                            <td colspan="7" class="text-center bg-red-200 dark:bg-red-900 text-red-600 p-4">
                                No se encontraron usuarios.
                            </td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
            </CollapsibleCard>
            
            
        </div>
        {{ isRoleModalOpen }}
            <!-- Modal de gestión de roles -->
        <UserRolesModal
            :show="isRoleModalOpen"
            max-width="2xl"
            @close="isRoleModalOpen = false"
        >
            <template #title>
                Gestionar roles del usuario {{ selectedUserId }}
            </template>

            <template #content>
                Aquí iría la gestión de roles para el usuario **{{ selectedUserId }}**.
            </template>

            <template #footer>
                <button
                    class="bg-gray-500 text-white px-4 py-2 rounded"
                    @click="isRoleModalOpen = false"
                >
                    Cerrar
                </button>
            </template>
        </UserRolesModal>

    </AppLayout>
</template>
