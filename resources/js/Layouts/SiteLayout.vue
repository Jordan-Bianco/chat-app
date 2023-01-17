<script setup>
import FlashMessage from '@/Components/Chat/FlashMessage.vue';
import Sidebar from '@/Components/Chat/Aside/Sidebar.vue'

const props = defineProps({
    group: Object,
    member: Object,
})
</script>

<template>
    <div>
        <div class="flex min-h-screen">

            <transition name="from-right">
                <FlashMessage v-if="$page.props.flash.message"></FlashMessage>
            </transition>

            <div class="w-[245px] flex-none min-h-screen">
                <Sidebar
                    :groups="$page.props.auth.groups"
                    :group="group"
                    :member="member"
                >
                </Sidebar>
            </div>

            <div class="bg-secondary text-zinc-400 w-full relative">
                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
