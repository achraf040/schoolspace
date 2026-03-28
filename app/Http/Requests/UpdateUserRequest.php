<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Email supprimé des validations car généré automatiquement
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['boolean'],
            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,gif',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
            'remove_photo' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'profile_photo.image' => 'Le fichier doit être une image.',
            'profile_photo.mimes' => 'L\'image doit être au format JPEG, PNG ou GIF.',
            'profile_photo.max' => 'L\'image ne doit pas dépasser 2MB.',
            'profile_photo.dimensions' => 'L\'image doit faire au minimum 100x100 pixels et au maximum 2000x2000 pixels.',
        ];
    }
}