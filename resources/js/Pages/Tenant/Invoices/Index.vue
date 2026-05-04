<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    invoices: Object,
    clients: Array,
    filters: Object,
})

const statusFilter = ref(props.filters?.status ?? '')
const clientFilter = ref(props.filters?.client_id ?? '')

watch([statusFilter, clientFilter], () => {
    router.get('/invoices', {
        status: statusFilter.value,
        client_id: clientFilter.value,
    }, { preserveState: true, replace: true })
})

const statusColors = {
    draft: 'bg-gray-800 text-gray-400',
    sent: 'bg-blue-500/10 text-blue-400',
    paid: 'bg-green-500/10 text-green-400',
    overdue: 'bg-red-500/10 text-red-400',
    cancelled: 'bg-gray-800 text-gray-500',
}
</script>

<template>
    <AppLayout>
        <div class="p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Invoices</h1>
                    <p class="text-sm text-gray-400 mt-1">Track and manage your invoices</p>
                </div>
                <a
                    href="/invoices/create"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Invoice
                </a>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-3 mb-4">
                <select
                    v-model="statusFilter"
                    class="bg-gray-900 border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-violet-500"
                >
                    <option value="">All statuses</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select
                    v-model="clientFilter"
                    class="bg-gray-900 border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-violet-500"
                >
                    <option value="">All clients</option>
                    <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
                <table v-if="invoices.data.length" class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-3.5">
                                <a :href="`/invoices/${invoice.id}`" class="font-medium text-white hover:text-violet-400 transition-colors">
                                    {{ invoice.invoice_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-gray-400">{{ invoice.client?.name ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium" :class="statusColors[invoice.status]">
                                    {{ invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-400">{{ invoice.due_date }}</td>
                            <td class="px-5 py-3.5 text-right text-white font-medium">${{ Number(invoice.total).toFixed(2) }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a :href="`/invoices/${invoice.id}/edit`" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-white/5 transition-colors">Edit</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty state -->
                <div v-else class="p-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-violet-500/10 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-white mb-1">No invoices yet</h3>
                    <p class="text-xs text-gray-500 mb-4">Create your first invoice to get paid</p>
                    <a href="/invoices/create" class="inline-flex items-center gap-2 px-3 py-1.5 bg-violet-600 hover:bg-violet-500 text-white text-xs font-medium rounded-lg transition-colors">
                        New Invoice
                    </a>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="invoices.last_page > 1" class="flex items-center justify-between mt-4">
                <p class="text-xs text-gray-500">Showing {{ invoices.from }}–{{ invoices.to }} of {{ invoices.total }}</p>
                <div class="flex gap-1">
                    <a
                        v-for="link in invoices.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="px-2.5 py-1 text-xs rounded"
                        :class="link.active ? 'bg-violet-600 text-white' : link.url ? 'text-gray-400 hover:text-white hover:bg-white/5' : 'text-gray-600 cursor-default'"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
