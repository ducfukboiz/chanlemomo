<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSettingGameRequest2 extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'min' => 'required|integer',
            'max' => 'required|integer',
            'sdt' => 'required',
            'tile1' => 'required',
            'tile2' => 'required',
            'tile3' => 'required',
        ];
    }
}
