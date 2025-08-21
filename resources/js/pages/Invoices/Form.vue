<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Client {
    id: number;
    name: string;
}
interface Company {
    id: number;
    name: string;
}
interface Tax {
    id: number;
    name: string;
    rate: number;
}
interface InitialData {
    clients: Client[];
    companies: Company[];
    taxes: Tax[];
}
interface InvoiceItem {
    description: string;
    quantity: number;
    price: number;
}

interface Invoice {
    id: number;
    client_id: number;
    company_id: number;
    due_date: string;
    tax_id: number | null;
    notes: string;
    items: InvoiceItem[];
}

const props = defineProps<{
    formData: InitialData,
    invoice?: Invoice;
}>();
const isEditMode = computed(() => !!props.invoice);
const invoiceData = props.invoice?.data;
console.log('Invoice Data:', invoiceData);
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
if (isEditMode.value) {
    breadcrumbs.push({ title: `Editar Factura #${invoiceData?.id}`, href: `/invoices/${invoiceData?.id}/edit` });
} else {
    breadcrumbs.push({ title: 'Crear Factura', href: '/invoice/new' });
}


const form = useForm({
    company_id: invoiceData?.company.id ?? props.formData.companies[0]?.id,
    client_id: invoiceData?.client.id ?? null,
    due_date: invoiceData?.due_date.split('T')[0] ?? new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0],
    tax_id: invoiceData?.tax?.id ?? props.formData.taxes[0]?.id,
    notes: invoiceData?.notes ?? '',
    items: invoiceData?.items ? JSON.parse(JSON.stringify(invoiceData.items)) : [{ description: '', quantity: 1, price: 0 } as InvoiceItem]
});

const subtotal = computed(() => {
    return form.items.reduce((acc, item) => acc + (item.quantity * item.price), 0);
});

const selectedTax = computed(() => {
    return props.formData.taxes.find(tax => tax.id === form.tax_id);
});

const taxAmount = computed(() => {
    if (!selectedTax.value) return 0;
    return subtotal.value * (selectedTax.value.rate / 100);
});

const grandTotal = computed(() => {
    return subtotal.value + taxAmount.value;
});


const addItem = () => {
    form.items.push({ description: '', quantity: 1, price: 0 });
};

const removeItem = (index: number) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submitForm = () => {
    if (isEditMode.value && invoiceData) {
        form.put(`/api/invoices/${invoiceData.id}`, {
            onSuccess: () => {
                alert('¡Factura actualizada con éxito!');
            },
            onError: (errors) => {
                console.error(errors);
            }
        });
    } else {
        form.post('/api/invoices', {
            onSuccess: () => {
                alert('¡Factura creada con éxito!');
            },
            onError: (errors) => {
                console.error(errors);
            }
        });
    }
};
</script>

<template>
    <Head title="Crear Factura" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-y-auto">
            <form @submit.prevent="submitForm" class="relative flex-1 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border bg-white dark:bg-gray-800">

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Nueva Factura</h1>
                    <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-blue-300">
                        {{ form.processing ? 'Guardando...' : 'Guardar Factura' }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label for="client" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Cliente</label>
                        <select id="client" v-model="form.client_id" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option :value="null" disabled>Selecciona un cliente</option>
                            <option v-for="client in props.formData.clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                        </select>
                        <p v-if="form.errors.client_id" class="text-sm text-red-500 mt-1">{{ form.errors.client_id }}</p>
                    </div>
                    <div>
                        <label for="company" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Compañía Emisora</label>
                        <select id="company" v-model="form.company_id" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option v-for="company in props.formData.companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                        </select>
                        <p v-if="form.errors.company_id" class="text-sm text-red-500 mt-1">{{ form.errors.company_id }}</p>
                    </div>
                    <div>
                        <label for="due_date" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Vencimiento</label>
                        <input type="date" id="due_date" v-model="form.due_date" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p v-if="form.errors.due_date" class="text-sm text-red-500 mt-1">{{ form.errors.due_date }}</p>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="grid grid-cols-12 gap-4 mb-2 pb-2 border-b font-semibold text-gray-600 dark:text-gray-300">
                        <div class="col-span-5">Descripción</div>
                        <div class="col-span-2 text-right">Cantidad</div>
                        <div class="col-span-2 text-right">Precio</div>
                        <div class="col-span-2 text-right">Total</div>
                        <div class="col-span-1"></div>
                    </div>
                    <div v-for="(item, index) in form.items" :key="index" class="grid grid-cols-12 gap-4 mb-4 items-center">
                        <input type="text" v-model="item.description" placeholder="Descripción del servicio" class="col-span-5 p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                        <input type="number" step="0.01" v-model.number="item.quantity" class="col-span-2 p-2 border rounded-lg text-right dark:bg-gray-700 dark:border-gray-600">
                        <input type="number" step="0.01" v-model.number="item.price" class="col-span-2 p-2 border rounded-lg text-right dark:bg-gray-700 dark:border-gray-600">
                        <span class="col-span-2 p-2 text-right text-gray-800 dark:text-gray-200">${{ (item.quantity * item.price).toFixed(2) }}</span>
                        <button @click.prevent="removeItem(index)" class="col-span-1 text-red-500 hover:text-red-700">✕</button>
                    </div>
                    <button @click.prevent="addItem" type="button" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
                        + Añadir Item
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="notes" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Notas</label>
                        <textarea id="notes" v-model="form.notes" rows="4" class="w-full p-2.5 border rounded-lg dark:bg-gray-700 dark:border-gray-600"></textarea>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="w-full max-w-sm space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-300">Subtotal:</span>
                                <span class="font-semibold text-gray-800 dark:text-white">${{ subtotal.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <select v-model="form.tax_id" class="p-1 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    <option :value="null">Sin Impuesto</option>
                                    <option v-for="tax in props.formData.taxes" :key="tax.id" :value="tax.id">{{ tax.name }} ({{ tax.rate }}%)</option>
                                </select>
                                <span class="font-semibold text-gray-800 dark:text-white">${{ taxAmount.toFixed(2) }}</span>
                            </div>
                            <div class="border-t my-2 dark:border-gray-600"></div>
                            <div class="flex justify-between text-xl font-bold">
                                <span class="text-gray-800 dark:text-white">Total:</span>
                                <span class="text-gray-800 dark:text-white">${{ grandTotal.toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
