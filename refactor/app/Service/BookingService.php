<?php

namespace DTApi\Services;

use DTApi\Enums\HttpResponseType;
use DTApi\Http\Requests\CreateJobRequest;
use DTApi\Http\Requests\UpdateJobRequest;
use DTApi\Http\Responses\Common\BooleanResponse;
use DTApi\Http\Responses\Common\GenericListResponse;
use DTApi\Http\Responses\Common\GenericObjectResponse;
use DTApi\Repository\Interfaces\IBookingRepository;
use DTApi\Service\BaseService;
use DTApi\Service\Interfaces\IBookingService;
use DTApi\Model\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class BookingService extends BaseService implements IBookingService
{
    private readonly IBookingRepository $_bookingRepository;

    public function __construct(IBookingRepository $bookingRepository)
    {
        $this->_bookingRepository = $bookingRepository;
    }

    public function fetchAll(Request $request): GenericListResponse
    {
        $response = new GenericListResponse();

        try {
            $cacheKey = 'get_all';
            $cachedData = Redis::get($cacheKey);

            $all = ($cachedData) ? json_decode($cachedData, true) : function() use($cacheKey, $request) {
                $resultGetAll = $this->_bookingRepository->getAll($request);

                Redis::setex($cacheKey, 60, json_encode($resultGetAll));
            };

            $this->setGenericListResponse($response,
                $all,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function fetchUserJob(int $user_id): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        try {
            $cacheKey = 'get_users_jobs';
            $cacheData = Redis::get($cacheKey);

            $userJob = ($cacheData) ? json_decode($cacheData, true) : function() use($cacheKey, $user_id) {
                $resultGetUserJob = $this->_bookingRepository->getUsersJobs($user_id);

                Redis::setex($cacheKey, 60, json_encode($resultGetUserJob));
            };

            $this->setGenericListResponse($response,
                $userJob,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function fetchJob(int $id): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        try {
            $cacheKey = 'find_job_' . $id;
            $cacheData = Redis::get($cacheKey);

            $job = ($cacheData) ? json_decode($cacheData, true) : function() use($cacheKey, $id) {
                $resultFindJob = $this->_bookingRepository->with('translatorJobRel.user')->find($id);

                Redis::setex($cacheKey, 60, json_encode($resultFindJob));
            };

            $this->setGenericListResponse($response,
                $job,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function createJob(CreateJobRequest $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $data = $request->all();
            $createJob = $this->_bookingRepository->store(Auth::user(), $data);

            DB::commit();

            $this->setGenericListResponse($response,
                $createJob,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function updateJob(int $id, UpdateJobRequest $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $updateJob = $this->_bookingRepository->updateJob($id, array_except($request->all(), ['_token', 'submit']), Auth::user());

            DB::commit();

            $this->setGenericListResponse($response,
                $updateJob,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function addJobEmail(Request $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $data = $request->all();

            $storeJobEmail = $this->_bookingRepository->storeJobEmail($data);

            DB::commit();

            $this->setGenericListResponse($response,
                $storeJobEmail,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function fetchUsersJobsHistory(Request $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        try {
            $user_id = $request->get('user_id');
            $cacheKey = 'find_user_jobs_history' . $user_id;
            $cacheData = Redis::get($cacheKey);

            $getUsersJobsHistory = ($cacheData) ? json_decode($cacheData, true) : function() use($cacheKey, $user_id, $request) {
                $resultFindJobHistory = $this->_bookingRepository->getUsersJobsHistory($user_id, $request);

                Redis::setex($cacheKey, 60, json_encode($resultFindJobHistory));
            };

            $this->setGenericListResponse($response,
                $getUsersJobsHistory,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function admitJob(Request $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $data = $request->all();

            $acceptJob = $this->_bookingRepository->acceptJob($data, Auth::user());

            DB::commit();

            $this->setGenericListResponse($response,
                $acceptJob,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function admitJobById(Request $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $data = $request->get('job_id');

            $acceptJobWithId = $this->_bookingRepository->acceptJobWithId($data, Auth::user());

            DB::commit();

            $this->setGenericListResponse($response,
                $acceptJobWithId,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function revokeJob(Request $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $data = $request->all();

            $cancelJobAjax = $this->_bookingRepository->cancelJobAjax($data, Auth::user());

            DB::commit();

            $this->setGenericListResponse($response,
                $cancelJobAjax,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function terminateJob(Request $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $data = $request->all();

            $endJob = $this->_bookingRepository->endJob($data);

            DB::commit();

            $this->setGenericListResponse($response,
                $endJob,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function customerNotCall(Request $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $data = $request->all();

            $customerToCall = $this->_bookingRepository->customerNotCall($data);

            DB::commit();

            $this->setGenericListResponse($response,
                $customerToCall,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function fetchPotentialJob(Request $request): GenericListResponse
    {
        $response = new GenericListResponse();

        try {
            $cacheKey = 'find_potential_job' . Auth::user()->id;
            $cacheData = Redis::get($cacheKey);

            $getUsersJobsHistory = ($cacheData) ? json_decode($cacheData, true) : function() use($cacheKey) {
                $resultFindPotentialJob = $this->_bookingRepository->getPotentialJobs(Auth::user());

                Redis::setex($cacheKey, 60, json_encode($resultFindPotentialJob));
            };

            $this->setGenericListResponse($response,
                $getUsersJobsHistory,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function distanceFeed(Request $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $data = $request->all();

            if (isset($data['distance']) && $data['distance'] != "") {
                $distance = $data['distance'];
            } else {
                $distance = "";
            }

            if (isset($data['time']) && $data['time'] != "") {
                $time = $data['time'];
            } else {
                $time = "";
            }

            if (isset($data['jobid']) && $data['jobid'] != "") {
                $jobid = $data['jobid'];
            }

            if (isset($data['session_time']) && $data['session_time'] != "") {
                $session = $data['session_time'];
            } else {
                $session = "";
            }

            if ($data['flagged'] == 'true') {
                if($data['admincomment'] == '') return "Please, add comment";
                $flagged = 'yes';
            } else {
                $flagged = 'no';
            }

            if ($data['manually_handled'] == 'true') {
                $manually_handled = 'yes';
            } else {
                $manually_handled = 'no';
            }

            if ($data['by_admin'] == 'true') {
                $by_admin = 'yes';
            } else {
                $by_admin = 'no';
            }

            if (isset($data['admincomment']) && $data['admincomment'] != "") {
                $admincomment = $data['admincomment'];
            } else {
                $admincomment = "";
            }

            $updateDistance = $this->_bookingRepository->updateDistanceByJobId($jobid, ["time" => $time, "distance" => $distance]);

            if (!$updateDistance["status"]) {
                throw new QueryException("Invalid update distance by job id:" . $jobid);
            }

            $updateJob = $this->_bookingRepository->updateJobById($jobid, ["admincomment" => $admincomment,
                "session" => $session,
                "flagged" => $flagged,
                "manually_handled" => $manually_handled,
                "by_admin" => $by_admin]);

            if (!$updateJob["status"]) {
                throw new QueryException("Invalid update job by job id:" . $jobid);
            }

            DB::commit();

            $this->setGenericListResponse($response,
                collect(["status" => "success"]),
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        } catch(QueryException $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::BAD_REQUEST,
                'Invalid query');

            Log::error("Invalid query on " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function reopenJob(Request $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $data = $request->all();

            $reOpen = $this->_bookingRepository->reopen($data);

            DB::commit();

            $this->setGenericListResponse($response,
                $reOpen,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            DB::rollBack();

            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function reSendNotification(Request $request): BooleanResponse
    {
        $response = new BooleanResponse();

        try {
            $data = $request->all();

            $job = $this->_bookingRepository->find($data['jobid']);
            $job_data = $this->_bookingRepository->jobToData($job);

            $this->_bookingRepository->sendNotificationTranslator($job, $job_data, '*');

            $this->setBooleanResponse($response,
                true,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }

    public function reSendSMSNotification(Request $request): BooleanResponse
    {
        $response = new BooleanResponse();

        try {
            $data = $request->all();
            $job = $this->repository->find($data['jobid']);

            $this->_bookingRepository->sendSMSNotificationToTranslator($job);

            $this->setBooleanResponse($response,
                true,
                'SUCCESS',
                HttpResponseType::SUCCESS);
        } catch (Exception $ex) {
            $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR,
                'Something went wrong');

            Log::error("Something went wrong " . __FUNCTION__ . "()", [$ex->getMessage()]);
        }

        return $response;
    }


}
