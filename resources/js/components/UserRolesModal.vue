<script setup>
import Modal from './Modal.vue';
import { ref, computed } from 'vue';
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';

const props = defineProps({
    show: {
    type: Boolean,
    default: false
    },
    maxWidth: {
        type: String,
        default: '2xl'
    },
    closeable: {
        type: Boolean,
        default: true
    },
    userId: {
        type: [String, Number],
        default: null
    }
});

const emit = defineEmits(['close']);

const close = () => {
  emit('close');
};

const saveRoles = () => {
  console.log('Guardando roles para usuario:', props.userId);
  close();
};



</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="pop-up-inner">
        <div class="pop-up-close" @click="close">x</div>

        <h2 class="text-lg font-medium text-gray-900 mb-4">
            Gestionar Roles para Usuario ID: {{ userId }}
        </h2>
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            Gestionar Roles para Usuario ID: {{ userId }}
        </h2>
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            Gestionar Roles para Usuario ID: {{ userId }}
        </h2>
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            Gestionar Roles para Usuario ID: {{ userId }}
        </h2>
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            Gestionar Roles para Usuario ID: {{ userId }}
        </h2>
        <div class="flex justify-center space-x-4 mt-6">
            <SecondaryButton @click="close">
            Cancelar
            </SecondaryButton>
            <PrimaryButton @click="saveRoles">
            Guardar Cambios
            </PrimaryButton>
        </div>
        </div>
    </Modal>
</template>
<style lang="scss">
    .pop-up {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 10;
        padding: 32px 16px 120px;
        height: 100vh;
        width: 100%;
        background-color: rgba(87, 79, 79, 0.836);
        display: grid;
        place-items: center;

        &-close {
            position: absolute;
            height: 40px;
            width: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            top: 0;
            right: 0;
            font-size: 2rem;
            color: rgb(0, 0, 0);
            cursor: pointer;
        }

        &-inner {
            background-color: rgb(255, 255, 255);
            color: #000;
            position: relative;
            width: 100%;
            height: 80%;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 5px 5px rgba(87, 79, 79, 0.2);
        }
    }
</style>