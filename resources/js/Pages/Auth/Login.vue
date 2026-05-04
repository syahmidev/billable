<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <AuthLayout>
        <h1 class="text-xl font-semibold text-white mb-1">Welcome back</h1>
        <p class="text-sm text-gray-400 mb-6">Sign in to your workspace</p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    placeholder="you@example.com"
                    class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors"
                />
                <p v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Password</label>
                <input
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="Your password"
                    class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors"
                />
                <p v-if="form.errors.password" class="text-xs text-red-400 mt-1">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center gap-2">
                <input
                    v-model="form.remember"
                    id="remember"
                    type="checkbox"
                    class="w-3.5 h-3.5 accent-violet-500"
                />
                <label for="remember" class="text-xs text-gray-400">Remember me</label>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="flex w-full items-center justify-center gap-2 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg py-2.5 transition-colors"
            >
                <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ form.processing ? 'Signing in...' : 'Sign in' }}
            </button>
        </form>

        <p class="text-center text-xs text-gray-500 mt-6">
            Don't have an account?
            <a href="/register" class="text-violet-400 hover:text-violet-300 transition-colors">Create one</a>
        </p>
    </AuthLayout>
</template>
