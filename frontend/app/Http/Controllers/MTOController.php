<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMTORequest;
use App\Http\Requests\UpdateMTORequest;
use App\Models\MTO;
use App\Models\VLKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class MTOController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('mto.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMTORequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        function checkVision($privateKey, $jsonData)
        {
            $apiUrl = env('API_URL', 'http://localhost:3001');
            $decryption_endpoint = '/api/decryptdata';
            $has2020Vision = false;

            foreach ($jsonData as $item) {
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post($apiUrl . $decryption_endpoint, [
                                'privateKey' => $privateKey,
                                'encryptedData' => $item["data"]
                            ]);

                    if ($response->successful()) {
                        $result = $response->json();
                        if (isset($result['result']) && strpos($result['result'], "20/20") !== false) {
                            $has2020Vision = true;
                            break;  // Exit the loop as soon as we find "20/20"
                        }
                    }
                } catch (\Exception $e) {
                    // Log the error if needed
                }
            }

            return $has2020Vision;
        }


        function constructPrivateKey($userId, $userKey)
        {
            $health_care_key = VLKey::all()->where('user_id', $userId)->first()->health_care_key;
            $user_key = $userKey;

            $apiUrl = env('API_URL', 'http://localhost:3001');
            $decryptkeys_endpoint = '/api/decryptkeys';

            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($apiUrl . $decryptkeys_endpoint, [
                            'part1' => $health_care_key,
                            'part2' => $user_key,
                        ]);

                if ($response->successful()) {
                    $privateKey = (string) $response->json()["message"];
                    return $privateKey;
                } else {
                    return "Could not create private key";
                }
            } catch (\Exception $e) {
                return "Error occured while decrypting";
            }
        }

        // Actual implementation
        $userId = Auth::id();

        $blockchainUrl = env('BLOCKCHAIN_URL', 'http://localhost:3002');
        $endpoint = '/get-user-data';
        $response = Http::post($blockchainUrl . $endpoint, [
            'user_id' => $userId
        ]);
        $jsonData = $response->json();
        $userKey = $request->UserKey;
        $private_key = constructPrivateKey($userId, $userKey);

        if ($response->successful()) {
            $decryptedData = checkVision($private_key, $jsonData);
            if ($decryptedData) {
                return view('mto.success');
            } else {
                return view('mto.failure');
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MTO $mTO)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMTORequest $request, MTO $mTO)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MTO $mTO)
    {
        //
    }
}
