<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    clients: Array,
    defaultIssueDate: String,
    defaultDueDate: String,
})

const form = useForm({
    client_id: '',
    issue_date: props.defaultIssueDate,
    due_date: props.defaultDueDate,
    discount_percent: 0,
    tax_percent: 0,
    notes: '',
    items: [],
})

const items = ref([{ description: '', quantity: 1, unit_price: 0 }])

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
    form.post('/invoices')
}
</script>

<template>
    <AppLayout>
        <div class="p-8 max-w-4xl">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-8">
                <a href="/invoices" class="text-gray-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-white">New Invoice</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Draft a new invoice for a client</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Client + Dates -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-6">
                    <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Invoice Details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Client <span class="text-red-400">*</span></label>
                            <select
                                v-model="form.client_id"
                                class="w-full bg-gray-800 border rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-violet-500"
                                :class="form.errors.client_id ? 'border-red-500' : 'border-white/10'"
                            >
                                <option value="">Select a client</option>
                                <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <p v-if="form.errors.client_id" class="text-xs text-red-400 mt-1">{{ form.errors.client_id }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Issue Date</label>
                            <input
                                v-model="form.issue_date"
                                type="date"
                                class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-violet-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1.5">Due Date</label>
                            <input
                                v-model="form.due_date"
                                type="date"
                                class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-violet-500"
                            />
                        </div>
                    </div>
                </div>

                <!-- Line Items -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-6">
                    <h2 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-4">Line Items</h2>

                    <div class="space-y-2 mb-3">
                        <!-- Header row -->
                        <div class="grid grid-cols-12 gap-2 px-1">
                            <div class="col-span-6 text-xs text-gray-600">Description</div>
                            <div class="col-span-2 text-xs text-gray-600 text-right">Qty</div>
                            <div class="col-span-3 text-xs text-gray-600 text-right">Unit Price</div>
                            <div class="col-span-1"></div>
                        </div>

                        <div v-for="(item, index) in items" :key="index" class="grid grid-cols-12 gap-2 items-center">
                            <input
                                v-model="item.description"
                                placeholder="Service description"
                                class="col-span-6 bg-gray-800 border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-violet-500"
                            />
                            <input
                                v-model="item.quantity"
                                type="number"
                                min="0.01"
                                step="0.01"
                                class="col-span-2 bg-gray-800 border border-white/10 rounded-lg px-3 py-2 text-sm text-white text-right focus:outline-none focus:ring-1 focus:ring-violet-500"
                            />
                            <div class="col-span-3 relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">$</span>
                                <input
                                    v-model="item.unit_price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-full bg-gray-800 border border-white/10 rounded-lg pl-6 pr-3 py-2 text-sm text-white text-right focus:outline-none focus:ring-1 focus:ring-violet-500"
                                />
                            </div>
                            <button
                                type="button"
                                @click="removeItem(index)"
                                :disabled="items.length === 1"
                                class="col-span-1 flex justify-center text-gray-600 hover:text-red-400 disabled:opacity-20 transition-colors"
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
                        class="flex items-center gap-2 text-xs text-violet-400 hover:text-violet-300 transition-colors"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add line item
                    </button>
                </div>

                <!-- Totals + Discount/Tax -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-6">
                    <div class="flex gap-8">
                        <!-- Discount + Tax inputs -->
                        <div class="flex-1 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Discount %</label>
                                <input
                                    v-model="form.discount_percent"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-violet-500"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1.5">Tax %</label>
                                <input
                                    v-model="form.tax_percent"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-violet-500"
                                />
                            </div>
                        </div>

                        <!-- Live totals -->
                        <div class="w-56 space-y-2 text-sm">
                            <div class="flex justify-between text-gray-400">
                                <span>Subtotal</span>
                                <span>${{ fmt(subtotal) }}</span>
                            </div>
                            <div v-if="form.discount_percent > 0" class="flex justify-between text-gray-400">
                                <span>Discount ({{ form.discount_percent }}%)</span>
                                <span class="text-red-400">-${{ fmt(discountAmount) }}</span>
                            </div>
                            <div v-if="form.tax_percent > 0" class="flex justify-between text-gray-400">
                                <span>Tax ({{ form.tax_percent }}%)</span>
                                <span>${{ fmt(taxAmount) }}</span>
                            </div>
                            <div class="flex justify-between font-semibold text-white pt-2 border-t border-white/10">
                                <span>Total</span>
                                <span class="text-violet-400">${{ fmt(total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-gray-900 border border-white/10 rounded-xl p-6">
                    <label class="block text-xs font-medium text-gray-400 mb-1.5">Notes</label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        placeholder="Payment terms, additional instructions, or thank you note..."
                        class="w-full bg-gray-800 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-violet-500 resize-none"
                    />
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        {{ form.processing ? 'Saving...' : 'Create Invoice' }}
                    </button>
                    <a href="/invoices" class="px-5 py-2.5 text-sm text-gray-400 hover:text-white transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
