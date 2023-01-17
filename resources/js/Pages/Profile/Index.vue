<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';

const props = defineProps({
    user: Object
})
</script>
    
<template>
    <ProfileLayout :user="user">
        {{ user.name }}
    </ProfileLayout>
</template>