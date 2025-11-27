<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah jadi true agar request diizinkan
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email', // Email harus unik
            'password' => 'required|string|min:8',
            'role' => 'required|in:teacher,student', // Hanya boleh 'teacher' atau 'student'
        ];
    }
}