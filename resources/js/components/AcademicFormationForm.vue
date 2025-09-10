<!-- components/NewAcademicForm.vue -->
<script setup>
import { ref } from 'vue'
import SecondaryButton from './SecondaryButton.vue'
import PrimaryButton from './PrimaryButton.vue'
import { useToast } from "vue-toastification"

const toast = useToast()
const emit = defineEmits(['save', 'update', 'cancel'])

const props = defineProps({
  academicEdit: Object,
  index: Number
})

const academic = ref({
  institution: '',
  degree: '',
  startYear: '',
  endYear: ''
})

if (props.academicEdit) {
  academic.value = { ...props.academicEdit }
}

const institutionInput = ref(null)
const degreeInput = ref(null)
const startYearInput = ref(null)
const endYearInput = ref(null)

function save() {
  if (!academic.value.institution) {
    institutionInput.value.focus()
    return
  }
  if (!academic.value.degree) {
    degreeInput.value.focus()
    return
  }
  if (!academic.value.startYear) {
    startYearInput.value.focus()
    return
  }
  if (!academic.value.endYear) {
    endYearInput.value.focus()
    return
  }
  if (academic.value.startYear > academic.value.endYear) {
    startYearInput.value.focus()
    toast.error("El año de inicio no puede ser mayor al año de fin.", { position: "top-right" });
    return
  }
  if (props.academicEdit) {
    emit('update', { ...academic.value, index:  props.index })
  } else {
    emit('save', { ...academic.value })
  }
  academic.value = { institution: '', degree: '', startYear: '', endYear: '' }
  institutionInput.value.focus()
}

function cancel() {
  academic.value = { institution: '', degree: '', startYear: '', endYear: ''  }
  props.academicEdit = null
  props.index = null
  emit('cancel')
}
</script>

<template>
  <div class="grid grid-cols-4 items-center gap-4 border border-gray-700 p-3 rounded space-y-2 bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    <Label for="name">Institución</Label>
    <Label for="last_name">Titulo / Grado</Label>
    <Label for="last_name">Año Inicio</Label>
    <Label for="last_name">Año Fin</Label>
    <input 
      ref="institutionInput"
      v-model="academic.institution" 
      placeholder="Institución" 
      class="border border-gray-500 p-1 rounded w-full mb-2"
    />

    <input 
      ref="degreeInput"
      v-model="academic.degree" 
      placeholder="Título / Grado" 
      class="border border-gray-500 p-1 rounded w-full mb-2"
    />

    <input 
      ref="startYearInput"
      v-model="academic.startYear" 
      type="number" 
      placeholder="Año Inicio" 
      class="border border-gray-500 p-1 rounded w-full mb-2"
    />
    <input 
      ref="endYearInput"
      v-model="academic.endYear" 
      type="number" 
      placeholder="Año Fin" 
      class="border border-gray-500 p-1 rounded w-full mb-2"
    />
    <div class="flex gap-2 mt-2">
      <PrimaryButton :type="'button'" @click="save" >
        {{ props.academicEdit ? 'Actualizar' : 'Agregar' }}
      </PrimaryButton>
      <SecondaryButton type="button" @click="cancel" >
        Cancelar
      </SecondaryButton>
    </div>
  </div>
  
</template>
