<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const form = useForm({
    workspace_name: '',
    subdomain: '',
})

watch(() => form.workspace_name, (val) => {
    form.subdomain = val
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/[\s_]+/g, '-')
        .replace(/-+/g, '-')
        .slice(0, 50)
})

function submit() {
    form.post('/onboarding')
}
</script>

<template>
    <AuthLayout>
        <h1 class="text-xl font-semibold text-white mb-1">Set up your workspace</h1>
        <p class="text-sm text-gray-400 mb-6">This is where your team will manage invoices</p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Workspace name</label>
                <input
                    v-model="form.workspace_name"
                    type="text"
                    placeholder="Acme Studio"
                    class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors"
                />
                <p v-if="form.errors.workspace_name" class="text-xs text-red-400 mt-1">{{ form.errors.workspace_name }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1.5">Subdomain</label>
                <div class="flex items-center bg-gray-800 border border-white/10 rounded-lg focus-within:border-violet-500 transition-colors overflow-hidden">
                    <input
                        v-model="form.subdomain"
                        type="text"
                        placeholder="acme"
                        class="flex-1 bg-transparent px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none"
                    />
                    <span class="px-3.5 py-2.5 text-sm text-gray-500 border-l border-white/10 bg-gray-900 select-none">
                        .billable.test
                    </span>
                </div>
                <p v-if="form.errors.subdomain" class="text-xs text-red-400 mt-1">{{ form.errors.subdomain }}</p>
                <p v-else class="text-xs text-gray-600 mt-1">Only lowercase letters, numbers and hyphens</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing || !form.workspace_name || !form.subdomain"
                class="relative w-full bg-violet-600 hover:bg-violet-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg py-2.5 transition-colors flex items-center justify-center gap-2"
            >
                <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ form.processing ? 'Setting up your workspace...' : 'Create workspace →' }}
            </button>
        </form>
    </AuthLayout>
</template>
