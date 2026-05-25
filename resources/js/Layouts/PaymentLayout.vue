<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
    title: { type: String, default: 'Secure Invoice Payment' },
})
</script>

<template>
    <Head :title="title">
        <meta name="robots" content="noindex, nofollow" />
    </Head>

    <div class="min-h-screen flex flex-col" style="background: linear-gradient(160deg, #EDE9FF 0%, #F0F4FF 50%, #E8F5F0 100%);">
        <!-- Decorative blobs -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-32 -left-32 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl"></div>
            <div class="absolute top-1/2 -right-24 h-64 w-64 rounded-full bg-violet-200/30 blur-3xl"></div>
        </div>

        <!-- Header -->
        <header class="relative z-10 border-b-2 border-indigo-100/60 bg-white/70 backdrop-blur-sm px-6 py-4">
            <div class="max-w-2xl mx-auto flex items-center justify-between">
                <span
                    class="text-xl font-black text-indigo-600"
                    style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;"
                >
                    bill<span class="text-indigo-400">able</span>
                </span>
                <span class="flex items-center gap-1.5 text-xs font-bold text-indigo-400">
                    <svg class="h-3.5 w-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Secure payment powered by Stripe
                </span>
            </div>
        </header>

        <!-- Main content -->
        <main class="relative z-10 flex-1 flex items-start justify-center px-4 py-10">
            <div class="w-full max-w-2xl">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t-2 border-indigo-100/60 bg-white/50 px-6 py-4 text-center">
            <p class="text-xs font-medium text-indigo-300">
                Protected by 256-bit SSL encryption · Powered by Stripe
            </p>
        </footer>
    </div>
</template>
