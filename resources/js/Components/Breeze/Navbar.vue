<script setup>
import { Link } from '@inertiajs/inertia-vue3';
</script>
    
<template>
    <div class="w-full py-2 bg-zinc-800 text-white">
        <div v-if="$page.props.auth.user">
            <span>{{ $page.props.auth.user.name }}</span>
        
            <Link
                :href="route('dashboard')"
                class="text-sm  underline">
                    Dashboard
            </Link>
        </div>

        <div v-else>
            <Link :href="route('login')" class="text-sm  underline">Log in</Link>

            <Link
                :href="route('register')"
                class="ml-4 text-sm  underline">
                    Register
            </Link>
        </div>
    </div>
</template>