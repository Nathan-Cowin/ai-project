<template>
    <div class="grid grid-cols-3">
        <div
            class="grid h-full col-span-2"
            :style="{ gridTemplateColumns: '1fr 0.1fr 1fr', gridTemplateRows: 'auto' }"
        >
            <!-- Headers -->
            <div class="p-2 flex items-center font-bold">Original</div>
            <div></div>
            <div class="p-2 flex items-center font-bold">AI Generated</div>

            <template v-for="(part, index) in diffParts" :key="index">
                <template v-if="part.removed || part.added">
                    <!-- Original -->
                    <div class="flex flex-col">
            <span
                v-if="part.removed && part.active"
                :class="[
                'p-1 rounded',
                hoveredIndex === index ? 'bg-red-200' : 'bg-red-50'
              ]"
            >
              {{ part.removed.value }}
            </span>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col">
                        <div
                            v-if="part.removed && part.active"
                            :class="[
                'flex justify-between w-full',
                hoveredIndex === index ? 'bg-red-200' : 'bg-red-50'
              ]"
                        >
                            <Button variant="text" size="small" @click="rejectChange(index)">x</Button>
                            <Button variant="text" size="small" @click="acceptChange(index)">>></Button>
                        </div>
                    </div>

                    <!-- AI Generated -->
                    <div class="flex flex-col">
            <span
                v-if="part.added"
                :class="[
                'p-1 rounded',
                hoveredIndex === index && part.accepted
                  ? 'bg-red-200'
                  : hoveredIndex === index ? 'bg-blue-200' :
                  part.accepted
                  ? 'bg-red-50'
                  : 'bg-blue-50'
              ]"
            >
              {{ part.added.value }}
            </span>
                    </div>
                </template>

                <template v-else>
                    <div></div>
                    <div></div>
                    <div class="flex flex-col bg-gray-50">
                        {{ part.unchanged.value }}
                    </div>
                </template>
            </template>
        </div>

        <div>
            <div class="p-2 flex items-center font-bold">Changes</div>
            <template v-for="(part, index) in diffParts" :key="index">
                <div
                    v-if="part.removed || part.added"
                    class="flex flex-col gap-1 p-2"
                    @mouseenter="hoveredIndex = index"
                    @mouseleave="hoveredIndex = null"

                >
                    <!-- Replaced -->
                    <div :class="hoveredIndex === index ? 'border-yellow-500' : ''" class="border-2 rounded-xl p-2" v-if="part.changeType === 'Replaced'">
                        <div class="font-bold mb-1">Replaced</div>
                        <div class="text-sm whitespace-pre-line mb-1 bg-red-50 rounded">
                            <template v-if="!expandedParts[index]">
                                {{ truncateText(part.originalRemoved.trim()).visible }}...
                                <span
                                    v-if="truncateText(part.originalRemoved.trim()).extraWords > 0"
                                    @click="toggleExpand(index)"
                                    class="text-blue-500 cursor-pointer"
                                >
                  +{{ truncateText(part.originalRemoved.trim()).extraWords }} words
                </span>
                            </template>
                            <template v-else>
                                {{ part.originalRemoved.trim() }}
                            </template>
                        </div>
                        <div class="text-sm whitespace-pre-line bg-blue-50">
                            <template v-if="!expandedParts[index]">
                                {{ truncateText(part.originalAdded.trim()).visible }}...
                                <span
                                    v-if="truncateText(part.originalAdded.trim()).extraWords > 0"
                                    @click="toggleExpand(index)"
                                    class="text-blue-500 cursor-pointer"
                                >
                  +{{ truncateText(part.originalAdded.trim()).extraWords }} words
                </span>
                            </template>
                            <template v-else>
                                {{ part.originalAdded.trim() }}
                            </template>
                        </div>
                    </div>

                    <!-- Inserted -->
                    <div :class="hoveredIndex === index ? 'border-yellow-500' : ''" class="border-2 rounded-xl p-2 shadow" v-else-if="part.changeType === 'Inserted'">
                        <div class="font-bold mb-1">Inserted</div>
                        <div class="text-sm whitespace-pre-line bg-blue-50 rounded">
                            <template v-if="!expandedParts[index]">
                                {{ truncateText(part.originalAdded.trim()).visible }}...
                                <span
                                    v-if="truncateText(part.originalAdded.trim()).extraWords > 0"
                                    @click="toggleExpand(index)"
                                    class="text-blue-500 cursor-pointer"
                                >
                  +{{ truncateText(part.originalAdded.trim()).extraWords }} words
                </span>
                            </template>
                            <template v-else>
                                {{ part.originalAdded.trim() }}
                            </template>
                        </div>
                    </div>

                    <!-- Removed -->
                    <div :class="hoveredIndex === index ? 'border-yellow-500' : ''" class="border-2 rounded-xl p-2 shadow" v-else-if="part.changeType === 'Removed'">
                        <div class="font-bold mb-1">Removed</div>
                        <div class="text-sm whitespace-pre-line bg-red-50 rounded">
                            <template v-if="!expandedParts[index]">
                                {{ truncateText(part.originalRemoved.trim()).visible }}...
                                <span
                                    v-if="truncateText(part.originalRemoved.trim()).extraWords > 0"
                                    @click="toggleExpand(index)"
                                    class="text-blue-500 cursor-pointer"
                                >
                  +{{ truncateText(part.originalRemoved.trim()).extraWords }} words
                </span>
                            </template>
                            <template v-else>
                                {{ part.originalRemoved.trim() }}
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import { diffLines } from 'diff';

