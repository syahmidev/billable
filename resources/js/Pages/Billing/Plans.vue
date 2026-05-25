<script setup>
import SeoHead from '@/Components/SeoHead.vue'
import { usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    plans: Array,
    selectedPlan: { type: String, default: null },
})

const page = usePage()
const user = page.props.auth.user

const loadingPlan = ref(null)

function subscribe(plan) {
    loadingPlan.value = plan.id
    router.post(`/plans/${plan.id}/subscribe`, {}, {
        onFinish: () => (loadingPlan.value = null),
    })
}

function isSelectedPlan(plan) {
    return props.selectedPlan === plan.slug
}

function cardShadow(plan) {
    if (isSelectedPlan(plan)) {
        return 'inset -2px -4px 8px rgba(255,255,255,0.80), inset 2px 2px 4px rgba(16,185,129,0.04), 6px 10px 32px rgba(16,185,129,0.12), 0 2px 4px rgba(0,0,0,0.05)'
    }
    if (plan.slug === 'pro') {
        return 'inset -2px -4px 8px rgba(255,255,255,0.80), inset 2px 2px 4px rgba(99,102,241,0.04), 6px 10px 32px rgba(99,102,241,0.16), 0 2px 4px rgba(0,0,0,0.05)'
    }
    return 'inset -2px -4px 8px rgba(255,255,255,0.80), inset 2px 2px 4px rgba(99,102,241,0.04), 6px 10px 32px rgba(99,102,241,0.08), 0 2px 4px rgba(0,0,0,0.04)'
}
</script>

<template>
    <SeoHead
        title="Choose Your Plan"
        description="Choose a Billable subscription plan for your workspace."
        canonical="/plans"
        robots="noindex, nofollow"
    />

    <div class="min-h-screen px-4 py-16" style="background: linear-gradient(160deg, #EDE9FF 0%, #F0F4FF 50%, #E8F5F0 100%);">
        <!-- Decorative blobs -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-indigo-200/40 blur-3xl"></div>
            <div class="absolute top-1/2 -right-32 h-80 w-80 rounded-full bg-violet-200/30 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-sky-200/25 blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-5xl mx-auto">
            <!-- Logo + heading -->
            <div class="text-center mb-12">
                <span
                    class="text-3xl font-black text-indigo-600"
                    style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;"
                >
                    bill<span class="text-indigo-400">able</span>
                </span>
                <h1
                    class="text-4xl font-bold text-indigo-950 mt-6 mb-3"
                    style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
                >
                    Choose your plan
                </h1>
                <p class="text-sm font-medium text-indigo-400">Start free, upgrade when you're ready</p>
            </div>

            <!-- Plan cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="relative flex flex-col rounded-3xl bg-white p-7 transition-all"
                    :class="
                        isSelectedPlan(plan)
                            ? 'border-3 border-emerald-300'
                            : plan.slug === 'pro'
                              ? 'border-3 border-indigo-300'
                              : 'border-2 border-indigo-100'"
                    :style="{ boxShadow: cardShadow(plan) }"
                >
                    <!-- Popular badge -->
                    <div v-if="plan.slug === 'pro'" class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                        <span class="inline-flex items-center gap-1 rounded-2xl bg-indigo-500 px-4 py-1.5 text-xs font-black text-white"
                            style="box-shadow: 0 4px 12px rgba(99,102,241,0.35);">
                            Most popular
                        </span>
                    </div>

                    <!-- Selected badge -->
                    <div v-if="isSelectedPlan(plan)" class="absolute right-4 top-4">
                        <span class="rounded-xl border-2 border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-extrabold text-emerald-600">
                            Selected
                        </span>
                    </div>

                    <!-- Plan name + price -->
                    <div class="mb-6">
                        <h2
                            class="text-xl font-bold text-indigo-900 mb-1"
                            style="font-family: 'Fredoka', sans-serif;"
                        >
                            {{ plan.name }}
                        </h2>
                        <div class="flex items-baseline gap-1">
                            <span
                                class="text-3xl font-black text-indigo-800"
                                style="font-family: 'Fredoka', sans-serif;"
                            >
                                {{ plan.is_free ? 'Free' : '$' + plan.price }}
                            </span>
                            <span v-if="!plan.is_free" class="text-sm font-bold text-indigo-400">/month</span>
                        </div>
                    </div>

                    <!-- Features -->
                    <ul class="space-y-3 flex-1 mb-8">
                        <li
                            v-for="feature in plan.features"
                            :key="feature"
                            class="flex items-start gap-2.5 text-sm font-medium text-indigo-600"
                        >
                            <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ feature }}
                        </li>
                    </ul>

                    <!-- CTA button -->
                    <button
                        @click="subscribe(plan)"
                        :disabled="loadingPlan !== null"
                        class="cursor-pointer w-full rounded-2xl py-3 text-sm font-black transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="plan.slug === 'pro'
                            ? 'clay-btn bg-indigo-500 text-white'
                            : 'border-2 border-indigo-200 bg-indigo-50 text-indigo-600 hover:border-indigo-300 hover:bg-indigo-100'"
                        :style="plan.slug === 'pro'
                            ? 'box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);'
                            : ''"
                    >
                        <span v-if="loadingPlan === plan.id">Redirecting…</span>
                        <span v-else-if="plan.is_free">Get started free</span>
                        <span v-else>Subscribe to {{ plan.name }}</span>
                    </button>
                </div>
            </div>

            <!-- Footer note -->
            <p class="text-center text-xs font-medium text-indigo-400 mt-8">
                Logged in as {{ user.email }} ·
                <button
                    @click="router.post('/logout')"
                    class="cursor-pointer font-bold text-indigo-500 hover:text-indigo-700 transition-colors"
                >
                    Sign out
                </button>
            </p>
        </div>
    </div>
</template>
