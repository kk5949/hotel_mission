<?php
namespace App\Repositories\Hotel;

use App\Enum\ReservationStatus;
use App\Exceptions\ReportableException;
use App\Http\Requests\Hotel\HotelCreateRequest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Response\CustomPaginateResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelRepository implements HotelRepositoryImpl
{
    use CustomPaginateResponse;
    protected Hotel $hotel;

    /**
     * HotelRepository constructor.
     * @param Hotel $hotel
     */
    public function __construct(Hotel $hotel)
    {
        $this->hotel = $hotel;
    }

    public function index(Request $request){
        $hotels = $this->hotel->query();
        $exceptSoldout = $request->input("exceptSoldout",false);

        // 검색어가 있으면 검색 조건 추가
        if ($request->has('search')) {
            $search = $request->input('search');
            $hotels->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
            });
        }

        // 만실제외 옵션
        if($exceptSoldout){
            $hotels->whereHas("reservations",function($query){
                $query->whereIn('step', [ReservationStatus::PROGRESSING, ReservationStatus::CONFIRMED]);
            },"<", DB::raw('room'));
        }

        // 페이지네이션
        $hotels = $hotels->paginate($request->input('per_page', 10));

        return self::customPaginateResponse($hotels);
    }

    public function store(HotelCreateRequest $request){
        if (Auth::user()->type != "S") {
            throw new ReportableException("Staff only", 401);
        }

        $hotel = Hotel::create([
            "name"=>$request->input("name"),
            "address"=>$request->input("address"),
            "star"=>$request->input("star",1),
            "room"=>$request->input("room",1),
        ]);
        return response()->json($hotel);
    }

    public function show($id){
        if (Auth::user()->type != "S") {
            $hotel = Hotel::find($id);
        }else{
            $hotel = Hotel::with("reservations")->find($id);
        }

        if(empty($hotel)){
            return ["code"=>"404","message"=>"Hotel not found"];
        }

        return $hotel;
    }

    public function update(HotelCreateRequest $request,$id){
        if (Auth::user()->type != "S") {
            throw new ReportableException("Staff only", 400);
        }
        $star = $request->input("star",1);
        $room = $request->input("room",1);

        $hotel = Hotel::with('reservations')->find($id);
        if(empty($hotel)){
            return ["code"=>"404","message"=>"Hotel not found"];
        }

        $reservations = Reservation::where("hotel_id",$id)->whereIn("step",[ReservationStatus::PROGRESSING,ReservationStatus::CONFIRMED])->count();
        if($room < $reservations){
            return ["code"=>"400","message"=>"$reservations Reservation remains."];
        }

        $hotel->name=$request->input("name");
        $hotel->address=$request->input("address");
        $hotel->star=$star;
        $hotel->room=$room;

        $hotel->save();
        return ["code"=>200, "message"=>"Hotel has been modified successfully"];
    }

    public function destroy($id){
        //미구현
    }
}
