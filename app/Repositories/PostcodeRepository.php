<?php


namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Postcode;


class PostcodeRepository
{
    public function getByPartialMatch($query)
    {
        return Postcode::where('postcode', 'LIKE', "%$query%")->get();
    }

    public function getNearbyPostcodes($latitude, $longitude, $radius)
    {
        return DB::table('postcodes')
            ->selectRaw('*, (6371 * ACOS(COS(RADIANS(:latitude)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(:longitude)) + SIN(RADIANS(:latitude)) * SIN(RADIANS(latitude)))) AS distance')
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->setBindings(['latitude' => $latitude, 'longitude' => $longitude])
            ->get();
    }

    public function updateOrCreateByPostcode($postcode, array $data)
    {
        return Postcode::updateOrCreate(['postcode' => $postcode], $data);
    }
}