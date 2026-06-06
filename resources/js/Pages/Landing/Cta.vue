<script setup>
    import { Link } from '@inertiajs/vue3'

    function registerUrl() {
        return '/register'
    }
</script>

<template>
    <section class="mx-auto max-w-6xl pb-20 sm:pb-24">
        <div
            class="relative overflow-hidden rounded-3xl p-10 text-center md:p-16"
            style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 50%, #A78BFA 100%); box-shadow: 0 20px 60px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.20), inset -2px -4px 8px rgba(0,0,0,0.10);"
        >
            <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -left-6 bottom-0 h-24 w-24 rounded-full bg-white/8"></div>
            <div class="pointer-events-none absolute right-[20%] -bottom-4 h-16 w-16 rounded-full bg-amber-300/30"></div>
            <div class="pointer-events-none absolute left-[25%] -top-4 h-12 w-12 rounded-full bg-emerald-300/30"></div>

            <div class="relative">
                <h2
                    class="text-4xl font-bold text-white sm:text-5xl"
                    style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
                >
                    Ready to make billing easier?
                </h2>
                <p class="mx-auto mt-4 max-w-xl text-lg text-white/75">
                    Create your workspace, add your first client, and send your first invoice — no credit card needed.
                </p>
                <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                    <Link
                        :href="registerUrl()"
                        class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-white px-8 py-4 text-base font-black text-indigo-600"
                        style="box-shadow: 0 8px 24px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.9);"
                    >
                        Create free workspace
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                    <a
                        href="#pricing"
                        class="cursor-pointer text-base font-semibold text-white/75 transition-colors hover:text-white"
                    >
                        See pricing →
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
