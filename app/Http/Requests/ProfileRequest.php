<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'name' => ['required', 'min:3', 'max:24'],
            'current_password' => ['required'],
            'password' => ['required', 'min:6', 'max:24', 'confirmed'],
            'phone' => ['required', 'numeric', 'digits_between:6,14'],
        ];
   

    }

    public function messages()
    {
        return [
            'name.required' => 'Nama harus diisi',
            'name.min' => 'Nama minimal 3 huruf',
            'name.max' => 'Nama maximal 24 huruf',
            'email.required' => 'Email harus diisi',
            'password.required' => 'Status blokir harus diisi',
            'password.max' => 'Password maksimal 24 huruf',
            'password.min' => 'Password minimal 6 huruf',
            'password.confirmed' => 'Isi konfirmasi password',
            'phone.required' => 'Nomor telpon / whatsapp harus diisi',
            'phone.numeric' => 'Nomor telpon / whatsapp harus berupa angka',
            'phone.digits_between' => 'Nomor telpon / whatsapp antara 6 sampai 16 angka',
        ];
    }
    
    protected function failedValidation(Validator $validator) { 
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()->all(),
            'status' => false 
        ], 422)); 
    }
}
