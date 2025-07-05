<template>
    <div
        class="grid h-full"
        :style="{ gridTemplateColumns: '1fr 0.1fr 1fr 1fr', gridTemplateRows: 'auto' }"
    >
        <!-- Headers -->
        <div class="p-2 flex items-center font-bold">Original</div>
        <div></div>
        <div class="p-2 flex items-center justify-between font-bold">AI Generated</div>
        <div class="p-2 flex items-center font-bold">Changes</div>

        <template v-for="(part, index) in diffParts" :key="index">
            <!-- Left (Original) -->
            <div v-if="part.removed || part.added" class="flex flex-col justify-center">
        <span
            v-if="part.removed && part.active"
            class="bg-red-100 p-1 rounded"
        >
          {{ part.removed.value }}
        </span>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col justify-center">
                <div
                    v-if="part.removed && part.active"
                    class="justify-between flex bg-red-100 w-full"
                >
                    <Button variant="text" size="small" @click="rejectChange(index)">
                        x
                    </Button>
                    <Button variant="text" size="small" @click="acceptChange(index)">
                        >>
                    </Button>
                </div>
            </div>

            <!-- Right (AI generated / merged) -->
            <div v-if="part.removed || part.added" class="flex flex-col">
        <span
            v-if="part.added"
            :class="['p-1 rounded', part.accepted ? 'bg-red-100' : 'bg-green-100']"
        >
          {{ part.added.value }}
        </span>
            </div>

            <div v-if="part.removed || part.added" class="flex flex-col justify-center gap-1 px-1"></div>

            <!-- Unchanged -->
            <template v-else>
                <div></div>
                <div class="flex flex-col">
                    {{ part.unchanged.value }}
                </div>
                <div></div>
            </template>
        </template>
    </div>
</template>

<script>
import { diffLines } from 'diff';

export default {
    data() {
        return {
            originalText: `Personal Profile
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
telephone and reception duties, post duties, filing and other general office duties. `,
            aiText: `Personal Profile
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
            diffParts: []
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
                            active: true,
                            accepted: false
                        });
                        i++;
                    } else {
                        pairedParts.push({
                            removed: part,
                            added: null,
                            active: true,
                            accepted: false
                        });
                    }
                } else if (part.added) {
                    pairedParts.push({
                        removed: null,
                        added: part,
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
                part.accepted = true; // triggers red background on right
            }
        }
    }
};
</script>

<style scoped>
.line-through {
    text-decoration: line-through;
}
</style>
