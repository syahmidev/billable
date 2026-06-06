<script setup>
    import { Link, usePage } from '@inertiajs/vue3'
    import { computed } from 'vue'

    const props = defineProps({
        workspaceUrl: { type: String, default: null },
    })

    const page = usePage()
    const isLoggedIn = computed(() => !!page.props.auth.user)

    function registerUrl() {
        return '/register'
    }
</script>

<template>
    <nav class="sticky top-0 z-50 px-5 pt-4 pb-0 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <div
                class="clay-card flex items-center justify-between px-5 py-3"
                style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px);"
            >
                <Link href="/" class="cursor-pointer flex items-center gap-2.5" aria-label="Billable home">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-2xl bg-indigo-500"
                        style="box-shadow: 0 4px 12px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.3);"
                    >
                        <svg class="h-4.5 w-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">
                        bill<span class="text-indigo-500">able</span>
                    </span>
                </Link>

                <div class="flex items-center gap-2">
                    <template v-if="isLoggedIn">
                        <a
                            :href="workspaceUrl"
                            class="clay-btn cursor-pointer inline-flex items-center gap-1.5 rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-bold text-white"
                            style="box-shadow: 0 6px 20px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.25);"
                        >
                            Dashboard
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="cursor-pointer rounded-2xl px-4 py-2 text-sm font-semibold text-indigo-600 transition-colors hover:bg-indigo-50"
                        >
                            Sign in
                        </Link>
                        <Link
                            :href="registerUrl()"
                            class="clay-btn cursor-pointer inline-flex items-center gap-1.5 rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-bold text-white"
                            style="box-shadow: 0 6px 20px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.25);"
                        >
                            Start free
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</template>
