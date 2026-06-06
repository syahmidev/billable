<script setup>
    const steps = [
        { n: '01', color: 'bg-indigo-500', ring: 'ring-indigo-200', title: 'Create a client', desc: 'Add the customer once — contacts and billing details ready every time.' },
        { n: '02', color: 'bg-sky-500', ring: 'ring-sky-200', title: 'Send the invoice', desc: 'Build a clean invoice, generate a PDF, and send a secure payment link.' },
        { n: '03', color: 'bg-emerald-500', ring: 'ring-emerald-200', title: 'Get paid online', desc: 'Stripe handles checkout; Billable keeps status in sync automatically.' },
        { n: '04', color: 'bg-amber-500', ring: 'ring-amber-200', title: 'Track the result', desc: 'See paid, outstanding, and overdue revenue before the next follow-up.' },
    ]
</script>

<template>
    <section class="mx-auto max-w-6xl py-16 sm:py-20">
        <div class="mb-12 text-center">
            <p class="mb-2 text-sm font-bold uppercase tracking-widest text-emerald-500">How it works</p>
            <h2
                class="text-4xl font-bold text-indigo-950 sm:text-5xl"
                style="font-family: 'Fredoka', sans-serif; letter-spacing: -0.02em;"
            >
                Four steps. Client to collected.
            </h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article
                v-for="step in steps"
                :key="step.n"
                class="clay-card bg-white p-6"
            >
                <div
                    class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl ring-4 text-white"
                    :class="[step.color, step.ring]"
                    style="box-shadow: 0 6px 16px rgba(0,0,0,0.12), inset 0 1px 0 rgba(255,255,255,0.25);"
                >
                    <span class="text-lg font-black" style="font-family: 'Fredoka', sans-serif;">{{ step.n }}</span>
                </div>
                <h3 class="mb-2 text-base font-extrabold text-indigo-950" style="font-family: 'Fredoka', sans-serif;">
                    {{ step.title }}
                </h3>
                <p class="text-sm leading-6 text-indigo-800/60">{{ step.desc }}</p>
            </article>
        </div>
    </section>
</template>
