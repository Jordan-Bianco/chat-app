<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import { ref, watch, onMounted } from 'vue';
import MessageAttachment from '@/Components/Chat/MessageAttachment.vue'

const props = defineProps({
    group: Object,
    messagesFeed: Object,
    onlineMembers: Array
})

const messagesContainer = ref(null)

watch(props.messagesFeed, () => {
    scrollToEnd()
})

onMounted(() => { 
    scrollToEnd()
});

const scrollToEnd = () => {
    const el = messagesContainer.value;

    if (el) {
        setTimeout(() => {
            el.scrollTop = el.scrollHeight;        
        }, 0);
    }
}
</script>
    
<template>
    <div
        ref="messagesContainer"
        style="height: calc(100vh - 200px)"
        class="px-5 pb-8 overflow-y-auto">
        <div
            v-for="message in props.messagesFeed"
            :key="message.id">

                <div class="border-b border-tertiary py-4">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <img
                                v-if="message.sender"
                                :src="'https://i.pravatar.cc/150?img=' + message.sender.id"
                                alt="user_avatar"
                                class="w-8 h-8 rounded-full flex-none">

                            <div
                                class="absolute right-0 -bottom-1 w-2 h-2 rounded-full"
                                :class="onlineMembers.includes(message.sender.id) ? 'bg-green-400' : 'bg-zinc-600'"    
                            ></div>
                        </div>

                        <div class="w-full">
                            <div class="flex justify-between items-center mb-1">
                                <div class="space-x-1.5">
                                    <span class="text-sm text-white font-medium">{{ message.sender.name }}</span>
                                    <span class="text-xs text-zinc-500">&bull;</span>
                                    <span class="text-[11px] text-zinc-500">{{ message.created_at }}</span>
                                </div>

                                <Link
                                    v-if="message.sender.id !== $page.props.auth.user.id"
                                    :href="route('group.member.show', { gslug: props.group.slug, uslug: message.sender.slug })"
                                >
                                    <svg class="w-5 h-5 flex-none text-zinc-500 hover:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                                </Link>
                            </div>

                            <span
                                v-if="message.body"
                                :class="{ 'mb-2' : message.attachments }"
                                class="block text-sm leading-tight text-zinc-300">
                                    {{ message.body }}
                            </span>

                            <MessageAttachment
                                v-if="message.attachments"
                                :attachments="message.attachments"
                                :group_id="message.group_id"
                            />
                        </div>
                    </div>
                </div>
        </div>
    </div>
</template>