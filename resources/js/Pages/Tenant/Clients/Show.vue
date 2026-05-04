<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    client: Object,
})

function archive() {
    if (!confirm(`Archive ${props.client.name}?`)) return
    router.delete(`/clients/${props.client.id}`)
}
</script>

<template>
    <AppLayout>
        <div class="p-8">
            <!-- Header -->
            <div class="flex items-start justify-between mb-8">
                <div class="flex items-center gap-3">
                    <a href="/clients" class="text-gray-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-semibold text-white">{{ client.name }}</h1>
                        <p v-if="client.company" class="text-sm text-gray-400 mt-0.5">{{ client.company }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        :href="`/clients/${client.id}/edit`"
                        class="px-3.5 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        Edit
                    </a>
                    <button
                        @click="archive"
                        class="px-3.5 py-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 text-sm font-medium rounded-lg transition-colors"
                    >
                        Archive
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Contact info -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-6">
                    <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Contact Info</h2>
                    <dl class="space-y-3">
                        <div v-if="client.email">
                            <dt class="text-xs text-gray-600">Email</dt>
                            <dd class="text-sm text-white mt-0.5">{{ client.email }}</dd>
                        </div>
                        <div v-if="client.phone">
                            <dt class="text-xs text-gray-600">Phone</dt>
                            <dd class="text-sm text-white mt-0.5">{{ client.phone }}</dd>
                        </div>
                        <div v-if="client.address">
                            <dt class="text-xs text-gray-600">Address</dt>
                            <dd class="text-sm text-white mt-0.5 whitespace-pre-line">{{ client.address }}</dd>
                        </div>
                        <div v-if="!client.email && !client.phone && !client.address">
                            <p class="text-xs text-gray-600">No contact info added yet.</p>
                        </div>
                    </dl>

                    <div v-if="client.notes" class="mt-5 pt-5 border-t border-white/10">
                        <dt class="text-xs text-gray-600 mb-1">Notes</dt>
                        <dd class="text-sm text-gray-300 whitespace-pre-line">{{ client.notes }}</dd>
                    </div>
                </div>

                <!-- Invoice history (Phase 4 placeholder) -->
                <div class="lg:col-span-2 bg-gray-900 border border-white/10 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice History</h2>
                    </div>
                    <div class="py-10 text-center">
                        <div class="w-10 h-10 rounded-full bg-violet-500/10 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">No invoices yet</p>
                        <p class="text-xs text-gray-600 mt-1">Invoices will appear here once Phase 4 is built</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
