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
    if (props.billing.status_label) return props.billing.status_label
    if (!status || status === 'none') return 'No plan'
    if (status === 'free') return 'Free plan'
    return status.charAt(0).toUpperCase() + status.slice(1)
}

function statusMessage() {
    if (props.billing.is_managed_by_owner && props.billing.status_message) {
        return props.billing.status_message.replace('this workspace', 'the workspace')
    }
    if (props.billing.status_message) return props.billing.status_message
    if (props.billing.is_subscribed) return 'Stripe subscription is active for this workspace.'
    return 'No paid subscription is active.'
}

function statusToneClasses(tone) {
    if (tone === 'success') return 'bg-emerald-100 text-emerald-700 border-emerald-200'
    if (tone === 'danger')  return 'bg-rose-100 text-rose-600 border-rose-200'
    if (tone === 'warning') return 'bg-amber-100 text-amber-700 border-amber-200'
    return 'bg-indigo-50 text-indigo-500 border-indigo-100'
}

function alertBg(tone) {
    if (tone === 'danger')  return 'border-rose-200 bg-rose-50'
    return 'border-amber-200 bg-amber-50'
}

function alertText(tone) {
    if (tone === 'danger')  return 'text-rose-700'
    return 'text-amber-800'
}

function isCurrent(plan) {
    return currentPlan.value?.slug === plan.slug
}

