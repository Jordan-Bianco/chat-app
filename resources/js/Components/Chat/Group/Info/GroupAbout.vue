<script setup>
const props = defineProps({
    content: String,
    group: Object
});

const emits = defineEmits(['showContentFor'])

const showContentFor = (value) => {
    emits('showContentFor', value)
}
</script>

<template>
    <div class="px-5 py-4 border-b border-tertiary overflow-hidden">
        <div
            @click="showContentFor('about')"
            class="flex items-center justify-between cursor-pointer">
                <span class="text-[11px] text-zinc-500 uppercase font-medium">About</span>
                <svg
                    :class="{ 'rotate-90' : content === 'about' }"
                    class="w-4 h-4 flex-none transform transition duration-200 text-zinc-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
        </div>
    
        <div v-auto-animate>
            <div v-if="content === 'about'">                
                <div class="pt-4">
                    <p class="text-xs leading-tight text-zinc-400">{{ group.description }}</p>
                </div>
            </div>
        </div>
    </div>
</template>