<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    title: { type: String, default: null },
    description: { type: String, default: null },
    canonical: { type: String, default: null },
    robots: { type: String, default: 'index, follow' },
    type: { type: String, default: 'website' },
    image: { type: String, default: null },
})

const page = usePage()

const seo = computed(() => page.props.seo ?? {})
const pageTitle = computed(() => props.title ?? seo.value.title ?? 'Billable')
const description = computed(() => props.description ?? seo.value.description ?? '')
const siteName = computed(() => seo.value.site_name ?? 'Billable')
const fullTitle = computed(() => (pageTitle.value === siteName.value ? siteName.value : `${pageTitle.value} — ${siteName.value}`))
const imageUrl = computed(() => absoluteUrl(props.image ?? seo.value.image))
const canonicalUrl = computed(() => absoluteUrl(props.canonical ?? seo.value.current_url))
const twitterSite = computed(() => seo.value.twitter_site)

function absoluteUrl(url) {
    if (!url) return null
    if (/^https?:\/\//i.test(url)) return url

    const baseUrl = String(seo.value.base_url ?? '').replace(/\/$/, '')
    const path = String(url).startsWith('/') ? url : `/${url}`

    return `${baseUrl}${path}`
}
</script>

<template>
    <Head :title="pageTitle">
        <meta name="description" :content="description" />
        <meta name="robots" :content="robots" />
        <link v-if="canonicalUrl" rel="canonical" :href="canonicalUrl" />

        <meta property="og:site_name" :content="siteName" />
        <meta property="og:type" :content="type" />
        <meta property="og:title" :content="fullTitle" />
        <meta property="og:description" :content="description" />
        <meta v-if="canonicalUrl" property="og:url" :content="canonicalUrl" />
        <meta v-if="imageUrl" property="og:image" :content="imageUrl" />

        <meta name="twitter:card" :content="imageUrl ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title" :content="fullTitle" />
        <meta name="twitter:description" :content="description" />
        <meta v-if="imageUrl" name="twitter:image" :content="imageUrl" />
        <meta v-if="twitterSite" name="twitter:site" :content="twitterSite" />
    </Head>
</template>
