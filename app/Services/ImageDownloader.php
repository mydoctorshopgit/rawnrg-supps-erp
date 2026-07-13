<?php
// app/Services/ImageDownloader.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageDownloader
{
    public function download($url, $model)
    {
        $tries = 0;
        $maxTries = 3;
        
        do {
            try {
                $tempFile = tempnam(sys_get_temp_dir(), 'laravel_download_');
                
                $response = Http::withOptions([
                        'verify' => false,
                        'timeout' => 30,
                        'sink' => $tempFile,
                    ])
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                        'Accept' => 'image/*',
                    ])
                    ->get($url);
                
                if ($response->successful()) {
                    $filename = 'uploads/all/' . Str::random(40) . '.' . pathinfo($url, PATHINFO_EXTENSION);
                    Storage::put($filename, file_get_contents($tempFile));
                    
                    $model->update(['file_name' => $filename]);
                    return true;
                }
            } catch (\Exception $e) {
                // Log error if needed
            } finally {
                if (isset($tempFile) && file_exists($tempFile)) {
                    unlink($tempFile);
                }
            }
            
            $tries++;
            if ($tries < $maxTries) sleep(2 ** $tries); // Exponential backoff
            
        } while ($tries < $maxTries);
        
        return false;
    }
}