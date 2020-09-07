<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Role;
use Illuminate\Http\Request;
use App\User;
// use App\Model\UserDetails;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        
    //    dd($request);
        $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $success['role'] = [];

            if($user){
                $i = 0;
                foreach ($user->roles as $role) {
                    $success["role"][$i] = $role->name;
                    $i++;
                }
            }
            
            $success['token'] = $user->createToken('MyApp')->accessToken;

            return response()->json(['success' => $success], 200);
        }
        else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function register(Request $request) {
        $validateData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);
        $validateData['password'] = bcrypt($request->password);
        
        $user = User::create($validateData);        
        
        // $userDetails = new UserDetails();
        // //On left field name in DB and on right field name in Form/view
        // $userDetails->user_id = $user->id;
        // $userDetails->role = null;
        // $userDetails->info = null;
        // $userDetails->age = null;
        // $userDetails->hobby = null;
        // $userDetails->job = null;
        // $userDetails->save();

        $accessToken = $user->createToken('authToken', ['*'])->accessToken;
        
        return response([ 'user' => $user, 'access_token' => $accessToken]);
    }
}
