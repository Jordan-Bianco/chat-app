<script setup>
import SettingLayout from '@/Layouts/SettingLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { ref, onMounted } from 'vue';
import PrimaryButton from '@/Components/Breeze/PrimaryButton.vue';
import EmojiPicker from '@/Components/Chat/EmojiPicker.vue';

const props = defineProps({
    group: Object,
    joinRequestsCount: Number
});

const groupName = ref()
const processing = ref(false);
//  Create a copy to avoid to live update the name of the group before saving changes
const groupCopy = ref({})

onMounted(() => {
    Object.assign(groupCopy.value, props.group) 
});

const submit = () => {
    Inertia.patch(route('group.update', { id: props.group.id}), groupCopy.value, {
        onStart: () => { processing.value = true },
        onFinish: () => { processing.value = false },
    })
};

const appendEmoji = (value) => {
    let cursorPosition = groupName.value.selectionStart
    let substr = groupCopy.value.name.substring(0, cursorPosition)
    groupCopy.value.name = substr += value.i += groupCopy.value.name.substring(cursorPosition)
} 
</script>
    
<template>
    <SettingLayout
        :group="group"
        :joinRequestsCount="joinRequestsCount">
            
            <div class="mb-8">
                <h2 class="font-medium text-xl text-white">Edit group info</h2>
                <p class="text-sm text-zinc-500">In this section you can edit the name and description of your group.</p>
            </div>

            <div>
                <form @submit.prevent="submit">
                    <div class="mb-6">
                        <div class="relative">
                            <label class="block mb-1 text-[13px]" for="name">Name</label>
                            <input
                                ref="groupName"
                                v-model="groupCopy.name"
                                class="bg-transparent text-white text-[13px] w-full px-3 py-1.5 border border-zinc-700 rounded-lg focus:border-transparent focus:ring-2 focus:ring-zinc-600 transition placeholder-zinc-500"
                                type="text"
                                placeholder="Enter the name for this group"
                                name="name">

                            <div class="absolute bottom-0.5 right-3">
                                <EmojiPicker
                                    :classes="'absolute right-5'"
                                    @appendEmoji="appendEmoji"
                                ></EmojiPicker>
                            </div>
                        </div>
                        <p v-if="$page.props.errors.name" class="text-xs text-red-500 mt-1">{{ $page.props.errors.name }}</p>
                    </div>

                    <div class="mb-6">
                        <label class="block mb-1 text-[13px]" for="description">Description</label>
                        <textarea
                            v-model="groupCopy.description"
                            name="description"
                            class="bg-transparent text-white text-[13px] leading-tight resize-none w-full px-2 py-1.5 border border-zinc-700 rounded-lg focus:border-transparent focus:ring-2 focus:ring-zinc-600 transition placeholder-zinc-500"
                            placeholder="Add a short description"
                            rows="7"></textarea>
                        <p v-if="$page.props.errors.description" class="text-xs text-red-500">{{ $page.props.errors.description }}</p>
                    </div>

                    <footer>                    
                        <PrimaryButton class="w-20 flex items-center justify-center">
                            <svg
                                v-if="processing"
                                class="w-5 h-5 animate-spin-fast text-zinc-200 flex-none"
                                fill="currentColor"
                                version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g>
                                    <path d="M256,512c-34.6,0-68.1-6.8-99.6-20.1C125.9,479,98.5,460.5,75,437s-42-50.9-54.9-81.4C6.8,324.1,0,290.5,0,256
                                    c0-9.9,8.1-18,18-18s18,8.1,18,18c0,29.7,5.8,58.5,17.3,85.6c11.1,26.2,26.9,49.8,47.1,70c20.2,20.2,43.8,36.1,69.9,47.1
                                    c27.1,11.5,55.9,17.3,85.6,17.3s58.5-5.8,85.6-17.3c26.2-11.1,49.8-27,70-47.2c20.2-20.2,36.1-43.8,47.1-69.9
                                    c11.5-27.1,17.3-55.9,17.3-85.6c0-29.7-5.8-58.5-17.3-85.6c-11.1-26.1-27.1-49.9-47.2-70c-20-20.1-43.8-36.1-69.9-47.1
                                    C314.5,41.8,285.7,36,256,36c-9.9,0-18-8.1-18-18s8.1-18,18-18c34.6,0,68.1,6.8,99.6,20.1C386.2,33,413.5,51.5,437,75
                                    s42,50.9,54.9,81.4c13.4,31.5,20.1,65.1,20.1,99.6c0,34.5-6.8,68.1-20.1,99.6C479,386.1,460.5,413.5,437,437s-50.9,42-81.4,54.9
                                    C324.1,505.3,290.6,512,256,512z"/></g>
                            </svg>
                            <span v-else>Save</span>
                        </PrimaryButton>
                    </footer>
                </form>
            </div>
    </SettingLayout>
</template>