<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
    invoices: Object,
    clients: Array,
    filters: Object,
})

const statusFilter = ref(props.filters?.status ?? '')
const clientFilter = ref(props.filters?.client_id ?? '')
const hasFilters = computed(() => Boolean(statusFilter.value || clientFilter.value))
const page = usePage()
const permissions = computed(() => page.props.permissions ?? {})

watch([statusFilter, clientFilter], () => {
    router.get('/invoices', { status: statusFilter.value, client_id: clientFilter.value }, { preserveState: true, replace: true })
})

const statusConfig = {
    draft:     { bg: 'bg-indigo-100 text-indigo-500 border-indigo-200', label: 'Draft' },
    sent:      { bg: 'bg-sky-100 text-sky-600 border-sky-200', label: 'Sent' },
    paid:      { bg: 'bg-emerald-100 text-emerald-700 border-emerald-200', label: 'Paid' },
    overdue:   { bg: 'bg-rose-100 text-rose-600 border-rose-200', label: 'Overdue' },
    cancelled: { bg: 'bg-gray-100 text-gray-500 border-gray-200', label: 'Cancelled' },
}
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">Invoices</h1>
                    <p class="mt-1 text-sm font-medium text-indigo-400">Track and manage your invoices</p>
                </div>
                <Link
                    v-if="permissions.invoices?.create"
                    href="/invoices/create"
                    class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-indigo-500 px-4 py-2.5 text-sm font-black text-white"
                    style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    New Invoice
                </Link>
            </div>

            <!-- Filters -->
            <div class="mb-4 flex flex-wrap gap-3">
                <select v-model="statusFilter" class="clay-select w-auto min-w-36">
                    <option value="">All statuses</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select v-model="clientFilter" class="clay-select w-auto min-w-36">
                    <option value="">All clients</option>
                    <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <button
                    v-if="hasFilters"
                    class="cursor-pointer rounded-2xl border-2 border-indigo-100 bg-white px-4 py-2.5 text-sm font-bold text-indigo-400 transition-all hover:border-indigo-200 hover:text-indigo-600"
                    @click="statusFilter = ''; clientFilter = ''"
                >
                    Clear filters
                </button>
            </div>

            <!-- Table -->
            <div class="clay-card overflow-hidden bg-white">
                <table v-if="invoices.data.length" class="w-full">
                    <thead>
                        <tr class="border-b-2 border-indigo-50 bg-indigo-50/50">
                            <th class="px-6 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Invoice</th>
                            <th class="px-5 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Client</th>
                            <th class="px-5 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Status</th>
                            <th class="px-5 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Due Date</th>
                            <th class="px-6 py-3 text-right text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Amount</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-50">
                        <tr v-for="invoice in invoices.data" :key="invoice.id" class="group transition-colors hover:bg-indigo-50/40">
                            <td class="px-6 py-4">
                                <Link :href="`/invoices/${invoice.id}`" class="cursor-pointer font-bold text-indigo-700 transition-colors hover:text-indigo-500">
                                    {{ invoice.invoice_number }}
                                </Link>
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-indigo-400">{{ invoice.client?.name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-xl border-2 px-2.5 py-1 text-[11px] font-bold" :class="statusConfig[invoice.status]?.bg ?? 'bg-indigo-50 text-indigo-400 border-indigo-100'">
                                    {{ statusConfig[invoice.status]?.label ?? invoice.status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-indigo-400">{{ invoice.due_date }}</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-indigo-900">${{ Number(invoice.total).toFixed(2) }}</td>
                            <td class="px-5 py-4 text-right">
                                <Link
                                    v-if="permissions.invoices?.update"
                                    :href="`/invoices/${invoice.id}/edit`"
                                    class="cursor-pointer rounded-xl border-2 border-indigo-100 bg-white px-3 py-1.5 text-xs font-bold text-indigo-500 transition-all hover:border-indigo-200 hover:bg-indigo-50"
                                >
                                    Edit
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty state -->
                <div v-else class="py-16 text-center">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-100" style="box-shadow: 0 6px 20px rgba(99,102,241,0.15);">
                        <svg class="h-7 w-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-lg font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif;">
                        {{ hasFilters ? 'No matching invoices' : 'No invoices yet' }}
                    </p>
                    <p class="mt-1 text-sm font-medium text-indigo-400">
                        {{ hasFilters ? 'Try changing the filters.' : 'Create your first invoice to get paid.' }}
                    </p>
                    <div class="mt-5">
                        <button
                            v-if="hasFilters"
                            class="clay-btn-secondary cursor-pointer inline-flex items-center gap-2 rounded-2xl border-2 border-indigo-200 bg-white px-4 py-2.5 text-sm font-bold text-indigo-600"
                            @click="statusFilter = ''; clientFilter = ''"
                        >
                            Clear filters
                        </button>
                        <Link
                            v-else-if="permissions.invoices?.create"
                            href="/invoices/create"
                            class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-black text-white"
                            style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                        >
                            New invoice
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="invoices.last_page > 1" class="mt-5 flex items-center justify-between">
                <p class="text-xs font-semibold text-indigo-400">Showing {{ invoices.from }}–{{ invoices.to }} of {{ invoices.total }}</p>
                <div class="flex gap-1">
                    <a
                        v-for="link in invoices.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="cursor-pointer rounded-xl px-3 py-1.5 text-xs font-bold transition-all"
                        :class="link.active
                            ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-200'
                            : link.url
                                ? 'border-2 border-indigo-100 bg-white text-indigo-500 hover:border-indigo-200 hover:bg-indigo-50'
                                : 'text-indigo-200 cursor-default'"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
