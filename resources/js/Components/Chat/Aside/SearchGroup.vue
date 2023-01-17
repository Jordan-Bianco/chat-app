<script setup>
import { Link, usePage } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import axios from 'axios';
import debounce from "lodash/debounce";
import { clickOutSide as vClickOutSide } from '@mahdikhashan/vue3-click-outside'

const search = ref('')
const results = ref([]);
const processing = ref(false);

watch(() => search.value, debounce(async () => {
    processing.value = true

    if(search.value) {
        await axios.get('/groups?search=' + search.value)
        processing.value = false
    } else {
        processing.value = false
        results.value = [];
        await axios.get('/')
    }
}, 600));

window.Echo.channel('search-group.' + usePage().props.value.auth.user.id)
    .listen('SearchGroup', (e) => {
        results.value = e.groups;
    });

const customMethod = () => {
    results.value = []
    search.value = ''
}
</script>
    
<template>
    <section>
        <div v-click-out-side="customMethod" class="mt-7 px-5 pb-2 relative">
            <div class="relative">
                <input
                    type="text"
                    v-model="search"
                    placeholder="Search"
                    class="w-full pl-10 py-2.5 text-xs bg-secondary border border-tertiary text-white rounded-lg focus:border-transparent focus:ring-2 focus:ring-zinc-600 focus:ring-opacity-40 transition">
                <svg
                    class="absolute left-2 top-2 w-5 h-5 text-zinc-500"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div class="absolute left-5 right-5">
                <div class="mt-2">
                    <div v-if="processing" class="flex justify-center items-center p-4 bg-secondary border border-tertiary rounded-xl z-10">
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
                        v-if="(results.length >= 1 && !processing)"
                        class="overflow-hidden bg-secondary border border-tertiary rounded-xl z-10">
                            <div
                                v-for="group in results"
                                :key="group.id"
                                class="px-5 py-3 border-b border-tertiary">
                                    <Link
                                        :href="route('group.show', { slug: group.slug })"
                                        class="block text-zinc-300 font-medium text-sm">
                                            {{ group.name }}
                                    </Link>

                                    <div class="flex items-center space-x-1 text-zinc-400">
                                        <svg class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span class="block text-xs mt-0.5 whitespace-nowrap">{{ group.members_count }} members</span>
                                    </div>
                            </div>

                            <div>
                                <Link
                                    :href="(route('group.search.index', { search: search }))"
                                    class="flex items-center justify-between text-xs text-lime-500 px-5 py-3">
                                        <span>All results for "{{ search }}"</span>
                                        <svg class="w-4 h-4 flex-none" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </Link>
                            </div>
                    </div>

                </div>

            </div>
        </div>

    </section>
</template>