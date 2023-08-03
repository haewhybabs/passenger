<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use League\Csv\Reader;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use App\Services\PostcodeService;



class ImportPostcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postcodes:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'download postcodes to the database';

    /**
     * Execute the console command.
     */
 
    protected $postcodeService;

    public function __construct(PostcodeService $postcodeService)
    {
        parent::__construct();
        $this->postcodeService = $postcodeService;
    }
    public function handle()
    {
        $url = 'https://data.freemaptools.com/download/full-uk-postcodes/ukpostcodes.zip';

        try {
            // Download the ZIP file and save to local storage
            $zipFile = storage_path('app/postcodes.zip');
            Http::withOptions(['sink' => $zipFile])->get($url);

            // Unzip the downloaded file
            $zip = new ZipArchive;
            if ($zip->open($zipFile) === true) {
                $zip->extractTo(storage_path('app/postcodes'));
                $zip->close();

                // Import data from the CSV files
                $csvFile = storage_path('app/postcodes/ukpostcodes.csv');
                $csv = Reader::createFromPath($csvFile);
                $csv->setHeaderOffset(0);
                $recordsArray = iterator_to_array($csv->getRecords());
                $this->postcodeService->importPostcodes($recordsArray);
                $this->info('Postcodes imported successfully.');
            } else {
                $this->error('Failed to unzip the downloaded file.');
            }
        } catch (\Exception $e) {
            $this->error('An error occurred while importing postcodes: ' . $e->getMessage());
        }
    }
}
