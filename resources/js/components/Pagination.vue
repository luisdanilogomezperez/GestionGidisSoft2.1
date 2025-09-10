<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array
});
</script>

<template>
    <div class="flex space-x-1 mt-5 mb-2 text-sm font-medium text-center items-center justify-center">
        <template v-for="(link, index) in links" :key="index">
            <span
                v-if="!link.url"
                class="px-3 py-1 text-gray-400 border rounded"
                v-html="link.label"
            ></span>
            <Link
                v-else
                :href="link.url"
                class="px-3 py-1 border rounded"
                :class="{ 'bg-blue-500 text-white': link.active }"
                v-html="link.label"
            />
        </template>
    </div>
</template>
