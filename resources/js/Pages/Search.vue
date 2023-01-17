<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import { ref, onMounted } from 'vue';
import InertiaPagination from "@/Components/Chat/InertiaPagination.vue";
import SiteLayout from '@/Layouts/SiteLayout.vue'

const props = defineProps({
    results: Object,
    search: String
});

const searchResults = ref([]);

onMounted(() => {
   searchResults.value = props.results 
});
</script>

<template>
    <Head title="Search" />

    <SiteLayout>
        <div class="p-8">
            <p class="text-sm mb-4">Results for "{{ search}}"</p>
            <div
                v-for="group in searchResults.data"
                :key="group.id"
                class="p-4 bg-tertiary mb-2 rounded-lg flex justify-between items-center space-x-4">
                    <Link
                        :href="route('group.show', { slug: group.slug })"
                        class="block font-medium text-sm">
                            {{ group.name }}
                    </Link>

                    <div class="flex items-center space-x-1 text-zinc-500">
                        <svg class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="block text-xs mt-0.5">{{ group.members_count }} partecipants</span>
                    </div>
            </div>

            <InertiaPagination
                class="pt-2"
                v-if="searchResults.links"
                :links="searchResults.meta.links"></InertiaPagination>
        </div>
    </SiteLayout>
</template>
