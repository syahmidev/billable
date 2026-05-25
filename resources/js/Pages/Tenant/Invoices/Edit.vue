<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    invoice: Object,
    clients: Array,
})

const form = useForm({
    client_id: props.invoice.client_id,
    issue_date: props.invoice.issue_date,
    due_date: props.invoice.due_date,
    discount_percent: Number(props.invoice.discount_percent),
    tax_percent: Number(props.invoice.tax_percent),
    notes: props.invoice.notes ?? '',
    items: [],
})

const items = ref(props.invoice.items.map(i => ({
    description: i.description,
    quantity: Number(i.quantity),
    unit_price: Number(i.unit_price),
})))

function addItem() {
    items.value.push({ description: '', quantity: 1, unit_price: 0 })
}

function removeItem(index) {
    if (items.value.length > 1) items.value.splice(index, 1)
}

const subtotal = computed(() =>
    items.value.reduce((sum, item) => sum + Number(item.quantity) * Number(item.unit_price), 0)
)
const discountAmount = computed(() => subtotal.value * (Number(form.discount_percent) / 100))
const taxAmount = computed(() => (subtotal.value - discountAmount.value) * (Number(form.tax_percent) / 100))
const total = computed(() => subtotal.value - discountAmount.value + taxAmount.value)

function fmt(n) { return n.toFixed(2) }

function submit() {
    form.items = items.value.map(i => ({
        description: i.description,
        quantity: Number(i.quantity),
        unit_price: Number(i.unit_price),
    }))
    form.put(`/invoices/${props.invoice.id}`)
}
</script>

<template>
    <AppLayout>
        <div class="p-6 lg:p-8 max-w-4xl">
            <!-- Header -->
            <div class="mb-8 flex items-center gap-3">
                <Link
                    :href="`/invoices/${invoice.id}`"
                    class="cursor-pointer flex h-9 w-9 items-center justify-center rounded-2xl border-2 border-indigo-100 bg-white text-indigo-400 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
                    style="box-shadow: 0 2px 8px rgba(99,102,241,0.08);"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-3xl font-bold text-indigo-950" style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.01em;">Edit Invoice</h1>
                    <p class="mt-0.5 text-sm font-medium text-indigo-400">{{ invoice.invoice_number }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Invoice Details -->
                <div class="clay-card bg-white p-6">
                    <h2 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-indigo-400">Invoice Details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">
                                Client <span class="text-rose-400">*</span>
                            </label>
                            <select
                                v-model="form.client_id"
                                class="clay-select w-full"
                                :class="{ 'clay-error': form.errors.client_id }"
                            >
                                <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <p v-if="form.errors.client_id" class="mt-1.5 text-xs font-semibold text-rose-500">{{ form.errors.client_id }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Issue Date</label>
                            <input v-model="form.issue_date" type="date" class="clay-input w-full" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Due Date</label>
                            <input v-model="form.due_date" type="date" class="clay-input w-full" />
                        </div>
                    </div>
                </div>

                <!-- Line Items -->
                <div class="clay-card bg-white p-6">
                    <h2 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-indigo-400">Line Items</h2>

                    <div class="space-y-3 mb-4">
                        <!-- Column headers -->
                        <div class="grid grid-cols-12 gap-2 px-1">
                            <div class="col-span-6 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Description</div>
                            <div class="col-span-2 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 text-right">Qty</div>
                            <div class="col-span-3 text-[10px] font-extrabold uppercase tracking-wider text-indigo-300 text-right">Unit Price</div>
                            <div class="col-span-1"></div>
                        </div>

                        <div v-for="(item, index) in items" :key="index" class="grid grid-cols-12 gap-2 items-center">
                            <input
                                v-model="item.description"
                                placeholder="Service description"
                                class="clay-input col-span-6"
                            />
                            <input
                                v-model="item.quantity"
                                type="number"
                                min="0.01"
                                step="0.01"
                                class="clay-input col-span-2 text-right"
                            />
                            <div class="col-span-3 relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-indigo-300 text-sm font-bold">$</span>
                                <input
                                    v-model="item.unit_price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="clay-input w-full pl-7 text-right"
                                />
                            </div>
                            <button
                                type="button"
                                @click="removeItem(index)"
                                :disabled="items.length === 1"
                                class="cursor-pointer col-span-1 flex justify-center text-indigo-300 hover:text-rose-400 disabled:opacity-20 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="addItem"
                        class="cursor-pointer flex items-center gap-1.5 text-xs font-bold text-indigo-500 hover:text-indigo-700 transition-colors"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Add line item
                    </button>
                </div>

                <!-- Discount / Tax + Live Totals -->
                <div class="clay-card bg-white p-6">
                    <h2 class="mb-5 text-xs font-extrabold uppercase tracking-wider text-indigo-400">Adjustments & Totals</h2>
                    <div class="flex flex-col sm:flex-row gap-6">
                        <div class="flex-1 grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Discount %</label>
                                <input v-model="form.discount_percent" type="number" min="0" max="100" step="0.01" class="clay-input w-full" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Tax %</label>
                                <input v-model="form.tax_percent" type="number" min="0" max="100" step="0.01" class="clay-input w-full" />
                            </div>
                        </div>

                        <div class="sm:w-56 rounded-2xl bg-indigo-50/60 border-2 border-indigo-100 p-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium text-indigo-400">Subtotal</span>
                                <span class="font-bold text-indigo-700">${{ fmt(subtotal) }}</span>
                            </div>
                            <div v-if="form.discount_percent > 0" class="flex justify-between text-sm">
                                <span class="font-medium text-indigo-400">Discount ({{ form.discount_percent }}%)</span>
                                <span class="font-bold text-rose-500">-${{ fmt(discountAmount) }}</span>
                            </div>
                            <div v-if="form.tax_percent > 0" class="flex justify-between text-sm">
                                <span class="font-medium text-indigo-400">Tax ({{ form.tax_percent }}%)</span>
                                <span class="font-bold text-indigo-700">${{ fmt(taxAmount) }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t-2 border-indigo-100">
                                <span class="text-sm font-extrabold text-indigo-900">Total</span>
                                <span class="text-base font-black text-indigo-600" style="font-family: 'Fredoka', sans-serif;">${{ fmt(total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="clay-card bg-white p-6">
                    <label class="mb-1.5 block text-xs font-extrabold uppercase tracking-wider text-indigo-500">Notes</label>
                    <textarea v-model="form.notes" rows="3" class="clay-textarea w-full" />
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="clay-btn cursor-pointer inline-flex items-center gap-2 rounded-2xl bg-indigo-500 px-6 py-3 text-sm font-black text-white disabled:opacity-60"
                        style="box-shadow: 0 8px 20px rgba(99,102,241,0.30), inset 0 1px 0 rgba(255,255,255,0.25);"
                    >
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        {{ form.processing ? 'Saving…' : 'Save Changes' }}
                    </button>
                    <Link :href="`/invoices/${invoice.id}`" class="cursor-pointer px-4 py-3 text-sm font-bold text-indigo-400 transition-colors hover:text-indigo-700">
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
