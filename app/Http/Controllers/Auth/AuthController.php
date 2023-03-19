<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UserStoreRequest;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private AuthRepository $authRepository;

    /**
     * AuthController constructor.
     * @param AuthRepository $authRepository
     */
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function index()
    {
        //
    }

    public function userLogin(LoginRequest $request){
        return $this->authRepository->userLogin($request);
    }

    public function staffLogin(LoginRequest $request){
        return $this->authRepository->staffLogin($request);
    }

    public function logout():JsonResponse
    {
        $user = auth()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        $result['success'] = "success";
        $result['msg'] = "Logout";

        return response()->json($result);
    }


    public function store(UserStoreRequest $request)
    {
        return $this->authRepository->store($request);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
