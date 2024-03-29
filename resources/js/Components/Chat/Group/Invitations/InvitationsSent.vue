<script setup>
import InertiaPagination from "@/Components/Chat/InertiaPagination.vue";
import { ref } from "vue";
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    invitations: Object,
    group: Object
});

const processing = ref({
    index: '',
    value: false,
});

const cancelInvitation = (to, index) => {
    Inertia.post(route('group.invite-user', { id: props.group.id }), {
        to: to 
    }, {
        onStart: () => { 
            processing.value.index = index;
            processing.value.value = true;
        },
        onFinish: () => {
            processing.value.index = '';
            processing.value.value = false;
        },
    })
}
</script>
    
<template>
    <div>
        <div v-auto-animate>
            <div
                v-for="(invitation, index) in invitations.data"
                :key="invitation.id"
                class="p-4 bg-tertiary mb-2 rounded-lg flex justify-between items-center space-x-4 text-sm">
                    
                    <div class="flex items-center space-x-3">
                        <img
                            :src="'https://i.pravatar.cc/150?img=' + invitation.to"
                            alt="user_avatar"
                            class="w-8 h-8 rounded-full flex-none">

                        <div>
                            <p>You sent an invitation to
                                <span class="font-medium text-white">
                                    {{ invitation.name }}
                                </span>
                            </p>
                            <span class="text-xs text-zinc-500">{{ invitation.created_at }}</span>
                        </div>
                    </div>

                    <button
                        @click="cancelInvitation(invitation.to, index)"
                        class="w-16 flex items-center justify-center py-1.5 bg-lime-500 hover:bg-opacity-70 border border-transparent rounded-lg text-xs text-zinc-800 focus:outline-none focus:ring-2 focus:ring-lime-300 transition duration-150">
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
                            <span v-else>Cancel</span>
                    </button>
            </div>
        </div>

        <InertiaPagination
            class="pt-2"
            v-if="invitations.links && invitations.last_page !== 1"
            :links="invitations.links">
        </InertiaPagination>
    </div>
</template>