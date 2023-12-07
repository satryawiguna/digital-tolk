<?php
namespace DTApi\Http\Requests;

use DTApi\Http\Requests\Common\AuditableRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        // return AuditableRequest::setRuleAuthor($rules);
        return $rules;
    }

    public function prepareForValidation()
    {
        //AuditableRequest::setRequestAuthor($this);
    }
}