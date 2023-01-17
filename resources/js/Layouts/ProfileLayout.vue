<script setup>
import FlashMessage from '@/Components/Chat/FlashMessage.vue';
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object
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
                                :href="route('home')"
                                class="flex items-center space-x-1 text-lime-500 hover:text-lime-400"> 
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                                    <span class="text-xs">Home</span>
                            </Link>

                            <button class="sm:hidden" @click="toggleMenu()">
                                <svg class="w-5 h-5 flex-none text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                            </button>
                        </header>

                        <div v-auto-animate>
                            <div v-if="showMenu" class="mt-[26px] overflow-hidden">
                                <span class="block text-white mb-10">{{ user.name }}</span>
                                
                                <div class="mb-6">
                                    <span class="block text-[11px] text-zinc-500 uppercase font-medium mb-2">Manage group requests</span>
                                    
                                    <ul class="space-y-1.5">
                                        <Link
                                            :href="route('profile.requests-sent')"
                                            class="block text-sm"
                                            :class="[ route().current() === 'profile.requests-sent' ? 'text-white' : 'text-zinc-400' ]">
                                                Requests sent
                                        </Link>

                                        <Link
                                            :href="route('profile.invitations')"
                                            class="flex items-center justify-between w-44">
                                                <span
                                                    class="block text-sm"
                                                    :class="[ route().current() === 'profile.invitations' ? 'text-white' : 'text-zinc-400' ]">
                                                        Invitations
                                                </span>

                                                <div
                                                    v-if="$page.props.auth.invitations.length > 0"
                                                    class="bg-lime-500 text-zinc-800 font-medium w-4 h-4 flex items-center justify-center rounded-full text-[10px]">
                                                        {{ $page.props.auth.invitations.length }}
                                                </div>
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
