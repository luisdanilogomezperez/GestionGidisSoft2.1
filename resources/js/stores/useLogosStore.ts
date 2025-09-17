import { ref } from 'vue'

const logoApp = ref<string>('')
const logoInstitucion = ref<string>('')
const logoPrograma = ref<string>('')

export function useLogosStore() {
  return {
    logoApp,
    logoInstitucion,
    logoPrograma,
    setLogos({ app, institucion, programa }: { app: string, institucion: string, programa: string }) {
      logoApp.value = app
      logoInstitucion.value = institucion
      logoPrograma.value = programa
    }
  }
}
