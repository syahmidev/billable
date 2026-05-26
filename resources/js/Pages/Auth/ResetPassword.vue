<script setup>
    import AuthLayout from '@/Layouts/AuthLayout.vue'
    import { useForm } from '@inertiajs/vue3'

    const props = defineProps({
        token: String,
        email: String,
    })

    const form = useForm({
        token: props.token,
        email: props.email ?? '',
        password: '',
        password_confirmation: '',
    })

    function submit() {
        form.post('/reset-password', {
            onFinish: () => form.reset('password', 'password_confirmation'),
        })
    }
</script>

<template>
    <AuthLayout title="Set New Password">
        <div class="mb-7">
            <h1
                class="text-3xl font-bold text-indigo-950"
                style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;"
            >
                Set new password
            </h1>
            <p class="mt-1 text-sm font-medium text-indigo-400">Choose a strong password for your account.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4" novalidate>
            <!-- Email (read-only, pre-filled) -->
            <div class="space-y-1.5">
                <label for="reset-email" class="block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    Email
                </label>
                <input
                    id="reset-email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
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

            <!-- New Password -->
            <div class="space-y-1.5">
                <label for="reset-password" class="block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    New Password
                </label>
                <input
                    id="reset-password"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Min. 8 characters"
                    class="w-full rounded-2xl border-2 border-indigo-100 bg-indigo-50/50 px-4 py-3 text-sm font-medium text-indigo-900 placeholder-indigo-200 transition-all duration-200 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                    :class="{ 'border-rose-300 bg-rose-50 focus:border-rose-400 focus:ring-rose-100': form.errors.password }"
                />
                <p v-if="form.errors.password" class="flex items-center gap-1.5 text-xs font-semibold text-rose-500">
                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="reset-confirm" class="block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    Confirm Password
                </label>
                <input
                    id="reset-confirm"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Repeat your new password"
                    class="w-full rounded-2xl border-2 border-indigo-100 bg-indigo-50/50 px-4 py-3 text-sm font-medium text-indigo-900 placeholder-indigo-200 transition-all duration-200 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                    :class="{ 'border-rose-300 bg-rose-50 focus:border-rose-400 focus:ring-rose-100': form.errors.password_confirmation }"
                />
                <p v-if="form.errors.password_confirmation" class="flex items-center gap-1.5 text-xs font-semibold text-rose-500">
                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ form.errors.password_confirmation }}
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
                {{ form.processing ? 'Saving…' : 'Reset password' }}
            </button>
        </form>
    </AuthLayout>
</template>
