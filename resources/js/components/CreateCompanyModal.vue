<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
    show: boolean;
}>();
const emit = defineEmits(['close', 'company-created']);

const formData = ref({
    name: '',
    email: '',
    address: '',
});
const errors = ref<any>({});
const isLoading = ref(false);

watch(() => props.show, (isShown) => {
    if (isShown) {
        formData.value = { name: '', email: '', address: '' };
        errors.value = {};
    }
});

const saveCompany = async () => {
    isLoading.value = true;
    errors.value = {};

    try {
        const response = await axios.post('/api/companies', formData.value);

        emit('company-created', response.data.data);
        emit('close');

    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error('Ha ocurrido un error inesperado:', error);
            alert('No se pudo crear la compañía. Inténtalo de nuevo.');
        }
    } finally {
        isLoading.value = false;
    }
};
</script>
<template>
    <div v-if="show" @click.self="emit('close')" class="fixed inset-0 bg-black bg-opacity-50 z-40 flex justify-center items-center">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6">Crear Nueva Compañía</h2>
            <form @submit.prevent="saveCompany">
                <div class="mb-4">
                    <label for="new-company-name" class="block mb-2 text-sm font-medium">Nombre</label>
                    <input id="new-company-name" v-model="formData.name" type="text" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p v-if="errors.name" class="text-sm text-red-500 mt-1">{{ errors.name[0] }}</p>
                </div>
                <div class="mb-4">
                    <label for="new-company-email" class="block mb-2 text-sm font-medium">Email</label>
                    <input id="new-company-email" v-model="formData.email" type="email" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p v-if="errors.email" class="text-sm text-red-500 mt-1">{{ errors.email[0] }}</p>
                </div>
                <div class="mb-6">
                    <label for="new-company-address" class="block mb-2 text-sm font-medium">Dirección</label>
                    <textarea id="new-company-address" v-model="formData.address" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    <p v-if="errors.address" class="text-sm text-red-500 mt-1">{{ errors.address[0] }}</p>
                </div>
                <div class="flex justify-end gap-4">
                    <button @click.prevent="emit('close')" type="button" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancelar</button>
                    <button type="submit" :disabled="isLoading" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-blue-300">
                        {{ isLoading ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
