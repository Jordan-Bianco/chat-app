<script setup>
import { onMounted, ref } from 'vue';

defineProps(['modelValue']);

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="bg-transparent text-white text-[13px] w-full px-3 py-1.5 border border-zinc-700 rounded-lg focus:border-transparent focus:ring-2 focus:ring-zinc-600 transition placeholder-zinc-500"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
