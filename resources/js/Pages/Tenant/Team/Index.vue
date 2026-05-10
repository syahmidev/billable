<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, useForm, usePage } from '@inertiajs/vue3'

defineProps({
    members: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
})

const page = usePage()
const permissions = page.props.permissions ?? {}

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'member',
})

function submit() {
    form.post('/team', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('name', 'email', 'password')
            form.role = 'member'
        },
    })
}

function updateRole(member, role) {
    router.put(
        `/team/${member.id}`,
        { role },
        {
            preserveScroll: true,
        },
    )
}

function removeMember(member) {
    if (!confirm(`Remove ${member.name} from this workspace?`)) return

    router.delete(`/team/${member.id}`, {
        preserveScroll: true,
    })
}
</script>

<template>
    <AppLayout>
        <div class="p-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-white">Team</h1>
                    <p class="mt-1 text-sm text-gray-400">Manage who can access this workspace.</p>
                </div>
            </div>

            <div v-if="page.props.flash?.success" class="mb-6 rounded-lg border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400">
                {{ page.props.flash.success }}
            </div>
            <div v-if="page.props.flash?.error" class="mb-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                {{ page.props.flash.error }}
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <form v-if="permissions.team?.manage" class="bg-gray-900 border border-white/10 rounded-xl p-6" @submit.prevent="submit">
                    <h2 class="mb-5 text-sm font-semibold text-white">Add member</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-400">Name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-lg border bg-gray-800 px-3.5 py-2.5 text-sm text-white placeholder-gray-600 transition-colors focus:outline-none focus:ring-1 focus:ring-violet-500"
                                :class="form.errors.name ? 'border-red-500' : 'border-white/10'"
                                placeholder="Alex Tan"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-red-400">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-400">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-lg border bg-gray-800 px-3.5 py-2.5 text-sm text-white placeholder-gray-600 transition-colors focus:outline-none focus:ring-1 focus:ring-violet-500"
                                :class="form.errors.email ? 'border-red-500' : 'border-white/10'"
                                placeholder="alex@example.com"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-400">Initial password</label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="w-full rounded-lg border bg-gray-800 px-3.5 py-2.5 text-sm text-white placeholder-gray-600 transition-colors focus:outline-none focus:ring-1 focus:ring-violet-500"
                                :class="form.errors.password ? 'border-red-500' : 'border-white/10'"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-xs text-red-400">{{ form.errors.password }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-400">Role</label>
                            <select
                                v-model="form.role"
                                class="w-full rounded-lg border bg-gray-800 px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-violet-500"
                                :class="form.errors.role ? 'border-red-500' : 'border-white/10'"
                            >
                                <option v-for="role in roles" :key="role.value" :value="role.value">
                                    {{ role.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.role" class="mt-1 text-xs text-red-400">{{ form.errors.role }}</p>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-5 w-full rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-violet-500 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Adding...' : 'Add member' }}
                    </button>
                </form>

                <div :class="permissions.team?.manage ? 'xl:col-span-2' : 'xl:col-span-3'" class="bg-gray-900 border border-white/10 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                        <h2 class="text-sm font-semibold text-white">Members</h2>
                        <span class="text-xs text-gray-500">{{ members.length }} total</span>
                    </div>

                    <div v-if="members.length" class="divide-y divide-white/5">
                        <div
                            v-for="member in members"
                            :key="member.id"
                            class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-violet-500/15 text-sm font-semibold text-violet-300">
                                    {{ member.name.charAt(0).toUpperCase() }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="truncate text-sm font-medium text-white">{{ member.name }}</p>
                                        <span v-if="member.is_current_user" class="rounded bg-white/5 px-1.5 py-0.5 text-xs text-gray-400">You</span>
                                    </div>
                                    <p class="truncate text-xs text-gray-500">{{ member.email }}</p>
                                </div>
                            </div>

                            <div v-if="permissions.team?.manage" class="flex items-center gap-2">
                                <select
                                    :value="member.role"
                                    :disabled="member.is_current_user"
                                    class="rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-xs text-white focus:outline-none focus:ring-1 focus:ring-violet-500 disabled:opacity-50"
                                    @change="updateRole(member, $event.target.value)"
                                >
                                    <option v-for="role in roles" :key="role.value" :value="role.value">
                                        {{ role.label }}
                                    </option>
                                </select>
                                <button
                                    type="button"
                                    :disabled="member.is_current_user"
                                    class="rounded-lg px-3 py-2 text-xs font-medium text-red-400 transition-colors hover:bg-red-500/10 hover:text-red-300 disabled:cursor-not-allowed disabled:opacity-40"
                                    @click="removeMember(member)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-12 text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-violet-500/10">
                            <svg class="h-6 w-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-white">No team members yet</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
