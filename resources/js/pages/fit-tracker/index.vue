<template>
    <div class="p-4 pb-24">
        <div class="mb-6 flex items-center justify-between">
            <Button
                icon="pi pi-arrow-left"
                text
                rounded
                severity="success"
                @click="router.visit('/dashboard')"
            />
            <h1 class="text-3xl font-bold">Fit Tracker</h1>
            <Button
                icon="pi pi-plus"
                text
                rounded
                severity="success"
                @click="goToCreateCheckInPage"
            />
        </div>

        <!-- Calendar View -->
        <div v-if="groupedCheckIns.length > 0" class="space-y-8">
            <!-- Year Groups -->
            <div
                v-for="yearGroup in groupedCheckIns"
                :key="yearGroup.year"
                class="space-y-6"
            >
                <!-- Month Groups -->
                <div
                    v-for="monthGroup in yearGroup.months"
                    :key="monthGroup.month"
                    class="space-y-4"
                >
                    <h2 class="text-center text-2xl font-bold">
                        {{ monthGroup.monthName }} {{ yearGroup.year }}
                    </h2>

                    <!-- Days of week header -->
                    <div
                        class="grid grid-cols-7 gap-2 text-center text-sm text-gray-500"
                    >
                        <div>dom.</div>
                        <div>seg.</div>
                        <div>ter.</div>
                        <div>qua.</div>
                        <div>qui.</div>
                        <div>sex.</div>
                        <div>sáb.</div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="rounded-lg bg-gray-800 p-4">
                        <div class="grid grid-cols-7 gap-2">
                            <!-- Empty cells for offset -->
                            <div
                                v-for="i in monthGroup.startDayOffset"
                                :key="`empty-${i}`"
                            ></div>

                            <!-- Day cells -->
                            <div
                                v-for="day in monthGroup.days"
                                :key="day.day"
                                class="relative aspect-square"
                                :class="{
                                    'cursor-pointer transition-opacity hover:opacity-80':
                                        day.checkIns.length > 0,
                                }"
                                @click="
                                    day.checkIns.length > 0 &&
                                    openDayDrawer(
                                        day.day,
                                        monthGroup.monthName,
                                        yearGroup.year,
                                        day.checkIns,
                                    )
                                "
                            >
                                <!-- Day number -->
                                <div
                                    class="absolute top-1 right-1 text-sm font-medium text-gray-400"
                                >
                                    {{ day.day }}
                                </div>

                                <!-- Check-in thumbnails -->
                                <div
                                    v-if="day.checkIns.length > 0"
                                    class="flex h-full items-center justify-center p-1"
                                >
                                    <div
                                        v-if="day.checkIns.length === 1"
                                        class="flex h-full w-full items-center justify-center overflow-hidden rounded-full bg-gray-600 text-xs"
                                    >
                                        <span>{{
                                            getCheckInPreview(day.checkIns[0])
                                        }}</span>
                                    </div>
                                    <div
                                        v-else
                                        class="grid gap-0.5"
                                        :class="
                                            getGridClass(day.checkIns.length)
                                        "
                                    >
                                        <div
                                            v-for="(
                                                checkIn, idx
                                            ) in day.checkIns.slice(0, 4)"
                                            :key="idx"
                                            class="flex aspect-square items-center justify-center overflow-hidden rounded bg-gray-600 text-xs"
                                        >
                                            <span>{{
                                                getCheckInPreview(checkIn)
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-12 text-center text-gray-500">
            <p class="text-lg">Nenhum check-in registrado ainda</p>
            <p class="mt-2 text-sm">
                Clique no botão + para criar seu primeiro check-in
            </p>
        </div>

        <!-- Drawer for Day Check-ins -->
        <Drawer
            v-model:visible="drawerVisible"
            position="bottom"
            class="h-[70vh]"
        >
            <template #header>
                <h3 class="text-xl font-bold">{{ selectedDayLabel }}</h3>
            </template>

            <div class="space-y-4">
                <Card
                    v-for="(checkIn, idx) in selectedDayCheckIns"
                    :key="idx"
                    class="bg-gray-800"
                >
                    <template #content>
                        <div class="space-y-3">
                            <div>
                                <h4 class="text-lg font-semibold">
                                    {{ checkIn.title || 'Check-in sem título' }}
                                </h4>
                                <p
                                    v-if="checkIn.description"
                                    class="mt-1 text-sm text-gray-400"
                                >
                                    {{ checkIn.description }}
                                </p>
                            </div>

                            <!-- Photos Gallery -->
<div
                                v-if="checkIn.media.length > 0"
                                class="grid grid-cols-2 gap-2"
                            >
                                <img
                                    v-for="photo in checkIn.media"
                                    :key="photo.id"
                                    :src="showPhoto.url({ checkIn: checkIn.id, mediaId: photo.id })"
                                    :alt="`Check-in photo ${photo.name}`"
                                    class="h-48 w-full cursor-pointer rounded-lg object-cover transition-opacity hover:opacity-80"
                                />
                            </div>

                            <div
                                v-if="checkIn.activities.length > 0"
                                class="space-y-2"
                            >
                                <div
                                    v-for="(
                                        activity, actIdx
                                    ) in checkIn.activities"
                                    :key="actIdx"
                                    class="flex items-center gap-3 rounded-lg bg-gray-700 p-3"
                                >
                                    <span class="text-2xl">{{
                                        ActivityTypeEmoji[activity.type]
                                    }}</span>
                                    <div class="flex-1">
                                        <p class="font-medium">
                                            {{
                                                ActivityTypeLabel[activity.type]
                                            }}
                                        </p>
                                        <div
                                            class="space-y-1 text-sm text-gray-400"
                                        >
                                            <p v-if="activity.distance">
                                                📏 {{ activity.distance }} km
                                            </p>
                                            <p v-if="activity.calories_burned">
                                                🔥
                                                {{ activity.calories_burned }}
                                                cal
                                            </p>
                                            <p v-if="activity.steps">
                                                👣 {{ activity.steps }} passos
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </Drawer>
    </div>
</template>

<script setup lang="ts">
import { ActivityTypeEmoji, ActivityTypeLabel } from '@/lib/fit-tracker';
import { showPhoto } from '@/routes/check-ins';
import type { CheckIn } from '@/types';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Drawer from 'primevue/drawer';
import { computed, ref } from 'vue';

interface Props {
    checkIns: CheckIn[];
}

const props = defineProps<Props>();

const drawerVisible = ref(false);
const selectedDayLabel = ref('');
const selectedDayCheckIns = ref<CheckIn[]>([]);

function goToCreateCheckInPage() {
    router.visit('/fit-tracker/check-in');
}

function openDayDrawer(
    day: number,
    monthName: string,
    year: number,
    checkIns: CheckIn[],
) {
    selectedDayLabel.value = `${day} de ${monthName} de ${year}`;
    selectedDayCheckIns.value = checkIns;
    drawerVisible.value = true;
}

function getCheckInPreview(checkIn: CheckIn): string {
    if (checkIn.activities.length > 0) {
        return ActivityTypeEmoji[checkIn.activities[0].type];
    }
    return '📋';
}

function getGridClass(count: number): string {
    if (count === 2) return 'grid-cols-2';
    if (count === 3) return 'grid-cols-2 grid-rows-2';
    return 'grid-cols-2 grid-rows-2';
}

const monthNames = [
    'janeiro',
    'fevereiro',
    'março',
    'abril',
    'maio',
    'junho',
    'julho',
    'agosto',
    'setembro',
    'outubro',
    'novembro',
    'dezembro',
];

interface DayGroup {
    day: number;
    checkIns: CheckIn[];
}

interface MonthGroup {
    month: number;
    monthName: string;
    days: DayGroup[];
    startDayOffset: number;
}

interface YearGroup {
    year: number;
    months: MonthGroup[];
}

const groupedCheckIns = computed<YearGroup[]>(() => {
    const grouped = new Map<number, Map<number, Map<number, CheckIn[]>>>();

    // Group check-ins by year, month, and day
    props.checkIns.forEach((checkIn) => {
        const date = new Date(checkIn.checked_in_at);
        const year = date.getFullYear();
        const month = date.getMonth();
        const day = date.getDate();

        if (!grouped.has(year)) {
            grouped.set(year, new Map());
        }
        if (!grouped.get(year)!.has(month)) {
            grouped.get(year)!.set(month, new Map());
        }
        if (!grouped.get(year)!.get(month)!.has(day)) {
            grouped.get(year)!.get(month)!.set(day, []);
        }

        grouped.get(year)!.get(month)!.get(day)!.push(checkIn);
    });

    // Convert to array structure
    const result: YearGroup[] = [];

    Array.from(grouped.entries())
        .sort((a, b) => b[0] - a[0]) // Sort years descending
        .forEach(([year, monthsMap]) => {
            const months: MonthGroup[] = [];

            Array.from(monthsMap.entries())
                .sort((a, b) => b[0] - a[0]) // Sort months descending
                .forEach(([month, daysMap]) => {
                    // Calculate number of days in month
                    const daysInMonth = new Date(year, month + 1, 0).getDate();

                    // Get the day of week for the first day of the month (0 = Sunday, 6 = Saturday)
                    const firstDayOfMonth = new Date(year, month, 1).getDay();

                    const days: DayGroup[] = [];
                    for (let day = 1; day <= daysInMonth; day++) {
                        days.push({
                            day,
                            checkIns: daysMap.get(day) || [],
                        });
                    }

                    months.push({
                        month,
                        monthName: monthNames[month],
                        days,
                        startDayOffset: firstDayOfMonth,
                    });
                });

            result.push({ year, months });
        });

    return result;
});
</script>
