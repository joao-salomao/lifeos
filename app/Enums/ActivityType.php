<?php

namespace App\Enums;

enum ActivityType: string
{
    case CALISTHENICS = 'calisthenics';
    case CYCLING = 'cycling';
    case RUNNING = 'running';
    case MUSCLE_TRAINING = 'muscle_training';
    case WEIGHTLIFTING = 'weightlifting';
}
