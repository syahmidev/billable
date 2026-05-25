<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    client: Object,
})

const form = useForm({
    name: props.client.name,
    email: props.client.email ?? '',
    phone: props.client.phone ?? '',
    company: props.client.company ?? '',
    address: props.client.address ?? '',
    notes: props.client.notes ?? '',
})

function submit() {
    form.put(`/clients/${props.client.id}`)
}
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8 max-w-2xl">
            <!-- Header -->
            <div class="mb-8 flex items-center gap-3">
                <Link
                    :href="`/clients/${client.id}`"
                    class="cursor-pointer flex h-9 w-9 items-center justify-center rounded-2xl border-2 border-indigo-100 bg-white text-indigo-400 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
                    style="box-shadow: 0 2px 8px rgba(99,102,241,0.08);"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">Edit Client</h1>
                    <p class="mt-0.5 text-sm font-medium text-indigo-400">{{ client.name }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="clay-card bg-white p-6 space-y-5">
                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                            Name <span class="text-rose-400">*</span>
                        </label>
                        <input v-model="form.name" type="text" class="clay-input" :class="{ 'clay-error': form.errors.name }" />
                        <p v-if="form.errors.name" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Company</label>
                        <input v-model="form.company" type="text" class="clay-input" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Email</label>
                            <input v-model="form.email" type="email" class="clay-input" :class="{ 'clay-error': form.errors.email }" />
                            <p v-if="form.errors.email" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Phone</label>
                            <input v-model="form.phone" type="text" class="clay-input" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Address</label>
                        <textarea v-model="form.address" rows="2" class="clay-textarea" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Notes</label>
                        <textarea v-model="form.notes" rows="3" class="clay-textarea" />
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-indigo-500 px-6 py-3 text-sm font-black text-white disabled:opacity-60"
                        style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                    >
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        {{ form.processing ? 'Saving…' : 'Save Changes' }}
                    </button>
                    <Link :href="`/clients/${client.id}`" class="cursor-pointer px-4 py-3 text-sm font-bold text-indigo-400 transition-colors hover:text-indigo-700">
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
