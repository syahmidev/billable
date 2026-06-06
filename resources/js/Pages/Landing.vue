<script setup>
    import SeoHead from '@/Components/SeoHead.vue'
    import Navbar from './Landing/Navbar.vue'
    import Hero from './Landing/Hero.vue'
    import Features from './Landing/Features.vue'
    import HowItWorks from './Landing/HowItWorks.vue'
    import Pricing from './Landing/Pricing.vue'
    import Faq from './Landing/Faq.vue'
    import Cta from './Landing/Cta.vue'
    import Footer from './Landing/Footer.vue'
    import { usePage } from '@inertiajs/vue3'
    import { computed } from 'vue'

    const props = defineProps({
        plans: { type: Array, default: () => [] },
        workspace_url: { type: String, default: null },
    })

    const page = usePage()

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
                url: baseUrl.value ? `${baseUrl.value}/` : '/',
                description: 'Online invoicing software for small teams.',
                offers: props.plans.map((plan) => ({
                    '@type': 'Offer',
                    name: `${plan.name} plan`,
                    price: Number(plan.price),
                    priceCurrency: 'USD',
                    availability: 'https://schema.org/InStock',
                    url: plan.slug ? `${baseUrl.value}/register?plan=${encodeURIComponent(plan.slug)}` : `${baseUrl.value}/register`,
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
</script>

<template>
    <SeoHead
        title="Online Invoicing for Small Teams"
        description="Billable gives small teams a dedicated billing workspace to manage clients, send professional invoices, and collect Stripe payments online."
        canonical="/"
        :schema="structuredData"
    />

    <div class="min-h-screen overflow-x-hidden" style="background: linear-gradient(160deg, #EDE9FF 0%, #F0F4FF 40%, #E8F5F0 100%);">

        <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -top-24 -left-24 h-[480px] w-[480px] rounded-full bg-indigo-300/20 blur-[80px]"></div>
            <div class="absolute top-[30vh] -right-32 h-[400px] w-[400px] rounded-full bg-emerald-300/15 blur-[80px]"></div>
            <div class="absolute bottom-[20vh] -left-20 h-[360px] w-[360px] rounded-full bg-amber-300/12 blur-[80px]"></div>
            <div class="absolute -bottom-20 right-[20%] h-[300px] w-[400px] rounded-full bg-sky-300/15 blur-[80px]"></div>
        </div>

        <Navbar :workspace-url="workspace_url" />

        <main class="px-5 sm:px-6">
            <Hero />
            <Features />
            <HowItWorks />
            <Pricing :plans="plans" />
            <Faq :faqs="faqs" />
            <Cta />
        </main>

        <Footer />
    </div>
</template>
