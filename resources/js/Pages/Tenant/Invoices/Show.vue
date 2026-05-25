<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    invoice: Object,
    workspaceName: String,
    paymentUrl: String,
})

const copied = ref(false)
const page = usePage()

const statusConfig = {
    draft:     { bg: 'bg-indigo-100 text-indigo-500 border-indigo-200', label: 'Draft' },
    sent:      { bg: 'bg-sky-100 text-sky-600 border-sky-200', label: 'Sent' },
    paid:      { bg: 'bg-emerald-100 text-emerald-700 border-emerald-200', label: 'Paid' },
    overdue:   { bg: 'bg-rose-100 text-rose-600 border-rose-200', label: 'Overdue' },
    cancelled: { bg: 'bg-gray-100 text-gray-500 border-gray-200', label: 'Cancelled' },
}

function copyPaymentLink() {
    navigator.clipboard.writeText(props.paymentUrl)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
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
        <div class="p-6 lg:p-8 max-w-4xl">
            <!-- Flash messages -->
            <div v-if="page.props.flash?.success" class="mb-5 flex items-center gap-3 rounded-2xl border-2 border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                {{ page.props.flash.success }}
            </div>
            <div v-if="page.props.flash?.error || page.props.errors?.invoice" class="mb-5 flex items-center gap-3 rounded-2xl border-2 border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-600">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ page.props.flash?.error ?? page.props.errors?.invoice }}
            </div>

            <!-- Header -->
            <div class="mb-8 flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        href="/invoices"
                        class="cursor-pointer flex h-9 w-9 items-center justify-center rounded-2xl border-2 border-indigo-100 bg-white text-indigo-400 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
                        style="box-shadow: 0 2px 8px rgba(99,102,241,0.08);"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">{{ invoice.invoice_number }}</h1>
                            <span
                                class="inline-flex rounded-xl border-2 px-2.5 py-1 text-xs font-bold"
                                :class="statusConfig[invoice.status]?.bg"
                            >
                                {{ statusConfig[invoice.status]?.label ?? invoice.status }}
                            </span>
                        </div>
                        <p class="mt-0.5 text-sm font-medium text-indigo-400">{{ invoice.client?.name }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        :href="`/invoices/${invoice.id}/pdf`"
                        target="_blank"
                        class="clay-btn-secondary cursor-pointer inline-flex items-center gap-1.5 rounded-2xl border-2 border-indigo-200 bg-white px-3.5 py-2.5 text-sm font-bold text-indigo-600"
                        style="box-shadow: 0 4px 12px rgba(99,102,241,0.10);"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF
                    </a>
                    <Link
                        v-if="invoice.status === 'draft' && invoice.can?.update"
                        :href="`/invoices/${invoice.id}/edit`"
                        class="clay-btn-secondary cursor-pointer rounded-2xl border-2 border-indigo-200 bg-white px-3.5 py-2.5 text-sm font-bold text-indigo-600"
                        style="box-shadow: 0 4px 12px rgba(99,102,241,0.10);"
                    >
                        Edit
                    </Link>
                    <button
                        v-if="(invoice.status === 'draft' || invoice.status === 'sent') && invoice.can?.send"
                        class="clay-btn cursor-pointer inline-flex items-center gap-1.5 rounded-2xl bg-indigo-500 px-3.5 py-2.5 text-sm font-black text-white"
                        style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                        @click="send"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        {{ invoice.status === 'sent' ? 'Resend' : 'Send' }}
                    </button>
                    <button
                        v-if="invoice.can?.remind"
                        class="clay-btn cursor-pointer inline-flex items-center gap-1.5 rounded-2xl bg-amber-400 px-3.5 py-2.5 text-sm font-black text-white"
                        style="box-shadow: 0 8px 20px rgba(245,158,11,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                        @click="remind"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5" />
                        </svg>
                        Remind
                    </button>
                    <button
                        v-if="invoice.status === 'draft' && invoice.can?.delete"
                        class="cursor-pointer rounded-2xl border-2 border-rose-200 bg-white px-3.5 py-2.5 text-sm font-bold text-rose-500 transition-all hover:bg-rose-50"
                        @click="deleteInvoice"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <!-- Invoice card -->
            <div class="clay-card overflow-hidden bg-white">
                <!-- From / To -->
                <div class="grid grid-cols-2 gap-6 border-b-2 border-indigo-50 p-6">
                    <div>
                        <p class="mb-2 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">From</p>
                        <p class="font-bold text-indigo-900">{{ workspaceName }}</p>
                    </div>
                    <div>
                        <p class="mb-2 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Bill To</p>
                        <p class="font-bold text-indigo-900">{{ invoice.client?.name }}</p>
                        <p v-if="invoice.client?.company" class="mt-0.5 text-sm text-indigo-500">{{ invoice.client.company }}</p>
                        <p v-if="invoice.client?.email" class="text-sm text-indigo-400">{{ invoice.client.email }}</p>
                        <p v-if="invoice.client?.address" class="mt-0.5 text-sm text-indigo-400 whitespace-pre-line">{{ invoice.client.address }}</p>
                    </div>
                </div>

                <!-- Meta row -->
                <div class="grid grid-cols-3 gap-6 border-b-2 border-indigo-50 bg-indigo-50/40 px-6 py-4">
                    <div>
                        <p class="mb-1 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Invoice #</p>
                        <p class="font-bold text-indigo-900">{{ invoice.invoice_number }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Issue Date</p>
                        <p class="font-bold text-indigo-900">{{ invoice.issue_date }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Due Date</p>
                        <p class="font-bold text-indigo-900">{{ invoice.due_date }}</p>
                    </div>
                </div>

                <!-- Reminder notice -->
                <div v-if="invoice.reminders_sent > 0" class="border-b-2 border-amber-100 bg-amber-50 px-6 py-3">
                    <p class="text-xs font-bold text-amber-600">
                        {{ invoice.reminders_sent }} reminder{{ invoice.reminders_sent !== 1 ? 's' : '' }} sent
                        <span v-if="invoice.last_reminded_at">· last {{ invoice.last_reminded_at }}</span>
                    </p>
                </div>

                <!-- Line items -->
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-indigo-50 bg-indigo-50/40">
                            <th class="px-6 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Description</th>
                            <th class="w-20 px-4 py-3 text-right text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Qty</th>
                            <th class="w-28 px-4 py-3 text-right text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Unit Price</th>
                            <th class="w-28 px-6 py-3 text-right text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-50">
                        <tr v-for="item in invoice.items" :key="item.id">
                            <td class="px-6 py-4 font-medium text-indigo-900">{{ item.description }}</td>
                            <td class="px-4 py-4 text-right text-sm text-indigo-400">{{ Number(item.quantity).toFixed(2) }}</td>
                            <td class="px-4 py-4 text-right text-sm text-indigo-400">${{ Number(item.unit_price).toFixed(2) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-900">${{ Number(item.line_total).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Totals -->
                <div class="border-t-2 border-indigo-50 px-6 py-5">
                    <div class="ml-auto w-60 space-y-2.5">
                        <div class="flex justify-between text-sm font-medium text-indigo-400">
                            <span>Subtotal</span>
                            <span>${{ fmt(invoice.subtotal) }}</span>
                        </div>
                        <div v-if="Number(invoice.discount_percent) > 0" class="flex justify-between text-sm font-medium text-indigo-400">
                            <span>Discount ({{ Number(invoice.discount_percent) }}%)</span>
                            <span class="text-rose-500">-${{ fmt(invoice.discount_amount) }}</span>
                        </div>
                        <div v-if="Number(invoice.tax_percent) > 0" class="flex justify-between text-sm font-medium text-indigo-400">
                            <span>Tax ({{ Number(invoice.tax_percent) }}%)</span>
                            <span>${{ fmt(invoice.tax_amount) }}</span>
                        </div>
                        <div class="flex justify-between border-t-2 border-indigo-100 pt-3">
                            <span class="font-black text-indigo-900" style="font-family: 'Fredoka', sans-serif;">Total Due</span>
                            <span class="text-xl font-black text-indigo-600" style="font-family: 'Fredoka', sans-serif;">${{ fmt(invoice.total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div v-if="invoice.notes" class="border-t-2 border-indigo-50 px-6 py-5">
                    <p class="mb-2 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Notes</p>
                    <p class="text-sm font-medium text-indigo-700 whitespace-pre-line">{{ invoice.notes }}</p>
                </div>
            </div>

            <!-- Payment link -->
            <div
                v-if="paymentUrl && invoice.status !== 'paid' && invoice.status !== 'cancelled'"
                class="clay-card-emerald clay-card mt-5 bg-white p-5"
            >
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="font-bold text-emerald-800" style="font-family: 'Fredoka', sans-serif;">Payment Link</p>
                        <p class="mt-0.5 text-xs font-medium text-emerald-600">Share this link to collect payment online</p>
                    </div>
                    <button
                        class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-emerald-500 px-4 py-2.5 text-sm font-black text-white"
                        style="box-shadow: 0 8px 20px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.25);"
                        @click="copyPaymentLink"
                    >
                        <svg v-if="!copied" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ copied ? 'Copied!' : 'Copy Link' }}
                    </button>
                </div>
                <div class="rounded-2xl border-2 border-emerald-100 bg-emerald-50 px-4 py-3">
                    <p class="text-xs font-mono font-medium text-emerald-700 truncate">{{ paymentUrl }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
