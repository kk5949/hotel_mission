<?php


namespace App\Repositories\Auth;


use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UserStoreRequest;

interface AuthRepositoryImpl
{
    public function store(UserStoreRequest $request);
    public function userLogin(LoginRequest $request);
    public function staffLogin(LoginRequest $request);
}
