<script setup>
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <div>
        <div class="min-h-screen flex justify-center items-center bg-secondary text-zinc-400">
            <div class="w-full px-5 sm:px-0 sm:max-w-md mx-auto">
                <slot />
            </div>
        </div>
    </div>
</template>
