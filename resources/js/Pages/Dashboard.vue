<template>
    <div
        class="grid gap-2 h-full"
        :style="{ gridTemplateColumns: '1fr auto 1fr auto 1fr', gridTemplateRows: `auto repeat(${hunks.length}, 1fr) auto` }"
    >
        <!-- Headers -->
        <div class="border p-2 flex items-center font-bold">Original</div>
        <div></div> <!-- Empty for buttons between Original & Merged -->
        <div class="border p-2 flex items-center justify-between font-bold">
            Merged
            <Button severity="success" icon="pi pi-copy" label="Copy Merged" @click="copyMerged" />
        </div>
        <div></div> <!-- Empty for buttons between Merged & New -->
        <div class="border p-2 flex items-center font-bold">New</div>

        <!-- Rows for each hunk -->
        <template v-for="(hunk, idx) in hunks" :key="idx">
            <!-- Left column (Original) -->
            <div class="border p-2 overflow-auto">
                <pre class="whitespace-pre-wrap">{{ hunk.local.join('\n') }}</pre>
            </div>

            <!-- Buttons between Original & Merged -->
            <div class="flex flex-col justify-center gap-1 px-1">
                <Button
                    severity="info"
                    icon="pi pi-angle-double-right"
                    v-if="!applied[idx].local"
                    @click="applyHunk(idx, 'local')"
                    size="small"
                />
                <Button
                    severity="danger"
                    icon="pi pi-times"
                    v-if="applied[idx].local"
                    @click="removeHunkPart(idx, 'local')"
                    size="small"
                />
            </div>

            <!-- Center column (Merged) -->
            <div class="border p-2 flex flex-col">
        <textarea
            v-model="mergedText[idx]"
            class="w-full border rounded p-1 flex-grow resize-none"
            rows="3"
        ></textarea>
            </div>

            <!-- Buttons between Merged & New -->
            <div class="flex flex-col justify-center gap-1 px-1">
                <Button
                    severity="info"
                    icon="pi pi-angle-double-left"
                    v-if="!applied[idx].remote"
                    @click="applyHunk(idx, 'remote')"
                    size="small"
                />
                <Button
                    severity="danger"
                    icon="pi pi-times"
                    v-if="applied[idx].remote"
                    @click="removeHunkPart(idx, 'remote')"
                    size="small"
                />
            </div>

            <!-- Right column (New) -->
            <div class="border p-2 overflow-auto">
                <pre class="whitespace-pre-wrap">{{ hunk.remote.join('\n') }}</pre>
            </div>
        </template>

        <!-- Combined Merged Output below, spanning all columns -->
        <textarea
            ref="mergedOutput"
            :value="combinedMerged"
            class="w-full border rounded p-2 mt-2 col-span-5"
            rows="5"
            readonly
        ></textarea>
    </div>
</template>

<style scoped>
/* Each pre wraps text */
pre {
    margin: 0;
    white-space: pre-wrap;
    word-break: break-word;
    height: 100%;
}

/* Make grid cells fill and align */
.grid > div {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Ensure textareas stretch fully */
textarea {
    resize: none;
    flex-grow: 1;
    min-height: 0; /* Important for flex-grow in Firefox */
}

/* Scroll overflow in hunk cells */
.grid > div.overflow-auto {
    overflow: auto;
}
</style>

<script setup>
import { reactive, watch, computed, ref } from "vue";

// Example hunks
const hunks = reactive([
    { type: "conflict", local: ["line 1 fs /\nfdsfs"], remote: ["line 1"] },
    { type: "conflict", local: [""], remote: ["re"]},
    {
        type: "conflict",
        local: ["line 3 local"],
        remote: ["line 3 remote"],
    },
    { type: "common", local: ["line 4"], remote: ["line 4"] },
]);

const applied = reactive(
    hunks.map((hunk) => ({
        local: hunk.type === "common",
        remote: false,
    }))
);

const merged = reactive(
    hunks.map((hunk) => {
        if (hunk.type === "common") {
            return [...hunk.local];
        } else {
            return [];
        }
    })
);

const mergedText = reactive(merged.map((lines) => lines.join("\n")));

const mergedOutput = ref(null);

const combinedMerged = computed(() => {
    return merged.map((lines) => lines.join("\n")).join("\n");
});

function applyHunk(idx, side) {
    merged[idx] = [...hunks[idx][side]];
    mergedText[idx] = merged[idx].join("\n");
    applied[idx].local = side === "local";
    applied[idx].remote = side === "remote";
}

function removeHunkPart(idx, side) {
    if (applied[idx][side]) {
        merged[idx] = [];
        mergedText[idx] = "";
        applied[idx].local = false;
        applied[idx].remote = false;
    }
}

function getHunkClass(hunk, side) {
    if (hunk.type === "conflict") {
        return side === "local" ? "bg-blue-50" : "bg-green-50";
    }
    return "bg-gray-50";
}

function copyMerged() {
    if (mergedOutput.value) {
        mergedOutput.value.select();
        document.execCommand("copy");
    }
}

// Keep merged array in sync when mergedText changes
watch(
    mergedText,
    (newVals) => {
        newVals.forEach((val, idx) => {
            merged[idx] = val.split("\n");
        });
    },
    { deep: true }
);
</script>
