<script setup>
    import AuthLayout from '@/Layouts/AuthLayout.vue'
    import { useForm } from '@inertiajs/vue3'

    const props = defineProps({
        selectedPlan: { type: String, default: null },
    })

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        plan: props.selectedPlan,
    })

    function submit() {
        form.post('/register', {
            onFinish: () => form.reset('password', 'password_confirmation'),
        })
    }
</script>

<template>
    <AuthLayout title="Create Account">
        <div class="mb-7">
            <h1
                class="text-3xl font-bold text-indigo-950"
                style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;"
            >
                Create account
            </h1>
            <p class="mt-1 text-sm font-medium text-indigo-400">Start sending invoices in minutes</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4" novalidate>
            <input v-if="form.plan" type="hidden" name="plan" :value="form.plan" />

            <!-- Plan badge -->
            <div
                v-if="form.plan"
                class="flex items-center gap-3 rounded-2xl border-2 border-indigo-200 bg-indigo-50 px-4 py-3"
                style="box-shadow: 0 4px 12px rgba(99,102,241,0.10);"
            >
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-500" style="box-shadow: 0 4px 10px rgba(99,102,241,0.30);">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-400">Selected plan</p>
                    <p class="text-sm font-bold capitalize text-indigo-900">{{ form.plan }}</p>
                </div>
            </div>

            <!-- Full name -->
            <div class="space-y-1.5">
                <label for="reg-name" class="block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    Full name
                </label>
                <input
                    id="reg-name"
                    v-model="form.name"
                    type="text"
                    autocomplete="name"
                    placeholder="John Doe"
                    class="w-full rounded-2xl border-2 border-indigo-100 bg-indigo-50/50 px-4 py-3 text-sm font-medium text-indigo-900 placeholder-indigo-200 transition-all duration-200 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                    :class="{ 'border-rose-300 bg-rose-50 focus:border-rose-400 focus:ring-rose-100': form.errors.name }"
                />
                <p v-if="form.errors.name" class="flex items-center gap-1.5 text-xs font-semibold text-rose-500">
                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="reg-email" class="block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    Email address
                </label>
                <input
                    id="reg-email"
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

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="reg-password" class="block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    Password
                </label>
                <input
                    id="reg-password"
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

            <!-- Confirm -->
            <div class="space-y-1.5">
                <label for="reg-confirm" class="block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    Confirm password
                </label>
                <input
                    id="reg-confirm"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Repeat password"
                    class="w-full rounded-2xl border-2 border-indigo-100 bg-indigo-50/50 px-4 py-3 text-sm font-medium text-indigo-900 placeholder-indigo-200 transition-all duration-200 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                />
            </div>

            <!-- Submit -->
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
                <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ form.processing ? 'Creating account…' : 'Create account' }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm font-medium text-indigo-400">
            Already have an account?
            <a href="/login" class="font-extrabold text-indigo-600 transition-colors hover:text-indigo-500">
                Sign in
            </a>
        </p>
    </AuthLayout>
</template>
