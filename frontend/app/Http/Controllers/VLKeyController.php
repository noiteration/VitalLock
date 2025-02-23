<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVLKeyRequest;
use App\Http\Requests\UpdateVLKeyRequest;
use App\Models\VLKey;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Auth;

class VLKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreVLKeyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(VLKey $vLKey)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VLKey $vLKey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVLKeyRequest $request, VLKey $vLKey)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VLKey $vLKey)
    {
        //
    }

    /**
     * Generate Keys for a user
     */
    public function generateKeys()
    {
        $userId = Auth::id();
        $existingKey = VLKey::where('user_id', $userId)->first();

        if ($existingKey) {
            return view('users.user_keys', ['keysExist' => true]);
        }

        $apiUrl = env('API_URL', 'http://localhost:3001');
        $endpoint = '/api/generatekeys';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiUrl . $endpoint, [
                        'secret' => 'a1b1c1d1'
                    ]);

            if ($response->successful()) {
                $result = $response->json();
                $publicKey = $result["publicKey"];
                $idKey = $result["idKey"];
                $secretKey = $result["secretKey"];
                $healthCareKey = $result["healthCareKey"];
                $emergencyContactKey = $result["emergencyContactKey"];

                $vlKey = new VLKey();
                $vlKey->public_key = $publicKey;
                $vlKey->user_id = $userId;
                $vlKey->health_care_key = $healthCareKey;
                $vlKey->save();

                return view('users.user_keys', [
                    'idKey' => $idKey,
                    'secretKey' => $secretKey,
                    'emergencyContactKey' => $emergencyContactKey,
                    'keysExist' => false
                ]);
            } else {
                return response()->json([
                    'error' => 'API request failed',
                    'status' => $response->status()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
