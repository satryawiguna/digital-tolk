<?php
namespace DTApi\Service;

use DTApi\Http\Responses\Common\BasicResponse;
use DTApi\Http\Responses\Common\IntegerResponse;
use DTApi\Http\Responses\Common\BooleanResponse;
use DTApi\Http\Responses\Common\GenericObjectResponse;
use DTApi\Http\Responses\Common\GenericListResponse;
use DTApi\Http\Responses\Common\GenericListBySearchResponse;
use DTApi\Http\Responses\Common\GenericListBySearchAndPaginationResponse;
use DTApi\Service\Interfaces\IService;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseService implements IService
{
    public function setMessageResponse(BasicResponse $response,
                                    string|array $message = null,
                                    string $type,
                                    int $codeStatus)
    {
        $response->type = $type;
        $response->codeStatus = $codeStatus;

        if (is_array($message)) {
            foreach ($message as $key => $value) {
                foreach ($value as $item) {
                    $method = "add" . ucfirst($type) . "MessageResponse";
                    $response->$method($item);
                }
            }
        } else {
            $method = "add" . ucfirst($type) . "MessageResponse";

            $response->$method($message);
        }

        return $response;
    }

    public function setIntegerResponse(IntegerResponse $response, 
                                int $result, 
                                string $type, 
                                int $codeStatus): IntegerResponse
    {
        $response->result = $result;

        $this->setBaseResponse($response, $type, $codeStatus);
        
        return $response;
    }

    public function setBooleanResponse(BooleanResponse $response, 
                                bool $result,
                                string $type, 
                                int $codeStatus): BooleanResponse
    {
        $response->result = $result;

        $this->setBaseResponse($response, $type, $codeStatus);

        return $response;
    }


    public function setGenericObjectResponse(GenericObjectResponse $response,
                                        Model|null $dto,
                                        string $type,
                                        int $codeStatus): GenericObjectResponse
    {
        $response->dto = $dto;

        $this->setBaseResponse($response, $type, $codeStatus);

        return $response;
    }

    public function setGenericListResponse(GenericListResponse $response,
                                           Collection $dtoList,
                                           string $type,
                                           int $codeStatus): GenericListResponse
    {
        $response->dtoList = $dtoList;
        
        $$this->setBaseResponse($response, $type, $codeStatus);

        return $response;
    }

    public function setGenericListBySearchResponse(GenericListBySearchResponse $response,
                                                 Collection $dtoListBySearch,
                                                 string $type,
                                                 int $codeStatus,
                                                 int $totalCount): GenericListBySearchResponse
    {
        $response->dtoListBySearch = $dtoListBySearch;

        $this->setBaseResponse($response, $type, $codeStatus);

        $response->totalCount = $totalCount;

        return $response;
    }

    public function setGenericListBySearchAndPaginationResponse(GenericListBySearchAndPaginationResponse $response,
                                                     Collection $dtoListBySearchAndPagination,
                                                     string $type,
                                                     int $codeStatus,
                                                     int $totalCount,
                                                     array $meta): GenericListBySearchAndPaginationResponse
    {
        $response->dtoListBySearchAndPagination = $dtoListBySearchAndPagination;
        
        $this->setBaseResponse($response, $type, $codeStatus);

        $response->totalCount = $totalCount;
        $response->meta = $meta;

        return $response;
    }

    private function setBaseResponse($response, $type, $codeStatus): void
    {
        $response->type = $type;
        $response->codeStatus = $codeStatus;
    }
}