export default {
    data() {
        return {
            originalText: `Personal Profile
            another title
I am a lively and friendly individual with excellent customer services skills and a positive disposition. I am highly
organised with the ability to remain professional and calm in all situations and rise to a challenge. Self-motivated,
I possess excellent time management skills and can work well in a team environment.
Key Skills and Achievements
• Excellent communication and interpersonal skills: can communicate effectively on all levels
• IT literate: able to use Microsoft packages
• Highly Organised: able to prioritise tasks and work to deadlines
• Flexible: able to adaptable to suit the needs of the business
Employment Experience
Tesco, Taunton Checkout Operator
September 2015 – Present
Working on a part time basis my duties include serving and assisting customers, operating a computerised till,
processing all payments and any other duties as required.
Acacia Training & Development, Taunton Work Experience
May 2015
Working as an administration assistant in this work experience placement I was responsible for assisting with
telephone and reception duties, post duties, filing and other general office duties.`,
            aiText: `Personal Profile
            to do: make it so that you can remove this..
            another title
A friendly, enthusiastic individual with a strong background in customer service and administrative support. Highly organised and self-motivated with a positive attitude, I excel under pressure and enjoy contributing to a team environment. Professional in all situations, I possess excellent time management skills and a proactive approach to problem-solving.

Key Skills & Achievements
Strong Communication: Effective communicator with excellent interpersonal skills across all levels.

Customer Service: Proven ability to provide outstanding service in busy retail environments.

IT Proficiency: Competent in Microsoft Office applications (Word, Excel, Outlook, etc.).

Time Management: Efficient in prioritising tasks and meeting tight deadlines.

Adaptability: Flexible and quick to adjust to changing business needs.

Teamwork: Collaborative and supportive team player.

Employment Experience
Tesco, Taunton – Checkout Operator
September 2015 – Present
May 2015

Handled telephone and reception duties, offering professional first-line contact.

Assisted with general office administration including filing, post sorting, and data entry.

Supported staff with ad-hoc clerical tasks, developing an understanding of office procedures.

Education`,
            diffParts: [],
            expandedParts: {},
            hoveredIndex: null
        };
    },
    mounted() {
        this.runDiff();
    },
    methods: {
        runDiff() {
            const rawParts = diffLines(this.originalText, this.aiText);
            const pairedParts = [];

            for (let i = 0; i < rawParts.length; i++) {
                const part = rawParts[i];

                if (part.removed) {
                    const next = rawParts[i + 1];
                    if (next && next.added) {
                        pairedParts.push({
                            removed: part,
                            added: next,
                            changeType: 'Replaced',
                            originalRemoved: part.value,
                            originalAdded: next.value,
                            active: true,
                            accepted: false
                        });
                        i++;
                    } else {
                        pairedParts.push({
                            removed: part,
                            added: null,
                            changeType: 'Removed',
                            originalRemoved: part.value,
                            originalAdded: null,
                            active: true,
                            accepted: false
                        });
                    }
                } else if (part.added) {
                    pairedParts.push({
                        removed: null,
                        added: part,
                        changeType: 'Inserted',
                        originalRemoved: null,
                        originalAdded: part.value,
                        active: true,
                        accepted: false
                    });
                } else {
                    pairedParts.push({
                        unchanged: part,
                        active: true
                    });
                }
            }

            this.diffParts = pairedParts;
        },

        rejectChange(index) {
            const part = this.diffParts[index];
            if (part) {
                part.removed = null;
                part.active = false;
            }
        },

        acceptChange(index) {
            const part = this.diffParts[index];
            if (part) {
                part.added = { value: part.removed.value };
                part.removed = null;
                part.active = false;
                part.accepted = true;
            }
        },

        toggleExpand(index) {
            this.expandedParts[index] = !this.expandedParts[index];
        },

        truncateText(text, lines = 3) {
            const words = text.split(/\s+/);
            const limitedWords = words.slice(0, 50); // rough approximation
            return {
                visible: limitedWords.join(" "),
                remaining: words.slice(50).join(" "),
                extraWords: words.length - 50
            };
        }
    }
};
</script>

<style scoped>
.line-through {
    text-decoration: line-through;
}
</style>
