<script setup>
import { ref, watch } from 'vue';
import SettingLayout from '@/Layouts/SettingLayout.vue';
import axios from 'axios';
import debounce from "lodash/debounce";
import { Inertia } from '@inertiajs/inertia';
import InvitationsSent from '@/Components/Chat/Group/Invitations/InvitationsSent.vue';
import { usePage } from '@inertiajs/inertia-vue3';

const props = defineProps({
    group: Object,
    joinRequestsCount: Number,
    invitations: Object,
    search: String
});

const search = ref(props.search)
const results = ref([]);
const loading = ref(false);
const view = ref('invitations');

const processing = ref({
    index: '',
    value: false,
})

watch(() => search.value, debounce(async () => {
    loading.value = true

    if(search.value) {
        await axios.get('/groups/' + props.group.id + '/users?search=' + search.value)
        loading.value = false
    } else {
        loading.value = false
        results.value = [];
        await axios.get('/groups/' + props.group.slug + '/settings/invite-members')
    }
}, 600));

window.Echo.channel('search-user.' + usePage().props.value.auth.user.id)
    .listen('SearchUser', (e) => {
        results.value = e.users;
    });

const inviteUser = (user, index) => {
    Inertia.post(route('group.invite-user', { id: props.group.id }), {
        to: user.id 
    }, {
        onStart: () => { 
            processing.value.index = index;
            processing.value.value = true;
        },
        onFinish: () => {
            processing.value.index = '';
            processing.value.value = false;

            results.value.splice(index, 1)
        },
    })
}

const changeView = (value) => {
    view.value = value;
}
</script>

<template>
    <SettingLayout
        :group="group"
        :joinRequestsCount="joinRequestsCount">

        <div class="mb-8">
            <h2 class="font-medium text-xl text-white">Invite members</h2>
            <p class="text-sm text-zinc-500">In this section you can invite people to join your group.<br>You can also manage the requests to join the group you have sent.</p>
        </div>

        <div class="flex text-[13px] border-b border-tertiary mb-8">
           <button
                class="px-6 pb-3 border-b transition duration-200"
                @click="changeView('invitations')"
                :class="[ view === 'invitations' ? 'text-white border-lime-500' : 'text-zinc-500 border-transparent' ]">
                    Invitations sent
            </button>

            <button
                class="px-6 pb-3 border-b transition duration-200"
                @click="changeView('search')"
                :class="[ view === 'search' ? 'text-white border-lime-500' : 'text-zinc-500 border-transparent' ]">
                    Invite users
            </button>
        </div>

        <section v-if="view === 'search'">
            <div class="relative">
                <div class="relative">
                    <input
                        type="text"
                        v-model="search"
                        placeholder="Search user to invite"
                        class="w-full pl-10 py-2.5 text-xs bg-secondary border border-tertiary text-white rounded-lg focus:border-transparent focus:ring-2 focus:ring-zinc-600 focus:ring-opacity-40 transition">
                    <svg
                        class="absolute left-2 top-2 w-5 h-5 text-zinc-500"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <div class="absolute left-0 right-0">
                    <div class="mt-4">
                        <div v-if="loading" class="flex justify-center items-center p-4 bg-secondary border border-tertiary rounded-xl z-10">
                            <svg
                                class="w-5 h-5 animate-spin-fast text-zinc-300 flex-none"
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
                        </div>

                        <div
                            v-if="(results.length >= 1 && !loading)"
                            class="overflow-hidden bg-secondary border border-tertiary rounded-xl z-10">
                                <div v-auto-animate>
                                    <div
                                        v-for="(user, index) in results"
                                        :key="user.id"
                                        class="px-5 py-3 border-b border-tertiary flex items-center justify-between">

                                            <div class="flex items-center space-x-3">
                                                <img
                                                    :src="'https://i.pravatar.cc/150?img=' + user.id"
                                                    alt="user_avatar"
                                                    class="w-7 h-7 rounded-full flex-none">
                                                
                                                    <span class="block text-zinc-300 font-medium text-sm">
                                                        {{ user.name }}
                                                    </span>
                                            </div>

                                            <button
                                                @click="inviteUser(user, index)"
                                                class="w-16 flex items-center justify-center py-1 bg-lime-500 hover:bg-opacity-90 border border-transparent rounded-lg text-xs text-zinc-800 focus:outline-none focus:ring-2 focus:ring-lime-300 transition duration-150">
                                                    <svg
                                                        v-if="processing.value && processing.index === index"
                                                        class="w-4 h-4 animate-spin-fast text-zinc-200 flex-none"
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
                                                    <span v-else>Invite</span>
                                            </button>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <InvitationsSent
            v-if="view === 'invitations'"
            :invitations="invitations"
            :group="group"
        ></InvitationsSent>

    </SettingLayout>
</template>