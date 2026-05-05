<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
    clients: Object,
    filters: Object,
})

const search = ref(props.filters?.search ?? '')
const hasSearch = computed(() => search.value.trim().length > 0)

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
        <div class="p-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Clients</h1>
                    <p class="text-sm text-gray-400 mt-1">Manage your client list</p>
                </div>
                <a
                    href="/clients/create"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Client
                </a>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search by name, email or company..."
                    class="w-full sm:w-80 bg-gray-900 border border-white/10 rounded-lg px-4 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-violet-500"
                />
            </div>

            <!-- Table -->
            <div class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
                <table v-if="clients.data.length" class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr v-for="client in clients.data" :key="client.id" class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-5 py-3.5">
                                <a :href="`/clients/${client.id}`" class="font-medium text-white hover:text-violet-400 transition-colors">
                                    {{ client.name }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-gray-400">{{ client.company ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-400">{{ client.email ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-400">{{ client.phone ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="`/clients/${client.id}/edit`" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-white/5 transition-colors">Edit</a>
                                    <button @click="archive(client.id)" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Archive</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty state -->
                <div v-else class="p-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-violet-500/10 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-white mb-1">
                        {{ hasSearch ? 'No matching clients' : 'No clients yet' }}
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        {{ hasSearch ? 'Try a different name, email, or company.' : 'Add your first client to get started.' }}
                    </p>
                    <button
                        v-if="hasSearch"
                        type="button"
                        class="inline-flex items-center gap-2 px-3 py-1.5 border border-white/10 bg-white/5 hover:bg-white/10 text-white text-xs font-medium rounded-lg transition-colors"
                        @click="search = ''"
                    >
                        Clear search
                    </button>
                    <a v-else href="/clients/create" class="inline-flex items-center gap-2 px-3 py-1.5 bg-violet-600 hover:bg-violet-500 text-white text-xs font-medium rounded-lg transition-colors">
                        New client
                    </a>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="clients.last_page > 1" class="flex items-center justify-between mt-4">
                <p class="text-xs text-gray-500">
                    Showing {{ clients.from }}–{{ clients.to }} of {{ clients.total }}
                </p>
                <div class="flex gap-1">
                    <a
                        v-for="link in clients.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="px-2.5 py-1 text-xs rounded"
                        :class="link.active
                            ? 'bg-violet-600 text-white'
                            : link.url
                                ? 'text-gray-400 hover:text-white hover:bg-white/5'
                                : 'text-gray-600 cursor-default'"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
