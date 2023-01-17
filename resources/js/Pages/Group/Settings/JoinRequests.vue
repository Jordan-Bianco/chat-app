<script setup>
import SettingLayout from '@/Layouts/SettingLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { ref } from 'vue';
import InertiaPagination from "@/Components/Chat/InertiaPagination.vue";

const props = defineProps({
    group: Object,
    joinRequests: Object
});

const processing = ref({
    index: '',
    status: ''
})

const rejectRequest = (from, index) => {
    Inertia.post(route('group.join.reject', { id: props.group.id }), {
        from: from 
    }, {
        onStart: () => { 
            processing.value.index = index;
            processing.value.status = 'Rejected';
        },
        onFinish: () => {
            processing.value.index = '';
            processing.value.status = '';
        },
    }) 
}

const acceptRequest = (from, index) => {
    Inertia.post(route('group.join.accept', { id: props.group.id }), {
        from: from 
    }, {
        onStart: () => { 
            processing.value.index = index;
            processing.value.status = 'Accepted';
        },
        onFinish: () => {
            processing.value.index = '';
            processing.value.status = '';
        },
    }) 
}
</script>

<template>
    <SettingLayout
        :group="group"
        :joinRequestsCount="joinRequests.total">

            <div class="mb-8">
                <h2 class="font-medium text-xl text-white">Join Requests</h2>
                <p class="text-sm text-zinc-500">In this section, you can manage the requests received to join the group.</p>
            </div>

            <div v-auto-animate>
                <div
                    v-for="(request, index) in joinRequests.data"
                    :key="request.id"
                    class="p-4 bg-tertiary mb-2 rounded-lg flex justify-between items-center space-x-4 text-sm">
                        <div class="flex items-center space-x-3">

                            <img
                                :src="'https://i.pravatar.cc/150?img=' + request.from"
                                alt="user_avatar"
                                class="w-8 h-8 rounded-full flex-none">

                            <div>
                                <p>You have received a request to join the group from <span class="text-white">{{ request.name }}</span></p>
                                <span class="text-xs text-zinc-500">{{ request.created_at }}</span>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button
                                type="button"
                                @click="rejectRequest(request.from, index)"
                                class="w-16 flex justify-center items-center py-1.5 bg-zinc-600 hover:bg-opacity-70 border border-transparent rounded-lg text-xs text-zinc-300 focus:outline-none focus:ring-2 focus:ring-zinc-500 transition duration-150">
                                    <svg
                                        v-if="processing.index === index && processing.status === 'Rejected'"
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
                                    <span v-else>Reject</span>
                            </button>

                            <button
                                @click="acceptRequest(request.from, index)"
                                class="w-16 flex items-center justify-center py-1.5 bg-lime-500 hover:bg-opacity-70 border border-transparent rounded-lg text-xs text-zinc-800 focus:outline-none focus:ring-2 focus:ring-lime-300 transition duration-150">
                                    <svg
                                        v-if="processing.index === index && processing.status === 'Accepted'"
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
                                    <span v-else>Accept</span>
                            </button>
                        </div>
                </div>
            </div>

            <InertiaPagination
                class="pt-2"
                v-if="joinRequests.links && joinRequests.last_page !== 1"
                :links="joinRequests.links">
            </InertiaPagination>
    </SettingLayout>
</template>