<?php
namespace DTApi\Http\Requests\Common;

class ListBySearcRequest extends ListRequest
{
    private string | null $_search = null;

    public function rules()
    {
        return array_merge([
            'search' => ['string', 'nullable']
        ], parent::rules());
    }

    public function prepareForValidation()
    {
        $this->merge([
            'order_by' => ($this->has('order_by')) ? $this->get('order_by') : $this->_order_by,
            'sort' => ($this->has('sort')) ? $this->get('sort') : $this->_sort,
            'search' => ($this->has('search')) ? $this->get('search') : $this->_search
        ]);
    }
}
