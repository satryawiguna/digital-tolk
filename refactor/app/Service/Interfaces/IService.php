<?php
namespace DTApi\Service\Interfaces;

use DTApi\Http\Responses\Common\BasicResponse;
use DTApi\Http\Responses\Common\BooleanResponse;
use DTApi\Http\Responses\Common\GenericListBySearchAndPaginationResponse;
use DTApi\Http\Responses\Common\GenericListBySearchResponse;
use DTApi\Http\Responses\Common\GenericListResponse;
use DTApi\Http\Responses\Common\GenericListSearchPageResponse;
use DTApi\Http\Responses\Common\GenericListSearchResponse;
use DTApi\Http\Responses\Common\GenericObjectResponse;
use DTApi\Http\Responses\Common\IntegerResponse;
use DTApi\Http\Responses\Common\Booleanesponse;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface IService
{
    public function setMessageResponse(BasicResponse $response,
                                    string|array $message = null,
                                    string $type,
                                    int $codeStatus);

    public function setIntegerResponse(IntegerResponse $response, 
                                int $result, 
                                string $type, 
                                int $codeStatus): IntegerResponse;

    public function setBooleanResponse(BooleanResponse $response, 
                                    bool $result, 
                                    string $type, 
                                    int $codeStatus): BooleanResponse;

    public function setGenericObjectResponse(GenericObjectResponse $response,
                                        Model|null $dto,
                                        string $type,
                                        int $codeStatus): GenericObjectResponse;

    public function setGenericListResponse(GenericListResponse $response,
                                        Collection $dtoList,
                                        string $type,
                                        int $codeStatus): GenericListResponse;

    public function setGenericListBySearchResponse(GenericListBySearchResponse $response,
                                            Collection $dtoListBySearch,
                                            string $type,
                                            int $codeStatus,
                                            int $totalCount): GenericListBySearchResponse;

    public function setGenericListBySearchAndPaginationResponse(GenericListBySearchAndPaginationResponse $response,
                                                Collection $dtoListBySearchAndPagination,
                                                string $type,
                                                int $codeStatus,
                                                int $totalCount,
                                                array $meta): GenericListBySearchAndPaginationResponse;
}