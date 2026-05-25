<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
    clients: Object,
    filters: Object,
})

const search = ref(props.filters?.search ?? '')
const hasSearch = computed(() => search.value.trim().length > 0)
const page = usePage()
const permissions = computed(() => page.props.permissions ?? {})

let searchTimeout = null
watch(search, (val) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get('/clients', { search: val }, { preserveState: true, replace: true })
    }, 300)
})

function archive(id) {
    if (!confirm('Archive this client?')) return
    router.delete(`/clients/${id}`)
}
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">Clients</h1>
                    <p class="mt-1 text-sm font-medium text-indigo-400">Manage your client list</p>
                </div>
                <Link
                    v-if="permissions.clients?.create"
                    href="/clients/create"
                    class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-indigo-500 px-4 py-2.5 text-sm font-black text-white"
                    style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    New Client
                </Link>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <div class="relative w-full sm:w-80">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search name, email or company…"
                        class="clay-input pl-10"
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="clay-card overflow-hidden bg-white">
                <table v-if="clients.data.length" class="w-full">
                    <thead>
                        <tr class="border-b-2 border-indigo-50 bg-indigo-50/50">
                            <th class="px-6 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Name</th>
                            <th class="px-5 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Company</th>
                            <th class="px-5 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Email</th>
                            <th class="px-5 py-3 text-left text-[10px] font-extrabold uppercase tracking-wider text-indigo-400">Phone</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-50">
                        <tr v-for="client in clients.data" :key="client.id" class="group transition-colors hover:bg-indigo-50/40">
                            <td class="px-6 py-4">
                                <Link :href="`/clients/${client.id}`" class="cursor-pointer font-bold text-indigo-700 transition-colors hover:text-indigo-500">
                                    {{ client.name }}
                                </Link>
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-indigo-400">{{ client.company ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-indigo-400">{{ client.email ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-indigo-400">{{ client.phone ?? '—' }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        v-if="permissions.clients?.update"
                                        :href="`/clients/${client.id}/edit`"
                                        class="cursor-pointer rounded-xl border-2 border-indigo-100 bg-white px-3 py-1.5 text-xs font-bold text-indigo-500 transition-all hover:border-indigo-200 hover:bg-indigo-50"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        v-if="permissions.clients?.delete"
                                        class="cursor-pointer rounded-xl border-2 border-rose-100 bg-white px-3 py-1.5 text-xs font-bold text-rose-500 transition-all hover:bg-rose-50"
                                        @click="archive(client.id)"
                                    >
                                        Archive
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty state -->
                <div v-else class="py-16 text-center">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-100" style="box-shadow: 0 6px 20px rgba(99,102,241,0.15);">
                        <svg class="h-7 w-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <p class="text-lg font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif;">
                        {{ hasSearch ? 'No matching clients' : 'No clients yet' }}
                    </p>
                    <p class="mt-1 text-sm font-medium text-indigo-400">
                        {{ hasSearch ? 'Try a different name, email, or company.' : 'Add your first client to get started.' }}
                    </p>
                    <div class="mt-5">
                        <button
                            v-if="hasSearch"
                            class="clay-btn-secondary cursor-pointer inline-flex items-center gap-2 rounded-2xl border-2 border-indigo-200 bg-white px-4 py-2.5 text-sm font-bold text-indigo-600"
                            style="box-shadow: 0 4px 12px rgba(99,102,241,0.10);"
                            @click="search = ''"
                        >
                            Clear search
                        </button>
                        <Link
                            v-else-if="permissions.clients?.create"
                            href="/clients/create"
                            class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-indigo-500 px-5 py-2.5 text-sm font-black text-white"
                            style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                        >
                            New client
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="clients.last_page > 1" class="mt-5 flex items-center justify-between">
                <p class="text-xs font-semibold text-indigo-400">
                    Showing {{ clients.from }}–{{ clients.to }} of {{ clients.total }}
                </p>
                <div class="flex gap-1">
                    <a
                        v-for="link in clients.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="cursor-pointer rounded-xl px-3 py-1.5 text-xs font-bold transition-all"
                        :class="link.active
                            ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-200'
                            : link.url
                                ? 'border-2 border-indigo-100 bg-white text-indigo-500 hover:border-indigo-200 hover:bg-indigo-50'
                                : 'text-indigo-200 cursor-default'"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
