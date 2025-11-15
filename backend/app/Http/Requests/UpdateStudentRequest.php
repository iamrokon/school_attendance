<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
        $studentId = $this->route('student')->id ?? null;
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'student_id' => 'sometimes|required|string|max:255|unique:students,student_id,' . $studentId,
            'class' => 'sometimes|required|string|max:255',
            'section' => 'sometimes|required|string|max:255',
            'photo' => 'nullable|string|max:255',
        ];
    }
}
