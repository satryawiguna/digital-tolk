<?php
namespace DTApi\Http\Requests\Common;

class ListBySearchAndPaginationRequest extends ListBySearchRequest
{
    private int $_page = 1;

    private int $_per_page = 10;

    public function rules()
    {
        return array_merge([
            'page' => ['integer', 'min:1'],
            'per_page' => ['integer', 'min:2']
        ], parent::rules());
    }

    public function prepareForValidation()
    {
        $this->merge([
            'order_by' => ($this->has('order_by')) ? $this->get('order_by') : $this->_order_by,
            'sort' => ($this->has('sort')) ? $this->get('sort') : $this->_sort,
            'search' => ($this->has('search')) ? $this->get('search') : $this->_search,
            'page' => ($this->has('page')) ? $this->get('page') : $this->_page,
            'per_page' => ($this->has('per_page')) ? $this->get('per_page') : $this->_per_page
        ]);
    }
}
