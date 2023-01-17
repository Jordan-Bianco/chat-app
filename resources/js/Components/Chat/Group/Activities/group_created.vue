<script setup>
    const props = defineProps({
        activity: Object
    })
</script>
    
<template>
    <div class="flex items-start space-x-3">
        <img
            :src="'https://i.pravatar.cc/150?img=' + activity.author.id"
            alt="user_avatar"
            class="w-8 h-8 rounded-full flex-none">
        
        <div>
            <span class="block text-sm text-white mb-1">{{ activity.author.name }} created the group "{{ JSON.parse(activity.data).name }}".</span>
            <span class="block text-xs text-zinc-400">{{ activity.created_at }}</span>
        </div>
    </div>
</template>