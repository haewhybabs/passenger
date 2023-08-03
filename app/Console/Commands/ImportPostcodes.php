<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use App\Services\PostcodeService;
use League\Csv\Statement;
use League\Csv\Reader;

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
                
                $chunkSize = 1000;
                $offset=0;
                // Process the CSV data in chunks
                while ($chunk = (new Statement())->limit($chunkSize, $offset)->process($csv)) {
            
                    // Convert the chunk iterator to an array
                    $recordsArray = iterator_to_array($chunk);
                    Log::channel('command')->info('Postcodes data received', $recordsArray);
                    // Pass the chunk data to the PostcodeService for import
                    $this->postcodeService->importPostcodes($recordsArray);
            
                    $offset += $chunkSize;
                }

                $this->info('Postcodes imported successfully.');
            } else {
                $this->error('Failed to unzip the downloaded file.');
            }
        } catch (\Exception $e) {
            $this->error('An error occurred while importing postcodes: ' . $e->getMessage());
        }
    }
}
