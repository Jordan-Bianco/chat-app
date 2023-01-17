<script setup>
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/Breeze/PrimaryButton.vue';
import EmojiPicker from '@/Components/Chat/EmojiPicker.vue';

const props = defineProps({
    showCreateGroupModal: Boolean
});
const emits = defineEmits(['closeCreateGroupModal']);

const groupName = ref();
const form = useForm({
    name: '',
    description: ''
});

const closeCreateGroupModal = () => {
    emits('closeCreateGroupModal')
    
    usePage().props.value.errors = [];
    form.reset('name');
    form.reset('description');
}

const submit = () => {
    form.post(route('group.store'), {
        onFinish: () => {
            if (Object.keys(usePage().props.value.errors).length === 0) {
                closeCreateGroupModal()
            }
        }
    });
};

const appendEmoji = (value) => {
    let cursorPosition = groupName.value.selectionStart
    let substr = form.name.substring(0, cursorPosition)
    form.name = substr += value.i += form.name.substring(cursorPosition)
} 
</script>

<template>
    <section>
        <!-- Overlay -->
        <transition name="fade">
            <div
                v-if="showCreateGroupModal"
                class="fixed inset-0 bg-black bg-opacity-60 z-20">
            </div>
        </transition>

        <transition name="from-up">
            <div
                v-if="showCreateGroupModal"
                class="fixed inset-0 flex justify-center items-center z-20">
                    
                <div class="bg-secondary w-2/5 max-w-[460px] max-h-[465px] z-20 rounded-lg shadow-lg">

                    <header class="p-4 pb-2 flex items-start justify-between">
                        <div>
                            <span class="block text-white font-medium text-2xl mb-1">Create new group</span>
                            <p class="text-zinc-400 leading-tight text-[13px]">
                                Groups are where your team communicates. They're best when organized around a topic. &minus; #development. for example.
                            </p>
                        </div>

                        <button
                            @click="closeCreateGroupModal()"
                            class="border border-zinc-700 px-2 rounded-lg text-xl text-zinc-700 hover:text-zinc-400">
                                &times;
                        </button>
                    </header>

                        <form @submit.prevent="submit" class="p-4">
                            <div class="mb-6">
                                <div class="relative">
                                    <label class="block mb-1 text-[13px] text-zinc-500" for="name">Name</label>
                                    <input
                                        ref="groupName"
                                        v-model="form.name"
                                        class="text-[13px] w-full px-3 py-1.5 text-white bg-transparent border border-zinc-700 rounded-lg focus:border-transparent focus:ring-2 focus:ring-zinc-600 transition placeholder-zinc-500"
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
                                <p v-if="$page.props.errors.name" class="text-xs text-red-500 font-medium">{{ $page.props.errors.name }}</p>
                            </div>

                            <div class="mb-6">
                                <label class="block mb-1 text-[13px] text-zinc-500" for="description">Description</label>
                                <textarea
                                    v-model="form.description"
                                    name="description"
                                    class="text-[13px] leading-tight resize-none w-full px-3 py-1.5 text-white bg-transparent border border-zinc-700 rounded-lg focus:border-transparent focus:ring-2 focus:ring-zinc-600 transition placeholder-zinc-500"
                                    placeholder="Add a short description"
                                    rows="5"></textarea>
                                <p v-if="$page.props.errors.description" class="text-xs text-red-500 font-medium">{{ $page.props.errors.description }}</p>

                            </div>

                            <footer class="flex justify-end space-x-2">
                                <button
                                    type="button"
                                    @click="closeCreateGroupModal()"
                                    class="max-w-max px-6 py-1.5 bg-zinc-600 hover:bg-opacity-70 border border-transparent rounded-lg text-xs text-zinc-300 focus:outline-none focus:ring-2 focus:ring-zinc-500 transition duration-150">
                                        <span>Cancel</span>
                                </button>

                                <PrimaryButton class="w-20 flex items-center justify-center">
                                    <svg
                                        v-if="form.processing"
                                        class="w-[19px] h-[19px] animate-spin-fast text-zinc-200 flex-none"
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
            </div>
        </transition>
    </section>
</template>