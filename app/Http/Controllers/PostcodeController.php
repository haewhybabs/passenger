<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostcodeResource;
use App\Services\PostcodeService;
use Illuminate\Http\Request;

class PostcodeController extends Controller
{
    protected $postcodeService;
    protected $defaultRadius;

    public function __construct(PostcodeService $postcodeService)
    {
        $this->postcodeService = $postcodeService;
        $this->defaultRadius =10;
    }
   

    public function partialMatch(Request $request)
    {
        try {
            $query = $request->query('query');
            $postcodes = $this->postcodeService->getByPartialMatch($query);
            return response()->json(PostcodeResource::collection($postcodes));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function nearbyPostcodes(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'sometimes|numeric',
            ]);

            $latitude = $request->query('latitude');
            $longitude = $request->query('longitude');
            $radius = $request->query('radius', $this->defaultRadius);

            $postcodes = $this->postcodeService->getNearbyPostcodes($latitude, $longitude, $radius);
            return response()->json(PostcodeResource::collection($postcodes));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
