<?php

namespace Mlantz\Quarx\Requests;

use App\Http\Requests\Request;
use Mlantz\Quarx\Models\FAQ;

class CreateFAQRequest extends Request
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
        return FAQ::$rules;
    }

}