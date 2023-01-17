<script setup>
import FlashMessage from '@/Components/Chat/FlashMessage.vue';
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps({
    group: Object,
    joinRequestsCount: Number
});

const showMenu = ref(true);

const toggleMenu = () => {
    showMenu.value = !showMenu.value
}
</script>

<template>
    <div>
        <div class="min-h-screen bg-secondary">

            <transition name="from-right">
                <FlashMessage v-if="$page.props.flash.message"></FlashMessage>
            </transition>

            <div class="min-h-screen sm:flex">
                
                <div class="sm:w-80 bg-primary p-6">
                    <div>
                        <header class="flex items-center justify-between">
                            <Link
                                :href="route('group.show', { slug: group.slug })"
                                class="flex items-center space-x-1 text-lime-500 hover:text-lime-400"> 
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                                    <span class="text-xs">back</span>
                            </Link>

                            <button class="sm:hidden" @click="toggleMenu()">
                                <svg class="w-5 h-5 flex-none text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                            </button>
                        </header>

                        <div v-auto-animate>
                            <div v-if="showMenu" class="mt-[26px] overflow-hidden">
                                <span class="block text-white mb-10">{{ group.name }}</span>
                                
                                <div class="mb-8">
                                    <span class="block text-[11px] text-zinc-500 uppercase font-medium mb-2">Group setting</span>
                                    <ul class="space-y-1.5">
                                        <Link
                                            :href="route('group.settings.info', { slug: group.slug})"
                                            class="block text-sm"
                                            :class="[ route().current() === 'group.settings.info' ? 'text-white' : 'text-zinc-400' ]">
                                                Edit group info
                                        </Link>

                                        <Link
                                            :href="route('group.settings.delete', { slug: group.slug})"
                                            class="block text-sm"
                                            :class="[ route().current() === 'group.settings.delete' ? 'text-white' : 'text-zinc-400' ]">
                                                Delete group
                                        </Link>
                                    </ul>
                                </div>

                                <div class="mb-8">
                                    <span class="block text-[11px] text-zinc-500 uppercase font-medium mb-2">Manage group members</span>
                                    <ul class="space-y-1.5">
                                        <Link
                                            :href="route('group.settings.manage-roles', { slug: group.slug})"
                                            class="block text-sm"
                                            :class="[ route().current() === 'group.settings.manage-roles' ? 'text-white' : 'text-zinc-400' ]">
                                                Manage roles
                                        </Link>
                                        
                                        <Link
                                            :href="route('group.settings.remove-members', { slug: group.slug})"
                                            class="block text-sm"
                                            :class="[ route().current() === 'group.settings.remove-members' ? 'text-white' : 'text-zinc-400' ]">
                                                Remove members
                                        </Link>
                                    </ul>
                                </div>

                                <div class="mb-8">
                                    <span class="block text-[11px] text-zinc-500 uppercase font-medium mb-2">Manage group invitations</span>
                                    <ul class="space-y-1.5">
                                        <Link
                                            :href="route('group.settings.join-requests', { slug: group.slug})"
                                            class="text-sm flex items-center justify-between w-44"
                                            :class="[ route().current() === 'group.settings.join-requests' ? 'text-white' : 'text-zinc-400' ]">
                                                <span>Join requests</span>
                                                <span v-if="joinRequestsCount > 0" class="bg-lime-500 font-semibold text-zinc-800 w-[17px] h-[17px] flex justify-center items-center rounded-full text-[10px]">
                                                    {{ joinRequestsCount }}
                                                </span>
                                        </Link>

                                        <Link
                                            :href="route('group.settings.invite-members', { slug: group.slug})"
                                            class="block text-sm"
                                            :class="[ route().current() === 'group.settings.invite-members' ? 'text-white' : 'text-zinc-400' ]">
                                                Invite members
                                        </Link>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sm:w-full min-h-screen bg-secondary text-zinc-400 relative">
                    <main class="px-10 py-16">
                        <slot />
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
