<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { usePage, router  } from '@inertiajs/vue3'
import CollapsibleCard from '@/components/CollapsibleCard.vue';
import Pagination from '@/components/Pagination.vue';
import { ref, computed, watch, defineProps } from 'vue'
import PrimaryButton from '@/components/PrimaryButton.vue';
import CreateBooksModal from '../Partials/Modals/BooksModal/CreateBooksModal.vue';
import * as AllIcons from '@kalimahapps/vue-icons';
import ViewBooksModal from '../Partials/Modals/BooksModal/ViewBooksModal.vue';
import axios from 'axios'
import Swal from 'sweetalert2'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gestión de Libros',
        href: '/books',
    },
];

const props = defineProps({
    books: Array
});
const bookSelected = ref(null);
const page = usePage();
const isModalNuevoLibroOpen = ref('cerrar');
const isModalVerLibroOpen = ref('cerrar');
// Filtros individuales
const filterTitle = ref('')
const filterYear = ref('')
const filterMonth = ref('')

// Computed para filtrar la tabla
const filteredBooks = computed(() => {
  return props.books.data.filter((book: any) => {
    // Evitar errores si algún campo es null o numérico
    const title = (book.title || "").toString().toLowerCase();
    const year = (book.year || "").toString();
    const month = (book.month || "").toString().toLowerCase();

    const matchesTitle = title.includes(filterTitle.value.toLowerCase());
    const matchesYear = year.includes(filterYear.value);
    const matchesMonth = month.includes(filterMonth.value.toLowerCase());
    
    return (
      ///user.id !== props.authUser.id && // excluir usuario logueado
      matchesTitle &&
      matchesYear &&
      matchesMonth
    );
  });
});


const deleteBook = (bookId: any) => {
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

const abrirModalNuevoLibro = () => {
   isModalNuevoLibroOpen.value = 'abrir';
};

const editBook = (book: any) => {
    bookSelected.value = book;
    isModalNuevoLibroOpen.value = 'abrir';
};
const viewBook = (book: any) => {
    bookSelected.value = book;
    isModalVerLibroOpen.value = 'abrir';
};

</script>

<template>
    <Head title="Gestión de Libros" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <CollapsibleCard title="Filtro" :collapsible="true">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Filtro por nombre -->
                    <div>
                        <label for="filter-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Título
                        </label>
                        <input
                            id="filter-title"
                            type="text"
                            v-model="filterTitle"
                            placeholder="Buscar por Titulo..."
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-2"
                        />
                    </div>
                    <!-- Filtro por mes -->
                    <div>
                        <label for="filter-month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Mes
                        </label>
                        <input
                            id="filter-month"
                            type="text"
                            v-model="filterMonth"
                            placeholder="Buscar por mes..."
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-2"
                        />
                    </div>

                    <!-- Filtro por año -->
                    <div>
                        <label for="filter-year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Año
                        </label>
                        <input
                            id="filter-year"
                            type="text"
                            v-model="filterYear"
                            placeholder="Buscar por Año..."
                            inputmode="numeric"
                            pattern="[0-9]*"
                            @input="filterYear = filterYear.replace(/\D/g, '')"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-2"
                        />
                    </div>
                </div>
            </CollapsibleCard>
            <div class="block text-sm font-medium text-white-700 dark:text-white-300">
                <PrimaryButton @click="abrirModalNuevoLibro()">
                    Crear Nuevo Libro
                </PrimaryButton>
            </div>
            <CollapsibleCard title="Listado de Libros" :collapsible="false" class="flex-1 mt-1">
                <div
                    class="rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
                >
                    <table class="min-w-full border border-red-700 rounded-lg overflow-hidden">
                        <thead class="bg-red-500 dark:bg-red-500 shadow-md border border-red-500 dark:border-red-500">
                            <tr class="text-left text-sm font-semibold text-white dark:text-white border-b">
                            <th class="px-4 py-2">Titulo</th>
                            <th class="px-4 py-2">Mes/Año</th>
                            <th class="px-4 py-2">ISBN</th>
                            <th class="px-4 py-2">Disciplina</th>
                            <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in filteredBooks" :key="item.id" class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 truncate">{{ item.title }}</td>
                                <td class="px-4 py-2 truncate">{{ item.month }}/{{ item.year }}</td>
                                <td class="px-4 py-2 truncate"> {{ item.isbn }} </td>
                                <td class="px-4 py-2 truncate">{{ item.discipline }}</td>
                                <td class="px-4 py-2">
                                    <!-- Botón Eliminar -->
                                    <button
                                    type="button"
                                    title="Eliminar"
                                    @click="deleteBook(item.id)"
                                    class="p-1"
                                    >
                                    <component :is="AllIcons['AkTrashCan']" class="text-red-500 w-5 h-5" />
                                    </button>
                                    <!-- Botón Editar -->
                                    <button
                                    type="button"
                                    title="Editar"
                                    @click="editBook(item)"
                                    class="pl-2 p-1"
                                    >
                                    <component :is="AllIcons['BsPencilSquare']" class="text-blue-500 w-5 h-5" />
                                    </button>
                                    <!-- Botón ver -->
                                    <button
                                    type="button"
                                    title="Ver Info"
                                    @click="viewBook(item)"
                                    class="pl-2 p-1"
                                    >
                                    <component :is="AllIcons['CaDataViewAlt']" class="text-green-500 w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredBooks.length === 0">
                                <td colspan="7" class="text-center bg-red-100 dark:bg-red-100 text-red-600 p-4">
                                    No se encontraron Libros.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <Pagination :links="props.books.links" />
                </div>
            </CollapsibleCard>
            <!-- Modal de gestión de permisos de roles -->
            <CreateBooksModal
            :show="isModalNuevoLibroOpen === 'abrir'"
            :book-selected="bookSelected"
            :max-width="'80'"
            @close="() => { 
                isModalNuevoLibroOpen = 'cerrar'; 
                bookSelected = null 
            }"
            />
            <ViewBooksModal
            :show="isModalVerLibroOpen === 'abrir'"
            :book-selected="bookSelected"
            :max-width="'80'"
            @close="() => { 
                isModalVerLibroOpen = 'cerrar'; 
                bookSelected = null 
            }"
            />
        </div>
    </AppLayout>
</template>
