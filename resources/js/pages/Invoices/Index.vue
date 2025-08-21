<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';

defineProps<{
    invoices: {
        data: any[];
        links: any[];
        meta: any[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Facturas', href: '/invoices' },
];
const formatCurrency = (value: number, currency: string = 'MXN') => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: currency,
    }).format(value);
};
const downloadingId = ref<number | null>(null);

const downloadPdf = async (invoice: any) => {
    downloadingId.value = invoice.id;
    try {
        const response = await axios.post(`/api/invoices/${invoice.id}/pdf`, {}, {
            responseType: 'blob',
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `factura-${invoice.folio}.pdf`);
        document.body.appendChild(link);

        link.click();

        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

    } catch (error) {
        console.error('Error al descargar el PDF:', error);
        alert('No se pudo descargar el PDF.');
    } finally {
        downloadingId.value = null;
    }
};
</script>

<template>
    <Head title="Facturas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 md:p-6 overflow-y-auto">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Mis Facturas</h1>
                <Link href="/invoices/new" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">
                    + Crear Factura
                </Link>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white dark:bg-gray-800">
                <table class="w-full text-left">
                    <thead class="border-b border-sidebar-border/70 dark:border-sidebar-border">
                    <tr>
                        <th class="p-4">Folio</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Fecha Emisión</th>
                        <th class="p-4 text-right">Total</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b border-sidebar-border/70 dark:border-sidebar-border last:border-b-0">
                        <td class="p-4 font-medium">{{ invoice.folio }}</td>
                        <td class="p-4 text-gray-600 dark:text-gray-300">{{ invoice.client.name }}</td>
                        <td class="p-4 text-gray-600 dark:text-gray-300">{{ invoice.issue_date }}</td>
                        <td class="p-4 text-right font-mono">
                            {{ formatCurrency(invoice.total, invoice.currency) }} {{ invoice.currency}}
                        </td>
                        <td class="p-4">
                                <span class="px-2 py-1 text-xs rounded-full" :class="{
                                    'bg-yellow-100 text-yellow-800': invoice.status === 'draft',
                                    'bg-green-100 text-green-800': invoice.status === 'saved',
                                }">{{ invoice.status }}</span>
                        </td>
                        <td class="p-4">
                            <Link :href="`/invoices/${invoice.id}/edit`" class="text-blue-500 hover:underline">
                                Editar
                            </Link>

                            <button v-if="invoice.status === 'saved'"
                                    @click="downloadPdf(invoice)"
                                    :disabled="downloadingId === invoice.id"
                                    class="ml-2 px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 disabled:bg-green-300">
                                {{ downloadingId === invoice.id ? 'Descargando...' : 'Descargar PDF' }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="invoices.data.length === 0">
                        <td colspan="6" class="p-4 text-center text-gray-500">No se encontraron facturas.</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="invoices.meta.links.length > 3" class="flex justify-center mt-4">
                <div class="flex flex-wrap -mb-1">
                    <template v-for="(link, key) in invoices.meta.links" :key="key">
                        <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                        <Link v-else class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-blue-600 text-white': link.active }" :href="link.url" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
