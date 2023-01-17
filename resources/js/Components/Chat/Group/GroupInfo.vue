<script setup>
import GroupMembers from '@/Components/Chat/Group/Info/GroupMembers.vue';
import GroupAbout from '@/Components/Chat/Group/Info/GroupAbout.vue';
import { ref } from 'vue';

const props = defineProps({
    group: Object,
    onlineMembers: Array,
    isMember: Boolean,
    unreadGroupPrivateMessages: Object
});

const content = ref('');

const showContentFor = (value) => {
    if (value === content.value) {
        content.value = ''
    } else {
        content.value = value
    }
}
</script>
    
<template>
    <div class="w-[345px] border-l border-tertiary">

        <header class="border-b border-tertiary p-5">
            <span class="block text-base font-medium text-white mb-1">Group info</span>
            <span class="block text-xs text-zinc-500">Created {{ group.created_at }}</span>
        </header>

        <section>
            <GroupMembers
                v-if="isMember"
                :content="content"
                :group="group"
                :onlineMembers="onlineMembers"
                @showContentFor="showContentFor"
                :unreadGroupPrivateMessages="unreadGroupPrivateMessages"
            ></GroupMembers>

            <GroupAbout
                :content="content"
                :group="group"
                @showContentFor="showContentFor"
            ></GroupAbout>
        </section>
    </div>
</template>