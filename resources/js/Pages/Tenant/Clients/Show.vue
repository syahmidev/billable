<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    client: Object,
    invoices: Array,
})

function archive() {
    if (!confirm(`Archive ${props.client.name}?`)) return
    router.delete(`/clients/${props.client.id}`)
}

const page = usePage()
const permissions = computed(() => page.props.permissions ?? {})

const statusConfig = {
    draft:     { bg: 'bg-indigo-100 text-indigo-500 border-indigo-200' },
    sent:      { bg: 'bg-sky-100 text-sky-600 border-sky-200' },
    paid:      { bg: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
    overdue:   { bg: 'bg-rose-100 text-rose-600 border-rose-200' },
    cancelled: { bg: 'bg-gray-100 text-gray-500 border-gray-200' },
}

const totalInvoiced = computed(() => props.invoices.reduce((s, i) => s + i.total, 0))
const totalPaid = computed(() => props.invoices.filter(i => i.status === 'paid').reduce((s, i) => s + i.total, 0))
const totalOutstanding = computed(() => props.invoices.filter(i => ['sent','overdue'].includes(i.status)).reduce((s, i) => s + i.total, 0))
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8 flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        href="/clients"
                        class="cursor-pointer flex h-9 w-9 items-center justify-center rounded-2xl border-2 border-indigo-100 bg-white text-indigo-400 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
                        style="box-shadow: 0 2px 8px rgba(99,102,241,0.08);"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">{{ client.name }}</h1>
                        <p v-if="client.company" class="mt-0.5 text-sm font-medium text-indigo-400">{{ client.company }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        v-if="permissions.invoices?.create"
                        :href="`/invoices/create?client_id=${client.id}`"
                        class="clay-btn cursor-pointer inline-flex items-center gap-1.5 rounded-2xl bg-indigo-500 px-4 py-2.5 text-sm font-black text-white"
                        style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        New Invoice
                    </Link>
                    <Link
                        v-if="permissions.clients?.update"
                        :href="`/clients/${client.id}/edit`"
                        class="clay-btn-secondary cursor-pointer rounded-2xl border-2 border-indigo-200 bg-white px-4 py-2.5 text-sm font-bold text-indigo-600"
                        style="box-shadow: 0 4px 12px rgba(99,102,241,0.10);"
                    >
                        Edit
                    </Link>
                    <button
                        v-if="permissions.clients?.delete"
                        class="cursor-pointer rounded-2xl border-2 border-rose-200 bg-white px-4 py-2.5 text-sm font-bold text-rose-500 transition-all hover:bg-rose-50"
                        @click="archive"
                    >
                        Archive
                    </button>
                </div>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                <!-- Contact info sidebar -->
                <div class="clay-card bg-white p-6">
                    <h2 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-indigo-400">Contact Info</h2>
                    <dl class="space-y-4">
                        <div v-if="client.email">
                            <dt class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Email</dt>
                            <dd class="mt-1 text-sm font-medium text-indigo-900">{{ client.email }}</dd>
                        </div>
                        <div v-if="client.phone">
                            <dt class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Phone</dt>
                            <dd class="mt-1 text-sm font-medium text-indigo-900">{{ client.phone }}</dd>
                        </div>
                        <div v-if="client.address">
                            <dt class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Address</dt>
                            <dd class="mt-1 text-sm font-medium text-indigo-900 whitespace-pre-line">{{ client.address }}</dd>
                        </div>
                        <div v-if="!client.email && !client.phone && !client.address">
                            <p class="text-sm font-medium text-indigo-300">No contact info added yet.</p>
                        </div>
                    </dl>

                    <div v-if="client.notes" class="mt-5 border-t-2 border-indigo-50 pt-5">
                        <dt class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Notes</dt>
                        <dd class="text-sm text-indigo-700 whitespace-pre-line">{{ client.notes }}</dd>
                    </div>

                    <!-- Billing summary -->
                    <div class="mt-5 border-t-2 border-indigo-50 pt-5 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-indigo-400">Total invoiced</span>
                            <span class="text-sm font-black text-indigo-900">${{ totalInvoiced.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-indigo-400">Paid</span>
                            <span class="text-sm font-black text-emerald-600">${{ totalPaid.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-indigo-400">Outstanding</span>
                            <span class="text-sm font-black text-indigo-700">${{ totalOutstanding.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Invoice history -->
                <div class="clay-card overflow-hidden bg-white lg:col-span-2">
                    <div class="flex items-center justify-between border-b-2 border-indigo-50 px-6 py-4">
                        <h2 class="text-xs font-extrabold uppercase tracking-wider text-indigo-400">Invoice History</h2>
                        <span class="rounded-xl bg-indigo-100 px-2.5 py-1 text-xs font-bold text-indigo-500">
                            {{ invoices.length }} invoice{{ invoices.length !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    <table v-if="invoices.length" class="w-full">
                        <tbody class="divide-y divide-indigo-50">
                            <tr v-for="inv in invoices" :key="inv.id" class="group transition-colors hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <Link :href="`/invoices/${inv.id}`" class="cursor-pointer font-bold text-indigo-700 transition-colors hover:text-indigo-500">
                                        {{ inv.invoice_number }}
                                    </Link>
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex rounded-xl border-2 px-2.5 py-1 text-[11px] font-bold"
                                        :class="statusConfig[inv.status]?.bg ?? 'bg-indigo-50 text-indigo-400 border-indigo-100'"
                                    >
                                        {{ inv.status.charAt(0).toUpperCase() + inv.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-xs font-medium text-indigo-400">Due {{ inv.due_date }}</td>
                                <td class="px-6 py-4 text-right text-sm font-black text-indigo-900">${{ Number(inv.total).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-else class="py-12 text-center">
                        <p class="text-base font-bold text-indigo-400" style="font-family: 'Fredoka', sans-serif;">No invoices yet</p>
                        <Link :href="`/invoices/create`" class="cursor-pointer mt-2 inline-block text-sm font-bold text-indigo-500 hover:text-indigo-700">
                            Create one now →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
