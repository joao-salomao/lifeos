export enum ActivityType {
    CALISTHENICS = 'calisthenics',
    CYCLING = 'cycling',
    RUNNING = 'running',
    MUSCLE_TRAINING = 'muscle_training',
    WEIGHTLIFTING = 'weightlifting',
}

export interface Activity {
    id: number;
    check_in_id: number;
    type: ActivityType;
    started_at: string;
    ended_at: string;
    distance: number | null;
    calories_burned: number | null;
    steps: number | null;
    created_at: string;
    updated_at: string;
}

export interface Media {
    id: number;
    model_type: string;
    model_id: number;
    uuid: string;
    collection_name: string;
    name: string;
    file_name: string;
    mime_type: string;
    disk: string;
    conversions_disk: string;
    size: number;
    manipulations: Record<string, unknown>;
    custom_properties: Record<string, unknown>;
    generated_conversions: Record<string, unknown>;
    responsive_images: Record<string, unknown>;
    order_column: number;
    created_at: string;
    updated_at: string;
}

export interface CheckIn {
    id: number;
    user_id: number;
    title: string;
    description: string | null;
    checked_in_at: string;
    created_at: string;
    updated_at: string;
    activities: Activity[];
    media: Media[];
}

export const ActivityTypeEmoji: Record<ActivityType, string> = {
    [ActivityType.CALISTHENICS]: '🤸',
    [ActivityType.CYCLING]: '🚴',
    [ActivityType.RUNNING]: '🏃',
    [ActivityType.MUSCLE_TRAINING]: '💪',
    [ActivityType.WEIGHTLIFTING]: '🏋️',
};

export const ActivityTypeLabel: Record<ActivityType, string> = {
    [ActivityType.CALISTHENICS]: 'Calistenia',
    [ActivityType.CYCLING]: 'Ciclismo',
    [ActivityType.RUNNING]: 'Corrida',
    [ActivityType.MUSCLE_TRAINING]: 'Musculação',
    [ActivityType.WEIGHTLIFTING]: 'Levantamento de Peso',
};
