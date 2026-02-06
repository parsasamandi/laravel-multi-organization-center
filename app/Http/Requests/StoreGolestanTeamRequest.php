<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class StoreGolestanTeamRequest extends StoreCenterRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = parent::rules($request);

        // Remove the 'code' field from the validation rules
        unset($rules['code']);

        return $rules;
    }
}
