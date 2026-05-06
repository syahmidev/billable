<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    invoice: Object,
    workspaceName: String,
    paymentUrl: String,
})

const copied = ref(false)

function copyPaymentLink() {
    navigator.clipboard.writeText(props.paymentUrl)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
}

const page = usePage()

const statusColors = {
    draft: 'bg-gray-800 text-gray-400',
    sent: 'bg-blue-500/10 text-blue-400',
    paid: 'bg-green-500/10 text-green-400',
    overdue: 'bg-red-500/10 text-red-400',
    cancelled: 'bg-gray-800 text-gray-500',
}

function send() {
    if (!confirm(`Send ${props.invoice.invoice_number} to ${props.invoice.client?.email || props.invoice.client?.name}?`)) return
    router.post(`/invoices/${props.invoice.id}/send`)
}

function remind() {
    if (!confirm(`Send a payment reminder for ${props.invoice.invoice_number}?`)) return
    router.post(`/invoices/${props.invoice.id}/remind`)
}

function deleteInvoice() {
    if (!confirm(`Delete ${props.invoice.invoice_number}? This cannot be undone.`)) return
    router.delete(`/invoices/${props.invoice.id}`)
}

function fmt(n) { return Number(n).toFixed(2) }
</script>

<template>
    <AppLayout>
        <div class="p-8 max-w-4xl">
            <!-- Flash -->
            <div v-if="page.props.flash?.success" class="mb-6 px-4 py-3 bg-green-500/10 border border-green-500/20 rounded-lg text-sm text-green-400">
                {{ page.props.flash.success }}
            </div>
            <div v-if="page.props.flash?.error" class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-lg text-sm text-red-400">
                {{ page.props.flash.error }}
            </div>
            <div v-if="page.props.errors?.invoice" class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-lg text-sm text-red-400">
                {{ page.props.errors.invoice }}
            </div>

            <!-- Header -->
            <div class="flex items-start justify-between mb-8">
                <div class="flex items-center gap-3">
                    <a href="/invoices" class="text-gray-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-semibold text-white">{{ invoice.invoice_number }}</h1>
                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium" :class="statusColors[invoice.status]">
                                {{ invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-400 mt-0.5">{{ invoice.client?.name }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <a
                        :href="`/invoices/${invoice.id}/pdf`"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF
                    </a>
                    <a
                        v-if="invoice.status === 'draft'"
                        :href="`/invoices/${invoice.id}/edit`"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        Edit
                    </a>
                    <button
                        v-if="invoice.status === 'draft' || invoice.status === 'sent'"
                        @click="send"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        {{ invoice.status === 'sent' ? 'Resend' : 'Send' }}
                    </button>
                    <button
                        v-if="invoice.can_send_reminder"
                        @click="remind"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 text-amber-300 text-sm font-medium rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5" />
                        </svg>
                        Remind
                    </button>
                    <button
                        v-if="invoice.status === 'draft'"
                        @click="deleteInvoice"
                        class="inline-flex items-center px-3.5 py-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 text-sm font-medium rounded-lg transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <!-- Invoice preview card -->
            <div class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
                <!-- From / To -->
                <div class="grid grid-cols-2 gap-6 p-6 border-b border-white/10">
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-2">From</p>
                        <p class="text-sm font-semibold text-white">{{ workspaceName }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-2">Bill To</p>
                        <p class="text-sm font-semibold text-white">{{ invoice.client?.name }}</p>
                        <p v-if="invoice.client?.company" class="text-xs text-gray-400 mt-0.5">{{ invoice.client.company }}</p>
                        <p v-if="invoice.client?.email" class="text-xs text-gray-400">{{ invoice.client.email }}</p>
                        <p v-if="invoice.client?.address" class="text-xs text-gray-400 mt-0.5 whitespace-pre-line">{{ invoice.client.address }}</p>
                    </div>
                </div>

                <!-- Meta row -->
                <div class="grid grid-cols-3 gap-6 px-6 py-4 bg-white/[0.02] border-b border-white/10">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wider mb-1">Invoice #</p>
                        <p class="text-sm font-medium text-white">{{ invoice.invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wider mb-1">Issue Date</p>
                        <p class="text-sm font-medium text-white">{{ invoice.issue_date }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wider mb-1">Due Date</p>
                        <p class="text-sm font-medium text-white">{{ invoice.due_date }}</p>
                    </div>
                </div>

                <div v-if="invoice.reminders_sent > 0" class="px-6 py-3 bg-amber-500/5 border-b border-amber-500/10">
                    <p class="text-xs text-amber-300">
                        {{ invoice.reminders_sent }} reminder{{ invoice.reminders_sent !== 1 ? 's' : '' }} sent
                        <span v-if="invoice.last_reminded_at">· last {{ invoice.last_reminded_at }}</span>
                    </p>
                </div>

                <!-- Line items -->
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Qty</th>
                            <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Unit Price</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr v-for="item in invoice.items" :key="item.id">
                            <td class="px-6 py-3.5 text-white">{{ item.description }}</td>
                            <td class="px-4 py-3.5 text-right text-gray-400">{{ Number(item.quantity).toFixed(2) }}</td>
                            <td class="px-4 py-3.5 text-right text-gray-400">${{ Number(item.unit_price).toFixed(2) }}</td>
                            <td class="px-6 py-3.5 text-right text-white font-medium">${{ Number(item.line_total).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Totals -->
                <div class="px-6 py-5 border-t border-white/10">
                    <div class="ml-auto w-56 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-400">
                            <span>Subtotal</span>
                            <span>${{ fmt(invoice.subtotal) }}</span>
                        </div>
                        <div v-if="Number(invoice.discount_percent) > 0" class="flex justify-between text-gray-400">
                            <span>Discount ({{ Number(invoice.discount_percent) }}%)</span>
                            <span class="text-red-400">-${{ fmt(invoice.discount_amount) }}</span>
                        </div>
                        <div v-if="Number(invoice.tax_percent) > 0" class="flex justify-between text-gray-400">
                            <span>Tax ({{ Number(invoice.tax_percent) }}%)</span>
                            <span>${{ fmt(invoice.tax_amount) }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-white pt-2 border-t border-white/10">
                            <span>Total Due</span>
                            <span class="text-violet-400 text-lg">${{ fmt(invoice.total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div v-if="invoice.notes" class="px-6 py-5 border-t border-white/10">
                    <p class="text-xs text-gray-600 uppercase tracking-wider mb-2">Notes</p>
                    <p class="text-sm text-gray-400 whitespace-pre-line">{{ invoice.notes }}</p>
                </div>
            </div>

            <!-- Payment link -->
            <div v-if="paymentUrl && invoice.status !== 'paid' && invoice.status !== 'cancelled'" class="mt-5 bg-gray-900 border border-white/10 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-medium text-white">Payment Link</p>
                        <p class="text-xs text-gray-500 mt-0.5">Share this link with your client to collect payment online</p>
                    </div>
                    <button
                        @click="copyPaymentLink"
                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-violet-600 hover:bg-violet-500 text-white text-xs font-medium rounded-lg transition-colors"
                    >
                        <svg v-if="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ copied ? 'Copied!' : 'Copy Link' }}
                    </button>
                </div>
                <div class="bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5">
                    <p class="text-xs text-gray-400 font-mono truncate">{{ paymentUrl }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
