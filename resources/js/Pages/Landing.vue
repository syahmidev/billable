<script setup>
    import SeoHead from '@/Components/SeoHead.vue'
    import { Link, usePage } from '@inertiajs/vue3'
    import { computed } from 'vue'

    const props = defineProps({
        plans: { type: Array, default: () => [] },
    })

    const page = usePage()

    const proofPoints = [
        'Dedicated workspace',
        'PDF invoices',
        'Stripe checkout',
        'Client records',
        'Revenue overview',
    ]

    const metrics = [
        { label: 'Collected', value: '$18,240', tone: 'text-emerald-300' },
        { label: 'Outstanding', value: '$4,810', tone: 'text-amber-300' },
        { label: 'Overdue', value: '$920', tone: 'text-rose-300' },
    ]

    const invoices = [
        {
            client: 'Northstar Labs',
            number: 'INV-1042',
            amount: '$2,400',
            status: 'Paid',
            tone: 'bg-emerald-400/10 text-emerald-300',
        },
        {
            client: 'Meridian Studio',
            number: 'INV-1041',
            amount: '$1,250',
            status: 'Sent',
            tone: 'bg-sky-400/10 text-sky-300',
        },
        {
            client: 'Atlas Coffee',
            number: 'INV-1040',
            amount: '$780',
            status: 'Due soon',
            tone: 'bg-amber-400/10 text-amber-300',
        },
    ]

    const features = [
        {
            eyebrow: 'Workspace',
            title: 'Give every team a clear home for billing',
            desc: 'Each business gets a separate workspace with its own subdomain, clients, invoices, and billing settings.',
            accent: 'bg-violet-500',
        },
        {
            eyebrow: 'Invoices',
            title: 'Send professional invoices without extra admin',
            desc: 'Create invoices, generate PDFs, email clients, and keep draft, sent, paid, and overdue work organized.',
            accent: 'bg-sky-500',
        },
        {
            eyebrow: 'Payments',
            title: 'Collect online payments through Stripe',
            desc: 'Clients can pay from a secure invoice link while your team keeps the payment status visible.',
            accent: 'bg-emerald-500',
        },
        {
            eyebrow: 'Tracking',
            title: 'See revenue, unpaid work, and client balances',
            desc: 'Track monthly revenue, outstanding invoices, overdue accounts, and recent billing activity from one place.',
            accent: 'bg-amber-500',
        },
        {
            eyebrow: 'Clients',
            title: 'Keep client records next to the money',
            desc: 'Store contacts, billing details, invoice history, and outstanding balances without jumping between tools.',
            accent: 'bg-rose-500',
        },
        {
            eyebrow: 'Control',
            title: 'Manage plans and workspaces as the platform grows',
            desc: 'Platform owners can manage tenants, subscriptions, plans, and admin access from the built-in control area.',
            accent: 'bg-indigo-500',
        },
    ]

    const workflow = [
        {
            step: '01',
            title: 'Create a client',
            desc: 'Add the customer once and keep their contact and billing details ready for every invoice.',
        },
        {
            step: '02',
            title: 'Send the invoice',
            desc: 'Build a clean invoice, generate a PDF, and send it with a secure payment link.',
        },
        {
            step: '03',
            title: 'Get paid online',
            desc: 'Stripe handles checkout while Billable keeps the invoice status in sync for your team.',
        },
        {
            step: '04',
            title: 'Track the result',
            desc: 'Watch paid, outstanding, and overdue revenue from the dashboard before the next follow-up.',
        },
    ]

    const faqs = [
        {
            question: 'Can I start with a free plan?',
            answer: 'Yes. You can create a workspace and start on the free plan before upgrading.',
        },
        {
            question: 'Do clients need a Billable account to pay?',
            answer: 'No. Clients can pay from the invoice payment page without signing in.',
        },
        {
            question: 'Does each workspace get its own subdomain?',
            answer: 'Yes. Each team gets a separate workspace on its own subdomain so billing data stays organized.',
        },
        {
            question: 'Can I collect payments with Stripe?',
            answer: 'Yes. Paid plans use Stripe Checkout and Stripe billing tools for secure online payments.',
        },
        {
            question: 'Can I change plans later?',
            answer: 'Yes. You can start free, upgrade when you need more room, and manage billing from your workspace.',
        },
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
                description:
                    'Online invoicing software for small teams that manage clients, send invoices, and collect Stripe payments.',
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
                    acceptedAnswer: {
                        '@type': 'Answer',
                        text: faq.answer,
                    },
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

    <div class="min-h-screen bg-gray-950 text-white">
        <nav class="border-b border-white/10 px-5 py-4 sm:px-6">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4">
                <Link href="/" class="flex items-center gap-2" aria-label="Billable home">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-600 text-sm font-bold"
                    >
                        B
                    </span>
                    <span class="text-lg font-semibold">billable</span>
                </Link>
                <div class="flex items-center gap-2 sm:gap-3">
                    <Link
                        href="/login"
                        class="rounded-lg px-3 py-2 text-sm text-gray-400 transition hover:text-white"
                    >
                        Sign in
                    </Link>
                    <Link
                        :href="registerUrl()"
                        class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-500"
                    >
                        Start free
                    </Link>
                </div>
            </div>
        </nav>

        <main>
            <section class="px-5 py-10 sm:px-6 sm:py-14 lg:py-16">
                <div
                    class="mx-auto grid max-w-6xl items-center gap-10 lg:grid-cols-[0.92fr_1.08fr]"
                >
                    <div>
                        <div
                            class="mb-5 inline-flex items-center gap-2 rounded-lg border border-emerald-400/20 bg-emerald-400/10 px-3 py-1.5 text-sm text-emerald-200"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                            Online invoicing with dedicated workspaces
                        </div>

                        <h1 class="max-w-2xl text-4xl font-bold leading-tight sm:text-5xl">
                            Send invoices, collect payment, and know what is overdue.
                        </h1>

                        <p class="mt-5 max-w-xl text-base leading-7 text-gray-300 sm:text-lg">
                            Billable keeps clients, invoices, Stripe payments, and revenue tracking
                            together in one billing workspace for your team.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <Link
                                :href="registerUrl()"
                                class="inline-flex items-center justify-center rounded-lg bg-violet-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-violet-500"
                            >
                                Create free workspace
                            </Link>
                            <a
                                href="#pricing"
                                class="inline-flex items-center justify-center rounded-lg border border-white/10 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                            >
                                View plans
                            </a>
                        </div>
                    </div>

                    <div
                        class="rounded-lg border border-white/10 bg-gray-900 p-3 shadow-2xl shadow-black/30"
                    >
                        <div class="rounded-lg border border-white/10 bg-gray-950">
                            <div
                                class="flex items-center justify-between border-b border-white/10 px-4 py-3"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">Workspace</p>
                                    <p class="text-sm font-semibold text-white">
                                        acme-studio.billable.test
                                    </p>
                                </div>
                                <span
                                    class="rounded-lg bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-300"
                                >
                                    Live
                                </span>
                            </div>

                            <div class="grid gap-3 p-4 sm:grid-cols-3">
                                <div
                                    v-for="metric in metrics"
                                    :key="metric.label"
                                    class="rounded-lg border border-white/10 bg-white/[0.03] p-4"
                                >
                                    <p class="text-xs text-gray-500">{{ metric.label }}</p>
                                    <p class="mt-2 text-xl font-bold" :class="metric.tone">
                                        {{ metric.value }}
                                    </p>
                                </div>
                            </div>

                            <div class="px-4 pb-4">
                                <div class="overflow-hidden rounded-lg border border-white/10">
                                    <div
                                        class="grid grid-cols-[1.2fr_0.8fr_0.7fr_0.8fr] bg-white/[0.04] px-4 py-2 text-xs text-gray-500"
                                    >
                                        <span>Client</span>
                                        <span>Invoice</span>
                                        <span>Amount</span>
                                        <span>Status</span>
                                    </div>
                                    <div
                                        v-for="invoice in invoices"
                                        :key="invoice.number"
                                        class="grid grid-cols-[1.2fr_0.8fr_0.7fr_0.8fr] items-center border-t border-white/10 px-4 py-3 text-xs sm:text-sm"
                                    >
                                        <span class="truncate text-gray-200">{{
                                            invoice.client
                                        }}</span>
                                        <span class="text-gray-500">{{ invoice.number }}</span>
                                        <span class="font-medium text-white">{{
                                            invoice.amount
                                        }}</span>
                                        <span>
                                            <span
                                                class="rounded-lg px-2 py-1 text-xs font-medium"
                                                :class="invoice.tone"
                                            >
                                                {{ invoice.status }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="border-y border-white/5 bg-white/[0.02] px-5 py-4 sm:px-6">
                <div
                    class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-8 gap-y-3 text-sm text-gray-400"
                >
                    <span v-for="point in proofPoints" :key="point">{{ point }}</span>
                </div>
            </div>

            <section class="px-5 py-16 sm:px-6 sm:py-20">
                <div class="mx-auto max-w-6xl">
                    <div class="max-w-2xl">
                        <p class="text-sm font-medium text-violet-300">What Billable handles</p>
                        <h2 class="mt-3 text-3xl font-bold sm:text-4xl">
                            A billing workflow your team can actually follow.
                        </h2>
                        <p class="mt-4 text-gray-400">
                            From client setup to paid invoice, the important billing work stays
                            visible and connected.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="feature in features"
                            :key="feature.title"
                            class="rounded-lg border border-white/10 bg-white/[0.03] p-5 transition hover:border-white/20 hover:bg-white/[0.05]"
                        >
                            <div class="mb-4 flex items-center gap-3">
                                <span
                                    class="h-2.5 w-2.5 rounded-full"
                                    :class="feature.accent"
                                ></span>
                                <span class="text-xs font-medium uppercase text-gray-500">{{
                                    feature.eyebrow
                                }}</span>
                            </div>
                            <h3 class="text-base font-semibold text-white">{{ feature.title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-gray-400">{{ feature.desc }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="border-y border-white/5 bg-gray-900/70 px-5 py-16 sm:px-6 sm:py-20">
                <div class="mx-auto max-w-6xl">
                    <div class="grid gap-10 lg:grid-cols-[0.75fr_1.25fr] lg:items-start">
                        <div>
                            <p class="text-sm font-medium text-emerald-300">How it works</p>
                            <h2 class="mt-3 text-3xl font-bold sm:text-4xl">
                                Four steps from client to collected.
                            </h2>
                            <p class="mt-4 text-gray-400">
                                Billable is shaped around the repeated work of getting invoices out
                                and paid without losing track of follow-up.
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <article
                                v-for="item in workflow"
                                :key="item.step"
                                class="rounded-lg border border-white/10 bg-gray-950 p-5"
                            >
                                <p class="text-sm font-semibold text-violet-300">{{ item.step }}</p>
                                <h3 class="mt-3 text-base font-semibold text-white">
                                    {{ item.title }}
                                </h3>
                                <p class="mt-2 text-sm leading-6 text-gray-400">{{ item.desc }}</p>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <section id="pricing" class="px-5 py-16 sm:px-6 sm:py-20">
                <div class="mx-auto max-w-6xl">
                    <div class="mx-auto max-w-2xl text-center">
                        <p class="text-sm font-medium text-violet-300">Plans</p>
                        <h2 class="mt-3 text-3xl font-bold sm:text-4xl">
                            Start free. Upgrade when billing gets busy.
                        </h2>
                        <p class="mt-4 text-gray-400">
                            Choose the amount of room your team needs. You can move to a larger plan
                            later.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-4 md:grid-cols-3">
                        <article
                            v-for="plan in plans"
                            :key="plan.slug"
                            :class="[
                                'relative flex flex-col rounded-lg border p-6 transition',
                                isPro(plan)
                                    ? 'border-violet-400 bg-violet-500/10 ring-1 ring-violet-400/40'
                                    : 'border-white/10 bg-white/[0.03] hover:border-white/20',
                            ]"
                        >
                            <div v-if="isPro(plan)" class="absolute -top-3 left-6">
                                <span
                                    class="rounded-lg bg-violet-600 px-3 py-1 text-xs font-semibold text-white"
                                >
                                    Most popular
                                </span>
                            </div>

                            <div class="mb-6">
                                <h3 class="text-lg font-semibold capitalize text-white">
                                    {{ plan.name }}
                                </h3>
                                <div class="mt-3 text-4xl font-bold text-white">
                                    {{ formatPrice(plan.price) }}
                                </div>
                            </div>

                            <ul class="mb-8 flex-1 space-y-3">
                                <li
                                    v-for="feature in plan.features"
                                    :key="feature"
                                    class="flex items-start gap-2.5 text-sm text-gray-300"
                                >
                                    <span class="mt-0.5 text-emerald-300">✓</span>
                                    <span>{{ feature }}</span>
                                </li>
                            </ul>

                            <Link
                                :href="registerUrl(plan)"
                                :class="[
                                    'block rounded-lg py-3 text-center text-sm font-semibold transition',
                                    isPro(plan)
                                        ? 'bg-violet-600 text-white hover:bg-violet-500'
                                        : 'border border-white/10 bg-white/5 text-white hover:bg-white/10',
                                ]"
                            >
                                {{
                                    Number(plan.price) === 0 ? 'Start free' : 'Choose ' + plan.name
                                }}
                            </Link>
                        </article>
                    </div>
                </div>
            </section>

            <section class="border-y border-white/5 bg-gray-900/70 px-5 py-16 sm:px-6 sm:py-20">
                <div class="mx-auto grid max-w-6xl gap-10 lg:grid-cols-[0.8fr_1.2fr]">
                    <div>
                        <p class="text-sm font-medium text-amber-300">FAQ</p>
                        <h2 class="mt-3 text-3xl font-bold sm:text-4xl">
                            Questions before you create a workspace.
                        </h2>
                    </div>

                    <div class="space-y-3">
                        <details
                            v-for="faq in faqs"
                            :key="faq.question"
                            class="rounded-lg border border-white/10 bg-gray-950 p-5 open:border-white/20"
                        >
                            <summary class="cursor-pointer text-sm font-semibold text-white">
                                {{ faq.question }}
                            </summary>
                            <p class="mt-3 text-sm leading-6 text-gray-400">{{ faq.answer }}</p>
                        </details>
                    </div>
                </div>
            </section>

            <section class="px-5 py-16 sm:px-6 sm:py-20">
                <div
                    class="mx-auto flex max-w-6xl flex-col items-start justify-between gap-6 border-y border-white/10 py-10 md:flex-row md:items-center"
                >
                    <div>
                        <h2 class="text-3xl font-bold">Ready to make billing easier?</h2>
                        <p class="mt-3 max-w-xl text-gray-400">
                            Create your workspace, add your first client, and send your first
                            invoice when you are ready.
                        </p>
                    </div>
                    <Link
                        :href="registerUrl()"
                        class="inline-flex rounded-lg bg-violet-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-violet-500"
                    >
                        Create free workspace
                    </Link>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 px-5 py-8 sm:px-6">
            <div
                class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 sm:flex-row"
            >
                <Link href="/" class="flex items-center gap-2" aria-label="Billable home">
                    <span
                        class="flex h-6 w-6 items-center justify-center rounded bg-violet-600 text-xs font-bold"
                    >
                        B
                    </span>
                    <span class="text-sm font-medium text-gray-400">billable</span>
                </Link>
                <p class="text-xs text-gray-600">
                    Built with <span class="text-violet-400">♥</span> by Syahmi & Miyu
                </p>
            </div>
        </footer>
    </div>
</template>
