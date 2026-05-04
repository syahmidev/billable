<script setup>
</script>

<template>
    <div class="min-h-screen bg-gray-950 flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <span class="text-2xl font-bold text-white tracking-tight">
                    bill<span class="text-violet-500">able</span>
                </span>
            </div>
            <div class="bg-gray-900 border border-white/10 rounded-2xl p-8">
                <slot />
            </div>
        </div>
    </div>
</template>
