<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage } from '@inertiajs/vue3'

defineProps({
    workspace: Object,
})

const page = usePage()
const user = page.props.auth.user
</script>

<template>
    <AppLayout>
        <div class="p-8">
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-white">
                    Welcome back, {{ user.name.split(' ')[0] }} 👋
                </h1>
                <p class="text-sm text-gray-400 mt-1">Here's what's happening in {{ workspace.name }}</p>
            </div>

            <!-- Stats placeholder -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div
                    v-for="stat in [
                        { label: 'Total Revenue', value: '$0.00', sub: 'All time' },
                        { label: 'Outstanding', value: '$0.00', sub: '0 invoices' },
                        { label: 'Overdue', value: '$0.00', sub: '0 invoices' },
                        { label: 'Clients', value: '0', sub: 'Active clients' },
                    ]"
                    :key="stat.label"
                    class="bg-gray-900 border border-white/10 rounded-xl p-5"
                >
                    <p class="text-xs text-gray-500 mb-1">{{ stat.label }}</p>
                    <p class="text-2xl font-semibold text-white">{{ stat.value }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ stat.sub }}</p>
                </div>
            </div>

            <!-- Empty state -->
            <div class="bg-gray-900 border border-white/10 rounded-xl p-10 text-center">
                <div class="w-12 h-12 rounded-full bg-violet-500/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-white mb-1">No invoices yet</h3>
                <p class="text-xs text-gray-500">Create your first client and send your first invoice</p>
            </div>
        </div>
    </AppLayout>
</template>
