<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    workspace: Object,
    stats: { type: Object, default: () => ({}) },
    chartData: { type: Array, default: () => [] },
    recentInvoices: { type: Array, default: () => [] },
    recentActivity: { type: Array, default: () => [] },
})

const page = usePage()
const user = page.props.auth.user
const permissions = computed(() => page.props.permissions ?? {})
const isEmptyWorkspace = computed(() => Number(props.stats.client_count ?? 0) === 0 && props.recentInvoices.length === 0)

const statusColors = {
    draft: 'bg-gray-800 text-gray-400',
    sent: 'bg-blue-500/10 text-blue-400',
    paid: 'bg-green-500/10 text-green-400',
    overdue: 'bg-red-500/10 text-red-400',
    cancelled: 'bg-gray-800 text-gray-500',
}

function fmt(n) {
    if (n >= 1000) return '$' + (n / 1000).toFixed(1) + 'k'
    return '$' + n.toFixed(2)
}

function fmtFull(n) {
    return '$' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
</script>

<template>
    <AppLayout>
        <div class="p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-white">
                        Welcome back, {{ user.name.split(' ')[0] }} 👋
                    </h1>
                    <p class="text-sm text-gray-400 mt-1">Here's what's happening in {{ workspace.name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a v-if="permissions.clients?.create" href="/clients/create" class="px-3.5 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm font-medium rounded-lg transition-colors">
                        New Client
                    </a>
                    <a v-if="permissions.invoices?.create" href="/invoices/create" class="px-3.5 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium rounded-lg transition-colors">
                        New Invoice
                    </a>
                </div>
            </div>

            <div v-if="isEmptyWorkspace" class="mb-6 rounded-xl border border-violet-500/20 bg-violet-500/10 p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white">Set up your billing workspace</p>
                        <p class="mt-1 text-sm text-gray-400">
                            Add a client first, then create an invoice when you are ready to collect payment.
                        </p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a v-if="permissions.clients?.create" href="/clients/create" class="rounded-lg border border-white/10 bg-white/5 px-3.5 py-2 text-center text-sm font-medium text-white transition-colors hover:bg-white/10">
                            Add client
                        </a>
                        <a v-if="permissions.invoices?.create" href="/invoices/create" class="rounded-lg bg-violet-600 px-3.5 py-2 text-center text-sm font-medium text-white transition-colors hover:bg-violet-500">
                            Create invoice
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Revenue -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs text-gray-500">Total Revenue</p>
                        <div class="w-7 h-7 rounded-lg bg-green-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ fmtFull(stats.total_revenue) }}</p>
                    <p class="text-xs text-gray-600 mt-1">From paid invoices</p>
                </div>

                <!-- Outstanding -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs text-gray-500">Outstanding</p>
                        <div class="w-7 h-7 rounded-lg bg-blue-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ fmtFull(stats.outstanding) }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ stats.outstanding_count }} sent invoice{{ stats.outstanding_count !== 1 ? 's' : '' }}</p>
                </div>

                <!-- Overdue -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs text-gray-500">Overdue</p>
                        <div class="w-7 h-7 rounded-lg bg-red-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ fmtFull(stats.overdue) }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ stats.overdue_count }} overdue invoice{{ stats.overdue_count !== 1 ? 's' : '' }}</p>
                </div>

                <!-- Clients -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs text-gray-500">Clients</p>
                        <div class="w-7 h-7 rounded-lg bg-violet-500/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-white">{{ stats.client_count }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ stats.draft_count }} draft invoice{{ stats.draft_count !== 1 ? 's' : '' }}</p>
                </div>
            </div>

            <!-- Chart + Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                <!-- Revenue Chart -->
                <div class="lg:col-span-2 bg-gray-900 border border-white/10 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-sm font-semibold text-white">Revenue</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Last 6 months</p>
                        </div>
                        <span class="text-xs text-gray-600">Paid invoices only</span>
                    </div>

                    <!-- Bar chart -->
                    <div class="flex items-end gap-3 h-32">
                        <div
                            v-for="bar in chartData"
                            :key="bar.label"
                            class="flex-1 flex flex-col items-center gap-1.5 group"
                        >
                            <!-- Value on hover -->
                            <span class="text-xs text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                {{ fmt(bar.revenue) }}
                            </span>
                            <!-- Bar -->
                            <div class="w-full flex items-end justify-center flex-1">
                                <div
                                    class="w-full rounded-t-md transition-all duration-700"
                                    :style="{
                                        height: Math.max(3, (bar.revenue / Math.max(...chartData.map(d => d.revenue), 1)) * 100) + '%',
                                        background: bar.revenue > 0 ? '#7c3aed' : '#1f2937',
                                    }"
                                ></div>
                            </div>
                            <!-- Label -->
                            <span class="text-xs text-gray-600">{{ bar.label }}</span>
                        </div>
                    </div>

                    <!-- No data state -->
                    <div v-if="chartData.every(d => d.revenue === 0)" class="text-center mt-4">
                        <p class="text-xs text-gray-600">No paid invoices in the last 6 months</p>
                    </div>
                </div>

                <!-- Quick Actions + Summary -->
                <div class="space-y-4">
                    <!-- Quick Actions -->
                    <div class="bg-gray-900 border border-white/10 rounded-xl p-5">
                        <h2 class="text-sm font-semibold text-white mb-4">Quick Actions</h2>
                        <div class="space-y-2">
                            <a
                                v-if="permissions.invoices?.create"
                                href="/invoices/create"
                                class="flex items-center gap-3 px-3.5 py-2.5 bg-violet-600/10 hover:bg-violet-600/20 border border-violet-500/20 rounded-lg transition-colors group"
                            >
                                <div class="w-7 h-7 rounded-md bg-violet-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">New Invoice</p>
                                    <p class="text-xs text-gray-500">Create and send an invoice</p>
                                </div>
                            </a>
                            <a
                                v-if="permissions.clients?.create"
                                href="/clients/create"
                                class="flex items-center gap-3 px-3.5 py-2.5 bg-white/[0.03] hover:bg-white/[0.06] border border-white/10 rounded-lg transition-colors"
                            >
                                <div class="w-7 h-7 rounded-md bg-gray-800 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">New Client</p>
                                    <p class="text-xs text-gray-500">Add a client to your workspace</p>
                                </div>
                            </a>
                            <a
                                v-if="permissions.invoices?.view"
                                href="/invoices"
                                class="flex items-center gap-3 px-3.5 py-2.5 bg-white/[0.03] hover:bg-white/[0.06] border border-white/10 rounded-lg transition-colors"
                            >
                                <div class="w-7 h-7 rounded-md bg-gray-800 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">All Invoices</p>
                                    <p class="text-xs text-gray-500">View and manage invoices</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Outstanding alert -->
                    <div v-if="stats.overdue_count > 0" class="bg-red-500/5 border border-red-500/20 rounded-xl p-4">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-xs font-medium text-red-400">{{ stats.overdue_count }} overdue invoice{{ stats.overdue_count !== 1 ? 's' : '' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ fmtFull(stats.overdue) }} past due</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-900 border border-white/10 rounded-xl p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-white">Recent Activity</h2>
                            <a href="/activity" class="text-xs text-violet-400 transition-colors hover:text-violet-300">View all</a>
                        </div>
                        <div v-if="recentActivity.length" class="space-y-3">
                            <div v-for="activity in recentActivity" :key="activity.id" class="border-l border-white/10 pl-3">
                                <p class="text-xs font-medium text-white">{{ activity.label }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">{{ activity.description }}</p>
                                <p class="mt-1 text-xs text-gray-600">{{ activity.occurred_at }}</p>
                            </div>
                        </div>
                        <div v-else class="rounded-lg border border-white/10 bg-gray-950/40 px-4 py-5 text-center">
                            <p class="text-xs text-gray-500">No activity yet</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
                    <h2 class="text-sm font-semibold text-white">Recent Invoices</h2>
                    <a href="/invoices" class="text-xs text-violet-400 hover:text-violet-300 transition-colors">View all →</a>
                </div>

                <table v-if="recentInvoices.length" class="w-full text-sm">
                    <tbody class="divide-y divide-white/5">
                        <tr v-for="inv in recentInvoices" :key="inv.id" class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-3.5">
                                <a :href="`/invoices/${inv.id}`" class="font-medium text-white hover:text-violet-400 transition-colors">
                                    {{ inv.invoice_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 text-gray-400 text-xs">{{ inv.client_name }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium" :class="statusColors[inv.status]">
                                    {{ inv.status.charAt(0).toUpperCase() + inv.status.slice(1) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-500">{{ inv.issue_date }}</td>
                            <td class="px-5 py-3.5 text-right font-medium text-white">${{ inv.total.toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div v-else class="px-5 py-10 text-center">
                    <p class="text-sm text-gray-500 mb-1">No invoices yet</p>
                    <a href="/invoices/create" class="text-xs text-violet-400 hover:text-violet-300 transition-colors">Create your first invoice →</a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
