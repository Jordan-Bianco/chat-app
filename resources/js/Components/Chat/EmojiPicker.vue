<script setup>
import { ref } from 'vue';
import { clickOutSide as vClickOutSide } from '@mahdikhashan/vue3-click-outside'
import EmojiPicker from 'vue3-emoji-picker'
import 'vue3-emoji-picker/css'

const emits = defineEmits(['appendEmoji'])

const props = defineProps({
    classes: {
        type: String,
        required: false,
        default: ''
    }
})

const showEmojiList = ref(false);

const openEmojiList = () => {
    showEmojiList.value = showEmojiList.value === true ? false : true
}

const closeEmojiList = () => {
    showEmojiList.value = false
}

const onSelectEmoji = (value) => {
    emits('appendEmoji', value)
}

const customMethod = () => {
    closeEmojiList()
}

</script>

<template>
    <div>
        <div
            class="relative"
            v-click-out-side="customMethod">
                <button @click="openEmojiList()" type="button">
                    <svg class="w-5 h-5 text-zinc-500 hover:text-lime-500 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </button>

                <div v-auto-animate>
                    <EmojiPicker
                        :class="classes"
                        v-if="showEmojiList"
                        @select="onSelectEmoji"
                        :native="true" />
                </div>
        </div>
    </div>
</template>