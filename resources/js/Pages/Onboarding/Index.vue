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
    <AuthLayout title="Set Up Workspace">
        <h1 class="text-2xl font-bold text-indigo-950 mb-1" style="font-family: 'Fredoka', sans-serif;">
            Set up your workspace
        </h1>
        <p class="text-sm font-medium text-indigo-400 mb-7">This is where your team will manage invoices</p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    Workspace name
                </label>
                <input
                    v-model="form.workspace_name"
                    type="text"
                    placeholder="Acme Studio"
                    class="clay-input w-full"
                    :class="{ 'clay-error': form.errors.workspace_name }"
                />
                <p v-if="form.errors.workspace_name" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.workspace_name }}</p>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                    Subdomain
                </label>
                <div
                    class="flex items-center overflow-hidden rounded-2xl border-2 border-indigo-100 bg-indigo-50/50 focus-within:border-indigo-400 focus-within:bg-white transition-all"
                    :class="{ 'border-rose-300 bg-rose-50': form.errors.subdomain }"
                >
                    <input
                        v-model="form.subdomain"
                        type="text"
                        placeholder="acme"
                        class="flex-1 bg-transparent px-4 py-3 text-sm font-medium text-indigo-900 placeholder-indigo-300 focus:outline-none"
                    />
                    <span class="shrink-0 border-l-2 border-indigo-100 bg-indigo-50 px-3 py-3 text-xs font-bold text-indigo-400 select-none">
                        .billable.test
                    </span>
                </div>
                <p v-if="form.errors.subdomain" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.subdomain }}</p>
                <p v-else class="mt-1.5 text-xs font-medium text-indigo-300">Only lowercase letters, numbers and hyphens</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing || !form.workspace_name || !form.subdomain"
                class="clay-btn cursor-pointer w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500 py-3 text-sm font-black text-white disabled:opacity-50 disabled:cursor-not-allowed"
                style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
            >
                <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ form.processing ? 'Setting up your workspace…' : 'Create workspace →' }}
            </button>
        </form>
    </AuthLayout>
</template>
