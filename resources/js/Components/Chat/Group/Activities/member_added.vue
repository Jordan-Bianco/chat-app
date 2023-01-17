<script setup>
    const props = defineProps({
        activity: Object
    })
</script>
    
<template>
    <div class="flex items-start space-x-3">
        <img
            :src="'https://i.pravatar.cc/150?img=' + JSON.parse(activity.data).user.id"
            alt="user_avatar"
            class="w-8 h-8 rounded-full flex-none">
        
        <div>
            <p class="text-sm text-white mb-1">
                <span v-if="activity.author">{{ activity.author.name }} added {{ JSON.parse(activity.data).user.name }}.</span> 
                <span v-else>{{ JSON.parse(activity.data).user.name }} has been added to the group.</span> 
            </p>
            <span class="block text-xs text-zinc-400">{{ activity.created_at }}</span>
        </div>
    </div>
</template>