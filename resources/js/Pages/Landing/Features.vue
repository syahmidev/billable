<script setup>
    const features = [
        {
            eyebrow: 'Workspaces',
            title: 'One home for every team\'s billing',
            desc: 'Each business gets its own workspace with a subdomain, clients, invoices, and settings — all separated and tidy.',
            cardClass: 'clay-card-indigo',
            iconBg: 'bg-indigo-100',
            iconColor: 'text-indigo-600',
            badge: 'bg-indigo-100 text-indigo-600',
            icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            span: 'col-span-2',
        },
        {
            eyebrow: 'Invoices',
            title: 'Professional invoices in seconds',
            desc: 'Build, PDF, and email — organized by draft, sent, paid, and overdue.',
            cardClass: 'clay-card-sky',
            iconBg: 'bg-sky-100',
            iconColor: 'text-sky-600',
            badge: 'bg-sky-100 text-sky-600',
            icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            span: '',
        },
        {
            eyebrow: 'Payments',
            title: 'Stripe checkout, built right in',
            desc: 'Clients pay from a secure link. Your team sees status updates automatically.',
            cardClass: 'clay-card-emerald',
            iconBg: 'bg-emerald-100',
            iconColor: 'text-emerald-600',
            badge: 'bg-emerald-100 text-emerald-600',
            icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
            span: '',
        },
        {
            eyebrow: 'Tracking',
            title: 'See your revenue at a glance',
            desc: 'Monthly revenue, outstanding, and overdue all on one dashboard.',
            cardClass: 'clay-card-amber',
            iconBg: 'bg-amber-100',
            iconColor: 'text-amber-600',
            badge: 'bg-amber-100 text-amber-600',
            icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            span: '',
        },
        {
            eyebrow: 'Clients',
            title: 'Client records beside every invoice',
            desc: 'Contacts, billing details, invoice history, and balances — no extra tools.',
            cardClass: 'clay-card-rose',
            iconBg: 'bg-rose-100',
            iconColor: 'text-rose-500',
            badge: 'bg-rose-100 text-rose-600',
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            span: '',
        },
        {
            eyebrow: 'Control',
            title: 'Platform-level controls for owners',
            desc: 'Manage tenants, subscriptions, plans, and admin access from a built-in control area.',
            cardClass: 'clay-card-violet',
            iconBg: 'bg-violet-100',
            iconColor: 'text-violet-600',
            badge: 'bg-violet-100 text-violet-600',
            icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
            span: '',
        },
    ]
</script>

<template>
    <section class="mx-auto max-w-6xl py-16 sm:py-20">
        <div class="mb-12 text-center">
            <p class="mb-2 text-sm font-bold uppercase tracking-widest text-indigo-400">What Billable handles</p>
            <h2
                class="text-4xl font-bold text-indigo-950 sm:text-5xl"
                style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
            >
                A billing workflow<br class="hidden sm:block"> your team can follow.
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-indigo-800/60">
                From first client to paid invoice — every step is visible and connected.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <article
                v-for="feat in features"
                :key="feat.title"
                class="clay-card group cursor-pointer bg-white p-6 transition-transform duration-200 hover:-translate-y-1"
                :class="[feat.cardClass, feat.span === 'col-span-2' ? 'lg:col-span-2' : '']"
            >
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl border-2" :class="[feat.iconBg, feat.span === 'col-span-2' ? 'border-indigo-200' : 'border-current/20']">
                        <svg class="h-5 w-5" :class="feat.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="feat.icon" />
                        </svg>
                    </div>
                    <span class="rounded-xl px-3 py-1 text-[11px] font-bold uppercase tracking-wider" :class="feat.badge">
                        {{ feat.eyebrow }}
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-indigo-950" style="font-family: 'Fredoka', sans-serif;">
                    {{ feat.title }}
                </h3>
                <p class="mt-2 text-sm leading-6 text-indigo-800/60">{{ feat.desc }}</p>
            </article>
        </div>
    </section>
</template>
