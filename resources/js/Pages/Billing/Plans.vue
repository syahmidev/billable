<script setup>
import SeoHead from '@/Components/SeoHead.vue'
import { usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    plans: Array,
})

const page = usePage()
const user = page.props.auth.user

const loadingPlan = ref(null)

function subscribe(plan) {
    loadingPlan.value = plan.id
    router.post(`/plans/${plan.id}/subscribe`, {}, {
        onFinish: () => loadingPlan.value = null,
    })
}
</script>

<template>
    <SeoHead
        title="Choose Your Plan"
        description="Choose a Billable subscription plan for your workspace."
        canonical="/plans"
        robots="noindex, nofollow"
    />

    <div class="min-h-screen bg-gray-950 px-4 py-16">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <span class="text-2xl font-bold text-white tracking-tight">
                    bill<span class="text-violet-500">able</span>
                </span>
                <h1 class="text-3xl font-bold text-white mt-6 mb-3">Choose your plan</h1>
                <p class="text-gray-400">Start free, upgrade when you're ready</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="relative bg-gray-900 border rounded-2xl p-7 flex flex-col"
                    :class="plan.slug === 'pro' ? 'border-violet-500' : 'border-white/10'"
                >
                    <!-- Popular badge -->
                    <div v-if="plan.slug === 'pro'" class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="bg-violet-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                            Most popular
                        </span>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-white mb-1">{{ plan.name }}</h2>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-white">{{ plan.is_free ? 'Free' : '$' + plan.price }}</span>
                            <span v-if="!plan.is_free" class="text-sm text-gray-500">/month</span>
                        </div>
                    </div>

                    <ul class="space-y-3 flex-1 mb-8">
                        <li
                            v-for="feature in plan.features"
                            :key="feature"
                            class="flex items-start gap-2.5 text-sm text-gray-300"
                        >
                            <svg class="w-4 h-4 text-violet-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ feature }}
                        </li>
                    </ul>

                    <button
                        @click="subscribe(plan)"
                        :disabled="loadingPlan !== null"
                        class="w-full py-2.5 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="plan.slug === 'pro'
                            ? 'bg-violet-600 hover:bg-violet-500 text-white'
                            : 'bg-white/5 hover:bg-white/10 text-white border border-white/10'"
                    >
                        <span v-if="loadingPlan === plan.id">Redirecting...</span>
                        <span v-else-if="plan.is_free">Get started free</span>
                        <span v-else>Subscribe to {{ plan.name }}</span>
                    </button>
                </div>
            </div>

            <p class="text-center text-xs text-gray-600 mt-8">
                Logged in as {{ user.email }} ·
                <button @click="router.post('/logout')" class="text-gray-500 hover:text-white transition-colors">Sign out</button>
            </p>
        </div>
    </div>
</template>
