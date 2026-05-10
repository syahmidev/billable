<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
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
            <div class="flex items-start justify-between mb-8">
                <div class="flex items-center gap-3">
                    <a href="/clients" class="text-gray-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-semibold text-white">{{ client.name }}</h1>
                        <p v-if="client.company" class="text-sm text-gray-400 mt-0.5">{{ client.company }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        v-if="permissions.invoices?.create"
                        :href="`/invoices/create?client_id=${client.id}`"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Invoice
                    </a>
                    <a v-if="permissions.clients?.update" :href="`/clients/${client.id}/edit`" class="px-3.5 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm font-medium rounded-lg transition-colors">
                        Edit
                    </a>
                    <button v-if="permissions.clients?.delete" @click="archive" class="px-3.5 py-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 text-sm font-medium rounded-lg transition-colors">
                        Archive
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Contact info -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-6">
                    <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Contact Info</h2>
                    <dl class="space-y-3">
                        <div v-if="client.email">
                            <dt class="text-xs text-gray-600">Email</dt>
                            <dd class="text-sm text-white mt-0.5">{{ client.email }}</dd>
                        </div>
                        <div v-if="client.phone">
                            <dt class="text-xs text-gray-600">Phone</dt>
                            <dd class="text-sm text-white mt-0.5">{{ client.phone }}</dd>
                        </div>
                        <div v-if="client.address">
                            <dt class="text-xs text-gray-600">Address</dt>
                            <dd class="text-sm text-white mt-0.5 whitespace-pre-line">{{ client.address }}</dd>
                        </div>
                        <div v-if="!client.email && !client.phone && !client.address">
                            <p class="text-xs text-gray-600">No contact info added yet.</p>
                        </div>
                    </dl>
                    <div v-if="client.notes" class="mt-5 pt-5 border-t border-white/10">
                        <dt class="text-xs text-gray-600 mb-1">Notes</dt>
                        <dd class="text-sm text-gray-300 whitespace-pre-line">{{ client.notes }}</dd>
                    </div>

                    <!-- Invoice stats -->
                    <div class="mt-5 pt-5 border-t border-white/10 space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Total invoiced</span>
                            <span class="text-white font-medium">
                                ${{ invoices.reduce((s, i) => s + i.total, 0).toFixed(2) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Paid</span>
                            <span class="text-green-400 font-medium">
                                ${{ invoices.filter(i => i.status === 'paid').reduce((s, i) => s + i.total, 0).toFixed(2) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Outstanding</span>
                            <span class="text-white font-medium">
                                ${{ invoices.filter(i => i.status === 'sent' || i.status === 'overdue').reduce((s, i) => s + i.total, 0).toFixed(2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Invoice history -->
                <div class="lg:col-span-2 bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                        <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice History</h2>
                        <span class="text-xs text-gray-600">{{ invoices.length }} invoice{{ invoices.length !== 1 ? 's' : '' }}</span>
                    </div>

                    <table v-if="invoices.length" class="w-full text-sm">
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="inv in invoices" :key="inv.id" class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-5 py-3.5">
                                    <a :href="`/invoices/${inv.id}`" class="font-medium text-white hover:text-violet-400 transition-colors">
                                        {{ inv.invoice_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium" :class="statusColors[inv.status]">
                                        {{ inv.status.charAt(0).toUpperCase() + inv.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-gray-500">Due {{ inv.due_date }}</td>
                                <td class="px-5 py-3.5 text-right text-white font-medium">${{ Number(inv.total).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-else class="py-12 text-center">
                        <p class="text-sm text-gray-500 mb-1">No invoices yet</p>
                        <p class="text-xs text-gray-600">
                            <a :href="`/invoices/create`" class="text-violet-400 hover:text-violet-300">Create one now</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
