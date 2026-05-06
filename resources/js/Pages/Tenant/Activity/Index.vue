<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    activities: { type: Object, default: () => ({ data: [] }) },
})

const toneByType = {
    'invoice.paid': 'bg-green-500/10 text-green-400',
    'invoice.reminder_sent': 'bg-amber-500/10 text-amber-300',
    'invoice.sent': 'bg-blue-500/10 text-blue-400',
    'team.member_added': 'bg-violet-500/10 text-violet-300',
    'team.member_removed': 'bg-red-500/10 text-red-300',
}

function tone(type) {
    return toneByType[type] ?? 'bg-white/5 text-gray-300'
}
</script>

<template>
    <AppLayout>
        <div class="p-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Activity</h1>
                    <p class="mt-1 text-sm text-gray-400">Recent workspace changes and billing events.</p>
                </div>
            </div>

            <div class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
                <div v-if="activities.data?.length" class="divide-y divide-white/5">
                    <div
                        v-for="activity in activities.data"
                        :key="activity.id"
                        class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div class="flex min-w-0 gap-3">
                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full" :class="tone(activity.type)">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-white">{{ activity.label }}</p>
                                <p class="mt-0.5 text-sm text-gray-400">{{ activity.description }}</p>
                                <p v-if="activity.actor_name" class="mt-1 text-xs text-gray-600">
                                    {{ activity.actor_name }} · {{ activity.actor_email }}
                                </p>
                            </div>
                        </div>
                        <p class="shrink-0 text-xs text-gray-500" :title="activity.occurred_at_full">
                            {{ activity.occurred_at }}
                        </p>
                    </div>
                </div>

                <div v-else class="p-12 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-violet-500/10">
                        <svg class="h-6 w-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-white">No activity yet</p>
                    <p class="mt-1 text-xs text-gray-500">New clients, invoices, reminders, and team changes will appear here.</p>
                </div>
            </div>

            <div v-if="activities.last_page > 1" class="mt-4 flex items-center justify-between">
                <p class="text-xs text-gray-500">Showing {{ activities.from }}–{{ activities.to }} of {{ activities.total }}</p>
                <div class="flex gap-1">
                    <a
                        v-for="link in activities.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="rounded px-2.5 py-1 text-xs"
                        :class="link.active ? 'bg-violet-600 text-white' : link.url ? 'text-gray-400 hover:bg-white/5 hover:text-white' : 'cursor-default text-gray-600'"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
