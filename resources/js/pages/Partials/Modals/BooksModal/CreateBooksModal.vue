<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, router} from '@inertiajs/vue3'
import axios from 'axios'
import { useToast } from "vue-toastification"
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import FormSection from '@/components/FormSection.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import CollapsibleCard from '@/components/CollapsibleCard.vue';
import FileUpload from '@/components/FileUpload.vue';

const toast = useToast()

const props = defineProps({
    show: {
    type: String,
    default: 'cerrar'
    },
    maxWidth: {
        type: String,
        default: 'x1'
    },
    closeable: {
        type: Boolean,
        default: true
    },
    bookSelected: {
        type: Object,
        default: null
    }
});

// Ref para el valor del input del ISBN
let isbn = ref('');

const form = useForm({
    title: '',
    year: '',
    month:'', 
    dissemination_medium: '',
    start_page: '',
    end_page: '',
    total_pages: '',
    book_series: '',
    edition: '',
    isbn: '',
    publication_place: '',
    publisher: '',
    publisher_type: '',
    discipline: '',
    evidence_document: null,
    credits_certificate: null,
    institution_endorsement_certificate: null,
    
    // Archivos ya guardados (urls)
    evidence_document_url: null,
    credits_certificate_url: null,
    institution_endorsement_certificate_url: null,
});

function resetForm() {
  form.reset(); // limpia todos los campos de useForm
  isbn.value = '';
  editingId.value = '';
}

const editingId = ref('');

const emit = defineEmits(['update:show', 'close']);

function close() {
  emit('update:show', false);
  emit('close');
}

// sincronizar el checkbox maestro según los hijos
watch(props.bookSelected, (editingId) => {
  if (props.bookSelected !== null) {
    editingId = bookSelected.id
  }
});

// Observa cuando se abre o cierra el modal
watch(
  () => props.show,
  (newVal) => {
    if (!newVal) {
      // cuando el modal se cierra
      resetForm();
    } else if (props.bookSelected) {
      // cuando se abre y hay libro para editar
      editingId.value = props.bookSelected.id;
      form.title = props.bookSelected.title;
      form.year = props.bookSelected.year;
      form.month = props.bookSelected.month;
      form.dissemination_medium = props.bookSelected.dissemination_medium;
      form.start_page = props.bookSelected.start_page;
      form.end_page = props.bookSelected.end_page;
      form.total_pages = props.bookSelected.total_pages;
      form.book_series = props.bookSelected.book_series;
      form.edition = props.bookSelected.edition;
      isbn.value = props.bookSelected.isbn;
      form.publication_place = props.bookSelected.publication_place;
      form.publisher = props.bookSelected.publisher;
      form.publisher_type = props.bookSelected.publisher_type;
      form.discipline = props.bookSelected.discipline;
      
      // solo guardamos rutas (no archivos)
      form.evidence_document_url = props.bookSelected.evidence_document;
      form.credits_certificate_url = props.bookSelected.credits_certificate;
      form.institution_endorsement_certificate_url = props.bookSelected.institution_endorsement_certificate;
    }
  }
);


// Ref para el mensaje de error de validación
const isbnError = ref('');

/**
 * Valida un ISBN-10.
 * @param {string} isbnStr - El string del ISBN a validar.
 * @returns {boolean}
 */
function isValidIsbn10(isbnStr) {
    if (!/^\d{9}[\dX]$/i.test(isbnStr)) {
        return false;
    }

    let checksum = 0;
    for (let i = 0; i < 9; i++) {
        checksum += parseInt(isbnStr[i]) * (10 - i);
    }

    const lastChar = isbnStr[9].toUpperCase();
    checksum += (lastChar === 'X') ? 10 : parseInt(lastChar);

    return checksum % 11 === 0;
}

/**
 * Valida un ISBN-13.
 * @param {string} isbnStr - El string del ISBN a validar.
 * @returns {boolean}
 */
function isValidIsbn13(isbnStr) {
    if (!/^\d{13}$/.test(isbnStr)) {
        return false;
    }

    let checksum = 0;
    for (let i = 0; i < 12; i++) {
        checksum += parseInt(isbnStr[i]) * (i % 2 === 0 ? 1 : 3);
    }

    const checkDigit = (10 - (checksum % 10)) % 10;

    return checkDigit === parseInt(isbnStr[12]);
}

// Observador que se ejecuta cada vez que el valor de 'isbn' cambia.
watch(isbn, (newValue) => {
    if (!newValue) {
        isbnError.value = '';
        return;
    }

    // Limpiamos el ISBN de guiones y espacios para la validación
    const cleanedIsbn = newValue.replace(/[-\s]/g, '');
    const length = cleanedIsbn.length;

    // Solo validamos cuando la longitud es de 10 o 13 caracteres
    if (length === 10) {
        if (!isValidIsbn10(cleanedIsbn)) {
            isbnError.value = 'El ISBN-10 no es válido.';
        } else {
            isbnError.value = ''; // Es válido, limpiamos el error
        }
    } else if (length === 13) {
        if (!isValidIsbn13(cleanedIsbn)) {
            isbnError.value = 'El ISBN-13 no es válido.';
        } else {
            isbnError.value = ''; // Es válido, limpiamos el error
        }
    } else if (length > 0) {
        // Mensaje intermedio mientras el usuario escribe
        isbnError.value = 'El ISBN debe tener 10 o 13 dígitos.';
    } else {
        isbnError.value = '';
    }
});

