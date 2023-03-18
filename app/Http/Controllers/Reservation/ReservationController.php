<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\ReservationCreateRequest;
use App\Repositories\Reservation\ReservationRepository;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected ReservationRepository $reservationRepository;

    /**
     * ReservationController constructor.
     * @param ReservationRepository $reservationRepository
     */
    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }


    public function index(Request $request)
    {
        return $this->reservationRepository->index($request);
    }

    public function store(ReservationCreateRequest $request)
    {
        return $this->reservationRepository->store($request);
    }

    public function show($id)
    {
        return $this->reservationRepository->show($id);
    }

    public function cancel($id)
    {
        return $this->reservationRepository->cancel($id);
    }

    public function confirm($id)
    {
        return $this->reservationRepository->confirm($id);
    }

    public function reject($id)
    {
        return $this->reservationRepository->reject($id);
    }

}
