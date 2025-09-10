<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { ref } from 'vue'
import PrimaryButton from '@/components/PrimaryButton.vue';
import Heading from '@/components/Heading.vue';
import AcademicFormationForm from '@/components/AcademicFormationForm.vue';
import { AkTrashCan, BsPencilSquare, CaDataViewAlt } from '@kalimahapps/vue-icons';
import SecondaryButton from '@/components/SecondaryButton.vue';
import WorkExperienceForm from '@/components/WorkExperienceForm.vue';
import axios from "axios";
import { useToast } from "vue-toastification"

const toast = useToast()
interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Ajustes de perfil',
        href: '/settings/profile',
    },
];

const page = usePage();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
    last_name: user.last_name,
    document_type: user.document_type,
    document_number: user.document_number,
    phone_number: user.phone_number,
    is_enable: user.is_enable,
    academic_formation: user.academic_formation ?? [],
    work_experience: user.work_experience ?? [],
    errors: {},
});

const submit1 = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
const submit = async () => {
    try {
        const response = await axios.patch(route("profile.update"), form);

        if (response.data.success) {
            toast.success(response.data.message || "Perfil actualizado correctamente", { position: "top-right" });
        } 
    } catch (error) {
        toast.error(error.response?.data?.message || "Error desconocido.", { position: "top-right" });
    }
};
const documentTypes = ref([
    { value: 'CC', text: 'Cédula de Ciudadanía' },
    { value: 'TI', text: 'Tarjeta de Identidad' },
    { value: 'CE', text: 'Cédula de Extranjería' },
    { value: 'PS', text: 'Pasaporte' },
])


const showNewAcademic = ref(false)
const showNewExperience = ref(false)
const editExperienceLab = ref(null)
const indexExperiencia = ref(null)
const editAcademicFormation = ref(null)
const indexAcademic = ref(null)

function addAcademic(academic: any) {
  form.academic_formation.push(academic)
  showNewAcademic.value = false
}

function updateAcademic(academic: any, index: number) {
  form.academic_formation[index] = academic
  showNewAcademic.value = false
  editAcademicFormation.value = null
  indexAcademic.value = null
}

function removeAcademic(index: number) {
  form.academic_formation.splice(index, 1)
}

function addExperience(experience: any) {
  form.work_experience.push(experience)
  showNewExperience.value = false
}

function updateExperience(experience: any, index: number) {
  form.work_experience[index] = experience
  showNewExperience.value = false
  editExperienceLab.value = null
  indexExperiencia.value = null
}

function removeExperience(index: number) {
  form.work_experience.splice(index, 1)
}

function editExperience(index: number) {
  editExperienceLab.value = form.work_experience[index]
  indexExperiencia.value = index
  showNewExperience.value = true
}

