<script setup>
import SettingLayout from '@/Layouts/SettingLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { ref, watch } from 'vue';
import debounce from "lodash/debounce"
import pickBy from "lodash/pickBy"
import InertiaPagination from "@/Components/Chat/InertiaPagination.vue";

const props = defineProps({
    group: Object,
    members: Object,
    joinRequestsCount: Number
});

const changedMemberId = ref('');

const filters = ref({
    search: '',
});

const changeRole = (member) => {
    Inertia.patch(route('group.member.role.update', { groupId: props.group.id, userId: member.id }), {
        role: member.role
    }, {
        onFinish: () => { 
            changedMemberId.value = member.id
        }
    })
}

watch(() => filters.value.search, debounce((value) => {
    Inertia.get(route('group.settings.manage-roles', { slug: props.group.slug }), pickBy(filters.value), {
        preserveState: true
    })
}, 500));

watch(changedMemberId, () => {
    setTimeout(function() {
        changedMemberId.value = ''
    }, 1000)
});

</script>
    
<template>
    <SettingLayout
        :group="group"
        :joinRequestsCount="joinRequestsCount">

            <div class="mb-8">
                <h2 class="font-medium text-xl text-white">Manage roles</h2>
                <p class="text-sm text-zinc-500">In this section you can manage the roles of the group members.</p>
            </div>

            <div class="relative mb-4">
                <input
                    type="text"
                    v-model="filters.search"
                    placeholder="Search among the group members"
                    class="w-full pl-10 py-2.5 text-xs bg-secondary border border-tertiary text-white rounded-lg focus:border-transparent focus:ring-2 focus:ring-zinc-600 focus:ring-opacity-40 transition">
                <svg
                    class="absolute left-2 top-2 w-5 h-5 text-zinc-500"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div v-if="members.data.length !== 0">
                <div
                    v-for="member in members.data"
                    :key="member.id"
                    class="flex justify-between items-center py-2 border-b border-tertiary">

                    <div class="flex space-x-3 items-center">
                        <img
                            :src="'https://i.pravatar.cc/150?img=' + member.id"
                            alt="user_avatar"
                            class="w-8 h-8 rounded flex-none">
                            
                            <span class="block font-medium text-sm">{{ member.name }}</span>
                    </div>

                    <div
                        v-if="member.id !== $page.props.auth.user.id"
                        class="flex items-center space-x-2">
                            <transition name="fade">
                                <svg v-if="changedMemberId === member.id" class="w-4 h-4 flex-none text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </transition>

                            <select
                                :disabled="changedMemberId !== ''"
                                v-model="member.role"
                                @change="changeRole(member)"
                                class="bg-secondary border border-zinc-700 focus:border-transparent focus:ring-2 focus:ring-zinc-600 text-xs rounded py-1">
                                    <option value="Leader">Leader</option>
                                    <option value="User">User</option>
                            </select>
                    </div>
                </div>

                <InertiaPagination
                    class="pt-2"
                    v-if="members.links && members.last_page !== 1"
                    :links="members.links">
                </InertiaPagination>
            </div>
            <div
                v-if="members.data.length === 0 && filters.search"
                class="p-4 text-zinc-500 text-sm h-72">    
                    There are no users available for "{{ filters.search }}".
            </div>
    </SettingLayout>
</template>