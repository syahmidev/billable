<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    billing: { type: Object, default: () => ({}) },
    plans: { type: Array, default: () => [] },
})

const loadingPlan = ref(null)

const currentPlan = computed(() => props.billing.current_plan)

function money(price) {
    return Number(price) === 0 ? 'Free' : '$' + Number(price).toLocaleString('en-US') + '/mo'
}

function statusLabel(status) {
    if (!status || status === 'none') return 'No plan'
    if (status === 'free') return 'Free plan'

    return status.charAt(0).toUpperCase() + status.slice(1)
}

function isCurrent(plan) {
    return currentPlan.value?.slug === plan.slug
}

function subscribe(plan) {
    loadingPlan.value = plan.id

    router.post(`/billing/plans/${plan.id}/subscribe`, {}, {
        onFinish: () => {
            loadingPlan.value = null
        },
    })
}
</script>

<template>
    <AppLayout>
        <div class="p-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Billing</h1>
                    <p class="mt-1 text-sm text-gray-400">Manage your workspace plan and subscription.</p>
                </div>

                <a
                    v-if="billing.portal_url"
                    :href="billing.portal_url"
                    class="inline-flex items-center justify-center rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-violet-500"
                >
                    Manage in Stripe
                </a>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="bg-gray-900 border border-white/10 rounded-xl p-5">
                    <p class="mb-2 text-xs uppercase tracking-wider text-gray-500">Current Plan</p>
                    <p class="text-2xl font-semibold text-white">{{ currentPlan?.name ?? 'No plan' }}</p>
                    <p class="mt-1 text-sm text-gray-400">{{ currentPlan ? money(currentPlan.price) : 'Choose a plan to continue.' }}</p>
                </div>

                <div class="bg-gray-900 border border-white/10 rounded-xl p-5">
                    <p class="mb-2 text-xs uppercase tracking-wider text-gray-500">Status</p>
                    <p class="text-2xl font-semibold text-white">{{ statusLabel(billing.status) }}</p>
                    <p class="mt-1 text-sm text-gray-400">
                        {{ billing.is_subscribed ? 'Stripe subscription is active for this workspace.' : 'No paid subscription is active.' }}
                    </p>
                </div>

                <div class="bg-gray-900 border border-white/10 rounded-xl p-5">
                    <p class="mb-2 text-xs uppercase tracking-wider text-gray-500">Renewal</p>
                    <p class="text-2xl font-semibold text-white">{{ billing.ends_at ?? 'Active' }}</p>
                    <p class="mt-1 text-sm text-gray-400">
                        {{ billing.ends_at ? 'Your subscription is scheduled to end.' : 'No cancellation is scheduled.' }}
                    </p>
                </div>
            </div>

            <div class="bg-gray-900 border border-white/10 rounded-xl p-6">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white">Available Plans</h2>
                        <p class="mt-1 text-xs text-gray-500">
                            Free workspaces can upgrade here. Paid subscriptions are managed in Stripe.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="relative flex flex-col rounded-xl border p-5"
                        :class="isCurrent(plan) ? 'border-violet-500 bg-violet-500/5' : 'border-white/10 bg-gray-950/40'"
                    >
                        <div v-if="isCurrent(plan)" class="absolute right-4 top-4 rounded-full bg-violet-500/20 px-2.5 py-1 text-xs font-medium text-violet-300">
                            Current
                        </div>

                        <div class="mb-5">
                            <h3 class="text-lg font-semibold text-white">{{ plan.name }}</h3>
                            <p class="mt-1 text-2xl font-bold text-white">{{ money(plan.price) }}</p>
                        </div>

                        <ul class="mb-6 flex-1 space-y-3">
                            <li
                                v-for="feature in plan.features"
                                :key="feature"
                                class="flex items-start gap-2 text-sm text-gray-300"
                            >
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ feature }}
                            </li>
                        </ul>

                        <a
                            v-if="billing.is_subscribed && !billing.is_free"
                            :href="billing.portal_url"
                            class="w-full rounded-lg border border-white/10 bg-white/5 py-2.5 text-center text-sm font-medium text-white transition-colors hover:bg-white/10"
                        >
                            Manage subscription
                        </a>

                        <button
                            v-else
                            type="button"
                            :disabled="loadingPlan !== null || isCurrent(plan)"
                            class="w-full rounded-lg py-2.5 text-sm font-medium transition-colors disabled:cursor-not-allowed disabled:opacity-50"
                            :class="plan.is_free ? 'border border-white/10 bg-white/5 text-white hover:bg-white/10' : 'bg-violet-600 text-white hover:bg-violet-500'"
                            @click="subscribe(plan)"
                        >
                            <span v-if="loadingPlan === plan.id">Redirecting...</span>
                            <span v-else-if="isCurrent(plan)">Current plan</span>
                            <span v-else-if="plan.is_free">Switch to free</span>
                            <span v-else>Upgrade to {{ plan.name }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
