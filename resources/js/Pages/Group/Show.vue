<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import GroupMessagesFeed from '@/Components/Chat/Group/GroupMessagesFeed.vue'
import GroupInfo from '@/Components/Chat/Group//GroupInfo.vue';
import SendMessage from '@/Components/Chat/SendMessage.vue'
import JoinRequestButton from '@/Components/Chat/Group/JoinRequestButton.vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
    group: Object,
    messages: Object,
    isMember: Boolean,
    isLeader: Boolean,
    joinRequests: Object,
    unreadGroupPrivateMessages: Object
})

const onlineMembers = ref([]);
const messagesFeed = ref(props.messages);

const leaveGroup = () => {
    if (confirm('Are you sure you want to leave this group?')) {
        Inertia.delete(route('group.leave', { id: props.group.id }))
    }
}

onMounted(() => {
    axios.delete('http://localhost:8000/unread-messages/' + props.group.id)

    window.Echo.private('chat.' + usePage().props.value.auth.user.id)
        .listen('PrivateMessageSent', (e) => {
            props.unreadGroupPrivateMessages.push({
                group_id: e.message.group_id,
                user_id: e.message.receiver_id,
                message_id: e.message.id,
                is_private: true,
                from: e.message.from
            })
        });

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
        .listen('GroupMessageSent', (e) => {
            messagesFeed.value.push({
                body: e.message.body,
                attachments: e.message.attachments,
                sender: e.message.sender,
                group_id: e.message.group_id,
                created_at: e.message.created_at
            });

            axios.delete('http://localhost:8000/unread-messages/' + props.group.id)
        });
        
});

onUnmounted(() => {
    window.Echo.leave('group.' + props.group.id);
});
</script>
    
<template>
    <SiteLayout :group="isMember ? group : null">
        <div class="flex min-h-screen">
            <!-- Left side -->
            <div class="w-full relative">
                <header class="border-b border-tertiary flex items-center justify-between p-5">
                    <div>
                        <span class="block text-base font-medium text-white mb-0.5">{{ group.name }}</span>
                    
                        <div class="flex items-center space-x-1 text-zinc-500">
                            <svg class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="block text-xs mt-0.5">{{ group.members.length }} {{ group.members.length === 1 ? ' member' : ' members' }} <span class="mx-1">&bull;</span>{{ onlineMembers.length }} online</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">                        
                        <Link
                            v-if="isLeader"
                            class="relative"
                            :href="route('group.settings.info', { slug: group.slug })">
                                <svg class="w-5 h-5 text-zinc-500 hover:text-white flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <div
                                    v-if="joinRequests.length > 0"
                                    class="absolute -top-2 -right-2 bg-lime-500 text-zinc-800 font-medium w-[15px] h-[15px] flex justify-center items-center rounded-full text-[10px]">
                                        {{ joinRequests.length }}
                                </div>
                        </Link>

                        <Link
                            v-if="isMember"
                            :href="route('group.activity', { slug: group.slug })">
                                <svg class="w-5 h-5 text-zinc-500 hover:text-white flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </Link>

                        <button
                            v-if="isMember"
                            @click="leaveGroup()">
                                <svg class="w-5 h-5 text-zinc-500 hover:text-white flex-none" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path></svg>
                        </button>

                        <JoinRequestButton
                            v-if="!isMember"
                            :group="group"
                        ></JoinRequestButton>
                    </div>
                </header>

                <div
                    v-if="!isMember"
                    class="text-sm m-6 px-5 py-4 bg-blue-200 text-blue-500 font-medium rounded-lg flex items-center space-x-2">
                        <svg
                            class="w-6 h-6"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        
                        <span>Messages sent in this chat are visible to group members only.</span>
                </div>

                <GroupMessagesFeed
                    v-if="(group && isMember)"
                    :group="group"
                    :messagesFeed="messagesFeed"
                    :onlineMembers="onlineMembers"
                >
                </GroupMessagesFeed>

                <SendMessage
                    v-if="(group && isMember)"
                    :group="group"
                    :member="null">
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