<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import * as AllIcons from '@kalimahapps/vue-icons';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import CollapsibleCard from '@/components/CollapsibleCard.vue';
import Pagination from '@/components/Pagination.vue';
import { AkTrashCan, BsPencilSquare } from '@kalimahapps/vue-icons';

const breadcrumbs: BreadcrumbItem[] = [{
  title: 'Configuración Dashboard', 
  href: '/menu-items' 
}];

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
  { value: 'delete', text: 'Eliminar', icon: AkTrashCan },
  { value: 'edit', text: 'Modificar', icon: BsPencilSquare },
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
           const item = props.items.data.find(i => i.id === itemId);
            if (item) {
                form.title = item.title;
                form.href = item.href || '';
                form.icon = item.icon || '';
                form.permission = item.permission || '';
                form.order = item.order || 0;
                editingId.value = item.id;
            }
          break;
        
      default:
        break;
    }
}
</script>

<template>
    <Head title="Menú" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <!-- Formulario -->
            <form @submit.prevent="submit" class="mb-6 space-y-2">
                <input v-model="form.title" placeholder="Título" class="border p-2 w-full" />
                <input v-model="form.href" placeholder="Ruta /href" class="border p-2 w-full" />
                <input v-model="form.permission" placeholder="Permiso (opcional)" class="border p-2 w-full" />
                <input v-model.number="form.order" type="number" placeholder="Orden" class="border p-2 w-full" />
                <input v-model="form.icon" type="text" placeholder="Nombre del Icono" class="border p-2 w-full" />


                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-4">
                    {{ editingId ? 'Actualizar' : 'Guardar' }}
                </button>

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
                                  <select
                                  v-model="itemsActionsSelected[item.id]"
                                  @change="handleUserAction(item.id)"
                                  class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-1"
                                  >
                                    <option
                                    v-for="action in itemsActions"
                                    :key="action.value"
                                    :value="action.value"
                                    >
                                        {{ action.text }}
                                    </option>
                                  </select>
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
