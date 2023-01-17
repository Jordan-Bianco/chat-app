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

const filters = ref({
    search: ''
});

watch(() => filters.value.search, debounce((value) => {
    Inertia.get(route('group.settings.remove-members', { slug: props.group.slug }), pickBy(filters.value), {
        preserveState: true
    })
}, 500));

const removeMember = (member) => {
    Inertia.delete(route('group.member.destroy', { 
        groupId: props.group.id,
        userId: member.id
    }))
}
</script>
    
<template>
    <SettingLayout
        :group="group"
        :joinRequestsCount="joinRequestsCount">
        
            <div class="mb-8">
                <h2 class="font-medium text-xl text-white">Remove members</h2>
                <p class="text-sm text-zinc-500">In this section you can remove members from the group.</p>
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
                <div v-auto-animate>
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

                        <button
                            v-if="member.id !== $page.props.auth.user.id"
                            @click="removeMember(member)">
                                <svg
                                    class="w-5 h-5 flex-none text-zinc-400 hover:text-zinc-700"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                        </button>
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