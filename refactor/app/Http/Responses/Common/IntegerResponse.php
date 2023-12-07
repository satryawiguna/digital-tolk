<?php
namespace DTApi\Http\Responses\Common;

class IntegerResponse extends BasicResponse
{
    public int $result;

    /**
     * @return int
     */
    public function getResult(): int
    {
        return $this->result;
    }

    /**
     * @param int $result
     */
    public function setResult(int $result): void
    {
        $this->result = $result;
    }
}