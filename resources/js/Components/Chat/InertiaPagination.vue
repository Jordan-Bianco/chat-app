<script setup>
import { Link } from '@inertiajs/inertia-vue3';

const props = defineProps({
    links: Object,
});
</script>

<template>
    <div>
        <div class="flex items-center space-x-1 mt-4">
            <div v-for="(link, key) in links">
                <Link
                    v-if="link.url !== null"
                    :href="link.url"
                    class="text-xs py-2 px-3 rounded"
                    :class="[ link.active ? 'bg-lime-500 text-zinc-800' : 'bg-tertiary']"
                    v-html="link.label" />
            </div>
        </div>
    </div>
</template>
