<script setup>
import { ref, watch } from 'vue';
import { useForm} from '@inertiajs/vue3'
import Modal from '@/components/Modal.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import FormSection from '@/components/FormSection.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import CollapsibleCard from '@/components/CollapsibleCard.vue';


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


</script>

<template>
    
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
      <FormSection class="max-h-[70vh] overflow-y-auto pr-2">
        <template #title>
            Ver información del libro
        </template>
        <template #description>
            A continuacion vera toda la informacion del libro: {{ form.title }}
        </template>
        <!-- Campos del formulario -->
        <template #form>
          <CollapsibleCard title="Información Básica del Libro" :collapsible="true" class="flex-1">
            <div class="grid grid-cols-4 items-left text-left gap-6 w-full text-black dark:text-white">
              <Label for="title" class="col-span-2">Titulo</Label>
              <Label for="month">Mes</Label>
              <Label for="year">Año</Label>
              <Input id="title" class="col-span-2" disabled v-model="form.title" required autocomplete="title" placeholder="Titulo del libro" />
              <Input id="month" disabled v-model="form.month" required autocomplete="month" placeholder="Mes" />
              <Input id="year" disabled v-model="form.year" required autocomplete="year" placeholder="Año"/>

            </div>
            <div class="pt-6 grid grid-cols-4 items-left text-left gap-6 w-full text-black dark:text-white">
                <Label for="dissemination_medium" class="col-span-2">Medio de difusión</Label>
                <Label for="start_page">Página de Inicio</Label>
                <Label for="end_page">Página Final</Label>
                <Input id="dissemination_medium" class="col-span-2" disabled v-model="form.dissemination_medium" required autocomplete="dissemination_medium" placeholder="Medio de difusión"/>
                <Input id="start_page" disabled v-model="form.start_page" required autocomplete="start_page" placeholder="Página de Inicio"/>
                <Input id="end_page" disabled v-model="form.end_page" required autocomplete="end_page" placeholder="Página Final"/>
            </div>
            <div class="pt-6 grid grid-cols-3 items-left text-left gap-6 w-full text-black dark:text-white">
                <Label for="total_pages">Número de Páginas</Label>
                <Label for="book_series">Serie</Label>
                <Label for="edition">Edición</Label>
                <Input id="total_pages" disabled v-model="form.total_pages" required autocomplete="total_pages" placeholder="Número de Páginas" />
                <Input id="book_series" disabled v-model="form.book_series" required autocomplete="book_series" placeholder="Serie del Libro"/>
                <Input id="edition" disabled v-model="form.edition" required autocomplete="edition" placeholder="Edición del libro" />
                </div>
            <div class="grid grid-cols-3  pt-6 items-left text-left gap-6 w-full text-black dark:text-white">
                <Label for="isbn">ISBN</Label><br>
                <Label for="discipline">Disciplina</Label>
                <Input id="isbn" class="col-span-2" disabled v-model="isbn" required autocomplete="isbn" placeholder="978-3-16-148410-0" />
                <Input id="discipline" disabled v-model="form.discipline" required autocomplete="discipline" placeholder="Disciplina" />
                </div>
            <div class="pt-6 grid grid-cols-3 items-left text-left gap-6 w-full text-black dark:text-white">
                <Label for="publication_place">Lugar de Publicación</Label>
                <Label for="publisher">Editorial</Label>
                <Label for="publisher_type">Tipo de Editorial</Label>
                <Input id="publication_place" disabled v-model="form.publication_place" required autocomplete="publication_place" placeholder="Lugar de Publicación" />
                <Input id="publisher" disabled v-model="form.publisher" required autocomplete="publisher" placeholder="Editorial" />
                <Input id="publisher_type" disabled v-model="form.publisher_type" required autocomplete="publisher_type" placeholder="Tipo de Editorial" />
                </div>
          </CollapsibleCard>
          <CollapsibleCard title="Archivos de Evidencia del Libro" :collapsible="true" class="flex-1 mt-4">
            <div class="pt-6 grid grid-cols-3 gap-6 text-black dark:text-white">
                <div>
                  <Label for="evidence_document">Documento de evidencia</Label>
                    <!-- Mostrar enlace al archivo ya guardado si no se selecciona uno nuevo -->
                    <div v-if="form.evidence_document_url && !form.evidence_document" class="text-left mt-2">
                        <a :href="`/storage/${form.evidence_document_url}`" target="_blank" class="text-blue-500 underline">
                        Ver archivo actual
                        </a>
                    </div>
                </div>

                <div>
                  <Label for="credits_certificate">Certificado de créditos</Label>
                    <div v-if="form.credits_certificate_url && !form.credits_certificate" class="text-left mt-2">
                        <a :href="`/storage/${form.credits_certificate_url}`" target="_blank" class="text-blue-500 underline">
                        Ver archivo actual
                        </a>
                    </div>
                </div>

                <div>
                  <Label for="institution_endorsement_certificate">Certificado de la Institución que avala</Label>
              
                  <div v-if="form.institution_endorsement_certificate_url && !form.institution_endorsement_certificate" class="text-left mt-2">
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
            <PrimaryButton @click="close" class="mr-2">
                Cerrar
            </PrimaryButton>
        </template>
    </FormSection>
  </Modal>
</template>
<style lang="scss">
</style>