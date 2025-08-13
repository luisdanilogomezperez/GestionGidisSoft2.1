<template>
    <div class="mb-0 md:col-span-1 flex justify-center bg-red-500 dark:bg-red-800 shadow-md rounded-t-xl border border-red-200 dark:border-red-700">
        <div class="px-4 sm:px-0">
            <h3 class="block text-lg font-medium text-white dark:text-white mb-1 mt-1">
                <slot name="title" />
            </h3>
            <p class="block text-sm font-medium text-white dark:text-white mb-1">
                <slot name="description" />
            </p>
        </div>
        <div class="px-4 sm:px-0">
            <slot name="aside" />
        </div>
    </div>
</template>
