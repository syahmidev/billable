<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <AuthLayout>
        <h1 class="text-xl font-semibold text-white mb-1">Create your account</h1>
        <p class="text-sm text-gray-400 mb-6">Start sending invoices in minutes</p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Full name</label>
                <input
                    v-model="form.name"
                    type="text"
                    autocomplete="name"
                    placeholder="John Doe"
                    class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors"
                />
                <p v-if="form.errors.name" class="text-xs text-red-400 mt-1">{{ form.errors.name }}</p>
            </div>

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
                    autocomplete="new-password"
                    placeholder="Min 8 characters"
                    class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors"
                />
                <p v-if="form.errors.password" class="text-xs text-red-400 mt-1">{{ form.errors.password }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Confirm password</label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Repeat password"
                    class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors"
                />
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full bg-violet-600 hover:bg-violet-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg py-2.5 transition-colors"
            >
                {{ form.processing ? 'Creating account...' : 'Create account' }}
            </button>
        </form>

        <p class="text-center text-xs text-gray-500 mt-6">
            Already have an account?
            <a href="/login" class="text-violet-400 hover:text-violet-300 transition-colors">Sign in</a>
        </p>
    </AuthLayout>
</template>