function editAcademic(index: number) {
  editAcademicFormation.value = form.academic_formation[index]
  indexAcademic.value = index
  showNewAcademic.value = true
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Ajustes de perfil" />

        <SettingsLayout>
            <div class="flex flex-col space-y-12 w-full">
                <Heading title="Información Personal" description="Aquí podrás actualizar tu información personal." />

                <form @submit.prevent="submit" class="w-full flex flex-col space-y-6">
                    <div class="grid grid-cols-2 items-center gap-4 w-full">
                        <Label for="name">Nombres</Label>
                        <Label for="last_name">Apellidos</Label>
                        <Input id="name" v-model="form.name" required autocomplete="name" placeholder="Name" />
                        <Input id="last_name" v-model="form.last_name" required autocomplete="last_name" placeholder="Last name" />
                        <InputError class="col-span-2 text-red-500" :message="form.errors.name" />
                        <InputError class="col-span-2 text-red-500" :message="form.errors.last_name" />
                    </div>


                    <div class="grid grid-cols-2 items-center gap-4">
                        <Label for="document_type">Tipo de Documento</Label>
                        <Label for="document_number">Número de Documento</Label>
                        <select
                            id="document-type"
                            required autofocus :tabindex="1"
                            v-model="form.document_type"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-2"
                        >
                            <option
                            v-for="type in documentTypes"
                            :key="type.value"
                            :value="type.value"
                            >
                            {{ type.text }}
                            </option>
                        </select>
                        <Input id="document_number" class="mt-1 block w-full" v-model="form.document_number" required autocomplete="document number" placeholder="Document number" />
                        <InputError class="mt-2" :message="form.errors.document_type" />
                        <InputError class="mt-2" :message="form.errors.document_number" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>
                    <Heading title="Formación Académica" class="mt-4" description="A continuación registra tu formacion académica." />
                    <!-- Tabla de estudios -->
                    <div v-if="form.academic_formation.length" 
                        class="rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
                    >
                        <table class="min-w-full border border-red-700 rounded-lg overflow-hidden">
                            <thead class="bg-red-500 dark:bg-red-500 shadow-md border border-red-500 dark:border-red-500">
                                <tr class="text-left text-sm font-semibold text-white dark:text-white border-b">
                                    <th class="border p-2 text-left">Institución</th>
                                    <th class="border p-2 text-left">Título / Grado</th>
                                    <th class="border p-2 text-center">Año Inicio</th>
                                    <th class="border p-2 text-center">Año Fin</th>
                                    <th class="border p-2 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(academic, index) in form.academic_formation" :key="index">
                                <td class="border p-2">{{ academic.institution }}</td>
                                <td class="border p-2">{{ academic.degree }}</td>
                                <td class="border p-2 text-center">{{ academic.startYear }}</td>
                                <td class="border p-2 text-center">{{ academic.endYear }}</td>
                                <td class="border p-2 text-center">
                                    <button
                                    type="button"
                                    title="Eliminar"
                                    @click="removeAcademic(index)"
                                    class="p-1"
                                    >
                                    <AkTrashCan class="text-red-500 w-5 h-5"/>
                                    </button>
                                    <button
                                    type="button"
                                    title="Editar"
                                    @click="editAcademic(index)"
                                    class="pl-2 p-1"
                                    >
                                    <BsPencilSquare class="text-blue-500 w-5 h-5"/>
                                    </button>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Botón "Nuevo Estudio" -->
                    <div v-if="!showNewAcademic" class="mt-2">
                        <SecondaryButton :type="'button'" @click="showNewAcademic = true">
                            + Nuevo Registro
                        </SecondaryButton>
                    </div>

                    <!-- Formulario temporal como componente -->
                    <AcademicFormationForm
                    v-else
                    :academic-edit="editAcademicFormation"
                    :index="indexAcademic"
                    @save="addAcademic"
                    @update="updateAcademic($event, indexAcademic)"
                    @cancel="showNewAcademic = false, editAcademicFormation = null, indexAcademic = null"
                    />

                    <Heading title="Experiencia Laboral" class="mt-4" description="A continuación registra toda tu experiencia laboral." />
                    <!-- Tabla de estudios -->
                    <div v-if="form.work_experience.length"
                        class="rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border" >
                        <table class="min-w-full border border-red-700 rounded-lg overflow-hidden">
                            <thead class="bg-red-500 dark:bg-red-500 shadow-md border border-red-500 dark:border-red-500">
                                <tr class="text-left text-sm font-semibold text-white dark:text-white border-b">
                                    <th class="border p-2 text-left">Empresa</th>
                                    <th class="border p-2 text-left">Cargo / Puesto</th>
                                    <th class="border p-2 text-center">Año Inicio</th>
                                    <th class="border p-2 text-center">Año Fin</th>
                                    <th class="border p-2 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(experience, index) in form.work_experience" :key="index">
                                <td class="border p-2">{{ experience.company }}</td>
                                <td class="border p-2">{{ experience.position }}</td>
                                <td class="border p-2 text-center">{{ experience.startYear }}</td>
                                <td class="border p-2 text-center">{{ experience.endYear }}</td>
                                <td class="border p-2 text-center">
                                    <!-- Botón Eliminar -->
                                    <button
                                    type="button"
                                    title="Eliminar"
                                    @click="removeExperience(index)"
                                    class="p-1"
                                    >
                                    <AkTrashCan class="text-red-500 w-5 h-5"/>
                                    </button>
                                    <!-- Botón Editar -->
                                    <button
                                    type="button"
                                    title="Editar"
                                    @click="editExperience(index)"
                                    class="pl-2 p-1"
                                    >
                                    <BsPencilSquare class="text-blue-500 w-5 h-5"/>
                                    </button>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Botón "Nuevo Estudio" -->
                    <div v-if="!showNewExperience" class="mt-2">
                        <SecondaryButton :type="'button'" @click="showNewExperience = true" >
                            + Nuevo Registro
                        </SecondaryButton>
                    </div>

                    <!-- Formulario temporal como componente -->
                    <WorkExperienceForm
                    v-else
                    :experience-edit="editExperienceLab"
                    :index="indexExperiencia"
                    @save="addExperience"
                    @update="updateExperience($event, indexExperiencia)"
                    @cancel="showNewExperience = false, editExperienceLab = null, indexExperiencia = null"
                    />

                    <div class="flex items-center gap-4 mt-3">
                        <PrimaryButton :disabled="form.processing">Guardar</PrimaryButton>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Información guardada correctamente.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
