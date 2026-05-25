<script setup>
import PaymentLayout from '@/Layouts/PaymentLayout.vue'
import { loadStripe } from '@stripe/stripe-js'
import { onMounted, ref } from 'vue'

const props = defineProps({
    invoice: Object,
    workspaceName: String,
    stripeKey: String,
    alreadyPaid: Boolean,
})

const stripe = ref(null)
const elements = ref(null)
const loading = ref(true)
const processing = ref(false)
const success = ref(props.alreadyPaid)
const errorMsg = ref(null)

onMounted(async () => {
    if (props.alreadyPaid) { loading.value = false; return }

    stripe.value = await loadStripe(props.stripeKey)

    const csrfToken = decodeURIComponent(
        document.cookie.split('; ').find(r => r.startsWith('XSRF-TOKEN='))?.split('=')[1] ?? ''
    )

    const res = await fetch(`/pay/${props.invoice.payment_token}/intent`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-XSRF-TOKEN': csrfToken,
        },
    })

    if (!res.ok) {
        errorMsg.value = 'Failed to initialise payment. Please try again.'
        loading.value = false
        return
    }

    const { clientSecret } = await res.json()

    elements.value = stripe.value.elements({
        clientSecret,
        appearance: {
            theme: 'stripe',
            variables: {
                colorPrimary: '#6366F1',
                colorBackground: '#ffffff',
                colorText: '#1e1b4b',
                colorDanger: '#f43f5e',
                borderRadius: '16px',
                fontFamily: 'Nunito, ui-sans-serif, system-ui, sans-serif',
                fontSizeBase: '14px',
            },
        },
    })

    const paymentElement = elements.value.create('payment')
    paymentElement.mount('#payment-element')
    loading.value = false
})

async function pay() {
    if (!stripe.value || !elements.value) return
    processing.value = true
    errorMsg.value = null

    const { error } = await stripe.value.confirmPayment({
        elements: elements.value,
        confirmParams: { return_url: window.location.href },
        redirect: 'if_required',
    })

    if (error) {
        errorMsg.value = error.message
        processing.value = false
    } else {
        success.value = true
        processing.value = false
    }
}
</script>

<template>
    <PaymentLayout :title="`Pay ${invoice.invoice_number}`">
        <!-- Invoice summary card -->
        <div class="clay-card bg-white p-6 mb-5">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Invoice from</p>
                    <p class="text-base font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif;">{{ workspaceName }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Amount due</p>
                    <p class="text-2xl font-black text-indigo-600" style="font-family: 'Fredoka', sans-serif;">${{ invoice.total.toFixed(2) }}</p>
                </div>
            </div>

            <div class="border-t-2 border-indigo-50 pt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Invoice #</p>
                    <p class="text-sm font-bold text-indigo-700">{{ invoice.invoice_number }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Bill to</p>
                    <p class="text-sm font-bold text-indigo-700">{{ invoice.client.name }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Issue date</p>
                    <p class="text-sm font-medium text-indigo-500">{{ invoice.issue_date }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 mb-1">Due date</p>
                    <p class="text-sm font-medium text-indigo-500">{{ invoice.due_date }}</p>
                </div>
            </div>

            <!-- Line items -->
            <div class="border-t-2 border-indigo-50 mt-4 pt-4 space-y-2">
                <div v-for="item in invoice.items" :key="item.description" class="flex justify-between text-sm">
                    <span class="font-medium text-indigo-400">{{ item.description }} × {{ item.quantity }}</span>
                    <span class="font-bold text-indigo-700">${{ item.line_total.toFixed(2) }}</span>
                </div>
            </div>
        </div>

        <!-- Already paid -->
        <div v-if="success" class="clay-card-emerald bg-white p-10 text-center">
            <div
                class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-100"
                style="box-shadow: 0 6px 20px rgba(16,185,129,0.20);"
            >
                <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-emerald-800 mb-2" style="font-family: 'Fredoka', sans-serif;">Payment Complete!</h2>
            <p class="text-sm font-medium text-emerald-600">
                {{ alreadyPaid ? 'This invoice has already been paid.' : 'Your payment was successful. A receipt has been sent to your email.' }}
            </p>
        </div>

        <!-- Payment form -->
        <div v-else class="clay-card bg-white p-6">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-indigo-400 mb-5">Payment Details</h2>

            <!-- Loading -->
            <div v-if="loading" class="flex items-center justify-center py-10">
                <div class="h-8 w-8 rounded-full border-4 border-indigo-200 border-t-indigo-500 animate-spin"></div>
            </div>

            <!-- Error -->
            <div v-if="errorMsg" class="mb-4 flex items-center gap-2.5 rounded-2xl border-2 border-rose-200 bg-rose-50 px-4 py-3">
                <svg class="h-4 w-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-semibold text-rose-700">{{ errorMsg }}</p>
            </div>

            <!-- Stripe element mount point -->
            <div id="payment-element" class="mb-6"></div>

            <!-- Pay button -->
            <button
                v-if="!loading"
                @click="pay"
                :disabled="processing"
                class="clay-btn cursor-pointer w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500 py-3.5 text-sm font-black text-white disabled:opacity-50"
                style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
            >
                <svg v-if="processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ processing ? 'Processing…' : `Pay $${invoice.total.toFixed(2)}` }}
            </button>

            <p class="mt-3 text-center text-xs font-medium text-indigo-300">
                Your payment is secured and encrypted by Stripe
            </p>
        </div>
    </PaymentLayout>
</template>
