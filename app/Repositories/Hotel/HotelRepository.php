<?php
namespace App\Repositories\Hotel;

use App\Enum\ReservationStatus;
use App\Exceptions\ReportableException;
use App\Http\Requests\Hotel\HotelCreateRequest;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelRepository implements HotelRepositoryImpl
{
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

        // 검색어가 있으면 검색 조건 추가
        if ($request->has('search')) {
            $search = $request->input('search');
            $hotels->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
            });
        }
        $hotels->withCount([
            'reservations',
            'reservations as progressing' => function ($query) {
                $query->where('status', ReservationStatus::PROGRESSING);
            },
            'reservations as cancelled' => function ($query) {
                $query->where('status', ReservationStatus::CANCELLED);
            },
            'reservations as rejected' => function ($query) {
                $query->where('status', ReservationStatus::REJECTED);
            },
            'reservations as confirmed' => function ($query) {
                $query->where('status', ReservationStatus::CONFIRMED);
            },
        ]);

        // 페이지네이션
        $hotels = $hotels->paginate($request->input('per_page', 10));

        return response()->json($hotels);

    }

    public function store(HotelCreateRequest $request){
        if (Auth::user()->type != "S") {
            throw new ReportableException("Staff only", 400);
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
        return Hotel::find($id);
    }

    public function update(HotelCreateRequest $request,$id){
        if (Auth::user()->type != "S") {
            throw new ReportableException("Staff only", 400);
        }

        $hotel = Hotel::find($id);
        if(!empty($hotel)){
            throw new ReportableException("Not found",404);
        }
        $hotel->name=$request->input("name");
        $hotel->address=$request->input("address");
        $hotel->star=$request->input("star",1);
        $hotel->room=$request->input("room",1);

        $hotel->save();
        return response()->json($hotel);
    }
    public function destroy($id){
        //미구현
    }
}
