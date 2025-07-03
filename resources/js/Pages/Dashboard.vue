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
            <!-- New buttons for setting merged values -->
            <Button
                severity="info"
                icon="pi pi-refresh"
                label="Set to Original"
                @click="setToOriginal"
                size="small"
            />
            <Button
                severity="info"
                icon="pi pi-refresh"
                label="Set to New"
                @click="setToNew"
                size="small"
            />
        </div>
        <div></div> <!-- Empty for buttons between Merged & New -->
        <div class="border p-2 flex items-center font-bold">New</div>

        <!-- Rows for each hunk -->
        <template v-for="(hunk, idx) in hunks" :key="idx">
            <!-- Left column (Original) -->
            <div class="border p-2 overflow-auto">
                <pre :class="getHunkClass(hunk, 'local')">
                    {{ hunk.local.join('\n') }}
                </pre>
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
                <div
                    contenteditable="true"
                    class="w-full border rounded p-1 flex-grow resize-none editable-div"
                    :class="getMergedClass(idx)"
                    @input="onInput($event, idx)"
                    :style="{ whiteSpace: 'pre-wrap', overflowWrap: 'break-word' }"
                    v-html="mergedText[idx]"
                ></div>

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
                <pre :class="getHunkClass(hunk, 'remote')">
                    {{ hunk.remote.join('\n') }}
                </pre>
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

<script setup>
import { reactive, watch, computed, ref, onMounted } from "vue";

// Example hunks
const hunks = reactive([
    // Personal Profile: add marketing focus & leadership emphasis
    {
        type: "conflict",
        local: [
            "I am a lively and friendly individual with excellent customer services skills and a positive disposition."
        ],
        remote: [
            "I am a proactive and highly organized individual with strong communication skills, experienced in teamwork and leadership, with a keen interest in marketing and brand development."
        ],
    },

    // Key Skills and Achievements: emphasize marketing, digital & team leadership
    {
        type: "conflict",
        local: [
            "• Excellent communication and interpersonal skills: can communicate effectively on all levels",
        ],
        remote: [
            "",
        ],
    },
    {
        type: "conflict",
        local: ["• IT literate: able to use Microsoft packages"],
        remote: [
            "• Proficient in Microsoft Office and digital marketing tools including Google Ads and social media platforms",
        ],
    },
    {
        type: "conflict",
        local: ["• Highly Organised: able to prioritise tasks and work to deadlines"],
        remote: [
            "• Highly organized with the ability to prioritize tasks, manage budgets, and meet tight deadlines",
        ],
    },
    {
        type: "conflict",
        local: ["• Flexible: able to adaptable to suit the needs of the business"],
        remote: [
            "• Flexible and adaptable to changing business needs and market conditions",
        ],
    },

    // Employment Experience: Tesco checkout operator - streamline to focus on transferable skills
    {
        type: "conflict",
        local: [
            "Working on a part time basis my duties include serving and assisting customers, operating a computerised till, processing all payments and any other duties as required."
        ],
        remote: [
            "Worked part-time serving and assisting customers, demonstrating strong interpersonal skills and accuracy under pressure."
        ],
    },

    // Employment Experience: Add any relevant marketing or admin transferable skills for Acacia Training
    {
        type: "conflict",
        local: [
            "Working as an administration assistant in this work experience placement I was responsible for assisting with telephone and reception duties, post duties, filing and other general office duties."
        ],
        remote: [
            "Supported office administration, coordinated communications, and assisted with data management, gaining experience in organizational and team collaboration."
        ],
    },

    // Education: Add marketing relevance to diploma if possible (small addition)
    {
        type: "conflict",
        local: ["Level 3 Diploma in Business"],
        remote: ["Level 3 Diploma in Business, with focus on marketing principles and business strategy"],
    },

    // Remove Hobbies & Interests - Less relevant for marketing job, reduces noise
    {
        type: "conflict",
        local: [
            "I enjoy all kinds of music, films and books and I particularly enjoy going to live music events. I also play for my local football team, attending regular training and matches."
        ],
        remote: [""],
    },

    // Optional: Remove references line or leave as is - typically standard
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

// Method to apply color classes to the hunks
function getHunkClass(hunk, side) {
    if (hunk.type === "conflict") {
        if (side === "local") {
            return hunk.local[0] === ''
                ? "bg-red-100 text-red-700" // Local part removed
                : "bg-blue-100 text-blue-700"; // Local conflict
        } else if (side === "remote") {
            return hunk.remote[0] === ''
                ? "bg-red-100 text-red-700" // Remote part removed
                : "bg-green-100 text-green-700"; // Remote conflict
        }
    }
    return "bg-gray-100 text-gray-700"; // Common, unchanged content
}

function getMergedClass(idx) {
    const mergedStr = merged[idx].join('\n');
    const localStr = hunks[idx].local.join('\n');
    const remoteStr = hunks[idx].remote.join('\n');

if (hunks[idx].type === 'common') {
    return 'bg-gray-100 text-gray-700';
} else if (mergedStr === '' || merged[idx].every(line => line.trim() === '')) {
    return 'bg-red-100 text-red-700';
} else if (mergedStr === localStr) {
        return 'bg-blue-100 text-blue-700';
    } else if (mergedStr === remoteStr) {
        return 'bg-green-100 text-green-700';
    } else {
        return 'bg-gray-100 text-gray-700';
    }
}


function copyMerged() {
    if (mergedOutput.value) {
        mergedOutput.value.select();
        document.execCommand("copy");
    }
}

// Set all merged values to original
function setToOriginal() {
    hunks.forEach((hunk, idx) => {
        merged[idx] = [...hunk.local];
        mergedText[idx] = merged[idx].join("\n");
        applied[idx].local = true;
        applied[idx].remote = false;
    });
}

// Set all merged values to new
function setToNew() {
    hunks.forEach((hunk, idx) => {
        merged[idx] = [...hunk.remote];
        mergedText[idx] = merged[idx].join("\n");
        applied[idx].local = false;
        applied[idx].remote = true;
    });
}

// Automatically set to "New" when component mounts
onMounted(() => {
    setToNew();  // This will set all merged values to "New" on component load
});

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

/* Custom background colors for merge conflict states */
.bg-blue-100 { background-color: #ebf8ff; }
.text-blue-700 { color: #2b6cb0; }
.bg-green-100 { background-color: #f0fff4; }
.text-green-700 { color: #2f855a; }
.bg-gray-100 { background-color: #f7fafc; }
.text-gray-700 { color: #4a5568; }
</style>
