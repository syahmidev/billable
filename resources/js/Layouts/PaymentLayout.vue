<template>
    <div class="min-h-screen bg-gray-950 flex flex-col">
        <header class="border-b border-white/10 px-6 py-4">
            <div class="max-w-2xl mx-auto flex items-center justify-between">
                <span class="text-lg font-bold text-white tracking-tight">
                    bill<span class="text-violet-500">able</span>
                </span>
                <span class="text-xs text-gray-500">Secure payment powered by Stripe</span>
            </div>
        </header>

        <main class="flex-1 flex items-start justify-center px-4 py-10">
            <div class="w-full max-w-2xl">
                <slot />
            </div>
        </main>

        <footer class="border-t border-white/10 px-6 py-4 text-center">
            <p class="text-xs text-gray-600">
                Protected by 256-bit SSL encryption · Powered by
                <span class="text-gray-500">Stripe</span>
            </p>
        </footer>
    </div>
</template>
