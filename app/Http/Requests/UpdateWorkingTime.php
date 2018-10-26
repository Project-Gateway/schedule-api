<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateWorkingTime
 * @package App\Http\Requests
 * @property string $date
 * @property string $start_time
 * @property string $finish_time
 */
class UpdateWorkingTime extends FormRequest
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
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required:date_format:H:i:s',
            'finish_time' => 'required:date_format:H:i:s',
        ];
    }
}
