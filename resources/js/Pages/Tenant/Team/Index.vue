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
    router.put(`/team/${member.id}`, { role }, { preserveScroll: true })
}

function removeMember(member) {
    if (!confirm(`Remove ${member.name} from this workspace?`)) return
    router.delete(`/team/${member.id}`, { preserveScroll: true })
}
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">Team</h1>
                    <p class="mt-1 text-sm font-medium text-indigo-400">Manage who can access this workspace</p>
                </div>
            </div>

            <!-- Flash messages -->
            <div v-if="page.props.flash?.success" class="mb-6 flex items-center gap-3 rounded-2xl border-2 border-emerald-200 bg-emerald-50 px-4 py-3">
                <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-sm font-semibold text-emerald-700">{{ page.props.flash.success }}</p>
            </div>
            <div v-if="page.props.flash?.error" class="mb-6 flex items-center gap-3 rounded-2xl border-2 border-rose-200 bg-rose-50 px-4 py-3">
                <svg class="h-4 w-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-semibold text-rose-700">{{ page.props.flash.error }}</p>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <!-- Add member form -->
                <form v-if="permissions.team?.manage" class="clay-card bg-white p-6" @submit.prevent="submit">
                    <h2 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-indigo-400">Add Member</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Name</label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Alex Tan"
                                class="clay-input w-full"
                                :class="{ 'clay-error': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="alex@example.com"
                                class="clay-input w-full"
                                :class="{ 'clay-error': form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Initial Password</label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="clay-input w-full"
                                :class="{ 'clay-error': form.errors.password }"
                            />
                            <p v-if="form.errors.password" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.password }}</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Role</label>
                            <select
                                v-model="form.role"
                                class="clay-select w-full"
                                :class="{ 'clay-error': form.errors.role }"
                            >
                                <option v-for="role in roles" :key="role.value" :value="role.value">
                                    {{ role.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.role" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.role }}</p>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="clay-btn cursor-pointer mt-5 w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-500 px-4 py-3 text-sm font-black text-white disabled:opacity-60"
                        style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                    >
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        {{ form.processing ? 'Adding…' : 'Add Member' }}
                    </button>
                </form>

                <!-- Members list -->
                <div
                    :class="permissions.team?.manage ? 'xl:col-span-2' : 'xl:col-span-3'"
                    class="clay-card overflow-hidden bg-white"
                >
                    <div class="flex items-center justify-between border-b-2 border-indigo-50 px-6 py-4">
                        <h2 class="text-xs font-extrabold uppercase tracking-wider text-indigo-400">Members</h2>
                        <span class="rounded-xl bg-indigo-100 px-2.5 py-1 text-xs font-bold text-indigo-500">
                            {{ members.length }} total
                        </span>
                    </div>

                    <div v-if="members.length" class="divide-y divide-indigo-50">
                        <div
                            v-for="member in members"
                            :key="member.id"
                            class="flex flex-col gap-4 px-6 py-4 sm:flex-row sm:items-center sm:justify-between transition-colors hover:bg-indigo-50/40"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-500 text-sm font-black text-white"
                                    style="box-shadow: 0 4px 12px rgba(99,102,241,0.25);"
                                >
                                    {{ member.name.charAt(0).toUpperCase() }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="truncate text-sm font-bold text-indigo-900">{{ member.name }}</p>
                                        <span
                                            v-if="member.is_current_user"
                                            class="rounded-xl border-2 border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-500"
                                        >
                                            You
                                        </span>
                                    </div>
                                    <p class="truncate text-xs font-medium text-indigo-400">{{ member.email }}</p>
                                </div>
                            </div>

                            <div v-if="permissions.team?.manage" class="flex items-center gap-2">
                                <select
                                    :value="member.role"
                                    :disabled="member.is_current_user"
                                    class="clay-select py-1.5 text-xs disabled:opacity-50"
                                    @change="updateRole(member, $event.target.value)"
                                >
                                    <option v-for="role in roles" :key="role.value" :value="role.value">
                                        {{ role.label }}
                                    </option>
                                </select>
                                <button
                                    type="button"
                                    :disabled="member.is_current_user"
                                    class="cursor-pointer rounded-xl border-2 border-rose-200 bg-white px-3 py-1.5 text-xs font-bold text-rose-500 transition-all hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40"
                                    @click="removeMember(member)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="py-14 text-center">
                        <div
                            class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-100"
                            style="box-shadow: 0 6px 20px rgba(99,102,241,0.15);"
                        >
                            <svg class="h-7 w-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <p class="text-lg font-bold text-indigo-900" style="font-family: 'Fredoka', sans-serif;">No team members yet</p>
                        <p class="mt-1 text-sm font-medium text-indigo-400">Add your first team member using the form.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
