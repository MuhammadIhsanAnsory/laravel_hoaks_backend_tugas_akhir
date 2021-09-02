<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['required'],
            'content' => ['required'],
            'link' => ['min:1'],
            'images.*' => ['mimes:jpg,jpeg,png,svg,gif', 'max:4096'],
            // 'video' => ['mimetypes:mp4,x-flv,quicktime,x-msvideo,x-ms-wmv', 'max:40960'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul aduan harus ada',
            'content.required' => 'Isi aduan harus ada',
            'link.min' => 'Link harus ada',
            'image.mimes' => 'Ekstensi gambar harus jpg, jpeg, atau png',
            'image.max' => 'Gambar maksimal 3mb',
            'video.mimetypes' => 'Ekstensi video harus mp4, mpeg, atau avi',
            'video.max' => 'Gambar maksimal 20mb',
        ];
    }

    
    protected function failedValidation(Validator $validator) { 
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()->all(),
            'status' => false 
        ], 422)); 
    }
}
