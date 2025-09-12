<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GoogleProvider;
use SocialiteProviders\Discord\Provider as DiscordProvider;

class SocialLoginController extends Controller
{
    protected $discord_driver;
    protected $github_driver;
    protected $google_driver;
    public $authentik_driver;

    public function __construct()
    {
        $this->discord_driver = Socialite::buildProvider(DiscordProvider::class, [
            'client_id' => config('settings.oauth_discord_client_id'),
            'client_secret' => config('settings.oauth_discord_client_secret'),
            'redirect' => '/oauth/discord/callback',
        ]);

        $this->github_driver = Socialite::buildProvider(GithubProvider::class, [
            'client_id' => config('settings.oauth_github_client_id'),
            'client_secret' => config('settings.oauth_github_client_secret'),
            'redirect' => '/oauth/github/callback',
        ]);

        $this->google_driver = Socialite::buildProvider(GoogleProvider::class, [
            'client_id' => config('settings.oauth_google_client_id'),
            'client_secret' => config('settings.oauth_google_client_secret'),
            'redirect' => '/oauth/google/callback',
        ]);

        $this->authentik_driver = Socialite::buildProvider(\App\Providers\AuthentikProvider::class, [
            'client_id' => config('settings.oauth_authentik_client_id'),
            'client_secret' => config('settings.oauth_authentik_client_secret'),
            'redirect' => '/oauth/authentik/callback',
            'base_url' => config('settings.oauth_authentik_base_url'),
            'authorize_url' => config('settings.oauth_authentik_base_url') . '/application/o/authorize/',
            'token_url' => config('settings.oauth_authentik_base_url') . '/application/o/token/',
            'userinfo_url' => config('settings.oauth_authentik_base_url') . '/application/o/userinfo/',
        ]);
    }

    public function redirect($provider)
    {
        if (!config("settings.oauth_$provider")) {
            abort(404);
        }
        
        if ($provider === 'authentik') {
            return $this->authentik_driver
                ->scopes(['openid', 'email', 'profile'])
                ->with(['prompt' => 'consent'])
                ->redirect();
        }

        // Lógica para los otros proveedores
        return match ($provider) {
            'discord' => $this->discord_driver->scopes(['email'])->redirect(),
            'github' => $this->github_driver->scopes(['user:email'])->redirect(),
            'google' => $this->google_driver->scopes(['email'])->redirect(),
            default => abort(404)
        };
    }

