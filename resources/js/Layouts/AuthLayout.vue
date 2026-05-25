<script setup>
    import { Head, Link } from '@inertiajs/vue3'

    defineProps({
        title: { type: String, default: 'Account' },
    })
</script>

<template>
    <Head :title="title">
        <meta name="robots" content="noindex, nofollow" />
    </Head>

    <div
        class="relative min-h-screen flex items-center justify-center px-4 py-12 overflow-hidden"
        style="background: linear-gradient(160deg, #EDE9FF 0%, #F0F4FF 50%, #E8F5F0 100%);"
    >
        <!-- Background blobs -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -top-24 -left-24 h-80 w-80 rounded-full bg-indigo-300/20 blur-[80px]"></div>
            <div class="absolute -bottom-20 -right-20 h-72 w-72 rounded-full bg-emerald-300/20 blur-[80px]"></div>
            <div class="absolute top-1/3 right-1/4 h-48 w-48 rounded-full bg-amber-300/15 blur-[60px]"></div>
        </div>

        <!-- Floating decorative shapes -->
        <div class="pointer-events-none absolute top-16 right-[15%] h-10 w-10 rotate-12 rounded-xl bg-amber-300 opacity-50" style="box-shadow: 0 6px 16px rgba(245,158,11,0.25);"></div>
        <div class="pointer-events-none absolute bottom-24 left-[12%] h-8 w-8 -rotate-12 rounded-xl bg-emerald-400 opacity-40" style="box-shadow: 0 6px 16px rgba(16,185,129,0.25);"></div>
        <div class="pointer-events-none absolute top-1/3 left-[8%] h-6 w-6 rotate-6 rounded-lg bg-rose-400 opacity-35"></div>

        <div class="relative w-full max-w-md">
            <!-- Logo -->
            <div class="mb-8 flex justify-center">
                <Link href="/" class="cursor-pointer flex items-center gap-2.5" aria-label="Billable home">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-500"
                        style="box-shadow: 0 6px 16px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.25);"
                    >
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span
                        class="text-2xl font-bold text-indigo-900"
                        style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;"
                    >
                        bill<span class="text-indigo-500">able</span>
                    </span>
                </Link>
            </div>

            <!-- Auth card -->
            <div class="clay-card bg-white/90 p-8 backdrop-blur-sm">
                <slot />
            </div>
        </div>
    </div>
</template>
