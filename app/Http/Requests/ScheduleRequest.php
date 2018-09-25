<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
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
            'service_id' => 'required|integer|exists:services,id',
            'provider_id' => 'required|integer|exists:providers,id',
            'time' => 'required|regex:/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/',
        ];
    }
}
