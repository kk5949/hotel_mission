<?php

namespace App\Repositories\Hotel;

use App\Enum\ReservationStatus;
use App\Exceptions\ReportableException;
use App\Http\Requests\Hotel\HotelCreateRequest;
use App\Http\Requests\Reservation\ReservationCreateRequest;
use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationRepository implements ReservationRepositoryImpl
{
    protected Reservation $reservation;

    /**
     * ReservationRepository constructor.
     * @param Reservation $reservation
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }


    public function index(Request $request)
    {
        $reservations = $this->reservation->query();

        // 검색어가 있으면 검색 조건 추가
        if (Auth::user()->type != "S") {
            // 일반유저는 본인 데이터만 조회
            $reservations->where("user_id",Auth::user()->id);
        }

        $reservations = $reservations->paginate($request->input('per_page', 10));

        return response()->json($reservations);

    }

    // 예약기능
    public function store(ReservationCreateRequest $request)
    {
        //예약신청, 예약확정은 재고에서 차감하여 계산한다.
        //예약거절, 예약취소는 재고에 반영하지 않는다.
        $hotelId = $request->input("hotel_id");

        //TODO:예약 관련 기능 추가해야함

        $hotel = Reservation::create([
            "user_id" => Auth::user()->id,
            "hotel_id" => $hotelId,
            "step" => ReservationStatus::PROGRESSING,
        ]);
        return response()->json($hotel);
    }

    public function show($id)
    {
        return Hotel::find($id);
    }

    public function cancel($id){
        // 일반 유저는 본인 예약정보만 취소가능, 스태프는 모두 가능
        if (Auth::user()->type != "S") {
            $reservation = Reservation::where("user_id",Auth::user()->id)->find($id);
        }else{
            $reservation = Reservation::find($id);
        }

        if(empty($reservation)){
            throw new ReportableException("Not found",404);
        }

        if($reservation->step != ReservationStatus::CANCELLED){
            $reservation->step = ReservationStatus::CANCELLED;
            $reservation->save();
        }
        $result = ["code"=>200, "message"=>"Reservation has been cancelled successfully"];

        return response()->json($result);

    }

    public function confirm(){

    }

    public function reject(){}

    public function update(HotelCreateRequest $request, $id)
    {
        //미구현
    }

    public function destroy($id)
    {
        //미구현
    }
}