function subscribe(plan) {
    loadingPlan.value = plan.id
    router.post(`/billing/plans/${plan.id}/subscribe`, {}, {
        onFinish: () => { loadingPlan.value = null },
    })
}
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">Billing</h1>
                    <p class="mt-1 text-sm font-medium text-indigo-400">
                        {{ billing.can_manage ? 'Manage your workspace plan and subscription.' : 'View the workspace plan managed by the owner.' }}
                    </p>
                </div>
                <a
                    v-if="billing.can_manage && billing.portal_url"
                    :href="billing.portal_url"
                    class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-indigo-500 px-4 py-2.5 text-sm font-black text-white"
                    style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Manage in Stripe
                </a>
            </div>

            <!-- Owner-managed notice -->
            <div
                v-if="billing.is_managed_by_owner"
                class="mb-6 rounded-2xl border-2 border-sky-200 bg-sky-50 p-5"
            >
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-sky-800">Workspace billing is managed by the owner</p>
                        <p class="mt-1 text-sm font-medium text-sky-600">
                            {{ billing.owner?.name }} controls plan changes and Stripe billing for this workspace.
                        </p>
                    </div>
                    <div class="rounded-2xl border-2 border-sky-200 bg-white px-4 py-2.5">
                        <p class="text-xs font-bold text-sky-500">{{ billing.owner?.email }}</p>
                    </div>
                </div>
            </div>

            <!-- Needs attention alert -->
            <div
                v-if="billing.needs_attention"
                class="mb-6 rounded-2xl border-2 p-5"
                :class="alertBg(billing.status_tone)"
            >
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-bold" :class="alertText(billing.status_tone)">{{ statusLabel(billing.status) }}</p>
                        <p class="mt-1 text-sm font-medium text-indigo-500">{{ statusMessage() }}</p>
                    </div>
                    <a
                        v-if="billing.can_manage && billing.portal_url"
                        :href="billing.portal_url"
                        class="clay-btn-secondary cursor-pointer inline-flex items-center gap-2 rounded-2xl border-2 border-indigo-200 bg-white px-4 py-2.5 text-sm font-bold text-indigo-600"
                        style="box-shadow: 0 4px 12px rgba(99,102,241,0.10);"
                    >
                        Open billing portal
                    </a>
                </div>
            </div>

            <!-- Stat cards -->
            <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <!-- Current plan -->
                <div class="clay-card bg-white p-5">
                    <p class="mb-2 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Workspace Plan</p>
                    <p class="text-2xl font-black text-indigo-900" style="font-family: 'Fredoka', sans-serif;">
                        {{ currentPlan?.name ?? 'No plan' }}
                    </p>
                    <p class="mt-1 text-sm font-medium text-indigo-400">
                        {{ currentPlan ? money(currentPlan.price) : billing.can_manage ? 'Choose a plan to continue.' : 'The owner has not selected a plan yet.' }}
                    </p>
                </div>

                <!-- Status -->
                <div class="clay-card bg-white p-5">
                    <p class="mb-2 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Status</p>
                    <span
                        class="inline-flex rounded-xl border-2 px-3 py-1.5 text-sm font-bold"
                        :class="statusToneClasses(billing.status_tone)"
                    >
                        {{ statusLabel(billing.status) }}
                    </span>
                    <p class="mt-3 text-sm font-medium text-indigo-400">{{ statusMessage() }}</p>
                </div>

                <!-- Renewal -->
                <div class="clay-card bg-white p-5">
                    <p class="mb-2 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Renewal</p>
                    <p class="text-2xl font-black text-indigo-900" style="font-family: 'Fredoka', sans-serif;">
                        {{ billing.ends_at ?? 'Active' }}
                    </p>
                    <p class="mt-1 text-sm font-medium text-indigo-400">
                        {{ billing.ends_at ? 'Subscription is scheduled to end.' : 'No cancellation is scheduled.' }}
                    </p>
                </div>
            </div>

            <!-- Available plans (owner/can-manage only) -->
            <div v-if="billing.can_manage" class="clay-card bg-white p-6">
                <div class="mb-6">
                    <h2 class="text-xs font-extrabold uppercase tracking-wider text-indigo-400">Available Plans</h2>
                    <p class="mt-1 text-sm font-medium text-indigo-400">
                        Free workspaces can upgrade here. Paid subscriptions are managed in Stripe.
                    </p>
                </div>

                <div v-if="plans.length" class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="relative flex flex-col rounded-2xl border-2 p-5 transition-all"
                        :class="isCurrent(plan)
                            ? 'border-indigo-300 bg-indigo-50/60'
                            : 'border-indigo-100 bg-indigo-50/30 hover:border-indigo-200'"
                    >
                        <!-- Current badge -->
                        <div
                            v-if="isCurrent(plan)"
                            class="absolute right-4 top-4 rounded-xl border-2 border-indigo-200 bg-indigo-100 px-2.5 py-1 text-[10px] font-extrabold text-indigo-600"
                        >
                            Current
                        </div>

                        <div class="mb-5">
                            <h3 class="text-lg font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif;">{{ plan.name }}</h3>
                            <p class="mt-1 text-2xl font-black text-indigo-800" style="font-family: 'Fredoka', sans-serif;">{{ money(plan.price) }}</p>
                        </div>

                        <ul class="mb-6 flex-1 space-y-3">
                            <li
                                v-for="feature in plan.features"
                                :key="feature"
                                class="flex items-start gap-2 text-sm font-medium text-indigo-600"
                            >
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ feature }}
                            </li>
                        </ul>

                        <!-- Already subscribed: link to portal -->
                        <a
                            v-if="billing.is_subscribed && !billing.is_free && billing.portal_url"
                            :href="billing.portal_url"
                            class="cursor-pointer w-full rounded-2xl border-2 border-indigo-200 bg-white px-4 py-2.5 text-center text-sm font-bold text-indigo-600 transition-all hover:border-indigo-300 hover:bg-indigo-50"
                        >
                            Manage subscription
                        </a>

                        <!-- Free/upgrade button -->
                        <button
                            v-else
                            type="button"
                            :disabled="loadingPlan !== null || isCurrent(plan)"
                            class="cursor-pointer w-full rounded-2xl py-2.5 text-sm font-black transition-all disabled:cursor-not-allowed disabled:opacity-50"
                            :class="plan.is_free
                                ? 'border-2 border-indigo-200 bg-white text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50'
                                : 'clay-btn bg-indigo-500 text-white'"
                            :style="!plan.is_free ? 'box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);' : ''"
                            @click="subscribe(plan)"
                        >
                            <span v-if="loadingPlan === plan.id">Redirecting…</span>
                            <span v-else-if="isCurrent(plan)">Current plan</span>
                            <span v-else-if="plan.is_free">Switch to free</span>
                            <span v-else>Upgrade to {{ plan.name }}</span>
                        </button>
                    </div>
                </div>

                <!-- No plans -->
                <div v-else class="rounded-2xl border-2 border-indigo-100 bg-indigo-50/40 p-12 text-center">
                    <div
                        class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-100"
                        style="box-shadow: 0 6px 20px rgba(99,102,241,0.15);"
                    >
                        <svg class="h-7 w-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <p class="text-lg font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif;">No plans available</p>
                    <p class="mt-1 text-sm font-medium text-indigo-400">Ask a platform admin to create and activate billing plans.</p>
                </div>
            </div>

            <!-- Non-owner: owner-only message -->
            <div v-else class="clay-card bg-white p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold uppercase tracking-wider text-indigo-400">Plan changes are owner-only</h2>
                        <p class="mt-1 text-sm font-medium text-indigo-500">
                            Ask {{ billing.owner?.name }} to change plans, update payment details, or open the Stripe portal.
                        </p>
                    </div>
                    <div class="rounded-2xl border-2 border-indigo-100 bg-indigo-50/60 px-5 py-4">
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Billing Owner</p>
                        <p class="mt-1 text-sm font-bold text-indigo-900">{{ billing.owner?.name }}</p>
                        <p class="text-xs font-medium text-indigo-400">{{ billing.owner?.email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
