<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    address: '',
    notes: '',
})

function submit() {
    form.post('/clients')
}

const hasPreview = computed(() =>
    Boolean(form.name || form.email || form.phone || form.company || form.address)
)
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8 max-w-5xl">
            <!-- Header -->
            <div class="mb-8 flex items-center gap-3">
                <Link
                    href="/clients"
                    class="cursor-pointer flex h-9 w-9 items-center justify-center rounded-2xl border-2 border-indigo-100 bg-white text-indigo-400 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
                    style="box-shadow: 0 2px 8px rgba(99,102,241,0.08);"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">New Client</h1>
                    <p class="mt-0.5 text-sm font-medium text-indigo-400">Add a client to your workspace</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <form @submit.prevent="submit" class="space-y-5 lg:col-span-2">
                    <div class="clay-card bg-white p-6 space-y-5">
                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                                Name <span class="text-rose-400">*</span>
                            </label>
                            <input v-model="form.name" type="text" placeholder="Jane Smith" class="clay-input" :class="{ 'clay-error': form.errors.name }" />
                            <p v-if="form.errors.name" class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-rose-500">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Company</label>
                            <input v-model="form.company" type="text" placeholder="Acme Corp" class="clay-input" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Email</label>
                                <input v-model="form.email" type="email" placeholder="jane@acme.com" class="clay-input" :class="{ 'clay-error': form.errors.email }" />
                                <p v-if="form.errors.email" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Phone</label>
                                <input v-model="form.phone" type="text" placeholder="+1 555 000 0000" class="clay-input" />
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Address</label>
                            <textarea v-model="form.address" rows="2" placeholder="123 Main St, City, State 00000" class="clay-textarea" />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Notes</label>
                            <textarea v-model="form.notes" rows="3" placeholder="Any internal notes about this client…" class="clay-textarea" />
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
                            {{ form.processing ? 'Saving…' : 'Create Client' }}
                        </button>
                        <Link href="/clients" class="cursor-pointer px-4 py-3 text-sm font-bold text-indigo-400 transition-colors hover:text-indigo-700">
                            Cancel
                        </Link>
                    </div>
                </form>

                <!-- Preview sidebar -->
                <aside class="clay-card bg-white p-6 h-fit">
                    <h2 class="mb-4 text-xs font-extrabold uppercase tracking-wider text-indigo-400">Preview</h2>

                    <div v-if="hasPreview" class="space-y-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500 text-base font-black text-white"
                                style="box-shadow: 0 6px 16px rgba(99,102,241,0.30);"
                            >
                                {{ (form.name || form.company || 'C').charAt(0).toUpperCase() }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif;">{{ form.name || 'Unnamed client' }}</p>
                                <p v-if="form.company" class="truncate text-xs font-medium text-indigo-400">{{ form.company }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div v-if="form.email">
                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Email</p>
                                <p class="mt-0.5 break-all text-sm font-medium text-indigo-800">{{ form.email }}</p>
                            </div>
                            <div v-if="form.phone">
                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Phone</p>
                                <p class="mt-0.5 text-sm font-medium text-indigo-800">{{ form.phone }}</p>
                            </div>
                            <div v-if="form.address">
                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Address</p>
                                <p class="mt-0.5 whitespace-pre-line text-sm font-medium text-indigo-800">{{ form.address }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="rounded-2xl border-2 border-indigo-100 bg-indigo-50 px-4 py-10 text-center">
                        <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-100">
                            <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A9 9 0 1118.879 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-indigo-400" style="font-family: 'Fredoka', sans-serif;">Client preview is empty</p>
                    </div>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>
