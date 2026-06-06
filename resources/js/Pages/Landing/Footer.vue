<script setup>
    import { Link } from '@inertiajs/vue3'
</script>

<template>
    <footer class="border-t-2 border-indigo-100 px-5 py-8 sm:px-6">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 sm:flex-row">
            <Link href="/" class="cursor-pointer flex items-center gap-2" aria-label="Billable home">
                <div
                    class="flex h-7 w-7 items-center justify-center rounded-xl bg-indigo-500"
                    style="box-shadow: 0 4px 10px rgba(99,102,241,0.30);"
                >
                    <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-base font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif;">
                    bill<span class="text-indigo-500">able</span>
                </span>
            </Link>
            <p class="text-sm text-indigo-400">
                Built with <span class="text-rose-400">♥</span> by <a href="https://syahmidev.com" target="_blank" class="text-indigo-500">syahmidev</a>
            </p>
        </div>
    </footer>
</template>
