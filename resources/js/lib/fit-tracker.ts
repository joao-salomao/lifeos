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

export interface CheckIn {
    id: number;
    user_id: number;
    title: string;
    description: string | null;
    checked_in_at: string;
    created_at: string;
    updated_at: string;
    activities: Activity[];
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
