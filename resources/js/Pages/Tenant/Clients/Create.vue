<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
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
        <div class="p-8 max-w-5xl">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-8">
                <a href="/clients" class="text-gray-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-white">New Client</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Add a client to your workspace</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <form @submit.prevent="submit" class="space-y-5 lg:col-span-2">
                    <div class="bg-gray-900 border border-white/10 rounded-xl p-6 space-y-5">
                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Name <span class="text-red-400">*</span></label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Jane Smith"
                            class="w-full bg-gray-800 border rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-violet-500 transition-colors"
                            :class="form.errors.name ? 'border-red-500' : 'border-white/10'"
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-400 mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Company -->
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Company</label>
                        <input
                            v-model="form.company"
                            type="text"
                            placeholder="Acme Corp"
                            class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-violet-500 transition-colors"
                        />
                    </div>

                    <!-- Email + Phone -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="jane@acme.com"
                                class="w-full bg-gray-800 border rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-violet-500 transition-colors"
                                :class="form.errors.email ? 'border-red-500' : 'border-white/10'"
                            />
                            <p v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Phone</label>
                            <input
                                v-model="form.phone"
                                type="text"
                                placeholder="+1 555 000 0000"
                                class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-violet-500 transition-colors"
                            />
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Address</label>
                        <textarea
                            v-model="form.address"
                            rows="2"
                            placeholder="123 Main St, City, State 00000"
                            class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-violet-500 transition-colors resize-none"
                        />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1.5">Notes</label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            placeholder="Any internal notes about this client..."
                            class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-violet-500 transition-colors resize-none"
                        />
                    </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            {{ form.processing ? 'Saving...' : 'Create Client' }}
                        </button>
                        <a href="/clients" class="px-5 py-2.5 text-sm text-gray-400 hover:text-white transition-colors">Cancel</a>
                    </div>
                </form>

                <aside class="bg-gray-900 border border-white/10 rounded-xl p-6 h-fit">
                    <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Preview</h2>

                    <div v-if="hasPreview" class="space-y-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-violet-500/15 text-sm font-semibold text-violet-300">
                                {{ (form.name || form.company || 'C').charAt(0).toUpperCase() }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-white">{{ form.name || 'Unnamed client' }}</p>
                                <p v-if="form.company" class="truncate text-xs text-gray-500">{{ form.company }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div v-if="form.email">
                                <p class="text-xs text-gray-600">Email</p>
                                <p class="mt-0.5 break-all text-gray-300">{{ form.email }}</p>
                            </div>
                            <div v-if="form.phone">
                                <p class="text-xs text-gray-600">Phone</p>
                                <p class="mt-0.5 text-gray-300">{{ form.phone }}</p>
                            </div>
                            <div v-if="form.address">
                                <p class="text-xs text-gray-600">Address</p>
                                <p class="mt-0.5 whitespace-pre-line text-gray-300">{{ form.address }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="rounded-lg border border-white/10 bg-gray-950/40 px-4 py-8 text-center">
                        <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-white/5">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500">Client preview is empty</p>
                    </div>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>
