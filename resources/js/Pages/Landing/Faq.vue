<script setup>
    const props = defineProps({
        faqs: { type: Array, default: () => [] },
    })
</script>

<template>
    <section class="mx-auto max-w-5xl py-16 sm:py-20">
        <div class="mb-12 text-center">
            <p class="mb-2 text-sm font-bold uppercase tracking-widest text-amber-500">FAQ</p>
            <h2
                class="text-4xl font-bold text-indigo-950 sm:text-5xl"
                style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
            >
                Got questions?
            </h2>
            <p class="mt-4 text-lg text-indigo-800/60">Everything you need to know before creating a workspace.</p>
        </div>

        <div class="space-y-3">
            <details
                v-for="faq in faqs"
                :key="faq.question"
                class="clay-card group cursor-pointer bg-white px-6 py-5 transition-transform duration-150 open:-translate-y-0.5"
            >
                <summary class="flex list-none items-center justify-between gap-4 text-base font-extrabold text-indigo-900 focus:outline-none" style="font-family: 'Fredoka', sans-serif;">
                    {{ faq.question }}
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-indigo-100 transition-transform duration-200 group-open:rotate-180">
                        <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </summary>
                <p class="mt-3 text-base leading-7 text-indigo-800/60">{{ faq.answer }}</p>
            </details>
        </div>
    </section>
</template>
