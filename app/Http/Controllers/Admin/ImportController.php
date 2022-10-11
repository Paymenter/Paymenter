<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;


class ImportController extends Controller 
{
    public function index()
    {
        return view('admin.import.index');
    }

    function startImport(Request $request)
    {
        $whmcsUrl = $request->url;
        $api_identifier = $request->identifier;
        $api_secret = $request->secret;
        $postfields = array(
            'identifier' => $api_identifier,
            'secret' => $api_secret,
            'action' => 'GetClients',
            'responsetype' => 'json',
        );

        // Call the API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $whmcsUrl . 'includes/api.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
        $response = curl_exec($ch);
        if (curl_error($ch)) {
            die('Unable to connect: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);
        $jsonData = json_decode($response, true);
        foreach ($jsonData['clients']['client'] as $client) {
            $clientExists = User::where('email', $client['email'])->first();
            if ($clientExists) {
                $clientExists->name = $client['firstname'] . $client['lastname'];
                $clientExists->companyname = $client['companyname'];
                if (isset($client['address1'])) $clientExists->address = $client['address1'];
                if (isset($client['address2'])) $clientExists->address2 = $client['address2'];
                if (isset($client['city'])) $clientExists->city = $client['city'];
                if (isset($client['state'])) $clientExists->state = $client['state'];
                if (isset($client['postcode'])) $clientExists->zip = $client['postcode'];
                if (isset($client['country'])) $clientExists->country = $client['country'];
                if (isset($client['phonenumber'])) $clientExists->phonenumber = $client['phonenumber'];
                $clientExists->save();
            } else {
                $newClient = new User;
                $newClient->name = $client['firstname'] . $client['lastname'];
                $newClient->companyname = $client['companyname'];
                if(isset($client['address1'])) $newClient->address = $client['address1'];
                if(isset($client['address2'])) $newClient->address2 = $client['address2'];
                if(isset($client['city'])) $newClient->city = $client['city'];
                if(isset($client['state'])) $newClient->state = $client['state'];
                if(isset($client['postcode'])) $newClient->zip = $client['postcode'];
                if(isset($client['country'])) $newClient->country = $client['country'];
                if(isset($client['phonenumber'])) $newClient->phonenumber = $client['phonenumber'];
                // save password as random string
                $newClient->password = bcrypt($this->generateRandomString(10));
                $newClient->email = $client['email'];
                $newClient->save();
            }
        }
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function import(Request $request)
    {   
        $this->startImport($request);
        return redirect()->route('admin.import');
    }

}