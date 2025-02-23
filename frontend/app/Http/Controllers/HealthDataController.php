<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateHealthDataRequest;
use App\Models\HealthData;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Auth;

use App\Models\VLKey;

class HealthDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('health_data.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("health_data.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();
        $publicKey = VLKey::all()->where('user_id', $userId)->first()->public_key;

        $apiUrl = env('API_URL', 'http://localhost:3001');
        $encryption_endpoint = '/api/encryptdata';

        $dataToEncrypt = $request->data;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($apiUrl . $encryption_endpoint, [
                    'publicKey' => $publicKey,
                    'data' => $dataToEncrypt
                ]);

        $encryptedData = "";
        if ($response->successful()) {
            // Create a new item with the decrypted data
            $encryptedData = $response->json()["result"];
        } else {
            // If decryption fails, add the original item to the array with message
            // Can definitely upgrade to using aes for now if this fails
            $encryptedData = "Failed to encrypt, so the data is lost as of now";
        }

        $blockchainUrl = env('BLOCKCHAIN_URL', 'http://localhost:3002');
        $endpoint = '/add-block';
        $response = Http::post($blockchainUrl . $endpoint, [
            'user_id' => $userId,
            'data' => $encryptedData
        ]);

        if ($response->successful()) {
            return view('health_data.success');
        }

        return response()->json(['error' => 'Failed to add block'], 500);
    }

    

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        function decryptData($privateKey, $jsonData)
        {
            $apiUrl = env('API_URL', 'http://localhost:3001');
            $decryption_endpoint = '/api/decryptdata';
            $decryptedData = [];

            foreach ($jsonData as $item) {
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post($apiUrl . $decryption_endpoint, [
                                'privateKey' => (string)$privateKey,
                                'encryptedData' => $item["data"]
                            ]);
                    if ($response->successful()) {
                        $result = $response->json();
                        // Create a new item with the decrypted data
                        $decryptedItem = [
                            'user_id' => $item['user_id'],
                            'data' => $result['result'] ?? null,
                        ];
                    } else {
                        // If decryption fails, add the original item to the array with message
                        $decryptedItem = [
                            'user_id' => $item['user_id'],
                            'data' => "Failed to decrypt"
                        ];
                    }
                    $decryptedData[] = $decryptedItem;
                } catch (\Exception $e) {
                    // If an exception occurs, add the original item to the array
                    $decryptedData[] = $item;
                    // You might want to log the error here
                }
            }

            return $decryptedData;
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
            $decryptedData = decryptData($private_key, $jsonData);
            return view('health_data.show', [
                'decryptedData' => $decryptedData,
            ]);
        }

        return response()->json(['error' => 'Failed to retrieve user data'], 500);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HealthData $healthData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHealthDataRequest $request, HealthData $healthData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthData $healthData)
    {
        //
    }
}
