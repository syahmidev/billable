<script setup>
    import { computed, ref } from 'vue'
    import { Head, usePage, router, Link } from '@inertiajs/vue3'

    const page = usePage()
    const user = page.props.auth.user
    const workspace = page.props.workspace
    const permissions = computed(() => page.props.permissions ?? {})

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
            canShow: () => permissions.value.clients?.view,
            active: () => page.url.startsWith('/clients'),
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        },
        {
            href: '/invoices',
            label: 'Invoices',
            canShow: () => permissions.value.invoices?.view,
            active: () => page.url.startsWith('/invoices'),
            icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        },
        {
            href: '/billing',
            label: 'Billing',
            canShow: () => permissions.value.billing?.view,
            active: () => page.url.startsWith('/billing'),
            icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        },
        {
            href: '/team',
            label: 'Team',
            canShow: () => permissions.value.team?.view,
            active: () => page.url.startsWith('/team'),
            icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        },
        {
            href: '/activity',
            label: 'Activity',
            canShow: () => permissions.value.activity?.view,
            active: () => page.url.startsWith('/activity'),
            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        },
    ]

    const visibleNavItems = computed(() =>
        navItems.filter((item) => item.canShow?.() ?? true)
    )

    const userInitial = computed(() =>
        user?.name?.charAt(0).toUpperCase() ?? '?'
    )
</script>

<template>
    <Head :title="headTitle">
        <meta name="robots" content="noindex, nofollow" />
    </Head>

    <div class="min-h-screen" style="background: linear-gradient(160deg, #EDE9FF 0%, #F0F4FF 50%, #E8F5F0 100%);">

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
                class="fixed inset-0 z-20 bg-indigo-950/30 backdrop-blur-sm md:hidden"
                @click="sidebarOpen = false"
            />
        </Transition>

        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside
                :class="[
                    'fixed inset-y-0 left-0 z-30 flex w-56 flex-col border-r-2 border-indigo-100 bg-white/80 backdrop-blur-sm',
                    'md:relative md:translate-x-0 transition-transform duration-200',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
                ]"
                style="box-shadow: 4px 0 24px rgba(99,102,241,0.06);"
            >
                <!-- Brand -->
                <div class="flex items-center justify-between px-5 py-5">
                    <div class="min-w-0">
                        <span
                            class="text-xl font-bold text-indigo-900"
                            style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;"
                        >
                            bill<span class="text-indigo-500">able</span>
                        </span>
                        <p class="mt-0.5 truncate text-[11px] font-medium text-indigo-300">
                            {{ workspace?.name }}
                        </p>
                    </div>
                    <button
                        class="cursor-pointer rounded-xl p-1 text-indigo-300 transition-colors hover:text-indigo-600 md:hidden"
                        aria-label="Close sidebar"
                        @click="sidebarOpen = false"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Nav items -->
                <nav class="flex-1 space-y-0.5 px-3 pb-4" aria-label="Main navigation">
                    <Link
                        v-for="item in visibleNavItems"
                        :key="item.href"
                        :href="item.href"
                        :class="[
                            'group flex cursor-pointer items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition-all duration-150',
                            item.active()
                                ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-200'
                                : 'text-indigo-400 hover:bg-indigo-50 hover:text-indigo-700',
                        ]"
                        @click="sidebarOpen = false"
                    >
                        <svg
                            class="h-4 w-4 shrink-0"
                            :class="item.active() ? 'text-white' : 'text-indigo-300 group-hover:text-indigo-500'"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="item.icon" />
                        </svg>
                        {{ item.label }}
                    </Link>
                </nav>

                <!-- User section -->
                <div class="border-t-2 border-indigo-50 p-3">
                    <div class="flex items-center gap-3 rounded-2xl bg-indigo-50 px-3 py-2.5">
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-indigo-500 text-xs font-black text-white"
                            style="box-shadow: 0 4px 10px rgba(99,102,241,0.30);"
                        >
                            {{ userInitial }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13px] font-bold text-indigo-900">{{ user?.name }}</p>
                            <p class="truncate text-[11px] font-medium text-indigo-400">{{ user?.email }}</p>
                        </div>
                        <button
                            class="cursor-pointer rounded-xl p-1.5 text-indigo-300 transition-colors hover:bg-rose-50 hover:text-rose-500"
                            aria-label="Sign out"
                            @click="logout"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </div>
                </div>
            </aside>

            <!-- Content area -->
            <div class="flex min-w-0 flex-1 flex-col">
                <!-- Mobile top bar -->
                <div
                    class="flex items-center gap-4 border-b-2 border-indigo-100 bg-white/80 px-4 py-3.5 backdrop-blur-sm md:hidden"
                    style="box-shadow: 0 2px 12px rgba(99,102,241,0.06);"
                >
                    <button
                        class="cursor-pointer rounded-xl p-1.5 text-indigo-400 transition-colors hover:text-indigo-700"
                        aria-label="Open sidebar"
                        @click="sidebarOpen = true"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span
                        class="text-lg font-bold text-indigo-900"
                        style="font-family: 'Fredoka', sans-serif;"
                    >
                        bill<span class="text-indigo-500">able</span>
                    </span>
                </div>

                <main class="flex-1 overflow-auto">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
