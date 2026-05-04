<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'

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
</script>

<template>
    <AppLayout>
        <div class="p-8 max-w-2xl">
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

            <form @submit.prevent="submit" class="space-y-5">
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
        </div>
    </AppLayout>
</template>
