<script setup>
import { ref } from 'vue'

const props = defineProps({
    group_id: String,
    attachments: Array
})

const fullWidthImage = ref(null)

const openImageModal = (attachment) => {
    fullWidthImage.value = attachment
}

const closeImageModal = () => {
    fullWidthImage.value = null
}
</script>

<template>
    <div>
        <div v-for="attachment in attachments" :key="attachment.id">

            <!-- PDF -->
            <div v-if="attachment.extension === 'pdf'">
                <div class="flex items-center space-x-2 border border-tertiary p-2 rounded-lg">
                    <svg class="w-9 h-9 flex-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128"><path fill="#fd4233" d="M104 126H24c-5.514 0-10-4.486-10-10V12c0-5.514 4.486-10 10-10h40.687a9.932 9.932 0 0 1 7.07 2.929l39.314 39.314a9.932 9.932 0 0 1 2.929 7.07V116c0 5.514-4.486 10-10 10zM24 6c-3.309 0-6 2.691-6 6v104c0 3.309 2.691 6 6 6h80c3.309 0 6-2.691 6-6V51.313a6.044 6.044 0 0 0-1.757-4.242L68.929 7.757A5.96 5.96 0 0 0 64.687 6H24z"/><path fill="#ff402f" d="M95.21 80.32c-.07-.51-.48-1.15-.92-1.58-1.26-1.24-4.03-1.89-8.25-1.95-2.86-.03-6.3.22-9.92.73-1.62-.93-3.29-1.95-4.6-3.18-3.53-3.29-6.47-7.86-8.31-12.89.12-.47.22-.88.32-1.3 0 0 1.98-11.28 1.46-15.1-.07-.52-.12-.67-.26-1.08l-.17-.44c-.54-1.25-1.6-2.57-3.26-2.5l-.98-.03h-.02c-1.86 0-3.36.95-3.76 2.36-1.2 4.44.04 11.09 2.29 19.69l-.58 1.4c-1.61 3.94-3.63 7.9-5.41 11.39l-.23.45c-1.88 3.67-3.58 6.79-5.13 9.43l-1.59.84c-.12.06-2.85 1.51-3.49 1.89-5.43 3.25-9.03 6.93-9.63 9.85-.19.94-.05 2.13.92 2.68l1.54.78c.67.33 1.38.5 2.1.5 3.87 0 8.36-4.82 14.55-15.62 7.14-2.32 15.28-4.26 22.41-5.32 5.43 3.05 12.11 5.18 16.33 5.18.75 0 1.4-.07 1.92-.21.81-.22 1.49-.68 1.91-1.3.82-1.23.98-2.93.76-4.67zM36.49 99.33c.7-1.93 3.5-5.75 7.63-9.13.26-.21.9-.81 1.48-1.37-4.32 6.89-7.21 9.63-9.11 10.5zM60.95 43c1.24 0 1.95 3.13 2.01 6.07.06 2.94-.63 5-1.48 6.53-.71-2.26-1.05-5.82-1.05-8.15 0 0-.05-4.45.52-4.45zm-7.3 40.14c.87-1.55 1.77-3.19 2.69-4.92 2.25-4.25 3.67-7.57 4.72-10.3 2.1 3.82 4.72 7.07 7.79 9.67.39.32.8.65 1.22.98-6.25 1.23-11.66 2.74-16.42 4.57zm39.43-.35c-.38.23-1.47.37-2.17.37-2.26 0-5.07-1.03-9-2.72 1.51-.11 2.9-.17 4.14-.17 2.27 0 2.94-.01 5.17.56 2.22.57 2.25 1.72 1.86 1.96z"/></svg>

                    <div>
                        <a
                            :href="'/storage/attachments/' + group_id + '/' + attachment.message_id + '/' + attachment.name"
                            target="_blank"
                            class="block text-zinc-300 text-[13px] font-medium">
                                {{ attachment.name }}
                        </a>

                        <span class="text-zinc-500 text-xs mr-1.5">{{ attachment.size }} byte</span>
                        <span class="text-zinc-500 text-xs">{{ attachment.extension.toUpperCase() }}</span>
                    </div>
                </div>
            </div>

            <!-- DOCX -->
            <div v-if="attachment.extension === 'docx' || attachment.extension === 'doc'">
                <div class="flex items-center space-x-2 border border-tertiary p-2 rounded-lg">
                    <svg class="w-9 h-9 flex-none" xmlns="http://www.w3.org/2000/svg" version="1.2" viewBox="0 0 512 512"><path fill="#FFF" d="M422.3 477.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z"/><path fill="#2B669F" d="M333.6 6H103.5C88 6 75.4 18.6 75.4 34.1v443.8c0 15.5 12.6 28.1 28.1 28.1h305c15.5 0 28.1-12.6 28.1-28.1V109.1L333.6 6zm88.7 471.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z"/><path fill="#084272" d="M333.6 6v103.1h103zM465.9 450.8H46.1V308c0-9.8 7.9-17.7 17.7-17.7h384.3c9.8 0 17.7 7.9 17.7 17.7v142.8z"/><path fill="#1A252D" d="M436.6 450.8v19.5l29.3-19.5zM75.4 450.8v19.5l-29.3-19.5z"/><path fill="#2B669F" d="M64.1 308.4h383.7v124.5H64.1zM298.3 78.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM298.3 110.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM391.8 142.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 174.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 206.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 238.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 270.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4z"/><path fill="#FFF" d="M157.6 380.1c0-6.9 1.5-12.5 4.5-16.8 3-4.2 7.3-6.4 12.8-6.4 2.1 0 3.9.5 5.6 1.4 1.7.9 3.1 2.3 4.5 4V338h13.9v64.1h-12.2l-.9-5.4c-1.3 2-2.9 3.6-4.7 4.7-1.8 1.1-3.9 1.6-6.2 1.6-5.5 0-9.7-2-12.7-6.1s-4.5-9.4-4.5-16.1v-.7zm13.9.8c0 3.6.5 6.4 1.6 8.4s2.8 3 5.3 3c1.5 0 2.7-.3 3.8-.8 1.1-.5 2-1.3 2.7-2.4v-18c-.7-1.2-1.6-2.1-2.7-2.7s-2.4-.9-3.8-.9c-2.5 0-4.2 1.1-5.3 3.4-1.1 2.3-1.6 5.3-1.6 9v1zM210.8 379.6c0-6.7 1.9-12.1 5.7-16.3 3.8-4.2 9.1-6.3 15.8-6.3 6.8 0 12.1 2.1 15.9 6.3 3.8 4.2 5.7 9.7 5.7 16.3v.9c0 6.7-1.9 12.2-5.7 16.4-3.8 4.2-9 6.3-15.8 6.3-6.8 0-12.1-2.1-15.9-6.3-3.8-4.2-5.7-9.6-5.7-16.4v-.9zm13.9.8c0 3.7.6 6.7 1.7 8.8 1.2 2.1 3.1 3.2 5.9 3.2 2.7 0 4.7-1.1 5.8-3.2 1.2-2.1 1.8-5.1 1.8-8.8v-.9c0-3.6-.6-6.5-1.8-8.7-1.2-2.2-3.2-3.3-5.9-3.3s-4.7 1.1-5.9 3.3c-1.2 2.2-1.7 5.1-1.7 8.7v.9zM284.9 392.4c1.9 0 3.3-.5 4.2-1.5.9-1 1.4-2.5 1.4-4.5h12.7l.1.2c.1 4.9-1.6 8.8-5 11.9-3.5 3.1-7.9 4.6-13.3 4.6-6.8 0-12-2.1-15.6-6.3-3.7-4.2-5.5-9.6-5.5-16.3v-.9c0-6.7 1.8-12.1 5.5-16.3 3.6-4.2 8.8-6.3 15.6-6.3 5.6 0 10.2 1.6 13.5 4.7 3.4 3.1 5 7.4 4.9 12.9l-.1.2h-12.7c0-2.1-.5-3.8-1.4-5.2-1-1.3-2.4-2-4.3-2-2.7 0-4.6 1.1-5.6 3.2-1.1 2.2-1.6 5.1-1.6 8.7v.9c0 3.7.5 6.6 1.6 8.8 1 2.2 2.9 3.2 5.6 3.2zM332.5 370.9h.2l5.6-13.1h15.6l-12.9 22 13.4 22.5h-15.6l-5.9-13.5h-.2l-5.9 13.5h-15.6l13.4-22.5-13-22H327l5.5 13.1z"/></svg>

                    <div>
                        <a
                            :href="'/storage/attachments/' + group_id + '/' + attachment.message_id + '/' + attachment.name"
                            target="_blank"
                            class="block text-zinc-300 text-[13px] font-medium">
                                {{ attachment.name }}
                        </a>

                        <span class="text-zinc-500 text-xs mr-1.5">{{ attachment.size }} byte</span>
                        <span class="text-zinc-500 text-xs">{{ attachment.extension.toUpperCase() }}</span>
                    </div>
                </div>
            </div>

            <!-- TXT -->
            <div v-if="attachment.extension === 'txt'">
                <div class="flex items-center space-x-2 border border-tertiary p-2 rounded-lg">
                    <svg class="w-9 h-9 flex-none" xmlns="http://www.w3.org/2000/svg" baseProfile="tiny" version="1.2" viewBox="0 0 512 512"><path fill="#FFF" d="M422.3 477.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z"/><path fill="#2B669F" d="M333.6 6H103.5C88 6 75.4 18.6 75.4 34.1v443.8c0 15.5 12.6 28.1 28.1 28.1h305c15.5 0 28.1-12.6 28.1-28.1V109.1L333.6 6zm88.7 471.9c0 7.6-6.2 13.8-13.8 13.8h-305c-7.6 0-13.8-6.2-13.8-13.8V34.1c0-7.6 6.2-13.8 13.8-13.8h230.1V109h88.7v368.9z"/><path fill="#084272" d="M333.6 6v103.1h103z"/><g><path fill="#084272" d="M465.9 450.8H46.1V308c0-9.8 7.9-17.7 17.7-17.7h384.3c9.8 0 17.7 7.9 17.7 17.7v142.8z"/><path fill="#1A252D" d="M436.6 450.8v19.5l29.3-19.5zM75.4 450.8v19.5l-29.3-19.5z"/><path fill="#2B669F" d="M64.1 308.4h383.7v124.5H64.1z"/></g><g fill="#2B669F"><path d="M298.3 78.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM298.3 110.6h-177a6.7 6.7 0 010-13.4h177a6.7 6.7 0 010 13.4zM391.8 142.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 174.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 206.5H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 238.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4zM391.8 270.4H121.3a6.7 6.7 0 010-13.4h270.5a6.7 6.7 0 010 13.4z"/></g><g fill="#FFF"><path d="M207.8 333.1v14.7h9.3v12.8h-9.3v27.1c0 2.1.4 3.6 1.3 4.6.9.9 2 1.4 3.5 1.4.9 0 1.7 0 2.3-.1.6-.1 1.3-.2 2.2-.4l1.3 13.7c-1.9.5-3.5.8-5 1-1.5.2-3.3.3-5.3.3-6.1 0-10.7-1.6-13.9-4.9-3.2-3.2-4.7-8.4-4.7-15.4v-27.2H181v-12.8h8.3v-14.7h18.5zM257.3 365.2h.3l7.5-17.4h20.7L268.7 377l17.8 29.9h-20.7l-7.9-18h-.3l-7.9 18H229l17.8-29.9-17.2-29.2h20.6l7.1 17.4zM320.5 333.1v14.7h9.3v12.8h-9.3v27.1c0 2.1.4 3.6 1.3 4.6.9.9 2 1.4 3.5 1.4.9 0 1.7 0 2.3-.1.6-.1 1.3-.2 2.2-.4l1.3 13.7c-1.9.5-3.5.8-5 1-1.5.2-3.3.3-5.3.3-6.1 0-10.7-1.6-13.9-4.9-3.2-3.2-4.7-8.4-4.7-15.4v-27.2h-8.3v-12.8h8.3v-14.7h18.3z"/></g></svg>

                    <div>
                        <a
                            :href="'/storage/attachments/' + group_id + '/' + attachment.message_id + '/' + attachment.name"
                            target="_blank"
                            class="block text-zinc-300 text-[13px] font-medium">
                                {{ attachment.name }}
                        </a>

                        <span class="text-zinc-500 text-xs mr-1.5">{{ attachment.size }} byte</span>
                        <span class="text-zinc-500 text-xs">{{ attachment.extension.toUpperCase() }}</span>
                    </div>
                </div>
            </div>

            <!-- XLSX -->
            <div v-if="attachment.extension === 'xlsx' || attachment.extension === 'xls'">
                <div class="flex items-center space-x-2 border border-tertiary p-2 rounded-lg">
                    <svg class="w-9 h-9 flex-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128"><path fill="#007732" d="M80 96h-8.3l-8-13-8 13H48l11.4-17.7-10.7-16.6h8L64.1 74l7.2-12.3h7.8l-10.8 17L80 96z"/><path fill="#007732" d="M104 126H24c-5.5 0-10-4.5-10-10V12c0-5.5 4.5-10 10-10h40.7c2.7 0 5.2 1 7.1 2.9l39.3 39.3c1.9 1.9 2.9 4.4 2.9 7.1V116c0 5.5-4.5 10-10 10zM24 6c-3.3 0-6 2.7-6 6v104c0 3.3 2.7 6 6 6h80c3.3 0 6-2.7 6-6V51.3c0-1.6-.6-3.1-1.8-4.2L68.9 7.8C67.8 6.6 66.3 6 64.7 6H24z"/></svg>

                    <div>
                        <a
                            :href="'/storage/attachments/' + group_id + '/' + attachment.message_id + '/' + attachment.name"
                            target="_blank"
                            class="block text-zinc-300 text-[13px] font-medium">
                                {{ attachment.name }}
                        </a>

                        <span class="text-zinc-500 text-xs mr-1.5">{{ attachment.size }} byte</span>
                        <span class="text-zinc-500 text-xs">{{ attachment.extension.toUpperCase() }}</span>
                    </div>
                </div>
            </div>

            <!-- IMAGE -->
            <div v-if="attachment.extension === 'jpg' || attachment.extension === 'jpeg' || attachment.extension === 'png'">
                <div class="w-40 cursor-pointer pb1">
                    <img
                        :src="'/storage/attachments/' + props.group_id + '/' + attachment.message_id + '/' + attachment.name"
                        alt="attachment_preview"
                        @click="openImageModal(attachment)"
                    >
                </div>
            </div>

        </div>

        <transition name="image">
            <div
                v-if="fullWidthImage"
                @click="closeImageModal()"
                class="fixed inset-0 bg-black bg-opacity-60 z-20">
            </div>
        </transition>

        <transition name="image">
            <div
                v-if="fullWidthImage"
                @click="closeImageModal()"
                class="fixed inset-x-28 inset-y-10 z-20">
                    <img
                        :src="'/storage/attachments/' + props.group_id + '/' + fullWidthImage.message_id + '/' + fullWidthImage.name"
                        alt="full_width_attachment"
                        class="h-full w-full object-scale-down">
            </div>
        </transition>
    </div>
</template>