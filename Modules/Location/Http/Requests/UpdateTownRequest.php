<?php

namespace Modules\Location\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTownRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['name' => 'required', 'status' => 'required'];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
