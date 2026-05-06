<script setup>
    import { computed, ref } from 'vue'
    import { Head, usePage, router } from '@inertiajs/vue3'

    const page = usePage()
    const user = page.props.auth.user
    const workspace = page.props.workspace

    const sidebarOpen = ref(false)

    const headTitle = computed(() => {
        if (page.url.startsWith('/billing')) return 'Billing'
        if (page.url.startsWith('/team')) return 'Team'
        if (page.url.startsWith('/activity')) return 'Activity'
        if (page.url.startsWith('/clients/create')) return 'New Client'
        if (page.url.startsWith('/clients')) return 'Clients'
        if (page.url.startsWith('/invoices/create')) return 'New Invoice'
        if (page.url.startsWith('/invoices')) return 'Invoices'

        return 'Dashboard'
    })

    function logout() {
        router.post('/logout')
    }

    const navItems = [
        {
            href: '/dashboard',
            label: 'Dashboard',
            active: () => page.url === '/dashboard',
            icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        },
        {
            href: '/clients',
            label: 'Clients',
            active: () => page.url.startsWith('/clients'),
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        },
        {
            href: '/invoices',
            label: 'Invoices',
            active: () => page.url.startsWith('/invoices'),
            icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        },
        {
            href: '/billing',
            label: 'Billing',
            active: () => page.url.startsWith('/billing'),
            icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        },
        {
            href: '/team',
            label: 'Team',
            ownerOnly: true,
            active: () => page.url.startsWith('/team'),
            icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        },
        {
            href: '/activity',
            label: 'Activity',
            active: () => page.url.startsWith('/activity'),
            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        },
    ]

    const visibleNavItems = computed(() =>
        navItems.filter((item) => !item.ownerOnly || user?.role === 'owner')
    )
</script>

<template>
    <Head :title="headTitle">
        <meta name="robots" content="noindex, nofollow" />
    </Head>

    <div class="min-h-screen bg-gray-950">
        <!-- Mobile overlay -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-20 bg-black/60 md:hidden"
                @click="sidebarOpen = false"
            />
        </Transition>

        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <Transition
                enter-active-class="transition-transform duration-200"
                enter-from-class="-translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transition-transform duration-200"
                leave-from-class="translate-x-0"
                leave-to-class="-translate-x-full"
            >
                <aside
                    v-show="sidebarOpen || true"
                    :class="[
                        'fixed inset-y-0 left-0 z-30 flex w-60 flex-col bg-gray-900 border-r border-white/10',
                        'md:relative md:translate-x-0',
                        sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
                    ]"
                >
                    <div
                        class="flex items-center justify-between border-b border-white/10 px-6 py-5"
                    >
                        <div>
                            <span class="text-lg font-bold tracking-tight text-white">
                                bill<span class="text-violet-500">able</span>
                            </span>
                            <p class="mt-0.5 truncate text-xs text-gray-500">
                                {{ workspace?.name }}
                            </p>
                        </div>
                        <button
                            class="text-gray-500 hover:text-white md:hidden"
                            @click="sidebarOpen = false"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <nav class="flex-1 space-y-1 px-3 py-4">
                        <a
                            v-for="item in visibleNavItems"
                            :key="item.href"
                            :href="item.href"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                            :class="{ 'bg-violet-500/10 text-violet-400': item.active() }"
                            @click="sidebarOpen = false"
                        >
                            <svg
                                class="h-4 w-4 shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    :d="item.icon"
                                />
                            </svg>
                            {{ item.label }}
                        </a>
                    </nav>

                    <div class="border-t border-white/10 px-3 py-4">
                        <div class="flex items-center gap-3 px-3 py-2">
                            <div
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-violet-500/20 text-xs font-semibold text-violet-400"
                            >
                                {{ user?.name?.charAt(0).toUpperCase() }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-medium text-white">
                                    {{ user?.name }}
                                </p>
                                <p class="truncate text-xs text-gray-500">{{ user?.email }}</p>
                            </div>
                            <button
                                class="text-gray-500 transition-colors hover:text-white"
                                @click="logout"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </aside>
            </Transition>

            <!-- Content area -->
            <div class="flex min-w-0 flex-1 flex-col">
                <!-- Mobile top bar -->
                <div
                    class="flex items-center gap-4 border-b border-white/10 bg-gray-900 px-4 py-3 md:hidden"
                >
                    <button class="text-gray-400 hover:text-white" @click="sidebarOpen = true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                    <span class="text-sm font-bold tracking-tight text-white">
                        bill<span class="text-violet-500">able</span>
                    </span>
                </div>

                <main class="flex-1 overflow-auto">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
