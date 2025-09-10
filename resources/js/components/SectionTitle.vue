<template>
    <div class="mb-0 md:col-span-1 flex justify-center bg-grey-900 dark:bg-grey-800 text-black-900 dark:text-white-900">
        <div class="px-4 sm:px-0">
            <h3 class="block text-lg font-medium text-grey-900 dark:text-white mb-1 mt-1">
                <slot name="title" />
            </h3>
            <p class="block text-sm font-medium text-grey-900 dark:text-white mb-1">
                <slot name="description" />
            </p>
        </div>
        <div class="px-4 sm:px-0">
            <slot name="aside" />
        </div>
    </div>
</template>
