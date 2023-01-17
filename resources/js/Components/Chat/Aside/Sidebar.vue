<script setup>
import { Inertia } from '@inertiajs/inertia';
import { ref, watch, onMounted } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import { usePage } from '@inertiajs/inertia-vue3';

import SearchGroup from '@/Components/Chat/Aside/SearchGroup.vue';
import CreateGroupModal from '@/Components/Chat/Aside/CreateGroupModal.vue';

const props = defineProps({
    groups: Object,
    group: Object,
    member: Object,
})

const showCreateGroupModal = ref(false);
const selectedGroup = ref(props.group);

onMounted(() => {    
    let groupIds = usePage().props.value.auth.groups.map(group => {
        return group.id
    });

    groupIds.forEach(groupId => {
        window.Echo.channel('my-groups.' + groupId)
            .listen('GroupMessageSent', (e) => {

                if(e.message.from === usePage().props.value.auth.user.id) return;

                let selectedGroup = props.groups.find(group => {
                    return group.id === e.message.group_id
                })

                selectedGroup.unread_group_messages_count += 1;
        });
    });

    if (!props.member || props.member && !usePage().props.value.ziggy.location.includes(props.member.slug)) {
        window.Echo.private('chat.' + usePage().props.value.auth.user.id)
            .listen('PrivateMessageSent', (e) => {

                let selectedGroup = props.groups.find(group => {
                    return group.id === e.message.group_id
                })

                selectedGroup.unread_private_messages_count += 1;
        });
    }
});

const selectGroup = (group) => {
    if (selectedGroup.value && selectedGroup.value.id === group.id) {
        selectedGroup.value = ''
        Inertia.get(route('home'));
    } else {
        selectedGroup.value = group
        Inertia.get(route('group.show', { slug: group.slug }));
    }
}

const logout = () => {
    Inertia.post(route('logout'));
}

watch(() => props.group, () => {
    selectedGroup.value = props.group
})

const openCreateGroupModal = () => {
    showCreateGroupModal.value = true
}

const closeCreateGroupModal = () => {
    showCreateGroupModal.value = false
}

</script>
    
<template>
    <div class="w-full h-full overflow-y-auto border-r border-tertiary bg-zinc-900 relative">

        <SearchGroup></SearchGroup>

        <div class="mt-7">

            <div class="flex items-center justify-between px-5 pb-2">
                <span class="block text-xs uppercase font-medium text-zinc-500">My groups</span>
                <button @click="openCreateGroupModal()">
                    <svg
                        class="w-[18px] h-[18px] flex-none text-zinc-500 hover:text-zinc-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </button>
            </div>

            <div v-if="groups.length">
                <div
                    v-for="group in groups"
                    :key="group.id"
                    @click="selectGroup(group)"
                    :class="[ selectedGroup && group.id === selectedGroup.id ? 'border-lime-400 bg-secondary  text-white' : 'border-transparent text-zinc-400' ]"
                    class="px-5 py-1.5 border-l-[3px] cursor-pointer flex justify-between items-center space-x-4">
                        <span class="block text-[13px]">{{ group.name }}</span>

                        <div class="flex items-center space-x-2">
                            <div v-if="group.unread_group_messages_count > 0">
                                <div
                                    v-if="!selectedGroup || selectedGroup.id !== group.id || props.member"
                                    class="p-0 w-[15px] h-[15px] flex justify-center items-center rounded-full bg-red-500 text-white text-[11px]">
                                        <span class="block">{{ group.unread_group_messages_count }}</span>
                                </div>
                            </div>

                            <div v-if="group.unread_private_messages_count > 0">
                                <div
                                    class="p-0 w-[15px] h-[15px] flex justify-center items-center rounded-full bg-blue-500 text-white text-[11px]">
                                        <span class="block">{{ group.unread_private_messages_count }}</span>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div v-else class="px-5 py-1.5">
                <p class="text-zinc-400 text-[13px]">You currently don't belong to any groups and you haven't created one yet.</p>
            </div>
        </div>
        
        <div class="absolute left-0 right-0 bottom-0 px-5 py-2 border-t border-tertiary">
            <div class="flex items-center justify-between">
                <div class="relative">
                    <Link :href="route('profile')">
                        <img
                            :src="'https://i.pravatar.cc/150?img=' + $page.props.auth.user.id"
                            alt="user_avatar"
                            class="w-8 h-8 rounded-full flex-none">
                    </Link>

                    <div
                        v-if="$page.props.auth.invitations.length > 0"
                        class="absolute -top-2 -right-2 bg-lime-500 text-zinc-800 font-medium w-4 h-4 flex justify-center items-center rounded-full text-[11px]">
                            {{ $page.props.auth.invitations.length }}
                    </div>
                </div>

                <button @click="logout()">
                    <svg class="w-5 h-5 flex-none text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </div>
        </div>

        <CreateGroupModal
            :showCreateGroupModal="showCreateGroupModal"
            @closeCreateGroupModal="closeCreateGroupModal"    
        ></CreateGroupModal>
    </div>
</template>