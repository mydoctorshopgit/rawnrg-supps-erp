<?php

namespace App\Console\Commands;

use App\Models\Upload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadImages extends Command
{
    protected $signature = 'download:images';
    protected $description = 'Download external images';

    public function handle()
    {
        $uploads = Upload::where('external_link', '!=', 'N/A')
            ->where('external_link', '!=', '')
            ->whereNotNull('external_link')
            ->whereNull('file_name')
            ->get();
        foreach ($uploads as $upload) {
            $this->info("Trying: {$upload->external_link}");

            $success = $this->downloadImage($upload);
            
            $this->line($success ? '✓ Success' . $upload->id : '✗ Failed', null, $success ? 'info' : 'error');
        }
    }

    protected function downloadImage($upload)
    {
        try {

            $tempFile = tempnam(sys_get_temp_dir(), 'download_');
            
            Http::withOptions([
                    'verify' => false,
                    'timeout' => 30,
                    'sink' => $tempFile,
                ])
                ->get($upload->external_link);

            if (filesize($tempFile) > 0) {
                $filename = 'uploads/all/' . Str::random(40) . '.jpg';
                Storage::put($filename, file_get_contents($tempFile));
                $upload->update(['file_name' => $filename]);
                return true;
            }
        } catch (\Exception $e) {
            // Silent fail - we'll try again
        } finally {
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
        
        return false;
    }
}