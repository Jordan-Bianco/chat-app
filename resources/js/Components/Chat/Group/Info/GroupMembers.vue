<script setup>
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps({
    content: String,
    group: Object,
    onlineMembers: Array,
    unreadGroupPrivateMessages: Object
});

const emits = defineEmits(['showContentFor']);

const selectedMember = ref('');

const showContentFor = (value) => {
    emits('showContentFor', value)
}

const selectMember = (member) => {
    if (member.id === usePage().props.value.auth.user.id) return;

    if (selectedMember.value && selectedMember.value.id === member.id) {
        selectedMember.value = ''
        Inertia.get(route('group.show', { slug: props.group.slug }));
    } else {
        selectedMember.value = member
        Inertia.get(route('group.member.show', { gslug: props.group.slug, uslug: member.slug }));
    } 
}
</script>

<template>
    <div class="px-5 py-4 border-b border-tertiary overflow-hidden">
        <div
            @click="showContentFor('members')"
            class="flex items-center justify-between cursor-pointer">
                <div class="flex items-center space-x-2 text-[11px] text-zinc-500 uppercase font-medium">
                    <span>Members</span>
                    <span
                        v-if="unreadGroupPrivateMessages && unreadGroupPrivateMessages.length > 0"
                        class="mb-1 w-[15px] h-[15px] bg-blue-500 text-white text-[10px] rounded-full flex items-center justify-center">
                            <span class="block">{{ unreadGroupPrivateMessages.length }}</span>
                    </span>
                </div>
                <svg
                    :class="{ 'rotate-90' : content === 'members' }"
                    class="w-4 h-4 flex-none transform transition duration-200 text-zinc-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
        </div>

        <div v-auto-animate>
            <div v-if="content === 'members'" class="pt-4">
                <div v-if="group.members.length !== 0" class="overflow-y-auto" style="max-height: calc(100vh - 40vh)">
                    
                    <div
                        v-for="member in group.members"
                        :key="member.id"
                        class="flex items-center justify-between mb-3">
                            <div
                                @click="selectMember(member)"
                                class="flex items-center space-x-2"
                                :class="[ member.id === $page.props.auth.user.id ? 'cursor-default' : 'cursor-pointer hover:text-white' ]"
                            >
                                <div class="relative">
                                    <img
                                        :src="'https://i.pravatar.cc/150?img='+member.id"
                                        alt="user_avatar"
                                        class="w-7 h-7 rounded-full flex-none">

                                    <span
                                        :class="[ onlineMembers.includes(member.id) ? 'bg-green-400' : 'bg-zinc-600' ]"
                                        class="absolute right-0 -bottom-1 w-2 h-2 rounded-full">
                                    </span>
                                </div>

                                <span class="block text-[13px]">{{ member.name }}</span>

                                <span
                                    v-if="member.pivot.role === 'Leader'"
                                    class="block text-[10px] bg-zinc-700 text-white px-2 py-0.5 rounded">
                                        {{ member.pivot.role }}
                                </span>
                            </div>

                            <span
                                v-if="unreadGroupPrivateMessages.filter(message => { return message.from === member.id }).length > 0"
                                class="mb-0.5 w-[15px] h-[15px] bg-blue-500 flex-none text-white text-[10px] rounded-full flex items-center justify-center">
                                    {{ unreadGroupPrivateMessages.filter(message => { return message.from === member.id }).length }}
                            </span>
                    </div>

                </div>
                <div v-else>
                    <p class="pt-4 text-zinc-500 text-sm">This group has no members yet. Add them from the manage section</p>
                </div>
            </div>
        </div>
    </div>
</template>