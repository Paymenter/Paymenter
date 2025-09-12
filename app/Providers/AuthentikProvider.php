<?php  
  
namespace App\Providers;  
  
use Laravel\Socialite\Two\AbstractProvider;  
use Laravel\Socialite\Two\User;  
  
class AuthentikProvider extends AbstractProvider  
{  
    public $scopes = ['openid', 'email', 'profile'];  
    protected $scopeSeparator = ' ';
      
    public function getAuthUrl($state)  
    {  
        return $this->buildAuthUrlFromBase(  
            config('settings.oauth_authentik_base_url') . '/application/o/authorize/',  
            $state  
        );  
    }  
  
    public function getTokenUrl()  
    {  
        return config('settings.oauth_authentik_base_url') . '/application/o/token/';  
    }  
  
    public function getUserByToken($token)  
    {  
        $response = $this->getHttpClient()->get(  
            config('settings.oauth_authentik_base_url') . '/application/o/userinfo/',  
            ['headers' => ['Authorization' => 'Bearer ' . $token]]  
        );  

        return json_decode($response->getBody(), true);
    }
  
    public function mapUserToObject(array $user)  
    {  
        return (new User)->setRaw($user)->map([  
            'id' => $user['sub'],  
            'name' => $user['name'] ?? $user['preferred_username'] ?? $user['sub'],  
            'email' => $user['email'] ?? $user['sub'] . '@authentik.local',  
        ]);  
    }  
}
