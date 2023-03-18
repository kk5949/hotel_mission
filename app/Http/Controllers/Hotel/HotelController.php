<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hotel\HotelCreateRequest;
use App\Repositories\Hotel\HotelRepository;
use Illuminate\Http\Request;

class HotelController extends Controller
{

    protected HotelRepository $hotelRepository;

    /**
     * HotelController constructor.
     * @param HotelRepository $hotelRepository
     */
    public function __construct(HotelRepository $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }

    public function index(Request $request)
    {
        return $this->hotelRepository->index($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HotelCreateRequest $request)
    {
        return $this->hotelRepository->store($request);
    }

    public function show($id)
    {
        //
    }

    public function update(HotelCreateRequest $request, $id)
    {
        return $this->hotelRepository->update($request,$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
