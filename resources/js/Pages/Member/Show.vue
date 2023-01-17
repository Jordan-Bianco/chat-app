<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import PrivateChatMessagesFeed from '@/Components/Chat/PrivateChatMessagesFeed.vue'
import GroupInfo from '@/Components/Chat/Group//GroupInfo.vue';
import SendMessage from '@/Components/Chat/SendMessage.vue'
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/inertia-vue3';

const props = defineProps({
    group: Object,
    member: Object,
    isMember: Boolean,
    messages: Object,
    unreadGroupPrivateMessages: Object
})

const onlineMembers = ref([]);

onMounted(() => {
    window.Echo.join('group.' + props.group.id)
        .here((users) => {
            onlineMembers.value = users.map(user => {
                return user.id
            })
        })
        .joining((user) => {
            onlineMembers.value.push(user.id)
        })
        .leaving((user) => {
            onlineMembers.value.splice(onlineMembers.value.indexOf(user.id), 1)
        })
        .error((error) => {
            console.error(error);
        })

        // listen for messages that are not from the user I'm chatting with
        window.Echo.private('chat.' + usePage().props.value.auth.user.id)
            .listen('PrivateMessageSent', (e) => {
                if (e.message.from !== props.member.id) {
                    props.unreadGroupPrivateMessages.push({
                        group_id: e.message.group_id,
                        user_id: e.message.receiver_id,
                        message_id: e.message.id,
                        is_private: true,
                        from: e.message.from
                    })
                }
            })

    let selectedGroup = usePage().props.value.auth.groups.find(group => {
        return group.id === props.group.id;
    })

    selectedGroup.unread_private_messages_count = props.unreadGroupPrivateMessages.length;
});

onUnmounted(() => {
    window.Echo.leave('group.' + props.group.id);
});
</script>
    
<template>
    <SiteLayout
        :group="group"
        :member="member"
    >
        <div class="flex min-h-screen">
            <!-- Left side -->
            <div class="w-full relative">
                <header class="border-b border-tertiary flex items-center justify-between p-5">
                    <div class="flex items-center space-x-3">
                        <img
                            :src="'https://i.pravatar.cc/150?img='+member.id"
                            alt="user_avatar"
                            class="w-9 h-9 rounded-full flex-none">
                    
                        <div>
                            <span class="block text-base font-medium text-white mb-1">{{ member.name }}</span>
                            <span class="block text-xs text-zinc-400">{{ onlineMembers.includes(member.id) ? 'Online' : 'Offline' }}</span>
                        </div>
                    </div>

                    <Link
                        :href="route('group.show', { slug: group.slug })"
                        class="flex items-center space-x-1 text-lime-500 hover:text-lime-400"> 
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                            <span class="text-xs">back to group chat</span>
                    </Link>
                </header>

                <PrivateChatMessagesFeed
                    :group="group"
                    :member="member"
                    :messages="messages"
                ></PrivateChatMessagesFeed>

                <SendMessage
                    :member="member"
                    :group="group">
                </SendMessage>
            </div>

            <!-- Right side -->
            <GroupInfo
                :group="group"
                :onlineMembers="onlineMembers"
                :isMember="isMember"
                :unreadGroupPrivateMessages="unreadGroupPrivateMessages"
            ></GroupInfo>
            </div>
    </SiteLayout>
</template>