    public function handle(Request $request, $provider)
    {
  if ($provider == 'discord') {
            $oauth_user = $this->discord_driver->user();

            if ($oauth_user->user['verified'] == false) {
                return redirect()->route('login')->with('error', __('auth.oauth.unverified_discord_account'));
            }

            $user = User::where('email', $oauth_user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', __('auth.oauth.account_not_registered'));
            }

            Auth::login($user, true);

            return redirect()->route('home');
        } elseif ($provider == 'google') {
            $oauth_user = $this->google_driver->user();

            $user = User::where('email', $oauth_user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', __('auth.oauth.account_not_registered'));
            }

            Auth::login($user, true);

            return redirect()->route('home');
        } elseif ($provider == 'github') {
            $oauth_user = $this->github_driver->user();

            $user = User::where('email', $oauth_user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', __('auth.oauth.account_not_registered'));
            }

            Auth::login($user, true);

            return redirect()->route('home');
        } else if ($provider == 'authentik') {
            try {
                // Obtenemos el token de acceso usando el código de la URL
                $token = $this->authentik_driver->getAccessTokenResponse($request->get('code'));

                //Log::info('--- AUTHENTIK TOKEN DEBUG ---', ['token_response' => $token]);

                try {
                    $oauth_user_data = $this->authentik_driver->getUserByToken($token['access_token']);
//                    Log::info('--- AUTHENTIK USERINFO RESPONSE ---', ['userinfo_data' => $oauth_user_data]);
                } catch (\Exception $e) {
//                    Log::error('--- AUTHENTIK USERINFO ERROR ---', ['error' => $e->getMessage()]);
                    
                    $id_token = $token['id_token'];
                    $oauth_user_data = $this->decodeIdToken($id_token);
  //                  Log::info('--- AUTHENTIK ID TOKEN DECODED (FALLBACK) ---', ['user_data' => $oauth_user_data]);
                    
                    // INTENTAR OBTENER EMAIL REAL DESDE AUTHENTIK API
                    try {
                        $user_email = $this->getUserEmailFromAuthentik($token['access_token'], $oauth_user_data['sub']);
                        if ($user_email) {
                            $oauth_user_data['email'] = $user_email;
  //                          Log::info('--- AUTHENTIK EMAIL OBTAINED ---', ['email' => $user_email]);
                        }
                    } catch (\Exception $emailError) {
                        Log::error('--- AUTHENTIK EMAIL ERROR ---', ['error' => $emailError->getMessage()]);
                    }
                }
                
                $oauth_user = $this->authentik_driver->mapUserToObject($oauth_user_data);
                
                Log::info('--- AUTHENTIK USER OBJECT ---', [
                    'email' => $oauth_user->email,
                    'name' => $oauth_user->name,
                    'id' => $oauth_user->id,
                    'raw_data' => $oauth_user_data
                ]);

                $user = null;
                if (!empty($oauth_user->email)) {
                    $user = User::where('email', $oauth_user->email)->first();
                }
                
                if (!$user) {
                    Log::warning('--- AUTHENTIK USER NOT FOUND ---, creating new user', [
                        'searched_email' => $oauth_user->email,
                        'searched_authentik_id' => $oauth_user->id ?? null,
                        'user_data' => $oauth_user_data
                    ]);

                    // Derivar nombre y apellidos
                    $rawGivenName = $oauth_user_data['given_name'] ?? ($oauth_user_data['name'] ?? null);
                    $rawFamilyName = $oauth_user_data['family_name'] ?? null;
                    $fullName = $oauth_user_data['name'] ?? ($oauth_user->name ?? '');

                    $firstName = '';
                    $lastName = '';

                    if (!empty($rawGivenName) && !empty($rawFamilyName)) {
                        $firstName = $rawGivenName;
                        $lastName = $rawFamilyName;
                    } elseif (!empty($fullName)) {
                        $parts = preg_split('/\s+/', trim($fullName));
                        $firstName = $parts[0] ?? '';
                        $lastName = trim(implode(' ', array_slice($parts, 1)));
                    }

                    $email = $oauth_user->email ?? ($oauth_user_data['email'] ?? null);
                    if (empty($email)) {
                        throw new \Exception('Authentik did not provide an email for the user.');
                    }

                    $user = User::create([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'password' => Str::random(40),
                        'email_verified_at' => Carbon::now(),
                    ]);
                }

                Auth::login($user, true);
                return redirect()->route('home');

            } catch (\Exception $e) {
                // Si algo falla, lo guardamos en el log para tener todos los detalles
                Log::error('--- AUTHENTIK CALLBACK ERROR ---: ' . $e->getMessage());
                return redirect()->route('login')->with('error', 'Authentication failed: ' . $e->getMessage());
            }
        }else {
            return redirect()->route('login');
        }
    }

    /**
     * Decode JWT ID Token from Authentik
     */
    private function decodeIdToken($idToken)
    {
        // JWT tokens have 3 parts separated by dots
        $parts = explode('.', $idToken);
        
        if (count($parts) !== 3) {
            throw new \Exception('Invalid ID token format');
        }
        
        // Decode the payload (second part)
        $payload = $parts[1];
        
        // Add padding if needed
        $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4);
        
        // Decode base64
        $decoded = base64_decode($payload);
        
        if ($decoded === false) {
            throw new \Exception('Failed to decode ID token payload');
        }
        
        $userData = json_decode($decoded, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse ID token payload: ' . json_last_error_msg());
        }
        
        return $userData;
    }

    /**
     * Intentar obtener el email del usuario desde la API de Authentik
     */
    private function getUserEmailFromAuthentik($accessToken, $userId)
    {
        try {
            // Intentar diferentes endpoints de Authentik para obtener información del usuario
            $baseUrl = config('settings.oauth_authentik_base_url');
            
            // Endpoint 1: API de usuarios de Authentik
            $response = $this->getHttpClient()->get(
                $baseUrl . '/api/v3/core/users/' . $userId . '/',
                ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]
            );
            
            $userData = json_decode($response->getBody(), true);
            
            if (isset($userData['email']) && !empty($userData['email'])) {
                return $userData['email'];
            }
            
            // Endpoint 2: Información del usuario actual
            $response = $this->getHttpClient()->get(
                $baseUrl . '/api/v3/core/users/me/',
                ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]
            );
            
            $userData = json_decode($response->getBody(), true);
            
            if (isset($userData['email']) && !empty($userData['email'])) {
                return $userData['email'];
            }
            
        } catch (\Exception $e) {
            Log::error('--- AUTHENTIK API ERROR ---', ['error' => $e->getMessage()]);
        }
        
        return null;
    }

    /**
     * Obtener cliente HTTP para hacer llamadas a APIs externas
     */
    private function getHttpClient()
    {
        return new \GuzzleHttp\Client([
            'timeout' => 10,
            'verify' => false, // Solo para desarrollo, en producción debería ser true
        ]);
    }
}