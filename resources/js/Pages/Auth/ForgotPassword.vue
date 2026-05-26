<script setup>
    import AuthLayout from '@/Layouts/AuthLayout.vue'
    import { useForm, usePage } from '@inertiajs/vue3'
    import { computed } from 'vue'

    const flash = computed(() => usePage().props.flash)

    const form = useForm({ email: '' })

    function submit() {
        form.post('/forgot-password', {
            onFinish: () => form.reset(),
        })
    }
</script>

<template>
    <AuthLayout title="Reset Password">
        <div class="mb-7">
            <h1
                class="text-3xl font-bold text-indigo-950"
                style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;"
            >
                Forgot password?
            </h1>
            <p class="mt-1 text-sm font-medium text-indigo-400">
                Enter your email and we'll send you a reset link.
            </p>
        </div>

        <!-- Success state -->
        <div
            v-if="flash.success"
            class="mb-5 flex items-start gap-3 rounded-2xl border-2 border-emerald-100 bg-emerald-50 px-4 py-3"
        >
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-semibold text-emerald-700">{{ flash.success }}</p>
        </div>

        <form v-else @submit.prevent="submit" class="space-y-4" novalidate>
            <div class="space-y-1.5">
                <label for="forgot-email" class="block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    Email
                </label>
                <input
                    id="forgot-email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    placeholder="you@example.com"
                    class="w-full rounded-2xl border-2 border-indigo-100 bg-indigo-50/50 px-4 py-3 text-sm font-medium text-indigo-900 placeholder-indigo-200 transition-all duration-200 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                    :class="{ 'border-rose-300 bg-rose-50 focus:border-rose-400 focus:ring-rose-100': form.errors.email }"
                />
                <p v-if="form.errors.email" class="flex items-center gap-1.5 text-xs font-semibold text-rose-500">
                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ form.errors.email }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="clay-btn cursor-pointer flex w-full items-center justify-center gap-2 rounded-2xl bg-indigo-500 py-3.5 text-sm font-black text-white disabled:cursor-not-allowed disabled:opacity-60"
                style="box-shadow: 0 8px 24px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.25);"
            >
                <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ form.processing ? 'Sending…' : 'Send reset link' }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm font-medium text-indigo-400">
            Remember your password?
            <a href="/login" class="font-extrabold text-indigo-600 transition-colors hover:text-indigo-500">
                Sign in
            </a>
        </p>
    </AuthLayout>
</template>
