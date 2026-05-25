<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import { Link, usePage } from '@inertiajs/vue3'
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
    const isEmptyWorkspace = computed(
        () => Number(props.stats.client_count ?? 0) === 0 && props.recentInvoices.length === 0
    )

    const statusConfig = {
        draft:     { bg: 'bg-indigo-100 text-indigo-500 border-indigo-200', label: 'Draft' },
        sent:      { bg: 'bg-sky-100 text-sky-600 border-sky-200',           label: 'Sent' },
        paid:      { bg: 'bg-emerald-100 text-emerald-700 border-emerald-200', label: 'Paid' },
        overdue:   { bg: 'bg-rose-100 text-rose-600 border-rose-200',         label: 'Overdue' },
        cancelled: { bg: 'bg-gray-100 text-gray-500 border-gray-200',         label: 'Cancelled' },
    }

    const statCards = computed(() => [
        {
            label: 'Total Revenue',
            value: fmtFull(props.stats.total_revenue),
            sub: 'From paid invoices',
            icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            iconBg: 'bg-emerald-100',
            iconColor: 'text-emerald-600',
            cardClass: 'clay-card-emerald',
            valueColor: 'text-emerald-700',
        },
        {
            label: 'Outstanding',
            value: fmtFull(props.stats.outstanding),
            sub: `${props.stats.outstanding_count ?? 0} sent invoice${props.stats.outstanding_count !== 1 ? 's' : ''}`,
            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            iconBg: 'bg-sky-100',
            iconColor: 'text-sky-600',
            cardClass: 'clay-card-sky',
            valueColor: 'text-sky-700',
        },
        {
            label: 'Overdue',
            value: fmtFull(props.stats.overdue),
            sub: `${props.stats.overdue_count ?? 0} overdue invoice${props.stats.overdue_count !== 1 ? 's' : ''}`,
            icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            iconBg: 'bg-rose-100',
            iconColor: 'text-rose-500',
            cardClass: Number(props.stats.overdue) > 0 ? 'clay-card-rose' : 'clay-card',
            valueColor: Number(props.stats.overdue) > 0 ? 'text-rose-600' : 'text-indigo-900',
        },
        {
            label: 'Clients',
            value: props.stats.client_count ?? 0,
            sub: `${props.stats.draft_count ?? 0} draft invoice${props.stats.draft_count !== 1 ? 's' : ''}`,
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            iconBg: 'bg-indigo-100',
            iconColor: 'text-indigo-600',
            cardClass: 'clay-card-indigo',
            valueColor: 'text-indigo-700',
        },
    ])

    const maxChartVal = computed(() =>
        Math.max(...props.chartData.map((d) => d.revenue), 1)
    )

    function fmt(n) {
        if (n >= 1000) return '$' + (n / 1000).toFixed(1) + 'k'
        return '$' + n.toFixed(2)
    }

    function fmtFull(n) {
        return (
            '$' +
            Number(n).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        )
    }
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8">

            <!-- Page header -->
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <h1
                        class="text-3xl font-bold text-indigo-950"
                        style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;"
                    >
                        Good morning, {{ user.name.split(' ')[0] }}
                    </h1>
                    <p class="mt-1 text-sm font-medium text-indigo-400">
                        Here's what's happening in
                        <span class="font-bold text-indigo-600">{{ workspace.name }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Link
                        v-if="permissions.clients?.create"
                        href="/clients/create"
                        class="clay-btn-secondary cursor-pointer hidden sm:inline-flex items-center gap-1.5 rounded-2xl border-2 border-indigo-200 bg-white px-3.5 py-2.5 text-sm font-bold text-indigo-600"
                        style="box-shadow: 0 4px 12px rgba(99,102,241,0.10);"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        New Client
                    </Link>
                    <Link
                        v-if="permissions.invoices?.create"
                        href="/invoices/create"
                        class="clay-btn cursor-pointer inline-flex items-center gap-1.5 rounded-2xl bg-indigo-500 px-3.5 py-2.5 text-sm font-black text-white"
                        style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        New Invoice
                    </Link>
                </div>
            </div>

            <!-- Empty state banner -->
            <div
                v-if="isEmptyWorkspace"
                class="clay-card-indigo clay-card mb-6 p-5 bg-white"
            >
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-500"
                            style="box-shadow: 0 6px 16px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                        >
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-extrabold text-indigo-950" style="font-family: 'Fredoka', sans-serif;">Set up your billing workspace</p>
                            <p class="mt-0.5 text-sm text-indigo-400">
                                Add a client first, then create an invoice when you're ready.
                            </p>
                        </div>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <Link
                            v-if="permissions.clients?.create"
                            href="/clients/create"
                            class="clay-btn-secondary cursor-pointer rounded-2xl border-2 border-indigo-200 bg-white px-4 py-2.5 text-center text-sm font-bold text-indigo-600"
                            style="box-shadow: 0 4px 12px rgba(99,102,241,0.10);"
                        >
                            Add client
                        </Link>
                        <Link
                            v-if="permissions.invoices?.create"
                            href="/invoices/create"
                            class="clay-btn cursor-pointer rounded-2xl bg-indigo-500 px-4 py-2.5 text-center text-sm font-black text-white"
                            style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                        >
                            Create invoice
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Stat cards -->
            <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div
                    v-for="card in statCards"
                    :key="card.label"
                    class="clay-card bg-white p-5"
                    :class="card.cardClass"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-400">{{ card.label }}</p>
                        <div class="flex h-9 w-9 items-center justify-center rounded-2xl" :class="card.iconBg">
                            <svg class="h-4.5 w-4.5" :class="card.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="card.icon" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-black" :class="card.valueColor">{{ card.value }}</p>
                    <p class="mt-1 text-[11px] font-medium text-indigo-300">{{ card.sub }}</p>
                </div>
            </div>

            <!-- Chart + Quick Actions -->
            <div class="mb-6 grid gap-4 lg:grid-cols-3">
                <!-- Revenue chart -->
                <div class="clay-card lg:col-span-2 bg-white p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-extrabold text-indigo-950" style="font-family: 'Fredoka', sans-serif;">Revenue</h2>
                            <p class="mt-0.5 text-[11px] font-medium text-indigo-300">Last 6 months · paid only</p>
                        </div>
                    </div>

                    <div class="flex h-36 items-end gap-2">
                        <div
                            v-for="bar in chartData"
                            :key="bar.label"
                            class="group flex flex-1 flex-col items-center gap-1.5"
                        >
                            <span class="text-[10px] font-bold text-indigo-300 opacity-0 transition-opacity group-hover:opacity-100 whitespace-nowrap">
                                {{ fmt(bar.revenue) }}
                            </span>
                            <div class="flex w-full flex-1 items-end">
                                <div
                                    class="w-full rounded-t-xl transition-all duration-700"
                                    :style="{
                                        height: Math.max(6, (bar.revenue / maxChartVal) * 100) + '%',
                                        background: bar.revenue > 0
                                            ? 'linear-gradient(to top, #6366F1, #818CF8)'
                                            : '#EEF2FF',
                                        boxShadow: bar.revenue > 0 ? '0 4px 12px rgba(99,102,241,0.25)' : 'none',
                                    }"
                                ></div>
                            </div>
                            <span class="text-[10px] font-semibold text-indigo-300">{{ bar.label }}</span>
                        </div>
                    </div>

                    <div v-if="chartData.every((d) => d.revenue === 0)" class="mt-4 text-center">
                        <p class="text-sm font-medium text-indigo-300">No paid invoices in the last 6 months</p>
                    </div>
                </div>

                <!-- Right sidebar -->
                <div class="space-y-4">
                    <!-- Quick Actions -->
                    <div class="clay-card bg-white p-5">
                        <h2 class="mb-4 text-base font-extrabold text-indigo-950" style="font-family: 'Fredoka', sans-serif;">Quick Actions</h2>
                        <div class="space-y-2">
                            <Link
                                v-if="permissions.invoices?.create"
                                href="/invoices/create"
                                class="cursor-pointer flex items-center gap-3 rounded-2xl border-2 border-indigo-200 bg-indigo-50 px-4 py-3 transition-all duration-200 hover:-translate-y-px hover:border-indigo-300 hover:bg-indigo-100"
                                style="box-shadow: 0 4px 12px rgba(99,102,241,0.10);"
                            >
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-500" style="box-shadow: 0 4px 10px rgba(99,102,241,0.30);">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-indigo-900">New Invoice</p>
                                    <p class="text-[11px] font-medium text-indigo-400">Create and send</p>
                                </div>
                            </Link>

                            <Link
                                v-if="permissions.clients?.create"
                                href="/clients/create"
                                class="cursor-pointer flex items-center gap-3 rounded-2xl border-2 border-indigo-100 bg-white px-4 py-3 transition-all duration-200 hover:-translate-y-px hover:border-indigo-200 hover:bg-indigo-50"
                            >
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-100">
                                    <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-indigo-900">New Client</p>
                                    <p class="text-[11px] font-medium text-indigo-400">Add to workspace</p>
                                </div>
                            </Link>

                            <Link
                                v-if="permissions.invoices?.view"
                                href="/invoices"
                                class="cursor-pointer flex items-center gap-3 rounded-2xl border-2 border-indigo-100 bg-white px-4 py-3 transition-all duration-200 hover:-translate-y-px hover:border-indigo-200 hover:bg-indigo-50"
                            >
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-100">
                                    <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-indigo-900">All Invoices</p>
                                    <p class="text-[11px] font-medium text-indigo-400">View and manage</p>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Overdue alert -->
                    <div
                        v-if="stats.overdue_count > 0"
                        class="clay-card-rose clay-card bg-white p-4"
                    >
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-100">
                                <svg class="h-4 w-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-extrabold text-rose-600" style="font-family: 'Fredoka', sans-serif;">
                                    {{ stats.overdue_count }} overdue invoice{{ stats.overdue_count !== 1 ? 's' : '' }}
                                </p>
                                <p class="mt-0.5 text-[11px] font-medium text-rose-400">{{ fmtFull(stats.overdue) }} past due</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Invoices table -->
            <div class="clay-card overflow-hidden bg-white">
                <div class="flex items-center justify-between border-b-2 border-indigo-50 px-6 py-4">
                    <h2 class="text-base font-extrabold text-indigo-950" style="font-family: 'Fredoka', sans-serif;">Recent Invoices</h2>
                    <Link
                        href="/invoices"
                        class="cursor-pointer text-sm font-bold text-indigo-500 transition-colors hover:text-indigo-700"
                    >
                        View all →
                    </Link>
                </div>

                <table v-if="recentInvoices.length" class="w-full">
                    <thead>
                        <tr class="border-b border-indigo-50 bg-indigo-50/50">
                            <th class="px-6 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Invoice</th>
                            <th class="px-4 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Client</th>
                            <th class="px-4 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Status</th>
                            <th class="px-4 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Date</th>
                            <th class="px-6 py-3 text-right text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-50">
                        <tr
                            v-for="inv in recentInvoices"
                            :key="inv.id"
                            class="group transition-colors duration-150 hover:bg-indigo-50/40"
                        >
                            <td class="px-6 py-4">
                                <Link
                                    :href="`/invoices/${inv.id}`"
                                    class="cursor-pointer text-sm font-bold text-indigo-700 transition-colors hover:text-indigo-500"
                                >
                                    {{ inv.invoice_number }}
                                </Link>
                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-indigo-500">{{ inv.client_name }}</td>
                            <td class="px-4 py-4">
                                <span
                                    class="inline-flex rounded-xl border-2 px-2.5 py-1 text-[11px] font-bold"
                                    :class="statusConfig[inv.status]?.bg ?? 'bg-indigo-50 text-indigo-400 border-indigo-100'"
                                >
                                    {{ statusConfig[inv.status]?.label ?? inv.status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-[11px] font-medium text-indigo-300">{{ inv.issue_date }}</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-indigo-900">${{ inv.total.toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div v-else class="px-6 py-12 text-center">
                    <div
                        class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100"
                        style="box-shadow: 0 6px 16px rgba(99,102,241,0.12);"
                    >
                        <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-base font-bold text-indigo-400" style="font-family: 'Fredoka', sans-serif;">No invoices yet</p>
                    <Link
                        href="/invoices/create"
                        class="cursor-pointer mt-2 inline-block text-sm font-bold text-indigo-500 transition-colors hover:text-indigo-700"
                    >
                        Create your first invoice →
                    </Link>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
