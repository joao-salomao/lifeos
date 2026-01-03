<template>
    <div class="p-4">
        <div class="mb-6 flex items-center justify-between">
            <Button icon="pi pi-arrow-left" text @click="goBack" />
            <span class="text-xl">Novo check-in</span>
            <Button
                label="Publicar"
                text
                severity="success"
                @click="publishCheckIn"
                :disabled="form.processing"
            />
        </div>

        <Card class="mb-4">
            <template #title>Fotos</template>
            <template #content>
                <div v-if="photos.length > 0" class="mb-4 grid grid-cols-2 gap-2">
                    <div
                        v-for="(photo, index) in photos"
                        :key="photo.filepath"
                        class="relative aspect-square"
                    >
                        <img
                            :src="photo.webviewPath"
                            :alt="`Foto ${index + 1}`"
                            class="h-full w-full rounded-lg object-cover"
                        />
                        <Button
                            icon="pi pi-times"
                            rounded
                            text
                            severity="danger"
                            class="absolute right-1 top-1 bg-white/80"
                            @click="removePhoto(photo)"
                        />
                    </div>
                </div>
                <Button
                    label="Adicionar Foto"
                    icon="pi pi-camera"
                    outlined
                    @click="addPhoto"
                    class="w-full"
                    :disabled="photos.length >= 5"
                />
                <small v-if="photos.length >= 5" class="mt-2 block text-center text-gray-500">
                    Máximo de 5 fotos atingido
                </small>
                <small v-if="form.errors.photos" class="mt-2 block p-error">
                    {{ form.errors.photos }}
                </small>
            </template>
        </Card>

        <Card class="mb-4">
            <template #content>
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium"
                            >Data do check-in</label
                        >
                        <DatePicker
                            v-model="form.checked_in_at"
                            showTime
                            showIcon
                            iconDisplay="input"
                            dateFormat="dd/mm/yy"
                            hourFormat="24"
                            class="w-full"
                            inputClass="w-full"
                            :class="{ 'p-invalid': form.errors.checked_in_at }"
                        />
                        <small
                            v-if="form.errors.checked_in_at"
                            class="p-error"
                            >{{ form.errors.checked_in_at }}</small
                        >
                    </div>

                    <InputText
                        v-model="form.title"
                        placeholder="Título"
                        class="w-full"
                        :class="{ 'p-invalid': form.errors.title }"
                    />
                    <small v-if="form.errors.title" class="p-error">{{
                        form.errors.title
                    }}</small>

                    <Textarea
                        v-model="form.description"
                        placeholder="Descrição (opcional)"
                        :rows="3"
                        class="w-full"
                        :class="{ 'p-invalid': form.errors.description }"
                    />
                    <small v-if="form.errors.description" class="p-error">{{
                        form.errors.description
                    }}</small>
                </div>
            </template>
        </Card>

        <Card>
            <template #title>Atividades</template>
            <template #content>
                <div
                    v-if="form.activities.length > 0"
                    class="mb-4 flex flex-wrap gap-2"
                >
                    <Chip
                        v-for="(activity, index) in form.activities"
                        :key="index"
                        @click="editActivity(index)"
                        class="cursor-pointer transition-shadow hover:shadow-md"
                    >
                        <span class="mr-2 text-2xl">{{
                            ActivityTypeEmoji[activity.type]
                        }}</span>
                        <span>{{ ActivityTypeLabel[activity.type] }}</span>
                    </Chip>
                </div>
                <Button
                    label="Adicionar Atividade"
                    outlined
                    @click="addActivity"
                    class="w-full"
                />
            </template>
        </Card>


        <!-- Activity Drawer -->
        <Drawer
            v-model:visible="activityDrawerVisible"
            position="bottom"
            class="h-[80vh]"
        >
            <template #header>
                <div class="flex items-center justify-between">
                    <h3 class="font-bold">Detalhes da atividade</h3>
                    <Button
                        label="Ok"
                        text
                        severity="success"
                        @click="saveActivity"
                    />
                </div>
            </template>

            <Card>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium"
                                >Tipo de atividade</label
                            >
                            <Select
                                v-model="currentActivity.type"
                                :options="activityTypeOptions"
                                optionLabel="text"
                                optionValue="value"
                                placeholder="Selecione uma atividade"
                                class="w-full"
                            />
                        </div>

                        <div class="w-full">
                            <label class="mb-2 block text-sm font-medium"
                                >Hora de início *</label
                            >
                            <DatePicker
                                v-model="currentActivity.started_at"
                                timeOnly
                                showIcon
                                iconDisplay="input"
                                hourFormat="24"
                                class="w-full"
                                inputClass="w-full"
                                :class="{
                                    'p-invalid':
                                        !currentActivity.started_at &&
                                        activityTouched,
                                }"
                            />
                            <small
                                v-if="
                                    !currentActivity.started_at &&
                                    activityTouched
                                "
                                class="p-error"
                            >
                                Hora de início é obrigatória
                            </small>
                        </div>

                        <div class="w-full">
                            <label class="mb-2 block text-sm font-medium"
                                >Duração *</label
                            >
                            <DatePicker
                                v-model="currentActivity.duration"
                                timeOnly
                                showIcon
                                iconDisplay="input"
                                hourFormat="24"
                                class="w-full"
                                inputClass="w-full"
                                :class="{ 'p-invalid': isDurationInvalid }"
                            />
                            <small v-if="isDurationInvalid" class="p-error">
                                Duração deve ser maior que 00:00
                            </small>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium"
                                >Distância (km)</label
                            >
                            <InputNumber
                                v-model="currentActivity.distance"
                                :minFractionDigits="1"
                                placeholder="0.0"
                                class="w-full"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium"
                                >Calorias queimadas</label
                            >
                            <InputNumber
                                v-model="currentActivity.calories_burned"
                                placeholder="0"
                                class="w-full"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium"
                                >Passos</label
                            >
                            <InputNumber
                                v-model="currentActivity.steps"
                                placeholder="0"
                                class="w-full"
                            />
                        </div>
                    </div>
                </template>
            </Card>
        </Drawer>
    </div>
