<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $upload;
    /**
     * Create a new job instance.
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->upload->update(['status' => 'processing']);

        try {
            $file = Storage::get($this->upload->file_path);
            $file = mb_convert_encoding($file, 'UTF-8', 'UTF-8');

            $file = preg_replace('/^\xEF\xBB\xBF/', '', $file); 
            $file = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $file); 
            $file = iconv('UTF-8', 'UTF-8//IGNORE', $file); 
            
            $handle = fopen('php://memory', 'r+');
            fwrite($handle, $file);
            rewind($handle);
            
            $header = null;
            $batchSize = 1000;
            $batch = [];
            
            while (($row = fgetcsv($handle)) !== false) {
                if (!$header) {
                    $header = $row;
                    continue;
                }

                if (count($row) !== count($header)) continue;

                $csvRow = array_combine($header, $row);
                $productData = [
                    'unique_key'         => trim($csvRow['UNIQUE_KEY'] ?? ''),
                    'product_title'      => trim($csvRow['PRODUCT_TITLE'] ?? ''),
                    'product_description'=> trim($csvRow['PRODUCT_DESCRIPTION'] ?? ''),
                    'style'              => trim($csvRow['STYLE#'] ?? ''),
                    'mainframe_color'    => trim($csvRow['SANMAR_MAINFRAME_COLOR'] ?? ''),
                    'size'               => trim($csvRow['SIZE'] ?? ''),
                    'color_name'         => trim($csvRow['COLOR_NAME'] ?? ''),
                    'piece_price'        => is_numeric($csvRow['PIECE_PRICE'] ?? null) ? (float) $csvRow['PIECE_PRICE'] : null,
                ];
                
                if (!$productData['unique_key']) continue;
                
                $batch[] = $productData;

                if (count($batch) >= $batchSize) {
                    $this->processBatch($batch, $header);
                    $batch = [];
                }
            }
            // Process remaining records
            if (!empty($batch)) {
                $this->processBatch($batch, $header);
            }

            fclose($handle);

            $this->upload->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);
        } catch (\Exception $e) {
            $this->upload->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->upload->update([
            'status' => 'failed',
        ]);
    }

    protected function processBatch(array $batch, array $header): void
    {
        foreach ($batch as $productData) {
            Product::updateOrCreate(
                ['unique_key' => $productData['unique_key']],
                $productData
            );
            
        }
    }
}