async function guardarLibro() {
  const fd = new FormData();
  Object.entries({
    title: form.title,
    year: form.year,
    month: form.month,
    dissemination_medium: form.dissemination_medium,
    start_page: form.start_page,
    end_page: form.end_page,
    total_pages: form.total_pages,
    book_series: form.book_series,
    edition: form.edition,
    isbn: isbn.value,
    publication_place: form.publication_place,
    publisher: form.publisher,
    publisher_type: form.publisher_type,
    discipline: form.discipline,
  }).forEach(([k, v]) => fd.append(k, v ?? ''));

  if (form.evidence_document) fd.append('evidence_document', form.evidence_document);
  if (form.credits_certificate) fd.append('credits_certificate', form.credits_certificate);
  if (form.institution_endorsement_certificate) {
    fd.append('institution_endorsement_certificate', form.institution_endorsement_certificate);
  }

  try {
    if (editingId.value) {
      // Actualizar
      fd.append('_method', 'PUT'); // spoofing para Laravel
      const response = await axios.post(`/books/${editingId.value}`, fd, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      // mostrar mensaje del backend o fallback
      toast.success(
        response.data?.message || "Libro actualizado correctamente",
        { position: "top-right" }
      );
    } else {
      // Crear
      const response = await axios.post('/books', fd, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      toast.success(
        response.data?.message || "Libro creado correctamente",
        { position: "top-right" }
      );
    }

    router.reload({ only: ['books'] });
    form.reset();
    editingId.value = null;
    close();
  } catch (error) {
    if (error.response?.data?.errors) {
      // errores de validación (422)
      const messages = Object.values(error.response.data.errors).flat();
      messages.forEach(msg =>
        toast.error(msg, { position: "top-right" })
      );
    } else {
      // mensaje genérico o mensaje que mande backend en error
      toast.error(
        error.response?.data?.error || "Error el libro",
        { position: "top-right" }
      );
    }
  } 
}

// Computada para saber si hay algún valor lleno
const hasContent = computed(() => {
  return Object.values(form.data()).some(v => {
    if (v == null) return false
    if (typeof v === 'string') return v.trim() !== ''
    if (v instanceof FileList) return v.length > 0
    return true
  })
})
</script>

<template>
    
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
      <FormSection @submitted="guardarLibro" class="max-h-[70vh] overflow-y-auto pr-2">
        <template #title>
            {{ editingId ? 'Editar Libro' : 'Crear Libro' }}
        </template>
        <template #description>
            A continuacion ingrese la informacion necesaria para  {{ editingId ? 'editar el libro.' : 'crear el libro.' }}
        </template>
        <!-- Campos del formulario -->
        <template #form>
          <CollapsibleCard title="Información Básica del Libro" :collapsible="true" class="flex-1">
            <div class="grid grid-cols-4 items-left text-left gap-6 w-full text-black dark:text-white">
              <Label for="title" class="col-span-2">Titulo</Label>
              <Label for="month">Mes</Label>
              <Label for="year">Año</Label>
              <Input id="title" class="col-span-2" v-model="form.title" required autocomplete="title" placeholder="Titulo del libro" />
              <Input id="month" v-model="form.month" required autocomplete="month" placeholder="Mes" />
              <Input id="year" v-model="form.year" required autocomplete="year" placeholder="Año"/>
              <InputError class="text-red-500" :message="form.errors.title" />
              <InputError class=" text-red-500" :message="form.errors.year" />
            </div>
            <div class="pt-6 grid grid-cols-4 items-left text-left gap-6 w-full text-black dark:text-white">
                <Label for="dissemination_medium" class="col-span-2">Medio de difusión</Label>
                <Label for="start_page">Página de Inicio</Label>
                <Label for="end_page">Página Final</Label>
                <Input id="dissemination_medium" class="col-span-2" v-model="form.dissemination_medium" required autocomplete="dissemination_medium" placeholder="Medio de difusión"/>
                <Input id="start_page" v-model="form.start_page" required autocomplete="start_page" placeholder="Página de Inicio"/>
                <Input id="end_page" v-model="form.end_page" required autocomplete="end_page" placeholder="Página Final"/>
                <InputError class="col-span-2 text-red-500" :message="form.errors.dissemination_medium" />
                <InputError class="col-span-2 text-red-500" :message="form.errors.start_page" />
                <InputError class="col-span-2 text-red-500" :message="form.errors.end_page" />
            </div>
            <div class="pt-6 grid grid-cols-3 items-left text-left gap-6 w-full text-black dark:text-white">
                <Label for="total_pages">Número de Páginas</Label>
                <Label for="book_series">Serie</Label>
                <Label for="edition">Edición</Label>
                <Input id="total_pages" v-model="form.total_pages" required autocomplete="total_pages" placeholder="Número de Páginas" />
                <Input id="book_series" v-model="form.book_series" required autocomplete="book_series" placeholder="Serie del Libro"/>
                <Input id="edition" v-model="form.edition" required autocomplete="edition" placeholder="Edición del libro" />
                <InputError class="col-span-2 text-red-500" :message="form.errors.total_pages" />
                <InputError class="col-span-2 text-red-500" :message="form.errors.book_series" />
                <InputError class="col-span-2 text-red-500" :message="form.errors.edition" />
            </div>
            <div class="grid grid-cols-3  pt-6 items-left text-left gap-6 w-full text-black dark:text-white">
                <Label for="isbn">ISBN</Label><br>
                <Label for="discipline">Disciplina</Label>
                <Input id="isbn" class="col-span-2" v-model="isbn" required autocomplete="isbn" placeholder="978-3-16-148410-0" />
                <Input id="discipline" v-model="form.discipline" required autocomplete="discipline" placeholder="Disciplina" />
                <p v-if="isbnError" class="text-sm text-red-600 mt-2">
                    {{ isbnError }}
                </p>
                <InputError class="col-span-1 text-red-500" :message="isbnError.value" />
            </div>
            <div class="pt-6 grid grid-cols-3 items-left text-left gap-6 w-full text-black dark:text-white">
                <Label for="publication_place">Lugar de Publicación</Label>
                <Label for="publisher">Editorial</Label>
                <Label for="publisher_type">Tipo de Editorial</Label>
                <Input id="publication_place" v-model="form.publication_place" required autocomplete="publication_place" placeholder="Lugar de Publicación" />
                <Input id="publisher" v-model="form.publisher" required autocomplete="publisher" placeholder="Editorial" />
                <Input id="publisher_type" v-model="form.publisher_type" required autocomplete="publisher_type" placeholder="Tipo de Editorial" />
                <InputError class="col-span-2 text-red-500" :message="form.errors.publication_place" />
                <InputError class="col-span-2 text-red-500" :message="form.errors.publisher" />
                <InputError class="col-span-2 text-red-500" :message="form.errors.publisher_type" />
            </div>
          </CollapsibleCard>
          <CollapsibleCard title="Archivos de Evidencia del Libro" :collapsible="true" class="flex-1 mt-4">
            <div class="pt-6 grid grid-cols-3 gap-6 text-black dark:text-white">
                <div>
                <Label for="evidence_document">Documento de evidencia</Label>
                <FileUpload
                    id="evidence_document"
                    v-model="form.evidence_document"
                    accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                    placeholder="Subir evidencia"
                    class="pt-2"
                />
                    <!-- Mostrar enlace al archivo ya guardado si no se selecciona uno nuevo -->
                    <div v-if="form.evidence_document_url && !form.evidence_document" class="mt-2">
                        <a :href="`/storage/${form.evidence_document_url}`" target="_blank" class="text-blue-500 underline">
                        Ver archivo actual
                        </a>
                    </div>
                </div>

                <div>
                <Label for="credits_certificate">Certificado de créditos</Label>
                <FileUpload
                    id="credits_certificate"
                    v-model="form.credits_certificate"
                    accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                    placeholder="Subir certificado de créditos"
                    class="pt-2"
                />
                    <div v-if="form.credits_certificate_url && !form.credits_certificate" class="mt-2">
                        <a :href="`/storage/${form.credits_certificate_url}`" target="_blank" class="text-blue-500 underline">
                        Ver archivo actual
                        </a>
                    </div>
                </div>

                <div>
                <Label for="institution_endorsement_certificate">Certificado de la Institución que avala</Label>
                <FileUpload
                    id="institution_endorsement_certificate"
                    v-model="form.institution_endorsement_certificate"
                    accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                    placeholder="Subir aval de institución"
                    class="pt-2"
                />
                    <div v-if="form.institution_endorsement_certificate_url && !form.institution_endorsement_certificate" class="mt-2">
                        <a :href="`/storage/${form.institution_endorsement_certificate_url}`" target="_blank" class="text-blue-500 underline">
                        Ver archivo actual
                        </a>
                    </div>
                </div>
                </div>
            </CollapsibleCard>
        </template>
        <!-- Botones de acción -->
        <template #actions>
            <SecondaryButton v-if="hasContent" @click="resetForm" class="mr-2">
                Limpiar
            </SecondaryButton>
            <SecondaryButton @click="close" class="mr-2">
                cerrar
            </SecondaryButton>
            <PrimaryButton :disabled="form.processing">
                {{ editingId ? 'Actualizar' : 'Guardar' }}
            </PrimaryButton>
        </template>
    </FormSection>
  </Modal>
</template>
<style lang="scss">
</style>