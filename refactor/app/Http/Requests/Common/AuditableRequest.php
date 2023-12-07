<?php
namespace DTApi\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AuditableRequest
{
    public string $request_by = "system";

    public function rules()
    {
        return [
            'request_by' => ['string']
        ];
    }

    public static function setRuleAuthor(array $rules)
    {
        return array_merge($rules, $this->rules());
    }

    public static function setRequestAuthor(FormRequest $request): void
    {
        $request->merge(['request_by' => (Auth::user()) ? Auth::user()->username : $this->request_by]);
    }
}
