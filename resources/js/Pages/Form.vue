<template>
    <div class="bg-surface-50 dark:bg-surface-950 px-8 md:px-20 py-12 md:py-20 lg:px-[20rem]">
        <div class="flex flex-col gap-7">
            <div class="flex flex-col gap-2">
                <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">Contact</div>
                <p class="m-0 p-0 text-surface-600 dark:text-surface-200 leading-normal">
                    This information shapes your public identity—choose wisely to reflect your best self while
                    maintaining privacy.
                </p>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">Experience</div>
                <p class="m-0 p-0 text-surface-600 dark:text-surface-200 leading-normal">
                    This information shapes your public identity—choose wisely to reflect your best self while
                    maintaining privacy.
                </p>
            </div>
            <Experience
                v-for="(experience, index) in experiences"
                :key="index"
                :experience="experience"
                @remove="removeExperience"
            />
            <div class="flex justify-end">
                <Button icon="pi pi-plus" label="Add Experience" @click="addExperience"/>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">Projects</div>
                <p class="m-0 p-0 text-surface-600 dark:text-surface-200 leading-normal">
                    This information shapes your public identity—choose wisely to reflect your best self while
                    maintaining privacy.
                </p>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">Education</div>
                <p class="m-0 p-0 text-surface-600 dark:text-surface-200 leading-normal">
                    This information shapes your public identity—choose wisely to reflect your best self while
                    maintaining privacy.
                </p>
            </div>
            <div class="flex flex-col gap-2">
                <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">Beyond Work</div>
                <p class="m-0 p-0 text-surface-600 dark:text-surface-200 leading-normal">
                    This information shapes your public identity—choose wisely to reflect your best self while
                    maintaining privacy.
                </p>
            </div>
            <div class="relative bg-surface-0 dark:bg-surface-900 p-7 shadow rounded-2xl">
                <div class="flex flex-col gap-10">
                    <div class="flex flex-col gap-6">
                        <div class="grid grid-cols-12 gap-7">
                            <div class="col-span-12 flex flex-col gap-2">
                                <label class="font-medium text-surface-900 dark:text-surface-0">Beyond Work</label>
                                <Textarea :auto-resize="true" :rows="5" class="w-full"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue';
import Button from 'primevue/button';
import Experience from "@/Components/Form/Experience.vue";
import Textarea from "primevue/textarea";

const experiences = ref([
    {
        jobTitle: '',
        company: '',
        startDate: null,
        endDate: null,
        skills: [],
        description: '',
    }
]);

const addExperience = () => {
    experiences.value.push({
        jobTitle: '',
        company: '',
        startDate: null,
        endDate: null,
        skills: [],
        description: '',
    });
};

const removeExperience = (index) => {
    experiences.value.splice(index, 1);
};
</script>
