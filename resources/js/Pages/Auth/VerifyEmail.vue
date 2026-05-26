<script setup>
    import AuthLayout from '@/Layouts/AuthLayout.vue'
    import { useForm, usePage } from '@inertiajs/vue3'
    import { computed } from 'vue'

    defineProps({ email: String })

    const flash = computed(() => usePage().props.flash)

    const resendForm = useForm({})
    const logoutForm = useForm({})

    function resend() {
        resendForm.post('/email/verification-notification')
    }

    function logout() {
        logoutForm.post('/logout')
    }
</script>

<template>
    <AuthLayout title="Verify Email">
        <div class="mb-7 text-center">
            <!-- Icon -->
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100">
                <svg class="h-7 w-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1
                class="text-3xl font-bold text-indigo-950"
                style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;"
            >
                Check your inbox
            </h1>
            <p class="mt-2 text-sm font-medium text-indigo-400">
                We sent a verification link to
            </p>
            <p class="mt-0.5 text-sm font-extrabold text-indigo-700">{{ email }}</p>
        </div>

        <!-- Success flash -->
        <div
            v-if="flash.success"
            class="mb-5 flex items-start gap-3 rounded-2xl border-2 border-emerald-100 bg-emerald-50 px-4 py-3"
        >
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-semibold text-emerald-700">{{ flash.success }}</p>
        </div>

        <div class="space-y-3">
            <!-- Resend -->
            <button
                type="button"
                :disabled="resendForm.processing"
                @click="resend"
                class="clay-btn cursor-pointer flex w-full items-center justify-center gap-2 rounded-2xl bg-indigo-500 py-3.5 text-sm font-black text-white disabled:cursor-not-allowed disabled:opacity-60"
                style="box-shadow: 0 8px 24px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.25);"
            >
                <svg v-if="resendForm.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ resendForm.processing ? 'Sending…' : 'Resend verification email' }}
            </button>

            <!-- Logout -->
            <button
                type="button"
                :disabled="logoutForm.processing"
                @click="logout"
                class="cursor-pointer flex w-full items-center justify-center rounded-2xl border-2 border-indigo-100 bg-white py-3 text-sm font-bold text-indigo-500 transition-colors hover:border-indigo-200 hover:bg-indigo-50 disabled:opacity-60"
            >
                Sign out
            </button>
        </div>

        <p class="mt-6 text-center text-xs font-medium text-indigo-300">
            Didn't get the email? Check your spam folder or resend above.
        </p>
    </AuthLayout>
</template>
