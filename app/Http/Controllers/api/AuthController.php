<?php

namespace App\Http\Controllers\api;

use App\Models\Cart;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public $successStatus = 200;
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $oClient = OClient::where('password_client', 1)->first();
            return $this->getTokenAndRefreshToken($oClient, $request->email, $request->password);
        } else {
            return response()->json(['error' => 'Email or Password not correct!'], 401);
        }
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $password = $request->password;
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $cart = Cart::create([
            'id_user' => $user->id,
        ]);
        $oClient = OClient::where('password_client', 1)->first();
        return $this->getTokenAndRefreshToken($oClient, $user->email, $password);
    }
    public function getTokenAndRefreshToken($oClient, $email, $password)
    {
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;
        $response = $http->request('POST', 'https://apishoes-production.up.railway.app/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'username' => $email,
                'password' => $password,
                'scope' => '',
            ],
        ]);
        $result = json_decode((string) $response->getBody(), true);
        return response()->json($result, $this->successStatus);
    }
    public function reFreshToken(Request $request)
    {
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;
        $response = $http->request('POST', 'https://apishoes-production.up.railway.app/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'refresh_token' => $request->refreshToken,
                'scope' => '',
            ],
        ]);
        $result = json_decode((string) $response->getBody(), true);
        return response()->json($result, $this->successStatus);
    }
}
