<?php

namespace App\Http\Requests;

use App\Enums\ActivityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCheckInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'checked_in_at' => 'required|date',
            'activities' => 'nullable|array',
            'activities.*.type' => ['required', Rule::enum(ActivityType::class)],
            'activities.*.started_at' => 'required|date',
            'activities.*.ended_at' => 'nullable|date|after:activities.*.started_at',
            'activities.*.distance' => 'nullable|numeric|min:0',
            'activities.*.calories_burned' => 'nullable|numeric|min:0',
            'activities.*.steps' => 'nullable|integer|min:0',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'required|file|image|mimes:jpeg,jpg,png,webp|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'checked_in_at.required' => 'A data do check-in é obrigatória.',
            'activities.*.started_at.required' => 'A hora de início da atividade é obrigatória.',
            'activities.*.ended_at.after' => 'A hora de término deve ser posterior à hora de início.',
            'photos.max' => 'Você pode adicionar no máximo 5 fotos.',
            'photos.*.image' => 'O arquivo deve ser uma imagem.',
            'photos.*.mimes' => 'As fotos devem ser do tipo: jpeg, jpg, png ou webp.',
            'photos.*.max' => 'Cada foto não pode ser maior que 10MB.',
        ];
    }
}
