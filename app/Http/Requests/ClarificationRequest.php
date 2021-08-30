<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClarificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'report_id' => ['required'],
            'title' => ['required'],
            'content' => ['required'],
            'link' => ['min:1'],
            'images.0' => ['mimes:jpg,jpeg,png', 'max:3000'],
            'video' => ['mimes:mp4,mpeg,avi', 'max:40000'],
            'hoax' => ['required', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'report_id.required' => 'Aduan harus ada',
            'title.required' => 'Judul klarifikasi harus ada',
            'content.required' => 'Isi klarifikasi harus ada',
            'link.min' => 'Link harus ada',
            'images.mimes' => 'Ekstensi gambar harus jpg, jpeg, atau png',
            'images.max' => 'Gambar maksimal 3mb',
            'video.mimes' => 'Ekstensi video harus mp4, mpeg, atau avi',
            'video.max' => 'Gambar maksimal 20mb',
            'hoax.required' => 'Status hoax harus ada',
            'hoax.boolean' => 'Status hoax tidak valid',
        ];
    }

    
    protected function failedValidation(Validator $validator) { 
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()->all(),
            'status' => false 
        ], 422)); 
    }
}
