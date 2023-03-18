<?php

namespace App\Repositories\Reservation;

use App\Enum\ReservationStatus;
use App\Exceptions\ReportableException;
use App\Http\Requests\Hotel\HotelCreateRequest;
use App\Http\Requests\Reservation\ReservationCreateRequest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Repositories\Reservation\ReservationRepositoryImpl;
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

    /**
     * @param ReservationCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * 예약은 일단 유저, 스탶 모두 가능함
     * 예약신청, 예약확정은 재고에서 차감하여 계산한다.
     * 예약거절, 예약취소는 재고에 반영하지 않는다.
     */
    public function store(ReservationCreateRequest $request)
    {
        $hotelId = $request->input("hotel_id");

        $hotel = Hotel::find($hotelId);
        $soldout = $hotel->soldout;

        if(!$soldout){
            $reservation = new Reservation;
            $reservation->user_id = Auth::user()->id;
            $reservation->hotel_id = $hotelId;
            $reservation->step = ReservationStatus::PROGRESSING;
            $reservation->save();

            $result = ["code"=>200, "message"=>"Reservation has been registered successfully"];
        }else{
            $result = ["code"=>400, "message"=>"Reservation fail. Soldout rooms"];
        }

        return response()->json($result);
    }

    public function show($id)
    {
        return Hotel::find($id);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ReportableException
     *
     * 일반유저는 본인의 예약정보만 취소가 가능합니다.
     * 스태프는 모든 예약정보를 취소 할 수 있습니다.
     *
     * 예약중, 예약완료인 예약만 취소 가능하며 반려되거나 이미 취소한 데이터는 처리되지 않습니다.
     */
    public function cancel($id){
        // 일반 유저는 본인 예약정보만 취소가능, 스태프는 모두 가능
        if (Auth::user()->type != "S") {
            $reservation = Reservation::where("user_id",Auth::user()->id)->find($id);
        }else{
            $reservation = Reservation::find($id);
        }

        if(empty($reservation)){
            $result = ["code"=>404, "message"=>"Reservation not found"];
            return response()->json($result);
        }

        if(in_array($reservation->step, [ReservationStatus::PROGRESSING,ReservationStatus::CONFIRMED])){
            $reservation->step = ReservationStatus::CANCELLED;
            $reservation->save();
            $result = ["code"=>200, "message"=>"Reservation has been cancelled successfully"];
        }else{
            $result = ["code"=>400, "message"=>"Reservation is not progressing, please check again"];
        }

        return response()->json($result);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     *
     * 최종 확정은 스태프만 가능하다.
     * 반려, 취소건은 확정이 불가능하고 예약중인(progressing) 데이터만 가능하다.
     */
    public function confirm($id){
        if (Auth::user()->type != "S") {
            $result = ["code"=>401, "message"=>"Staff only"];
            return response()->json($result);
        }

        $reservation = Reservation::find($id);
        if(empty($reservation)){
            $result = ["code"=>404, "message"=>"Reservation not found"];
            return response()->json($result);
        }

        if($reservation->step == ReservationStatus::PROGRESSING){
            $reservation->step = ReservationStatus::CONFIRMED;
            $reservation->save();
            $result = ["code"=>200, "message"=>"Reservation has been confirmed successfully"];
        }else{
            $result = ["code"=>400, "message"=>"Reservation is not progressing, please check again"];
        }
        return response()->json($result);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 반려는 스태프만 가능합니다.
     * 반려는 예약중, 예약완료만 가능하며 예약취소이거나 이미 반려된 경우 처리되지 않습니다.
     */
    public function reject($id){
        if (Auth::user()->type != "S") {
            $result = ["code"=>401, "message"=>"Staff only"];
            return response()->json($result);
        }

        $reservation = Reservation::find($id);
        if(empty($reservation)){
            $result = ["code"=>404, "message"=>"Reservation not found"];
            return response()->json($result);
        }

        if(in_array($reservation->step, [ReservationStatus::PROGRESSING,ReservationStatus::CONFIRMED])){
            $reservation->step = ReservationStatus::REJECTED;
            $reservation->save();
            $result = ["code"=>200, "message"=>"Reservation has been rejected successfully"];
        }else{
            $result = ["code"=>400, "message"=>"Reservation is not progressing, please check again"];
        }

        return response()->json($result);
    }

    public function update(HotelCreateRequest $request, $id)
    {
        //미구현
    }

    public function destroy($id)
    {
        //미구현
    }
}
