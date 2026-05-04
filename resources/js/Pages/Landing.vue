<script setup>
import SeoHead from '@/Components/SeoHead.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    plans: { type: Array, default: () => [] },
})

const features = [
    {
        icon: '🏢',
        title: 'Multi-Tenant Workspaces',
        desc: 'Every team gets their own isolated workspace with a unique subdomain. Clean, secure, and scalable.',
    },
    {
        icon: '📄',
        title: 'Professional Invoices',
        desc: 'Generate PDF invoices, send them by email, and track status from draft to paid — all in one place.',
    },
    {
        icon: '⚡',
        title: 'Stripe Payments',
        desc: 'Clients pay invoices online via a secure payment link. Stripe handles the rest, automatically.',
    },
    {
        icon: '📊',
        title: 'Revenue Analytics',
        desc: 'Track MRR, outstanding invoices, overdue accounts, and 6-month revenue trends at a glance.',
    },
    {
        icon: '👥',
        title: 'Client Management',
        desc: 'Maintain a full client directory with contact info, invoice history, and outstanding balances.',
    },
    {
        icon: '🛡️',
        title: 'Super Admin Panel',
        desc: 'Built-in Filament admin for platform owners to manage tenants, plans, and subscriptions.',
    },
]

function formatPrice(price) {
    return Number(price) === 0 ? 'Free' : `$${Number(price)}/mo`
}

function isPro(plan) {
    return plan.slug === 'pro'
}
</script>

<template>
    <SeoHead
        title="Online Invoicing for Small Teams"
        description="Billable gives teams a dedicated workspace to manage clients, send professional invoices, and collect Stripe payments online."
        canonical="/"
    />

    <div class="min-h-screen bg-gray-950 text-white">
        <!-- Nav -->
        <nav class="border-b border-white/10 px-6 py-4">
            <div class="mx-auto flex max-w-6xl items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-600">
                        <span class="text-sm font-bold">B</span>
                    </div>
                    <span class="text-lg font-semibold tracking-tight">billable</span>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/login"
                        class="rounded-lg px-4 py-2 text-sm text-gray-400 transition hover:text-white"
                    >
                        Sign In
                    </Link>
                    <Link
                        href="/register"
                        class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-violet-500"
                    >
                        Get Started
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section class="px-6 py-24 text-center">
            <div class="mx-auto max-w-3xl">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-violet-500/30 bg-violet-500/10 px-4 py-1.5 text-sm text-violet-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-violet-400"></span>
                    Multi-tenant SaaS invoicing platform
                </div>

                <h1 class="mb-6 text-5xl font-bold leading-tight tracking-tight sm:text-6xl">
                    Invoice smarter.<br />
                    <span class="text-violet-400">Get paid faster.</span>
                </h1>

                <p class="mb-10 text-lg text-gray-400">
                    billable gives your team a dedicated workspace to manage clients, send professional invoices,
                    and collect payments online — all powered by Stripe.
                </p>

                <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                    <Link
                        href="/register"
                        class="w-full rounded-xl bg-violet-600 px-8 py-3.5 text-base font-semibold text-white transition hover:bg-violet-500 sm:w-auto"
                    >
                        Start for free
                    </Link>
                    <Link
                        href="/login"
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-8 py-3.5 text-base font-semibold text-white transition hover:bg-white/10 sm:w-auto"
                    >
                        Sign in to workspace
                    </Link>
                </div>
            </div>
        </section>

        <!-- Social proof strip -->
        <div class="border-y border-white/5 bg-white/[0.02] px-6 py-5">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-center gap-x-10 gap-y-3 text-sm text-gray-500">
                <span>Laravel 13</span>
                <span class="text-gray-700">·</span>
                <span>Inertia.js + Vue 3</span>
                <span class="text-gray-700">·</span>
                <span>stancl/tenancy v3</span>
                <span class="text-gray-700">·</span>
                <span>Stripe Cashier</span>
                <span class="text-gray-700">·</span>
                <span>Filament v5</span>
                <span class="text-gray-700">·</span>
                <span>PostgreSQL</span>
            </div>
        </div>

        <!-- Features -->
        <section class="px-6 py-24">
            <div class="mx-auto max-w-6xl">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold sm:text-4xl">Everything your team needs</h2>
                    <p class="text-gray-400">Built for freelancers and small teams who need a real invoicing workflow.</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="rounded-2xl border border-white/10 bg-white/[0.03] p-6 transition hover:border-white/20 hover:bg-white/[0.05]"
                    >
                        <div class="mb-4 text-3xl">{{ feature.icon }}</div>
                        <h3 class="mb-2 text-base font-semibold">{{ feature.title }}</h3>
                        <p class="text-sm leading-relaxed text-gray-400">{{ feature.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section class="px-6 py-24">
            <div class="mx-auto max-w-5xl">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold sm:text-4xl">Simple, transparent pricing</h2>
                    <p class="text-gray-400">Start free. Upgrade when you're ready.</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-3">
                    <div
                        v-for="plan in plans"
                        :key="plan.slug"
                        :class="[
                            'relative flex flex-col rounded-2xl border p-8 transition',
                            isPro(plan)
                                ? 'border-violet-500 bg-violet-600/10 ring-1 ring-violet-500/50'
                                : 'border-white/10 bg-white/[0.03] hover:border-white/20',
                        ]"
                    >
                        <div v-if="isPro(plan)" class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="rounded-full bg-violet-600 px-3 py-1 text-xs font-semibold text-white">
                                Most Popular
                            </span>
                        </div>

                        <div class="mb-6">
                            <h3 class="mb-2 text-lg font-semibold capitalize">{{ plan.name }}</h3>
                            <div class="text-4xl font-bold">
                                {{ formatPrice(plan.price) }}
                            </div>
                        </div>

                        <ul class="mb-8 flex-1 space-y-3">
                            <li
                                v-for="feature in plan.features"
                                :key="feature"
                                class="flex items-start gap-2.5 text-sm text-gray-300"
                            >
                                <span class="mt-0.5 text-violet-400">✓</span>
                                {{ feature }}
                            </li>
                        </ul>

                        <Link
                            href="/register"
                            :class="[
                                'block rounded-xl py-3 text-center text-sm font-semibold transition',
                                isPro(plan)
                                    ? 'bg-violet-600 text-white hover:bg-violet-500'
                                    : 'border border-white/10 bg-white/5 text-white hover:bg-white/10',
                            ]"
                        >
                            {{ Number(plan.price) === 0 ? 'Get started free' : 'Start ' + plan.name }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="px-6 py-24">
            <div class="mx-auto max-w-2xl rounded-3xl border border-violet-500/20 bg-violet-600/10 p-12 text-center">
                <h2 class="mb-4 text-3xl font-bold">Ready to get paid on time?</h2>
                <p class="mb-8 text-gray-400">Create your workspace in under a minute. No credit card required.</p>
                <Link
                    href="/register"
                    class="inline-block rounded-xl bg-violet-600 px-8 py-3.5 text-base font-semibold text-white transition hover:bg-violet-500"
                >
                    Create your free workspace
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-white/10 px-6 py-8">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded bg-violet-600">
                        <span class="text-xs font-bold">B</span>
                    </div>
                    <span class="text-sm font-medium text-gray-400">billable</span>
                </div>
                <p class="text-xs text-gray-600">
                    Built with <span class="text-violet-400">♥</span> by Syahmi & Miyu
                </p>
            </div>
        </footer>
    </div>
</template>
