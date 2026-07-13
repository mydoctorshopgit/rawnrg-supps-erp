<?php

namespace App\Models;

use App\Models\ProductStock;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, ToModel
{
    private $rows = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $productStock = null;

            // Try to find ProductStock by product_code
            if (!empty($row['product_code'])) {
                $productStock = ProductStock::where('sku', $row['product_code'])->first();
            }

            // If not found, try pip_code
            if (!$productStock && !empty($row['pip_code'])) {
                $productStock = ProductStock::where('pip_code', $row['pip_code'])->first();
            }

            // If product stock exists, update the thumbnail
            if ($productStock) {
                $productStock->thumbnail_img = $this->downloadThumbnail($row['thumbnails'] ?? '');
                $productStock->save();
            }
        }

        flash('Product stock entries imported successfully')->success();
    }

    public function model(array $row)
    {
        ++$this->rows;
        return null; // Required to fulfill ToModel interface
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function rules(): array
    {
        return []; // Add validation rules if needed
    }

    public function downloadThumbnail($url)
    {
        try {
            if (!$url) return null;

            $upload = new Upload;
            $upload->external_link = $url;
            $upload->type = 'image';
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {
            // Log error if needed
            return null;
        }
    }

    public function downloadGalleryImages($urls)
    {
        $data = [];

        foreach (explode(',', str_replace(' ', '', $urls)) as $url) {
            $id = $this->downloadThumbnail($url);
            if ($id) {
                $data[] = $id;
            }
        }

        return implode(',', $data);
    }
}
