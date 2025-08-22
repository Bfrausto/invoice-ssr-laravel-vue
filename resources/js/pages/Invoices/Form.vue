<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import CreateClientModal from '@/components/CreateClientModal.vue';
import CreateCompanyModal from '@/components/CreateCompanyModal.vue';
import CreateTaxModal from '@/components/CreateTaxModal.vue';

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
interface FormData {
    clients: Client[];
    companies: Company[];
    taxes: Tax[];
    currencies: [];
    exchangeRate: number;
    nextFolio: number;
}
interface InvoiceItem {
    description: string;
    quantity: number;
    price: number;
    discount: number;
}

interface Invoice {
    id: number;
    client_id: number;
    company_id: number;
    due_date: string;
    tax_id: number | null;
    notes: string;
    items: InvoiceItem[];
    global_discount: number;
    folio: number;
    series: string;
}

const props = defineProps<{
    formData: FormData,
    invoice?: Invoice;
}>();
const isEditMode = computed(() => !!props.invoice);
const invoiceData = props.invoice?.data;

const showClientModal = ref(false);
const showCompanyModal = ref(false);
const showTaxModal = ref(false);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
if (isEditMode.value) {
    breadcrumbs.push({ title: `Editar Factura #${invoiceData?.id}`, href: `/invoices/${invoiceData?.id}/edit` });
} else {
    breadcrumbs.push({ title: 'Crear Factura', href: '/invoice/new' });
}


const form = useForm({
    series: invoiceData?.series ?? 'F',
    folio: invoiceData?.folio ?? props.formData.nextFolio,
    company_id: invoiceData?.company.id ?? props.formData.companies[0]?.id,
    client_id: invoiceData?.client.id ?? null,
    due_date: invoiceData?.due_date.split('T')[0] ?? new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0],
    tax_id: invoiceData?.tax?.id ?? props.formData.taxes[0]?.id,
    notes: invoiceData?.notes ?? '',
    items: invoiceData?.items ? JSON.parse(JSON.stringify(invoiceData.items)) : [{ description: '', quantity: 1, price: 0, discount: 0 } as InvoiceItem],
    currency: invoiceData?.currency ?? 'MXN',
    global_discount: invoiceData?.global_discount ?? 0,
    status: invoiceData?.status ?? 'draft'
});

watch(() => form.currency, (newCurrency, oldCurrency) => {
    if (!oldCurrency) {
        return;
    }

    const rate = props.formData.exchangeRate;

    form.items = form.items.map(item => {
        let newPrice = item.price;
        if (newCurrency === 'USD' && oldCurrency === 'MXN') {
            newPrice = item.price / rate;
        } else if (newCurrency === 'MXN' && oldCurrency === 'USD') {
            newPrice = item.price * rate;
        }
        newPrice = newPrice.toFixed(2);

        return { ...item, price: newPrice };
    });
});
const subtotal = computed(() => {
    return form.items.reduce((acc, item) => {
        const itemTotal = item.quantity * item.price;
        const discountAmount = itemTotal * (item.discount / 100);
        return acc + (itemTotal - discountAmount);
    }, 0);
});

const globalDiscountAmount = computed(() => {
    return subtotal.value * (form.global_discount / 100);
});

const subtotalAfterDiscount = computed(() => {
    return subtotal.value - globalDiscountAmount.value;
});

const selectedTax = computed(() => {
    return props.formData.taxes.find(tax => tax.id === form.tax_id);
});

const taxAmount = computed(() => {
    if (!selectedTax.value) return 0;
    return subtotalAfterDiscount.value * (selectedTax.value.rate / 100);
});

const grandTotal = computed(() => {
    return subtotalAfterDiscount.value + taxAmount.value;
});


const addItem = () => {
    form.items.push({ description: '', quantity: 1, price: 0, discount: 0 });
};

