<script setup>
    import SeoHead from '@/Components/SeoHead.vue'
    import { Link, usePage } from '@inertiajs/vue3'
    import { computed } from 'vue'

    const props = defineProps({
        plans: { type: Array, default: () => [] },
    })

    const page = usePage()

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

    const steps = [
        { n: '01', color: 'bg-indigo-500', ring: 'ring-indigo-200', title: 'Create a client', desc: 'Add the customer once — contacts and billing details ready every time.' },
        { n: '02', color: 'bg-sky-500', ring: 'ring-sky-200', title: 'Send the invoice', desc: 'Build a clean invoice, generate a PDF, and send a secure payment link.' },
        { n: '03', color: 'bg-emerald-500', ring: 'ring-emerald-200', title: 'Get paid online', desc: 'Stripe handles checkout; Billable keeps status in sync automatically.' },
        { n: '04', color: 'bg-amber-500', ring: 'ring-amber-200', title: 'Track the result', desc: 'See paid, outstanding, and overdue revenue before the next follow-up.' },
    ]

    const faqs = [
        { question: 'Can I start with a free plan?', answer: 'Yes. Create a workspace and start on the free plan before upgrading.' },
        { question: 'Do clients need a Billable account to pay?', answer: 'No. Clients pay from the invoice payment page without signing in.' },
        { question: 'Does each workspace get its own subdomain?', answer: 'Yes. Each team gets a separate subdomain so billing data stays organized.' },
        { question: 'Can I collect payments with Stripe?', answer: 'Yes. Paid plans use Stripe Checkout for secure online payments.' },
        { question: 'Can I change plans later?', answer: 'Yes. Start free, upgrade when needed, and manage billing from your workspace.' },
    ]

    const baseUrl = computed(() => String(page.props.seo?.base_url ?? '').replace(/\/$/, ''))
    const structuredData = computed(() => ({
        '@context': 'https://schema.org',
        '@graph': [
            {
                '@type': 'SoftwareApplication',
                name: 'Billable',
                applicationCategory: 'BusinessApplication',
                operatingSystem: 'Web',
                url: absoluteUrl('/'),
                description: 'Online invoicing software for small teams.',
                offers: props.plans.map((plan) => ({
                    '@type': 'Offer',
                    name: `${plan.name} plan`,
                    price: Number(plan.price),
                    priceCurrency: 'USD',
                    availability: 'https://schema.org/InStock',
                    url: absoluteUrl(registerUrl(plan)),
                })),
            },
            {
                '@type': 'FAQPage',
                mainEntity: faqs.map((faq) => ({
                    '@type': 'Question',
                    name: faq.question,
                    acceptedAnswer: { '@type': 'Answer', text: faq.answer },
                })),
            },
        ],
    }))

    function absoluteUrl(path) {
        if (/^https?:\/\//i.test(path)) return path
        const cleanPath = String(path).startsWith('/') ? path : `/${path}`
        return baseUrl.value ? `${baseUrl.value}${cleanPath}` : cleanPath
    }

    function formatPrice(price) {
        return Number(price) === 0 ? 'Free' : `$${Number(price)}/mo`
    }

    function isPro(plan) {
        return plan.slug === 'pro'
    }

    function registerUrl(plan = null) {
        return plan?.slug ? `/register?plan=${encodeURIComponent(plan.slug)}` : '/register'
    }
</script>

<template>
    <SeoHead
        title="Online Invoicing for Small Teams"
        description="Billable gives small teams a dedicated billing workspace to manage clients, send professional invoices, and collect Stripe payments online."
        canonical="/"
        :schema="structuredData"
    />

    <!-- Root: soft lavender background -->
    <div class="min-h-screen overflow-x-hidden" style="background: linear-gradient(160deg, #EDE9FF 0%, #F0F4FF 40%, #E8F5F0 100%);">

        <!-- Background blobs -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -top-24 -left-24 h-[480px] w-[480px] rounded-full bg-indigo-300/20 blur-[80px]"></div>
            <div class="absolute top-[30vh] -right-32 h-[400px] w-[400px] rounded-full bg-emerald-300/15 blur-[80px]"></div>
            <div class="absolute bottom-[20vh] -left-20 h-[360px] w-[360px] rounded-full bg-amber-300/12 blur-[80px]"></div>
            <div class="absolute -bottom-20 right-[20%] h-[300px] w-[400px] rounded-full bg-sky-300/15 blur-[80px]"></div>
        </div>

        <!-- ─── Navbar ─────────────────────────────── -->
        <nav class="sticky top-0 z-50 px-5 pt-4 pb-0 sm:px-6">
            <div class="mx-auto max-w-6xl">
                <div
                    class="clay-card flex items-center justify-between px-5 py-3"
                    style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px);"
                >
                    <Link href="/" class="cursor-pointer flex items-center gap-2.5" aria-label="Billable home">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-2xl bg-indigo-500"
                            style="box-shadow: 0 4px 12px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.3);"
                        >
                            <svg class="h-4.5 w-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">
                            bill<span class="text-indigo-500">able</span>
                        </span>
                    </Link>

                    <div class="flex items-center gap-2">
                        <Link
                            href="/login"
                            class="cursor-pointer rounded-2xl px-4 py-2 text-sm font-semibold text-indigo-600 transition-colors hover:bg-indigo-50"
                        >
                            Sign in
                        </Link>
                        <Link
                            :href="registerUrl()"
                            class="clay-btn cursor-pointer inline-flex items-center gap-1.5 rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-bold text-white"
                            style="box-shadow: 0 6px 20px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.25);"
                        >
                            Start free
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="px-5 sm:px-6">

            <!-- ─── Hero ──────────────────────────────── -->
            <section class="mx-auto max-w-6xl pt-14 pb-10 sm:pt-20 sm:pb-14">
                <div class="grid items-center gap-12 lg:grid-cols-[1fr_1.05fr]">

                    <!-- Left: Copy -->
                    <div>
                        <!-- Floating badge -->
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

                        <!-- Social proof chips -->
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
                        <!-- Floating decorative blobs -->
                        <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-2xl rotate-12 bg-amber-300 opacity-60" style="box-shadow: 0 8px 20px rgba(245,158,11,0.3);"></div>
                        <div class="pointer-events-none absolute -left-6 -bottom-6 h-12 w-12 rounded-xl -rotate-12 bg-emerald-400 opacity-50" style="box-shadow: 0 8px 20px rgba(16,185,129,0.3);"></div>
                        <div class="pointer-events-none absolute right-[10%] -bottom-4 h-8 w-8 rounded-xl rotate-6 bg-rose-400 opacity-40"></div>

                        <!-- Mock UI card -->
                        <div class="clay-card overflow-hidden bg-white/90 p-4 backdrop-blur-sm">

                            <!-- Top bar -->
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

                            <!-- Metric chips -->
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

                            <!-- Invoice rows -->
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

            <!-- ─── Features ───────────────────────────── -->
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

                <!-- Bento grid -->
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

            <!-- ─── How it works ───────────────────────── -->
            <section class="mx-auto max-w-6xl py-16 sm:py-20">
                <div class="mb-12 text-center">
                    <p class="mb-2 text-sm font-bold uppercase tracking-widest text-emerald-500">How it works</p>
                    <h2
                        class="text-4xl font-bold text-indigo-950 sm:text-5xl"
                        style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
                    >
                        Four steps. Client to collected.
                    </h2>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <article
                        v-for="step in steps"
                        :key="step.n"
                        class="clay-card bg-white p-6"
                    >
                        <!-- Step number bubble -->
                        <div
                            class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl ring-4 text-white"
                            :class="[step.color, step.ring]"
                            style="box-shadow: 0 6px 16px rgba(0,0,0,0.12), inset 0 1px 0 rgba(255,255,255,0.25);"
                        >
                            <span class="text-lg font-black" style="font-family: 'Fredoka', sans-serif;">{{ step.n }}</span>
                        </div>
                        <h3 class="mb-2 text-base font-extrabold text-indigo-950" style="font-family: 'Fredoka', sans-serif;">
                            {{ step.title }}
                        </h3>
                        <p class="text-sm leading-6 text-indigo-800/60">{{ step.desc }}</p>
                    </article>
                </div>
            </section>

            <!-- ─── Pricing ─────────────────────────────── -->
            <section id="pricing" class="mx-auto max-w-6xl py-16 sm:py-20">
                <div class="mb-12 text-center">
                    <p class="mb-2 text-sm font-bold uppercase tracking-widest text-indigo-400">Pricing</p>
                    <h2
                        class="text-4xl font-bold text-indigo-950 sm:text-5xl"
                        style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
                    >
                        Start free. Grow when ready.
                    </h2>
                    <p class="mx-auto mt-4 max-w-xl text-lg text-indigo-800/60">
                        Pick the plan that fits your team. Switch anytime — no surprises.
                    </p>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <article
                        v-for="plan in plans"
                        :key="plan.slug"
                        :class="[
                            'relative flex flex-col rounded-3xl border-3 p-7 transition-transform duration-200 hover:-translate-y-1',
                            isPro(plan)
                                ? 'clay-card-indigo border-indigo-300 bg-indigo-50'
                                : 'clay-card bg-white',
                        ]"
                    >
                        <!-- Most popular badge -->
                        <div v-if="isPro(plan)" class="absolute -top-4 left-1/2 -translate-x-1/2">
                            <span
                                class="clay-btn inline-flex items-center gap-1.5 rounded-2xl bg-indigo-500 px-4 py-1.5 text-xs font-black text-white"
                                style="box-shadow: 0 6px 16px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.25);"
                            >
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                Most popular
                            </span>
                        </div>

                        <div class="mb-6">
                            <h3
                                class="text-xl font-bold capitalize"
                                style="font-family: 'Fredoka', sans-serif;"
                                :class="isPro(plan) ? 'text-indigo-900' : 'text-indigo-950'"
                            >
                                {{ plan.name }}
                            </h3>
                            <div class="mt-3">
                                <span
                                    class="text-5xl font-black"
                                    style="font-family: 'Fredoka', sans-serif;"
                                    :class="isPro(plan) ? 'text-indigo-600' : 'text-indigo-950'"
                                >
                                    {{ formatPrice(plan.price) }}
                                </span>
                            </div>
                        </div>

                        <ul class="mb-8 flex-1 space-y-3">
                            <li
                                v-for="feature in plan.features"
                                :key="feature"
                                class="flex items-start gap-2.5 text-sm text-indigo-800/70"
                            >
                                <span
                                    class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full"
                                    :class="isPro(plan) ? 'bg-indigo-500 text-white' : 'bg-emerald-100 text-emerald-600'"
                                >
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span>{{ feature }}</span>
                            </li>
                        </ul>

                        <Link
                            :href="registerUrl(plan)"
                            :class="[
                                'clay-btn cursor-pointer block rounded-2xl py-3.5 text-center text-sm font-black transition-colors',
                                isPro(plan)
                                    ? 'bg-indigo-500 text-white'
                                    : 'border-2 border-indigo-200 bg-white text-indigo-600',
                            ]"
                            :style="isPro(plan) ? 'box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);' : 'box-shadow: 0 4px 12px rgba(99,102,241,0.10);'"
                        >
                            {{ Number(plan.price) === 0 ? 'Start free' : 'Choose ' + plan.name }}
                        </Link>
                    </article>
                </div>
            </section>

            <!-- ─── FAQ ────────────────────────────────── -->
            <section class="mx-auto max-w-5xl py-16 sm:py-20">
                <div class="mb-12 text-center">
                    <p class="mb-2 text-sm font-bold uppercase tracking-widest text-amber-500">FAQ</p>
                    <h2
                        class="text-4xl font-bold text-indigo-950 sm:text-5xl"
                        style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
                    >
                        Got questions?
                    </h2>
                    <p class="mt-4 text-lg text-indigo-800/60">Everything you need to know before creating a workspace.</p>
                </div>

                <div class="space-y-3">
                    <details
                        v-for="faq in faqs"
                        :key="faq.question"
                        class="clay-card group cursor-pointer bg-white px-6 py-5 transition-transform duration-150 open:-translate-y-0.5"
                    >
                        <summary class="flex list-none items-center justify-between gap-4 text-base font-extrabold text-indigo-900 focus:outline-none" style="font-family: 'Fredoka', sans-serif;">
                            {{ faq.question }}
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-indigo-100 transition-transform duration-200 group-open:rotate-180">
                                <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </summary>
                        <p class="mt-3 text-base leading-7 text-indigo-800/60">{{ faq.answer }}</p>
                    </details>
                </div>
            </section>

            <!-- ─── CTA Banner ─────────────────────────── -->
            <section class="mx-auto max-w-6xl pb-20 sm:pb-24">
                <div
                    class="relative overflow-hidden rounded-3xl p-10 text-center md:p-16"
                    style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 50%, #A78BFA 100%); box-shadow: 0 20px 60px rgba(99,102,241,0.35), inset 0 1px 0 rgba(255,255,255,0.20), inset -2px -4px 8px rgba(0,0,0,0.10);"
                >
                    <!-- Decorative bubbles -->
                    <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10"></div>
                    <div class="pointer-events-none absolute -left-6 bottom-0 h-24 w-24 rounded-full bg-white/8"></div>
                    <div class="pointer-events-none absolute right-[20%] -bottom-4 h-16 w-16 rounded-full bg-amber-300/30"></div>
                    <div class="pointer-events-none absolute left-[25%] -top-4 h-12 w-12 rounded-full bg-emerald-300/30"></div>

                    <div class="relative">
                        <h2
                            class="text-4xl font-bold text-white sm:text-5xl"
                            style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
                        >
                            Ready to make billing easier?
                        </h2>
                        <p class="mx-auto mt-4 max-w-xl text-lg text-white/75">
                            Create your workspace, add your first client, and send your first invoice — no credit card needed.
                        </p>
                        <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                            <Link
                                :href="registerUrl()"
                                class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-white px-8 py-4 text-base font-black text-indigo-600"
                                style="box-shadow: 0 8px 24px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.9);"
                            >
                                Create free workspace
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </Link>
                            <a
                                href="#pricing"
                                class="cursor-pointer text-base font-semibold text-white/75 transition-colors hover:text-white"
                            >
                                See pricing →
                            </a>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- ─── Footer ─────────────────────────────── -->
        <footer class="border-t-2 border-indigo-100 px-5 py-8 sm:px-6">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 sm:flex-row">
                <Link href="/" class="cursor-pointer flex items-center gap-2" aria-label="Billable home">
                    <div
                        class="flex h-7 w-7 items-center justify-center rounded-xl bg-indigo-500"
                        style="box-shadow: 0 4px 10px rgba(99,102,241,0.30);"
                    >
                        <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-base font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif;">
                        bill<span class="text-indigo-500">able</span>
                    </span>
                </Link>
                <p class="text-sm text-indigo-400">
                    Built with <span class="text-rose-400">♥</span> by <a href="https://syahmidev.com" target="_blank" class="text-indigo-500">syahmidev</a>
                </p>
            </div>
        </footer>
    </div>
</template>
