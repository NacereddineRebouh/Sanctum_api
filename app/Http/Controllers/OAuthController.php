<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function redirectToProvider($provider)
    {
        $validated=$this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        // return Socialite::driver($provider)->stateless()->redirect();
        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ]);
    }


   /**
     * Obtain the user information from Provider.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }
        // dd($user);
        $userCreated = User::firstOrCreate(
            [
                'email' => $user->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $user->getName(),
                'status' => true,
            ]
        );
	
        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId(),
            ]
        );

        $token = $userCreated->createToken('myToken')->plainTextToken;

        $cookie=Cookie::make(
            'Access-Token',
            $token,
            14400, // time to expire
            null,
            null,
            false,
            true,
            false,
            'none'//same-site   <-----
        );
		// ->withCookie($cookie
        return response(['AccessToken' => $token,"user"=>$userCreated], 201);
    }

    /**
    * @param $provider
    * @return JsonResponse
    */
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google'])) {
            return response()->json(['error' => 'Please login using Google'], 422);
        }
    }
}