</template>

<script setup lang="ts">
import {
    ActivityType,
    ActivityTypeEmoji,
    ActivityTypeLabel,
} from '@/lib/fit-tracker';
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Chip from 'primevue/chip';
import DatePicker from 'primevue/datepicker';
import Drawer from 'primevue/drawer';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { computed, onMounted, ref } from 'vue';
import { useCamera } from '@/composables/useCamera';

interface ActivityForm {
    type: ActivityType;
    started_at: Date | null; // time only (hours:minutes)
    duration: Date; // duration as Date (hours:minutes), initialized with 00:00
    distance: number | null;
    calories_burned: number | null;
    steps: number | null;
}

const { takePhoto, removePhoto, photos } = useCamera();

const form = useForm({
    checked_in_at: new Date(),
    title: '',
    description: '',
    activities: [] as ActivityForm[],
    photos: [] as File[],
});

const activityDrawerVisible = ref(false);
const editingIndex = ref<number | null>(null);
const activityTouched = ref(false);

const currentActivity = ref<ActivityForm>({
    type: ActivityType.RUNNING,
    started_at: null,
    duration: new Date(0, 0, 0, 0, 0), // Initialize with 00:00
    distance: null,
    calories_burned: null,
    steps: null,
});

const activityTypeOptions = Object.values(ActivityType).map((type) => ({
    text: `${ActivityTypeEmoji[type]} ${ActivityTypeLabel[type]}`,
    value: type,
}));

const isDurationInvalid = computed(() => {
    if (!activityTouched.value) return false;
    if (!currentActivity.value.duration) return true;

    const durationMinutes =
        currentActivity.value.duration.getHours() * 60 +
        currentActivity.value.duration.getMinutes();

    return durationMinutes === 0;
});

onMounted(addPhoto);

function goBack() {
    router.visit('/fit-tracker');
}

function addPhoto() {
    if (photos.value.length < 5) {
        takePhoto();
    }
}

function addActivity() {
    editingIndex.value = null;
    activityTouched.value = false;
    currentActivity.value = {
        type: ActivityType.RUNNING,
        started_at: new Date(),
        duration: new Date(0, 0, 0, 0, 0), // Initialize with 00:00
        distance: null,
        calories_burned: null,
        steps: null,
    };
    activityDrawerVisible.value = true;
}

function editActivity(index: number) {
    editingIndex.value = index;
    activityTouched.value = false;
    const activity = form.activities[index];
    currentActivity.value = {
        type: activity.type,
        started_at: activity.started_at ? new Date(activity.started_at) : null,
        duration: activity.duration,
        distance: activity.distance,
        calories_burned: activity.calories_burned,
        steps: activity.steps,
    };
    activityDrawerVisible.value = true;
}

function saveActivity() {
    activityTouched.value = true;

    // Validate required fields
    if (!currentActivity.value.started_at) {
        return;
    }

    // Check if duration is greater than 00:00
    const durationMinutes = currentActivity.value.duration
        ? currentActivity.value.duration.getHours() * 60 +
          currentActivity.value.duration.getMinutes()
        : 0;

    if (durationMinutes === 0) {
        return;
    }

    if (editingIndex.value !== null) {
        // Update existing activity
        form.activities[editingIndex.value] = { ...currentActivity.value };
    } else {
        // Add new activity
        form.activities.push({ ...currentActivity.value });
    }
    activityDrawerVisible.value = false;
}

function publishCheckIn() {
    // Convert Date objects to ISO strings and calculate ended_at from duration
    const activitiesData = form.activities.map((activity) => {
        // Combine checked_in_at date with started_at time
        const checkedInDate = new Date(form.checked_in_at);
        const startedAt = activity.started_at
            ? new Date(
                  checkedInDate.getFullYear(),
                  checkedInDate.getMonth(),
                  checkedInDate.getDate(),
                  activity.started_at.getHours(),
                  activity.started_at.getMinutes(),
                  0,
              )
            : checkedInDate;

        // Extract hours and minutes from duration Date
        const durationHours = activity.duration.getHours();
        const durationMinutes = activity.duration.getMinutes();
        const totalMinutes = durationHours * 60 + durationMinutes;

        // Calculate ended_at by adding duration to started_at (null if duration is 0)
        const endedAt =
            totalMinutes > 0
                ? new Date(startedAt.getTime() + totalMinutes * 60000) // Convert minutes to milliseconds
                : null;

        return {
            type: activity.type,
            started_at: startedAt.toISOString(),
            ended_at: endedAt ? endedAt.toISOString() : null,
            distance: activity.distance,
            calories_burned: activity.calories_burned,
            steps: activity.steps,
        };
    });

    // Prepare photos as File objects for upload
    const photoFiles = photos.value
        .filter((photo) => photo.blob)
        .map((photo) => {
            // Create a File from the Blob with proper filename
            return new File([photo.blob!], photo.filepath, {
                type: photo.blob!.type,
            });
        });

    form.transform((data) => ({
        ...data,
        checked_in_at: new Date(data.checked_in_at).toISOString(),
        activities: activitiesData,
        photos: photoFiles,
    })).post('/fit-tracker/check-ins', {
        onSuccess: () => {
            router.visit('/fit-tracker');
        },
    });
}
</script>
