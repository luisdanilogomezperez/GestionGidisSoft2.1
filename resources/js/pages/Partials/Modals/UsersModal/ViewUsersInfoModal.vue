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
    userSelected: {
        type: Object,
        default: null
    },
    origin: {
        type: Object
    },
});

// Ref para el valor del input del ISBN
let isbn = ref('');

const form = useForm({
    name: '',
    last_name: '',
    email: '',
    phone_number: '',
    document_type: '',
    document_number: '',
    is_enable: false,

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
watch(props.userSelected, (editingId) => {
  if (props.userSelected !== null) {
    editingId = userSelected.id
  }
});

// Observa cuando se abre o cierra el modal
watch(
  () => props.show,
  (newVal) => {
    if (!newVal) {
      // cuando el modal se cierra
      resetForm();
    } else if (props.userSelected) {
      // cuando se abre y hay libro para editar
      editingId.value = props.userSelected.id;
      form.name = props.userSelected.name;
      form.last_name = props.userSelected.last_name;
      form.email = props.userSelected.email;
      form.phone_number = props.userSelected.phone_number;
      form.document_type = props.userSelected.document_type;
      form.document_number = props.userSelected.document_number;
      form.is_enable = props.userSelected.is_enable;
    }
  }
);

const documentTypes = ref([
    { value: 'CC', text: 'Cédula de Ciudadanía' },
    { value: 'TI', text: 'Tarjeta de Identidad' },
    { value: 'CE', text: 'Cédula de Extranjería' },
    { value: 'PS', text: 'Pasaporte' },
])

</script>

<template>
    
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
      <FormSection class="max-h-[70vh] overflow-y-auto pr-2">
        <template #title>
            Ver información de Usuarios
        </template>
        <template #description>
            A continuación vera toda la informacion del usuario: {{ form.name }} {{ form.last_name }}
        </template>

        <!-- Campos del formulario -->
        <template #form>
          <div class="block text-sm text-right font-medium text-white-700 dark:text-white-300 m-2">
              <PrimaryButton @click="abrirModalNuevoLibro()">
                  Ver producciones
              </PrimaryButton>
          </div>
          <CollapsibleCard title="Información Personal" :collapsible="true" class="flex-1">
            <div class="grid grid-cols-4 items-left text-left gap-6 w-full text-black dark:text-white">
              <Label for="nombre" class="col-span-2">Nombre</Label>
              <Label for="last_name">Apellido</Label>
              <Input id="title" class="col-span-2" disabled v-model="form.name" required autocomplete="name" />
              <Input id="last_name" class="col-span-2" disabled v-model="form.last_name"  />
            </div>
            <div class="pt-6 grid grid-cols-4 items-left text-left gap-6 w-full text-black dark:text-white">
                <Label for="document_type" class="col-span-2">Tipo de Documento</Label>
                <Label for="document_number">Número de Documento</Label>
                <select
                    id="document-type" 
                    required autofocus :tabindex="1"
                    v-model="form.document_type" disabled
                    class="col-span-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-2"
                >
                    <option
                    v-for="type in documentTypes"
                    :key="type.value"
                    :value="type.value"
                    >
                    {{ type.text }}
                    </option>
                </select>
                <Input id="document_number" class="col-span-2" disabled v-model="form.document_number" required autocomplete="document_number" />
            </div>
          </CollapsibleCard>
          <CollapsibleCard title="Información de Contacto" :collapsible="true" class="flex-1 mt-4">
            <div class="grid grid-cols-4 items-left text-left gap-6 w-full text-black dark:text-white">
              <Label for="email" class="col-span-2">Email</Label>
              <Label for="phone_number">Número de Celular</Label>
              <Input id="email" class="col-span-2" disabled v-model="form.email" required autocomplete="name" />
              <Input id="phone_number" class="col-span-2" disabled v-model="form.phone_number"  />
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