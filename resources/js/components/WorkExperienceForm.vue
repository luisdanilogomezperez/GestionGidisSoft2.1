<!-- components/NewAcademicForm.vue -->
<script setup>
import { ref } from 'vue'
import Input from './ui/input/Input.vue'
import SecondaryButton from './SecondaryButton.vue'
import PrimaryButton from './PrimaryButton.vue'
import { useToast } from "vue-toastification"

const toast = useToast()
const props = defineProps({
  experienceEdit: Object,
  index: Number
})

const emit = defineEmits(['save', 'update', 'cancel'])

const experience = ref({
  company: '',
  position: '',
  startYear: '',
  endYear: ''
})

if (props.experienceEdit) {
  experience.value = { ...props.experienceEdit }
}

const companyInput = ref(null)
const positionInput = ref(null)
const startYearInput = ref(null)
const endYearInput = ref(null)

function save() {
  if (!experience.value.company) {
    companyInput.value.focus()
    return
  }
  if (!experience.value.position) {
    positionInput.value.focus()
    return
  }
  if (!experience.value.startYear) {
    startYearInput.value.focus()
    return
  }
  if (!experience.value.endYear) {
    endYearInput.value.focus()
    return
  }
  if (experience.value.startYear > experience.value.endYear) {
    startYearInput.value.focus()
     toast.error("El año de inicio no puede ser mayor al año de fin.", { position: "top-right" });
    return
  }
  if (props.experienceEdit) {
    emit('update', { ...experience.value, index: props.index })
  } else {
    emit('save', { ...experience.value })
  }
  experience.value = { company: '', position: '', startYear: '', endYear: '' }
  companyInput.value.focus()
}

function cancel() {
  experience.value = { company: '', position: '', startYear: '', endYear: '' }
  props.experienceEdit = null
  props.index = null
  emit('cancel')
}
</script>

<template>
  <div class="grid grid-cols-4 items-center gap-4 border border-gray-700 p-3 rounded space-y-2 bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    <Label for="name">Empresa</Label>
    <Label for="last_name">Cargo / Puesto</Label>
    <Label for="last_name">Año Inicio</Label>
    <Label for="last_name">Año Fin</Label>
    <input 
      ref="companyInput"
      v-model="experience.company" 
      placeholder="Empresa" 
      class="border border-gray-500 p-1 rounded w-full mb-2"
    />

    <input 
      ref="positionInput"
      v-model="experience.position" 
      placeholder="Cargo / Puesto" 
      class="border border-gray-500 p-1 rounded w-full mb-2"
    />

    <input 
      ref="startYearInput"
      v-model="experience.startYear" 
      type="number" 
      placeholder="Año Inicio" 
      class="border border-gray-500 p-1 rounded w-full mb-2"
    />

    <input 
      ref="endYearInput"
      v-model="experience.endYear" 
      type="number" 
      placeholder="Año Fin" 
      class="border border-gray-500 p-1 rounded w-full mb-2"
    />
    <div class="flex gap-2 mt-2">
      <PrimaryButton :type="'button'" @click="save" >
        {{ props.experienceEdit ? 'Actualizar' : 'Agregar' }}
      </PrimaryButton>
      <SecondaryButton type="button" @click="cancel" >
        Cancelar
      </SecondaryButton>
    </div>
  </div>
  
</template>
