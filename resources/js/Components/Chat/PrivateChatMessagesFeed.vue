<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import MessageAttachment from '@/Components/Chat/MessageAttachment.vue'

const props = defineProps({
    group: Object,
    member: Object,
    messages: Object,
})

const messagesFeed = ref([]);
const messagesContainer = ref(null);

watch(() => props.messages, () => {
    scrollToEnd()
    messagesFeed.value = props.messages
})

onMounted(() => {
    scrollToEnd()
    messagesFeed.value = props.messages

    window.Echo.private('chat.' + usePage().props.value.auth.user.id)
        .listen('PrivateMessageSent', (e) => {
            if (e.message.from === props.member.id && e.message.group_id === props.group.id) {
                messagesFeed.value.push({
                    body: e.message.body,
                    attachments: e.message.attachments,
                    sender: e.message.sender,
                    created_at: e.message.created_at,
                    is_unread: null
                })

                // if user is in the current chat page, mark message as read
                axios.delete('http://localhost:8000/unread-messages/' + e.message.group_id + '/' + e.message.sender.id)

                scrollToEnd();
            }
        })
});

onUnmounted(() => {
    window.Echo.leave('chat.' + usePage().props.value.auth.user.id)
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
        class="px-5 pb-8 mt-4 overflow-y-auto">

            <div
                v-for="message in messagesFeed"
                :key="message.id"
                :class="{ 'flex justify-end' : message.sender.id === $page.props.auth.user.id }">
                    <div class="mb-2 bg-tertiary max-w-max px-4 py-2 rounded-xl relative">
                        <div class="flex items-center space-x-3">
                            <img
                                :src="'https://i.pravatar.cc/150?img=' + message.sender.id"
                                alt="user_avatar"
                                class="w-9 h-9 rounded-full flex-none">
                            <div>
                                <div class="space-x-1.5">
                                    <span class="text-sm text-white font-medium">{{ message.sender.name }}</span>
                                    <span class="text-xs text-zinc-500">&bull;</span>
                                    <span class="text-[11px] text-zinc-500">{{ message.created_at }}</span>
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
                                    :group_id="props.group.id"
                                />
                            </div>
                        </div>
                    </div>
            </div>
    </div>
</template>