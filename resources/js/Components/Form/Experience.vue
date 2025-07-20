<template>
    <div class="relative bg-surface-0 dark:bg-surface-900 p-7 shadow rounded-2xl">
        <div class="absolute -top-5 -right-5">
        <Button
            @click="emit('remove', index)"
            class="w-6 h-6 flex items-center justify-center text-white text-sm"
            severity="danger"
            aria-label="Remove experience"
            rounded
            icon="pi pi-times"
       />
        </div>

        <div class="flex flex-col gap-10">
            <div class="flex flex-col gap-6">
                <div class="grid grid-cols-12 gap-7">
                    <div class="col-span-12 flex flex-col gap-2">
                        <label class="font-medium text-surface-900 dark:text-surface-0">Job Title</label>
                        <InputText v-model="experience.jobTitle" type="text" class="w-full" />
                    </div>
                    <div class="col-span-12 md:col-span-6 flex flex-col gap-2">
                        <label class="font-medium text-surface-900 dark:text-surface-0">Company</label>
                        <InputText v-model="experience.company" type="text" class="w-full" />
                    </div>
                    <div class="col-span-12 md:col-span-3 flex flex-col gap-2">
                        <label class="font-medium text-surface-900 dark:text-surface-0">Date Start</label>
                        <DatePicker v-model="experience.startDate" showIcon fluid :showOnFocus="false" />
                    </div>
                    <div class="col-span-12 md:col-span-3 flex flex-col gap-2">
                        <label class="font-medium text-surface-900 dark:text-surface-0">Date End</label>
                        <DatePicker v-model="experience.endDate" showIcon fluid :showOnFocus="false" />
                    </div>
                    <div class="col-span-12 flex flex-col gap-2">
                        <label class="font-medium text-surface-900 dark:text-surface-0">Skills</label>
                        <MultiSelect
                            v-model="experience.skills"
                            display="chip"
                            optionLabel="name"
                            filter
                            placeholder="Select Skills"
                            :maxSelectedLabels="3"
                            class="w-full"
                        />
                    </div>
                    <div class="col-span-12 flex flex-col gap-2">
                        <label class="font-medium text-surface-900 dark:text-surface-0">Job Description</label>
                        <Textarea v-model="experience.description" :rows="5" :auto-resize="true" class="w-full" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';
import InputText from 'primevue/inputtext';
import DatePicker from 'primevue/datepicker';
import MultiSelect from 'primevue/multiselect';
import Textarea from 'primevue/textarea';

defineProps({
    experience: Object,
    index: Number
});

const emit = defineEmits(['remove']);
</script>
