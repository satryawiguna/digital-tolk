<?php
namespace DTApi\Service\Interfaces;


use DTApi\Http\Requests\Common\ListRequest;
use DTApi\Http\Requests\CreateJobRequest;
use DTApi\Http\Requests\UpdateJobRequest;
use DTApi\Http\Responses\Common\BooleanResponse;
use DTApi\Http\Responses\Common\GenericListResponse;
use DTApi\Http\Responses\Common\GenericObjectResponse;
use DTApi\Model\User;
use Illuminate\Http\Request;

interface IBookingService
{
    public function fetchAll(ListRequest $request): GenericListResponse;

    public function fetchUserJob(int $user_id): GenericObjectResponse;

    public function fetchJob(int $id): GenericObjectResponse;

    public function createJob(CreateJobRequest $request): GenericObjectResponse;

    public function updateJob(int $id, UpdateJobRequest $request): GenericObjectResponse;

    public function addJobEmail(Request $request): GenericObjectResponse;

    public function fetchUsersJobsHistory(Request $request): GenericObjectResponse;

    public function admitJob(Request $request): GenericObjectResponse;

    public function admitJobById(Request $request): GenericObjectResponse;

    public function revokeJob(Request $request): GenericObjectResponse;

    public function terminateJob(Request $request): GenericObjectResponse;

    public function customerNotCall(Request $request): GenericObjectResponse;

    public function fetchPotentialJob(Request $request): GenericListResponse;

    public function distanceFeed(Request $request): GenericObjectResponse;

    public function reopenJob(Request $request): GenericObjectResponse;

    public function reSendNotification(Request $request): BooleanResponse;

    public function reSendSMSNotification(Request $request): BooleanResponse;
}
