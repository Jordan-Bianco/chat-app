<script setup>
import { ref } from 'vue';

const props = defineProps({
    activity: Object
});

const showChanges = ref(false);

const openChanges = () => {
    showChanges.value = !showChanges.value
}
</script>
    
<template>
    <div class="relative">
        <div class="flex items-start space-x-3">
            <img
                :src="'https://i.pravatar.cc/150?img=' + activity.author.id"
                alt="user_avatar"
                class="w-8 h-8 rounded-full flex-none">
            
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <span class="block text-sm text-white mb-1">{{ activity.author.name }} updated the group.</span>
                    <span
                        @click="openChanges()"
                        class="block text-xs cursor-pointer">{{ showChanges ? 'Hide ' : 'View ' }} changes</span>
                </div>

                <span class="block text-xs text-zinc-400">{{ activity.created_at }}</span>
            </div>
        </div>

        <div
            v-auto-animate
            class="ml-[43px] mt-2 overflow-hidden">
                <div v-if="showChanges">
                    <!-- Before -->
                    <div class="text-xs py-3 text-zinc-500 border-b border-tertiary">
                        <div class="mb-2">
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-red-500 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                <div>
                                    <span class="block mb-1">Name</span>
                                    <span>{{ JSON.parse(activity.data)['before']['name'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-red-500 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                <div>
                                    <span class="block mb-1">Description</span>
                                    <span>{{ JSON.parse(activity.data)['before']['description'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- After -->
                    <div class="text-xs py-3">
                        <div class="mb-3">
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-green-500 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                <div>
                                    <span class="block text-zinc-500 mb-1">Name</span>
                                    <span class="text-white">{{ JSON.parse(activity.data)['after']['name'] ? JSON.parse(activity.data)['after']['name'] : JSON.parse(activity.data)['before']['name'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-green-500 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>

                            <div>
                                <span class="block text-zinc-500 mb-1">Description</span>
                                <span class="text-white">{{ JSON.parse(activity.data)['after']['description'] ? JSON.parse(activity.data)['after']['description'] : JSON.parse(activity.data)['before']['description'] }}</span>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</template>