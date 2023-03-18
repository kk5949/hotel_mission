<?php


namespace App\Repositories\Hotel;


use App\Http\Requests\Hotel\HotelCreateRequest;
use Illuminate\Http\Request;

interface HotelRepositoryImpl
{
    public function index(Request $request);
    public function store(HotelCreateRequest $request);
    public function show($id);
    public function update(HotelCreateRequest $request,$id);
    public function destroy($id);
}
