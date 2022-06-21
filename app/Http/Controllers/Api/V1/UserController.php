<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Laundry;
use App\Models\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Exception;
use Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function register(Request $registerRequest)
    {
            $validator = Validator::make($registerRequest->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8',
                'no_hp' => 'required|min:11|max:15'
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }

            $user = User::create(
                [
                    'name' => $registerRequest->name,
                    'email' => $registerRequest->email,
                    'password' => bcrypt($registerRequest->password),
                    'no_hp' => $registerRequest->no_hp,
                    'role' => User::ROLE_CUSTOMER
                ]
            );
    
            return UserResource::make($user);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'error' => 'Unauthorized'
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
            
        $laundry = Laundry::query()
            ->whereBelongsTo($user)
            ->first();
        
       
        if($user->role == 1){
            $token = $user->createToken('laundream',['adminDo'])->plainTextToken;
        }else if($user->role == 2){
            $token = $user->createToken('laundream',['ownerDo'])->plainTextToken;
        }else if($user->role == 3){
            $token = $user->createToken('laundream',['employeeDo'])->plainTextToken;
        }else if($user->role == 4){
            $token = $user->createToken('laundream',['customerDo'])->plainTextToken;
        }
        
    
        return response()->json([
            'user' => $user,
            'token' => $token,
            'error' => null,
            'laundry' => $user->role != User::ROLE_CUSTOMER ? $laundry : null
        ]);
       
    }


    public function update(Request $updateProfileRequest)
    {
            $validator = Validator::make($updateProfileRequest->all(),[
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'no_hp' => 'required|min:11|max:15'
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }
            $user =  auth()->user();
            $user->update(
                [
                    'name' => $updateProfileRequest->name,
                    'password' => bcrypt($updateProfileRequest->password),
                    'no_hp' => $updateProfileRequest->no_hp,
                ]
            );
        
            return UserResource::make($user);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
