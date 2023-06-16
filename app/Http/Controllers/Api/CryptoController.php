<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
class CryptoController extends Controller
{
    //
    public function pintest()
    {

        $isError=true;
        try{
        $headers = array('Accept' => 'application/json');

        $resp = Http::withHeaders($headers)->get(env('CRYPTO_PING'));
        $response = json_decode($resp->body());

        }
        catch (\Exception $e) {
            $isError=false;
            $error = $e->getMessage();
        }

        return $response;
    }
    public function coinresponse(Request $request)
    {

        $transId = strtotime('now') . uniqid() . Str::random(5);

        try{
        $headers = array('Accept' => 'application/json',
        'Cookie' => '__cf_bm=xvhzxyQqvyyBWmXdMLg1289mINmnE07RyA8IIyOaaXU-1686894870-0-AWa4aiajLmPmr54NNnsrIjo0Ms+iCow79PBkP4VOWu+GR1Oagrox4MVQUsl6zoACDpH9/BuCqrgUyTntcUIzcw0=');

        $resp = Http::withHeaders($headers)->get(env('CRYPTO_URL').'include_platform='.$request->include_platform);
        $response = $resp->body();

         DB::table('coin')
        ->insert([
            'trans_id' => $transId,
            'request' => json_encode($request->all()),
            'response' => $response,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s.u')
        ]);
        }
        catch (\Exception $e) {
            $error = $e->getMessage();
            DB::table('coin')
            ->insert([
                'trans_id' => $transId,
                'request' => json_encode($request->all()),
                'response' => $error,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s.u')
            ]);
        }
        return json_decode($response);
    }

}
