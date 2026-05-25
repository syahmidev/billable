<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    activities: { type: Object, default: () => ({ data: [] }) },
})

const toneConfig = {
    'invoice.paid':           { bg: 'bg-emerald-100 text-emerald-600', label: 'Paid' },
    'invoice.reminder_sent':  { bg: 'bg-amber-100 text-amber-600',     label: 'Reminder' },
    'invoice.sent':           { bg: 'bg-sky-100 text-sky-600',         label: 'Sent' },
    'team.member_added':      { bg: 'bg-indigo-100 text-indigo-600',   label: 'Team' },
    'team.member_removed':    { bg: 'bg-rose-100 text-rose-600',       label: 'Team' },
}

function tone(type) {
    return toneConfig[type]?.bg ?? 'bg-indigo-50 text-indigo-400'
}
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">Activity</h1>
                <p class="mt-1 text-sm font-medium text-indigo-400">Recent workspace changes and billing events</p>
            </div>

            <!-- Activity feed -->
            <div class="clay-card overflow-hidden bg-white">
                <div v-if="activities.data?.length" class="divide-y divide-indigo-50">
                    <div
                        v-for="activity in activities.data"
                        :key="activity.id"
                        class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-start sm:justify-between transition-colors hover:bg-indigo-50/40"
                    >
                        <div class="flex min-w-0 gap-4">
                            <!-- Activity icon bubble -->
                            <div
                                class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl border-2"
                                :class="tone(activity.type)"
                                style="border-color: rgba(99,102,241,0.12);"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-bold text-indigo-900">{{ activity.label }}</p>
                                <p class="mt-0.5 text-sm font-medium text-indigo-500">{{ activity.description }}</p>
                                <p v-if="activity.actor_name" class="mt-1 text-xs font-medium text-indigo-300">
                                    {{ activity.actor_name }} · {{ activity.actor_email }}
                                </p>
                            </div>
                        </div>

                        <p
                            class="shrink-0 rounded-xl border-2 border-indigo-100 bg-indigo-50/60 px-2.5 py-1 text-[11px] font-bold text-indigo-400"
                            :title="activity.occurred_at_full"
                        >
                            {{ activity.occurred_at }}
                        </p>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-else class="py-16 text-center">
                    <div
                        class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-100"
                        style="box-shadow: 0 6px 20px rgba(99,102,241,0.15);"
                    >
                        <svg class="h-7 w-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-lg font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif;">No activity yet</p>
                    <p class="mt-1 text-sm font-medium text-indigo-400 max-w-sm mx-auto">
                        New clients, invoices, reminders, and team changes will appear here.
                    </p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="activities.last_page > 1" class="mt-5 flex items-center justify-between">
                <p class="text-xs font-semibold text-indigo-400">Showing {{ activities.from }}–{{ activities.to }} of {{ activities.total }}</p>
                <div class="flex gap-1">
                    <a
                        v-for="link in activities.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="cursor-pointer rounded-xl px-3 py-1.5 text-xs font-bold transition-all"
                        :class="link.active
                            ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-200'
                            : link.url
                                ? 'border-2 border-indigo-100 bg-white text-indigo-500 hover:border-indigo-200 hover:bg-indigo-50'
                                : 'text-indigo-200 cursor-default'"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
