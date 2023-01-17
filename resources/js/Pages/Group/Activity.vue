<script setup>
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { Link } from '@inertiajs/inertia-vue3';

import group_created from "@/Components/Chat/Group/Activities/group_created.vue";
import group_updated from "@/Components/Chat/Group/Activities/group_updated.vue";
import member_removed from "@/Components/Chat/Group/Activities/member_removed.vue";
import member_added from "@/Components/Chat/Group/Activities/member_added.vue";
import member_promoted from "@/Components/Chat/Group/Activities/member_promoted.vue";
import member_demoted from "@/Components/Chat/Group/Activities/member_demoted.vue";
import member_left from "@/Components/Chat/Group/Activities/member_left.vue";

const props = defineProps({
    group: Object,
    activities: Object
});

const activityName = (activityName) => {
    switch (activityName) {
        case 'group_created':
            return group_created;
        case 'group_updated':
            return group_updated;
        case 'member_removed':
            return member_removed;
        case 'member_added':
            return member_added;
        case 'member_promoted':
            return member_promoted;
        case 'member_demoted':
            return member_demoted;
        case 'member_left':
            return member_left
    }
}
</script>
    
<template>
    <SiteLayout :group="group">
        <header class="px-5 pt-9 flex justify-between mb-4">
            <h2 class="text-white text-xl">Activity feed</h2>
            <Link
                :href="route('group.show', { slug: group.slug })"
                class="flex items-center space-x-1 text-lime-500 hover:text-lime-400"> 
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                    <span class="text-xs">back</span>
            </Link>
        </header>

        <div v-for="activity in activities" :key="activity.id">
            <component
                :is="activityName(activity.name)"
                :activity="activity"
                class="p-4 border-b border-tertiary"
            ></component>
        </div>
    </SiteLayout>
</template>