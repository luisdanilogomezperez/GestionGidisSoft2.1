<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, router, usePage} from '@inertiajs/vue3'
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
import ImageUpload from '@/components/ImageUpload.vue';

const page = usePage();
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
    }
});
const logos = ref({
  logo_application: page.props.logos?.logo_application,
  logo_institution: page.props.logos?.logo_institution,
  logo_academic_program: page.props.logos?.logo_academic_program
})
const form = useForm({
  logo_application: null,
  logo_institution: null,
  logo_academic_program: null,
})
const emit = defineEmits(['update:show', 'close']);

function close() {
  emit('update:show', false);
  emit('close');
  resetForm();
}
// Vista previa de imágenes cargadas
const preview = ref({
  logo_application: logos.value.logo_application ? `/${logos.value.logo_application}` : null,
  logo_institution: logos.value.logo_institution ? `/${logos.value.logo_institution}` : null,
  logo_academic_program: logos.value.logo_academic_program ? `/${logos.value.logo_academic_program}` : null
})

function resetForm() {
  form.reset();
  preview.value = {
    logo_application: logos.value.logo_application ? `/${logos.value.logo_application}` : null,
    logo_institution: logos.value.logo_institution ? `/${logos.value.logo_institution}` : null,
    logo_academic_program: logos.value.logo_academic_program ? `/${logos.value.logo_academic_program}` : null
  }
}
function onFileChange(type, file) {
  form[type] = file
  if (file) {
    preview.value[type] = URL.createObjectURL(file)
  }
}
async function guardarLogos() {
  const fd = new FormData()
  if (form.logo_application) fd.append('logo_application', form.logo_application)
  if (form.logo_institution) fd.append('logo_institution', form.logo_institution)
  if (form.logo_academic_program) fd.append('logo_academic_program', form.logo_academic_program)

  try {
    await axios.post(route('settings.update-logos'), fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    toast.success("Logos actualizados correctamente")
    resetForm()
    router.reload({ only: ['logos'] }) // 🔥 recarga solo los logos
  } catch (e) {
    toast.error("Error al actualizar los logos")
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
      <FormSection @submitted="guardarLogos" >
        <template #title>
            Logos del Sistema
        </template>
        <template #description>
            A continuación podrá ver todos los logos del sistema.
        </template>
        <!-- Campos del formulario -->
        <template #form>
          <CollapsibleCard title="Logos del Sistema" :collapsible="false" class="flex-1">
              <div class="pt-6 grid grid-cols-3 gap-6 text-black dark:text-white">
                <div>
                  <Label for="logo_application">Logo Aplicación</Label>
                    <div class="mt-2 flex flex-col items-center justify-center w-full h-40 dark:bg-gray-600 border-2 border-dashed border-red-400 rounded-xl cursor-pointer bg-red-50 hover:bg-red-100 dark:hover:bg-red-200 transition">
                      <img v-if="preview.logo_application" :src="preview.logo_application" class="w-38 h-38" />
                    </div>
                    <ImageUpload
                    id="logo_application"
                    v-model="form.logo_application"
                    @change="file => onFileChange('logo_application', file)"
                    accept=".png,.jpg,.jpeg"
                    placeholder="Subir logo aplicación"
                    class="pt-1"
                  />
                </div>

                <div>
                  <Label for="logo_institution">Logo Institución</Label>
                  <div class="mt-2 flex flex-col items-center justify-center w-full h-40 dark:bg-gray-600 border-2 border-dashed border-red-400 rounded-xl cursor-pointer bg-red-50 hover:bg-red-100 dark:hover:bg-red-200 transition">
                    <img v-if="preview.logo_institution" :src="preview.logo_institution" class="w-38 h-38" />
                  </div>
                  <ImageUpload
                    id="logo_institution"
                    v-model="form.logo_institution"
                    @change="file => onFileChange('logo_institution', file)"
                    accept=".png,.jpg,.jpeg"
                    placeholder="Subir logo institución"
                    class="pt-1"
                  />
                </div>

                <div>
                  <Label for="logo_academic_program">Logo Programa Académico</Label>
                  <div class="mt-2 flex flex-col items-center justify-center w-full h-40 dark:bg-gray-600 border-2 border-dashed border-red-400 rounded-xl cursor-pointer bg-red-50 hover:bg-red-100 dark:hover:bg-red-200 transition">
                    <img v-if="preview.logo_academic_program" :src="preview.logo_academic_program" class="w-38 h-38" />
                  </div>
                  <ImageUpload
                    id="logo_academic_program"
                    v-model="form.logo_academic_program"
                    @change="file => onFileChange('logo_academic_program', file)"
                    accept=".png,.jpg,.jpeg"
                    placeholder="Subir logo programa académico"
                    class="pt-1"
                  />
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
            <PrimaryButton type="submit" :disabled="form.processing">
                Guardar
            </PrimaryButton>
        </template>
    </FormSection>
  </Modal>
</template>
<style lang="scss">
</style>