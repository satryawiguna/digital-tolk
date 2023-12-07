<?php
namespace DTApi\Http\Responses\Common;

class GenericObjectResponse extends BasicResponse
{
    public $dto;

    public function getDto()
    {
        return $this->dto ?? new \stdClass();
    }
}
