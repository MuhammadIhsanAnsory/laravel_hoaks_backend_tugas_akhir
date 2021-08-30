<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserRegistration;
use App\Http\Resources\User as ResourcesUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct()
  {
    $this->middleware('jwt.verify', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword', 'submitResetPassword']]);
  }

  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');

    try {
      if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Email atau password salah'], 400);
      }
    } catch (JWTException $e) {
      return response()->json(['error' => 'could_not_create_token'], 500);
    }
    $user = auth()->user();
    if ($user->blocked == true) {
      return response()->json([
        'status' => false,
        'message' => 'User diblokir, silahkan kontak admin untuk membuka blokir'
      ], 403);
    }
    return response()->json(compact('token', 'user'));
  }

  public function logout(Request $request)
  {
    // Get JWT Token from the request header key "Authorization"
    $token = $request->header("Authorization");
    // Invalidate the token
    try {
      JWTAuth::invalidate(JWTAuth::getToken());
      return response()->json([
        "status" => "success",
        "message" => "User successfully logged out."
      ], 200);
    } catch (JWTException $e) {
      // something went wrong whilst attempting to encode the token
      return response()->json([
        "status" => "error",
        "message" => "Something wrong, please try again."
      ], 500);
    }
  }

  public function getAuthenticatedUser()
  {
    try {
      if (!$user = JWTAuth::parseToken()->authenticate()) {
        return response()->json(['user_not_found'], 404);
      }
    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
      return response()->json(['token_expired'], $e->getStatusCode());
    }
    return response()->json(compact('user'));
  }


  public function register(RegisterRequest $request)
  {
    User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password),
      'role' => 'user',
      'phone' => $request->phone,
    ]);
    return response()->json([
      'status' => true,
      'message' => 'Berhasil register, silahkan login'
    ], 200);
  }

  public function update(ProfileRequest $request)
  {
    $user_auth = JWTAuth::parseToken()->authenticate();
    $user = User::findOrFail($user_auth->id);
    if(Hash::check($request->current_password, $user->password)){
      $user->update([
        'name' => $request->name,
        'password' => Hash::make($request->password),
        'phone' => $request->phone,
      ]);
  
      return response()->json([
        'status' => true,
        'message' => 'Profile berhasil diupdate',
        'data' => compact('user')
      ], 200);
    }else{
      return response()->json([
        'status' => false,
        'message' => 'Password salah',
      ], 401);
    }
  }
}
