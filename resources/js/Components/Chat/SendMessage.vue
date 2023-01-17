<script setup>
import { Inertia } from '@inertiajs/inertia';
import { ref, onMounted } from 'vue';
import EmojiPicker from '@/Components/Chat/EmojiPicker.vue';

const props = defineProps({
    member: Object|null,
    group: Object
})

const body = ref('');
const attachment = ref('');
const receiver_id = ref('');
const preview = ref({
    name: '',
    image: ''
});

onMounted(() => {
    receiver_id.value = props.member ? props.member.id : props.group.id
});

const sendMessage = () => {

    if (!body.value && !attachment.value) return;

    Inertia.post(route('message.store'), { 
        body: body.value,
        attachment: attachment.value,
        receiver_id: receiver_id.value,
        group_id: props.group.id,
    });

    removeAttachment()
    body.value = '';
}

const appendEmoji = (value) => {
    body.value += value.i
}

const placeholder = () => {
    return props.member ? 'Message ' + props.member.name : 'Message ' + props.group.name; 
}

const uploadFile = (e) => {
    attachment.value = e.target.files[0];
    preview.value.name = attachment.value.name;

    if (attachment.value.type.includes('image')) {
        preview.value.image = URL.createObjectURL(attachment.value);
    }
}

const removeAttachment = () => {
    attachment.value = '';
    preview.value.name = '';
    preview.value.image = '';
}
</script>
    
<template>
    <div class="z-10">
        <div class="absolute bottom-4 left-8 right-8">
            
            <p v-if="$page.props.errors.attachment" class="text-red-500 text-xs mb-1">{{ $page.props.errors.attachment }}</p>
    
            <div class="border border-zinc-700 bg-secondary rounded-lg w-full text-white p-3">
                <div
                    v-if="preview.name"
                    class="flex justify-between items-start bg-tertiary p-3 rounded-lg mb-2">
                        <div class="flex space-x-3">
                            <div
                                v-if="preview.image"
                                class="relative w-48 h-32">
                                    <img :src="preview.image" class="w-full h-full rounded"/>
                            </div>
                            <div class="text-xs">{{ preview.name }}</div>
                        </div>
                    
                    <button
                        @click="removeAttachment()"
                        class="-mt-2 text-2xl text-red-500 hover:text-red-400">
                            &times;
                    </button>
                </div>

                <textarea
                    @keydown.enter.prevent="sendMessage()"
                    :placeholder="placeholder()"
                    rows="2"
                    class="w-full border-none focus:border-transparent focus:ring-0 resize-none bg-transparent text-sm pr-24"
                    v-model="body"></textarea>

                <div class="absolute right-6 bottom-1.5 flex items-baseline space-x-2">
                                        
                    <svg
                        @click="$refs.file.click()"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-paperclip text-zinc-500 hover:text-lime-500 flex-none w-[19px] h-[19px] cursor-pointer"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>

                    <input
                        type="file"
                        ref="file"
                        @change="uploadFile"
                        style="display: none"
                    />

                    <EmojiPicker
                        :classes="'absolute bottom-7 right-3'"
                        @appendEmoji="appendEmoji"
                    ></EmojiPicker>

                    <button
                        @click="sendMessage()"
                        :disabled="body === '' && attachment === ''"
                        class="text-zinc-500 hover:text-lime-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send w-5 h-5 transform rotate-45"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>