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
        return DB::table(function ($query) use ($latitude, $longitude) {
            $query->selectRaw('*, (6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude)))) AS distance', [$latitude, $longitude, $latitude]);
            $query->from('post_codes');
        })
        ->where('distance', '<', $radius)
        ->orderBy('distance')
        ->get();
    }

    public function updateOrCreateByPostcode($postcode, array $data)
    {
        return Postcode::updateOrCreate(['postcode' => $postcode], $data);
    }
}