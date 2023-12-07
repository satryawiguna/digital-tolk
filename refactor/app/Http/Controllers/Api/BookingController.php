<?php
namespace DTApi\Http\Controllers;

use DTApi\Http\Controllers\Api\ApiBaseController;
use DTApi\Http\Requests\CreateJobRequest;
use DTApi\Http\Requests\UpdateJobRequest;
use DTApi\Http\Responses\Common\GenericListResponse;
use DTApi\Service\Interfaces\IBookingService;
use DTApi\Models\Job;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends ApiBaseController
{

    protected IBookingService $_bookingService;

    public function __construct(IBookingService $bookingService)
    {
        $this->_bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        if ($user_id = $request->get('user_id')) {
            $response = $this->_bookingService->fetchUserJob($user_id);
        } else if ($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') ||
            $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID')) {
            $response = $this->_bookingService->fetchAll($request);
        }

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return ($response instanceof GenericListResponse) ? $this->getListJsonResponse($response) :
            $this->getObjectJsonResponse($response);
    }

    public function show(int $id)
    {
        $response = $this->_bookingService->fetchJob($id);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function store(CreateJobRequest $request)
    {
        $response = $this->_bookingService->createJob($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function update($id, UpdateJobRequest $request)
    {
        $response = $this->_bookingService->updateJob($id, $request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function immediateJobEmail(Request $request)
    {
        $response = $this->_bookingService->addJobEmail($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function getHistory(Request $request)
    {
        $response = $this->_bookingService->fetchUsersJobsHistory($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function acceptJob(Request $request)
    {
        $response = $this->_bookingService->admitJob($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $response = $this->_bookingService->admitJobById($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function cancelJob(Request $request)
    {
        $response = $this->_bookingService->revokeJob($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function endJob(Request $request)
    {
        $response = $this->_bookingService->terminateJob($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function customerNotCall(Request $request)
    {
        $response = $this->_bookingService->customerNotCall($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function getPotentialJobs(Request $request)
    {
        $response = $this->_bookingService->fetchPotentialJob($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function distanceFeed(Request $request)
    {
        $response = $this->_bookingService->distanceFeed($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function reopen(Request $request)
    {
        $response = $this->_bookingService->reopenJob($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getObjectJsonResponse($response);
    }

    public function resendNotifications(Request $request)
    {
        $response = $this->_bookingService->reSendNotification($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getJsonResponse($response);
    }

    public function resendSMSNotifications(Request $request)
    {
        $response = $this->_bookingService->reSendSMSNotification($request);

        if ($response->isError()) {
            return $this->getErrorLatestJsonResponse($response);
        }

        return $this->getJsonResponse($response);
    }

}
