<?php

namespace App\Repositories;

use App\Models\Satusehat;
use Illuminate\Support\Facades\Http;

class SatusehatRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getSatusehat()
    {
        $satusehat = Satusehat::first();
        return $satusehat;
    }

    public function saveSatusehat($request)
    {
        $satusehat = Satusehat::first();
        if ($satusehat) {
            $satusehat->update([
                'satatus'           => $request->status,
                'organization_id'   => $request->organization_id,
                'client_id'         => $request->client_id,
                'client_secret'     => $request->client_secret
            ]);
        } else {
            $satusehat = Satusehat::create([
                'organization_id'   => $request->organization_id,
                'client_id'         => $request->client_id,
                'client_secret'     => $request->client_secret,
                'satatus'           => $request->status,
            ]);
        }

        return $satusehat;
    }

    public function accesstoken()
    {
        $satusehat = Satusehat::first();
        switch ($satusehat->status) {
            case '1':
                $payload = ['client_id' => $satusehat->client_id, 'client_secret' => $satusehat->client_secret];
                $header = [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => '*/*',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_URL, env("URL_SANDBOX"). "/oauth2/v1/accesstoken?grant_type=client_credentials");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

                $response = curl_exec($ch);
                $err      = curl_error($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpcode == 200) {
                    $res = json_decode($response, true);
                    $satusehat->update([
                        'organization_name' => $res['organization_name'],
                        'developer_email'   => $res['developer.email'],
                        'access_token'      => $res['access_token'],
                        'expired_in'        => date("Y-m-d H:i:s", time() + $res['expires_in'])
                    ]);
                    return $satusehat;
                }

                break;
            case '2':
                $payload = ['client_id' => $satusehat->client_id, 'client_secret' => $satusehat->client_secret];
                $header = [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => '*/*',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_URL, env("URL_PRODUCTION"). "/oauth2/v1/accesstoken?grant_type=client_credentials");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

                $response = curl_exec($ch);
                $err      = curl_error($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpcode == 200) {
                    $res = json_decode($response, true);
                    $satusehat->update([
                        'organization_name' => $res['organization_name'],
                        'developer_email'   => $res['developer.email'],
                        'access_token'      => $res['access_token'],
                        'expired_in'        => date("Y-m-d H:i:s", time() + $res['expires_in'])
                    ]);
                    return $satusehat;
                }
                break;
            default:
                # code...
                break;
        }
    }
}
