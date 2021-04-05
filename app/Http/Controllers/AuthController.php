<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only('logout');
    }

    /**
     * @OA\Post(
     ** path="/login",
     *   tags={"Auth"},
     *   summary="Login",
     *   operationId="login",
     *
     *   @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     **/
    /**
     * Login
     */
    public function login(Request $request)
    {     
        $validator = $this->validateLogin($request);
        if(!$validator->fails()){
                $request = Request::create('/oauth/token', 'POST', [
                    'grant_type' => 'password',
                    'client_id' => config('services.client.id'),
                    'client_secret' => config('services.client.secret'),
                    'username' => $request->email,
                    'password' => $request->password,
                ]);

                return app()->handle($request);
        } else {
            return $this->error($validator->errors()->all(),422);
        }       
    }

    /**
     * Login validation rules.
     *
     * @return array
     */
    protected function validateLogin($request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:9',
        ]);
    }

    /**
     * @OA\Post(
     ** path="/logout",
     *   tags={"Auth"},
     *   summary="Logout",
     *   operationId="Logout",
     *   
     *   @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="Forbidden"
     *    )
     *)
     **/
    /**
     * Logout and revoke access token
     */
    public function logout()
    {
        $tokenId = Auth::user()->token()->id;
        
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');

        // Revoke an access token...
        $tokenRepository->revokeAccessToken($tokenId);

        // Revoke all of the token's refresh tokens...
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

        return $this->success('Logged Out', 200);
    }
}
