<script setup>
    import { Link } from '@inertiajs/vue3'

    const props = defineProps({
        plans: { type: Array, default: () => [] },
    })

    function formatPrice(price) {
        return Number(price) === 0 ? 'Free' : `$${Number(price)}/mo`
    }

    function isPro(plan) {
        return plan.slug === 'pro'
    }

    function registerUrl(plan = null) {
        return plan?.slug ? `/register?plan=${encodeURIComponent(plan.slug)}` : '/register'
    }
</script>

<template>
    <section id="pricing" class="mx-auto max-w-6xl py-16 sm:py-20">
        <div class="mb-12 text-center">
            <p class="mb-2 text-sm font-bold uppercase tracking-widest text-indigo-400">Pricing</p>
            <h2
                class="text-4xl font-bold text-indigo-950 sm:text-5xl"
                style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
            >
                Start free. Grow when ready.
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-indigo-800/60">
                Pick the plan that fits your team. Switch anytime — no surprises.
            </p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <article
                v-for="plan in plans"
                :key="plan.slug"
                :class="[
                    'relative flex flex-col rounded-3xl border-3 p-7 transition-transform duration-200 hover:-translate-y-1',
                    isPro(plan)
                        ? 'clay-card-indigo border-indigo-300 bg-indigo-50'
                        : 'clay-card bg-white',
                ]"
            >
                <div v-if="isPro(plan)" class="absolute -top-4 left-1/2 -translate-x-1/2">
                    <span
                        class="clay-btn inline-flex items-center gap-1.5 rounded-2xl bg-indigo-500 px-4 py-1.5 text-xs font-black text-white"
                        style="box-shadow: 0 6px 16px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.25);"
                    >
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        Most popular
                    </span>
                </div>

                <div class="mb-6">
                    <h3
                        class="text-xl font-bold capitalize"
                        style="font-family: 'Fredoka', sans-serif;"
                        :class="isPro(plan) ? 'text-indigo-900' : 'text-indigo-950'"
                    >
                        {{ plan.name }}
                    </h3>
                    <div class="mt-3">
                        <span
                            class="text-5xl font-black"
                            style="font-family: 'Fredoka', sans-serif;"
                            :class="isPro(plan) ? 'text-indigo-600' : 'text-indigo-950'"
                        >
                            {{ formatPrice(plan.price) }}
                        </span>
                    </div>
                </div>

                <ul class="mb-8 flex-1 space-y-3">
                    <li
                        v-for="feature in plan.features"
                        :key="feature"
                        class="flex items-start gap-2.5 text-sm text-indigo-800/70"
                    >
                        <span
                            class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full"
                            :class="isPro(plan) ? 'bg-indigo-500 text-white' : 'bg-emerald-100 text-emerald-600'"
                        >
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <span>{{ feature }}</span>
                    </li>
                </ul>

                <Link
                    :href="registerUrl(plan)"
                    :class="[
                        'clay-btn cursor-pointer block rounded-2xl py-3.5 text-center text-sm font-black transition-colors',
                        isPro(plan)
                            ? 'bg-indigo-500 text-white'
                            : 'border-2 border-indigo-200 bg-white text-indigo-600',
                    ]"
                    :style="isPro(plan) ? 'box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);' : 'box-shadow: 0 4px 12px rgba(99,102,241,0.10);'"
                >
                    {{ Number(plan.price) === 0 ? 'Start free' : 'Choose ' + plan.name }}
                </Link>
            </article>
        </div>
    </section>
</template>
