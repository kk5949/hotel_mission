<?php
namespace App\Repositories\Auth;

use App\Exceptions\ReportableException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryImpl
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function userLogin(LoginRequest $request){

        $user = User::where('email', $request->email)
            ->where("type","U")
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $return = ["code"=>401, "message"=>"Login invalid"];
            return response()->json($return);
        }

        // 토큰 발행
        return $this->with_token($user);
    }

    public function staffLogin(LoginRequest $request){
        $user = User::where('email', $request->email)
            ->where("type","S")
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $return = ["code"=>401, "message"=>"Login invalid"];
            return response()->json($return);
        }

        // 토큰 발행
        return $this->with_token($user);
    }

    private function with_token(User $user): User
    {
        $user->token = $user->createToken($user->id)->plainTextToken;
        return $user;
    }

    public function store(UserStoreRequest $request){
        $dup = $this->user->where("email",$request->input('email'))->first();
        if(!empty($dup)){
            throw new ReportableException("Already used email",401);
        }

        $user = new User();
        $user->name = $request->input('name',"");
        $user->email = $request->input('email');
        $user->type = "U";
        $user->password = Hash::make($request->input('password'));

        $user->save();

        $result = ["code"=>200, "message"=>"Congratulations on your new registration"];
        return response()->json($result);
    }
}
