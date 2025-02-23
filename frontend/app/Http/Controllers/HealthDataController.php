<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthDataRequest;
use App\Http\Requests\UpdateHealthDataRequest;
use App\Models\HealthData;

class HealthDataController extends Controller
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
    public function store(StoreHealthDataRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(HealthData $healthData)
    {
        //
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
