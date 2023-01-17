<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="flex items-center px-6 py-1.5 bg-lime-500 hover:bg-opacity-90 border border-transparent rounded-lg text-[13px] text-zinc-800 focus:outline-none focus:ring-2 focus:ring-lime-300 transition duration-150"
    >
        <slot />
    </button>
</template>