const removeItem = (index: number) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submitForm = (targetStatus: 'draft' | 'saved') => {
    form.status = targetStatus;
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
const handleClientCreated = (newClient) => {
    props.formData.clients.push(newClient);
    form.client_id = newClient.id;
    showClientModal.value = false;
    alert('¡Cliente creado y seleccionado!');
};

const handleCompanyCreated = (newCompany) => {
    props.formData.companies.push(newCompany);
    form.company_id = newCompany.id;
    showCompanyModal.value = false;
    alert('¡Compañía creada y seleccionada!');
};

const handleTaxCreated = (newTax) => {
    props.formData.taxes.push(newTax);
    form.tax_id = newTax.id;
    showTaxModal.value = false;
    alert('¡Impuesto creado y seleccionado!');
};
</script>

<template>
    <Head title="Crear Factura" />
    <CreateClientModal
        :show="showClientModal"
        @close="showClientModal = false"
        @client-created="handleClientCreated"
    />
    <CreateCompanyModal
        :show="showCompanyModal"
        @close="showCompanyModal = false"
        @company-created="handleCompanyCreated"
    />
    <CreateTaxModal
        :show="showTaxModal"
        @close="showTaxModal = false"
        @tax-created="handleTaxCreated"
    />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-y-auto">
            <form @submit.prevent="submitForm" class="relative flex-1 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border bg-white dark:bg-gray-800">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{isEditMode ? `Editando factura ${invoiceData.id}` : 'Nueva Factura'}}</h1>
                    <div class="flex justify-end items-center gap-4">
                        <button @click.prevent="submitForm('draft')" :disabled="form.processing" type="button" class="px-6 py-3 bg-gray-500 text-white rounded hover:bg-gray-600 disabled:bg-gray-300">
                            {{ form.processing ? 'Guardando...' : 'Guardar Borrador' }}
                        </button>
                        <button @click.prevent="submitForm('saved')" :disabled="form.processing" type="button" class="px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700 disabled:bg-green-300">
                            {{ form.processing ? 'Guardando...' : (isEditMode ? 'Guardar Cambios' : 'Guardar y Finalizar') }}
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div>
                        <label for="client" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Cliente</label>
                        <div class="flex items-center gap-2">
                            <select id="client" v-model="form.client_id" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option :value="null" disabled>Selecciona un cliente</option>
                                <option v-for="client in props.formData.clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                            <button @click.prevent="showClientModal = true" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-white">
                                Nuevo
                            </button>
                        </div>
                        <p v-if="form.errors.client_id" class="text-sm text-red-500 mt-1">{{ form.errors.client_id }}</p>
                    </div>
                    <div>
                        <label for="company" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Compañía Emisora</label>
                        <div class="flex items-center gap-2">

                            <select id="company" v-model="form.company_id" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option v-for="company in props.formData.companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                            </select>
                            <button @click.prevent="showCompanyModal = true" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-white">
                                Nuevo
                            </button>
                        </div>
                        <p v-if="form.errors.company_id" class="text-sm text-red-500 mt-1">{{ form.errors.company_id }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="due_date" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Vencimiento</label>
                            <input type="date" id="due_date" v-model="form.due_date" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p v-if="form.errors.due_date" class="text-sm text-red-500 mt-1">{{ form.errors.due_date }}</p>
                        </div>
                        <div>
                            <label for="currency" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Moneda</label>
                            <select id="currency" v-model="form.currency" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option v-for="(currency, key) in props.formData.currencies" :key="key" :value="key">
                                    {{ currency.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.currency" class="text-sm text-red-500 mt-1">{{ form.errors.currency }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="series" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Serie</label>
                            <input type="text" id="series" v-model="form.series" class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p v-if="form.errors.series" class="text-sm text-red-500 mt-1">{{ form.errors.series }}</p>
                        </div>
                        <div>
                            <label for="folio" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Folio</label>
                            <input type="number" id="folio" v-model="form.folio" required class="w-full p-2.5 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p v-if="form.errors.folio" class="text-sm text-red-500 mt-1">{{ form.errors.folio }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="grid grid-cols-12 gap-4 mb-2 pb-2 border-b font-semibold text-gray-600 dark:text-gray-300">
                        <div class="col-span-4">Descripción</div>
                        <div class="col-span-2 text-right">Cantidad</div>
                        <div class="col-span-2 text-right">Precio</div>
                        <div class="col-span-1 text-right">Desc. %</div> <div class="col-span-2 text-right">Total</div>
                        <div class="col-span-1"></div>
                    </div>
                    <div v-for="(item, index) in form.items" :key="index" class="grid grid-cols-12 gap-4 mb-4 items-center">
                        <input type="text" v-model="item.description" placeholder="Descripción del servicio" class="col-span-4 p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                        <input type="number" step="0.01" v-model.number="item.quantity" class="col-span-2 p-2 border rounded-lg text-right dark:bg-gray-700 dark:border-gray-600">
                        <input type="number" step="0.01" v-model.number="item.price" class="col-span-2 p-2 border rounded-lg text-right dark:bg-gray-700 dark:border-gray-600">
                        <input type="number" v-model.number="item.discount" class="col-span-1 p-2 border rounded-lg text-right dark:bg-gray-700 dark:border-gray-600">

                        <span class="col-span-2 p-2 text-right text-gray-800 dark:text-gray-200">
                            {{ props.formData.currencies[form.currency]?.symbol }}{{ ((item.quantity * item.price) * (1 - item.discount / 100)).toFixed(2) }}

                        </span>
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
                                <span class="font-semibold text-gray-800 dark:text-white">
                                    {{ props.formData.currencies[form.currency]?.symbol }}{{ subtotal.toFixed(2) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <label for="global_discount" class="text-gray-600 dark:text-gray-300">Descuento Global (%):</label>
                                <input id="global_discount" type="number" v-model.number="form.global_discount" class="w-20 p-1 border rounded-lg text-right bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-300">Monto Descuento:</span>
                                <span class="font-semibold text-gray-800 dark:text-white">-${{ globalDiscountAmount.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <select v-model="form.tax_id" class="p-1 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                        <option :value="null">Sin Impuesto</option>
                                        <option v-for="tax in props.formData.taxes" :key="tax.id" :value="tax.id">{{ tax.name }} ({{ tax.rate }}%)</option>
                                    </select>
                                    <button @click.prevent="showTaxModal = true" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-white">Nuevo</button>
                                </div>
                                <span class="font-semibold text-gray-800 dark:text-white">
                                    {{ props.formData.currencies[form.currency]?.symbol }}{{ taxAmount.toFixed(2) }}
                                </span>
                            </div>
                            <div class="border-t my-2 dark:border-gray-600"></div>
                            <div class="flex justify-between text-xl font-bold">
                                <span class="text-gray-800 dark:text-white">Total:</span>
                                <span class="text-gray-800 dark:text-white">
                                    {{ props.formData.currencies[form.currency]?.symbol }}{{ grandTotal.toFixed(2) }} {{form.currency}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
