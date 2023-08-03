<?php

namespace App\Services;

use App\Repositories\PostcodeRepository;

class PostcodeService
{
    protected $postcodeRepository;

    public function __construct(PostcodeRepository $postcodeRepository)
    {
        $this->postcodeRepository = $postcodeRepository;
    }

    public function getByPartialMatch($query)
    {
        return $this->postcodeRepository->getByPartialMatch($query);
    }

    public function getNearbyPostcodes($latitude, $longitude, $radius=10)
    {
        return $this->postcodeRepository->getNearbyPostcodes($latitude, $longitude, $radius);
    }

    public function importPostcodes(array $postcodeData)
    {
        foreach ($postcodeData as $data) {
            $this->postcodeRepository->updateOrCreateByPostcode(
                $data['postcode'],
                [
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]
            );
        }
    }
}