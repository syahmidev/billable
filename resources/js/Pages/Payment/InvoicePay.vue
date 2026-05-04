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
            theme: 'night',
            variables: {
                colorPrimary: '#7c3aed',
                colorBackground: '#111827',
                colorText: '#f9fafb',
                colorDanger: '#ef4444',
                borderRadius: '8px',
                fontFamily: 'ui-sans-serif, system-ui, sans-serif',
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
        <!-- Invoice summary -->
        <div class="bg-gray-900 border border-white/10 rounded-xl p-6 mb-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Invoice from</p>
                    <p class="text-base font-semibold text-white">{{ workspaceName }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Amount due</p>
                    <p class="text-2xl font-bold text-white">${{ invoice.total.toFixed(2) }}</p>
                </div>
            </div>

            <div class="border-t border-white/10 pt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <p class="text-gray-600 mb-0.5">Invoice #</p>
                    <p class="text-gray-300 font-medium">{{ invoice.invoice_number }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-0.5">Bill to</p>
                    <p class="text-gray-300 font-medium">{{ invoice.client.name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-0.5">Issue date</p>
                    <p class="text-gray-300">{{ invoice.issue_date }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-0.5">Due date</p>
                    <p class="text-gray-300">{{ invoice.due_date }}</p>
                </div>
            </div>

            <!-- Line items (collapsed summary) -->
            <div class="border-t border-white/10 mt-4 pt-4 space-y-1.5">
                <div v-for="item in invoice.items" :key="item.description" class="flex justify-between text-xs">
                    <span class="text-gray-400">{{ item.description }} × {{ item.quantity }}</span>
                    <span class="text-gray-300">${{ item.line_total.toFixed(2) }}</span>
                </div>
            </div>
        </div>

        <!-- Already paid state -->
        <div v-if="success" class="bg-green-500/10 border border-green-500/20 rounded-xl p-8 text-center">
            <div class="w-14 h-14 rounded-full bg-green-500/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-white mb-2">Payment Complete!</h2>
            <p class="text-sm text-gray-400">
                {{ alreadyPaid ? 'This invoice has already been paid.' : 'Your payment was successful. A receipt has been sent to your email.' }}
            </p>
        </div>

        <!-- Payment form -->
        <div v-else class="bg-gray-900 border border-white/10 rounded-xl p-6">
            <h2 class="text-sm font-semibold text-white mb-5">Payment Details</h2>

            <!-- Loading state -->
            <div v-if="loading" class="flex items-center justify-center py-10">
                <div class="w-6 h-6 border-2 border-violet-500 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <!-- Error state -->
            <div v-if="errorMsg" class="mb-4 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-lg text-sm text-red-400">
                {{ errorMsg }}
            </div>

            <!-- Stripe element -->
            <div id="payment-element" class="mb-6"></div>

            <!-- Pay button -->
            <button
                v-if="!loading"
                @click="pay"
                :disabled="processing"
                class="w-full py-3 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
            >
                <svg v-if="processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ processing ? 'Processing...' : `Pay $${invoice.total.toFixed(2)}` }}
            </button>

            <p class="text-xs text-gray-600 text-center mt-3">
                Your payment is secured and encrypted by Stripe
            </p>
        </div>
    </PaymentLayout>
</template>
