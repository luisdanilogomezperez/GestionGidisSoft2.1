<script setup lang="ts">
import { useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import * as AllIcons from '@kalimahapps/vue-icons';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import CollapsibleCard from '@/components/CollapsibleCard.vue';
import Pagination from '@/components/Pagination.vue';
import axios from 'axios'
import Swal from 'sweetalert2'
import PrimaryButton from '@/components/PrimaryButton.vue';
import Input from '@/components/ui/input/Input.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import SettingsModal from '../Partials/Modals/SettingsModal/SettingsModal.vue';

const breadcrumbs: BreadcrumbItem[] = [{
  title: 'Ajustes Generales', 
  href: '/menu-items' 
}];

const page = usePage();
const props = defineProps({
    items: Array
});

const form = useForm({
    title: '',
    href: '',
    icon: '',
    permission: '',
    order: 0,
});

const editingId = ref<number | null>(null);
const createNewItem = ref(false);
const showConfigLogo = ref('cerrar');

function createNewItemToggle() {
    createNewItem.value = true
}

function showConfigLogoToggle() {
    showConfigLogo.value = 'abrir'
}

function close() {
    createNewItem.value = false
    form.reset();
    editingId.value = null;
}

function submit() {
    if (editingId.value) {
        form.put(`/menu-items/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                editingId.value = null;
                form.reset();
                // Forzar recarga completa para actualizar el menú
                window.location.reload();
            }
        });
    } else {
        form.post(route('menu-items.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                // Forzar recarga completa para actualizar el menú
                window.location.reload();
            }
        });
    }
}


// Acciones disponibles
const itemsActions = ref([
  { value: '', text: 'Seleccione una acción', disabled: true },
  { value: 'delete', text: 'Eliminar' },
  { value: 'edit', text: 'Modificar' },
])


// Objeto para guardar el valor seleccionado por usuario
const itemsActionsSelected = ref({})

// Manejar la acción seleccionada
function handleUserAction(itemId) {
    const selectedValue = itemsActionsSelected.value[itemId];
    
    // Resetear el select después de la selección
    itemsActionsSelected.value[itemId] = '';

    switch(selectedValue) {
      case 'delete':
          if (confirm('¿Estás seguro de querer eliminar este item?')) {
              router.delete(route('menu-items.destroy', itemId), {
                  preserveScroll: true,
                  onSuccess: () => {
                      window.location.reload();
                  },
              });
          }
          break;
        
      case 'edit':
           
          break;
        
      default:
        break;
    }
}

const editItem =  (item: any) => {
    if (item) {
        form.title = item.title;
        form.href = item.href || '';
        form.icon = item.icon || '';
        form.permission = item.permission || '';
        form.order = item.order || 0;
        editingId.value = item.id;
    }
    createNewItemToggle();
}

const deleteItem = (itemId: any) => {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Este libro será eliminado permanentemente",
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
        html: "Eliminando libro, por favor espere.",
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
          const response = await axios.delete(`/books/${bookId}`);
          Swal.fire({
            title: "Éxito",
            text: response.data.message || "Libro eliminado correctamente",
            icon: "success",
            timer: 1500,
            showConfirmButton: false,
            timerProgressBar: true,
            color: "#b42529",
          });
          router.reload({ only: ['books'] });
        } catch (error) {
          Swal.fire({
            title: "Error",
            text: error.response?.data?.message || "Ocurrió un error al eliminar el libro.",
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
    <Head title="Menú" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <!-- Formulario -->
            <div class="flex justify-start gap-4">
                <PrimaryButton @click="showConfigLogoToggle" type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-4">
                        Gestionar Logos
                </PrimaryButton>
                <PrimaryButton v-if="!createNewItem" @click="createNewItemToggle" type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-4">
                        Nuevo Item
                </PrimaryButton>
                <SettingsModal
                :show=" showConfigLogo === 'abrir'"
                :max-width="'80'"
                :closeable="true"
                @close="() => { 
                    showConfigLogo = 'cerrar'; 
                }"
                />
            </div>
            
            <form v-if="createNewItem" @submit.prevent="submit" class="mb-6 space-y-2 mt-6">
                <Input v-model="form.title" required placeholder="Título"  />
                <Input v-model="form.href"  required placeholder="Ruta /href" />
                <Input v-model="form.permission" placeholder="Permiso (opcional)" />
                <Input v-model.number="form.order" required type="number" placeholder="Orden" />
                <Input v-model="form.icon" type="text" required placeholder="Nombre del Icono" />
                
                <SecondaryButton @click="close">
                    Cancelar
                </SecondaryButton>
                <PrimaryButton type="submit" class="ml-2">
                    {{ editingId ? 'Actualizar' : 'Guardar' }}
                </PrimaryButton>
            </form>

            <!-- Lista de menú existente -->
            <CollapsibleCard title="Listado de Items del Menú" :collapsible="false" class="flex-1 mt-4">
                <div
                    class="rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
                >
                    <table class="min-w-full border border-red-700 rounded-lg overflow-hidden">
                        <thead class="bg-red-500 dark:bg-red-500 shadow-md border border-red-500 dark:border-red-500">
                            <tr class="text-left text-sm font-semibold text-white dark:text-white border-b">
                            <th class="px-4 py-2">Titulo</th>
                            <th class="px-4 py-2">Ruta</th>
                            <th class="px-4 py-2">Icono</th>
                            <th class="px-4 py-2">Orden</th>
                            <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in props.items.data" :key="item.id" class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                              <td class="px-4 py-2 truncate">{{ item.title }}</td>
                              <td class="px-4 py-2 truncate">{{ item.href ?? 'sin ruta' }}</td>
                              <td class="px-4 py-2 truncate"><component v-if="item.icon" :is="AllIcons[item.icon]" class="w-5 h-5" /></td>
                              <td class="px-4 py-2 truncate">{{ item.order }}</td>
                              <td class="px-4 py-2">
                                <button
                                type="button"
                                title="Eliminar"
                                @click="deleteItem(item.id)"
                                class="p-1"
                                >   
                                  <component :is="AllIcons['AkTrashCan']" class="text-red-500 w-5 h-5" />
                                </button>
                                <!-- Botón Editar -->
                                <button
                                type="button"
                                title="Editar"
                                @click="editItem(item)"
                                class="pl-2 p-1"
                                >
                                  <component :is="AllIcons['BsPencilSquare']" class="text-blue-500 w-5 h-5" />
                                </button>
                              </td>
                            </tr>
                        </tbody>
                    </table>
                    <Pagination :links="props.items.links" />
                    
                </div>
            </CollapsibleCard>
        </div>
    </AppLayout>
</template>
