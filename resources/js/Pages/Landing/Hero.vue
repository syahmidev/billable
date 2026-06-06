<script setup>
    import { Link } from '@inertiajs/vue3'

    const metrics = [
        { label: 'Collected', value: '$18,240', color: 'text-emerald-600', bg: 'bg-emerald-50', border: 'border-emerald-200' },
        { label: 'Outstanding', value: '$4,810', color: 'text-amber-600', bg: 'bg-amber-50', border: 'border-amber-200' },
        { label: 'Overdue', value: '$920', color: 'text-rose-500', bg: 'bg-rose-50', border: 'border-rose-200' },
    ]

    const invoices = [
        { client: 'Northstar Labs', number: 'INV-1042', amount: '$2,400', status: 'Paid', bg: 'bg-emerald-100 text-emerald-700' },
        { client: 'Meridian Studio', number: 'INV-1041', amount: '$1,250', status: 'Sent', bg: 'bg-sky-100 text-sky-700' },
        { client: 'Atlas Coffee', number: 'INV-1040', amount: '$780', status: 'Due soon', bg: 'bg-amber-100 text-amber-700' },
    ]

    function registerUrl() {
        return '/register'
    }
</script>

<template>
    <section class="mx-auto max-w-6xl pt-14 pb-10 sm:pt-20 sm:pb-14">
        <div class="grid items-center gap-12 lg:grid-cols-[1fr_1.05fr]">

            <!-- Left: Copy -->
            <div>
                <div
                    class="mb-6 inline-flex items-center gap-2 rounded-2xl border-2 border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700"
                    style="box-shadow: 0 4px 12px rgba(16,185,129,0.15);"
                >
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                    Invoicing made actually fun
                </div>

                <h1
                    class="text-5xl font-bold leading-[1.1] text-indigo-950 sm:text-6xl"
                    style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
                >
                    Send invoices.<br>
                    <span class="text-indigo-500">Collect payment.</span><br>
                    Stay on top.
                </h1>

                <p class="mt-6 max-w-md text-lg leading-7 text-indigo-800/60">
                    Billable keeps clients, invoices, Stripe payments, and revenue in one billing workspace your team will actually use.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <Link
                        :href="registerUrl()"
                        class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-indigo-500 px-7 py-3.5 text-base font-bold text-white"
                        style="box-shadow: 0 8px 24px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.25);"
                    >
                        Create free workspace
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                    <a
                        href="#pricing"
                        class="clay-btn-secondary cursor-pointer inline-flex items-center gap-2 rounded-2xl border-2 border-indigo-200 bg-white px-7 py-3.5 text-base font-bold text-indigo-600"
                        style="box-shadow: 0 6px 16px rgba(99,102,241,0.10);"
                    >
                        View plans
                    </a>
                </div>

                <div class="mt-8 flex flex-wrap gap-2">
                    <span
                        v-for="label in ['PDF invoices', 'Stripe payments', 'Client records', 'Team billing']"
                        :key="label"
                        class="rounded-xl border-2 border-indigo-100 bg-white px-3 py-1 text-xs font-semibold text-indigo-500"
                        style="box-shadow: 0 2px 8px rgba(99,102,241,0.08);"
                    >
                        {{ label }}
                    </span>
                </div>
            </div>

            <!-- Right: App mock -->
            <div class="relative">
                <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-2xl rotate-12 bg-amber-300 opacity-60" style="box-shadow: 0 8px 20px rgba(245,158,11,0.3);"></div>
                <div class="pointer-events-none absolute -left-6 -bottom-6 h-12 w-12 rounded-xl -rotate-12 bg-emerald-400 opacity-50" style="box-shadow: 0 8px 20px rgba(16,185,129,0.3);"></div>
                <div class="pointer-events-none absolute right-[10%] -bottom-4 h-8 w-8 rounded-xl rotate-6 bg-rose-400 opacity-40"></div>

                <div class="clay-card overflow-hidden bg-white/90 p-4 backdrop-blur-sm">
                    <div class="mb-3 flex items-center justify-between rounded-2xl bg-indigo-50 px-4 py-3 border-2 border-indigo-100">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">Workspace</p>
                            <p class="text-sm font-bold text-indigo-900">acme-studio.billable.app</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-100 px-3 py-1 text-[11px] font-bold text-emerald-700 border-2 border-emerald-200">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                            Live
                        </span>
                    </div>

                    <div class="mb-3 grid grid-cols-3 gap-2">
                        <div
                            v-for="m in metrics"
                            :key="m.label"
                            class="rounded-2xl border-2 p-3 text-center"
                            :class="[m.bg, m.border]"
                        >
                            <p class="text-[10px] font-semibold" :class="m.color">{{ m.label }}</p>
                            <p class="mt-1 text-base font-black" :class="m.color">{{ m.value }}</p>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-2xl border-2 border-indigo-100 bg-white">
                        <div class="grid grid-cols-[1.2fr_0.8fr_0.7fr_0.9fr] bg-indigo-50 px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-indigo-400">
                            <span>Client</span>
                            <span>Invoice</span>
                            <span>Amount</span>
                            <span>Status</span>
                        </div>
                        <div
                            v-for="inv in invoices"
                            :key="inv.number"
                            class="grid grid-cols-[1.2fr_0.8fr_0.7fr_0.9fr] items-center border-t border-indigo-50 px-4 py-3 text-xs"
                        >
                            <span class="truncate font-bold text-indigo-900">{{ inv.client }}</span>
                            <span class="text-indigo-400">{{ inv.number }}</span>
                            <span class="font-black text-indigo-900">{{ inv.amount }}</span>
                            <span>
                                <span class="rounded-xl px-2.5 py-1 text-[11px] font-bold" :class="inv.bg">{{ inv.status }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
