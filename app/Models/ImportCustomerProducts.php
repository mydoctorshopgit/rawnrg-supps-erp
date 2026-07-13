<?php

namespace App\Models;

use App\Models\CustomerProductsAssign;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use Storage;

//class ProductsImport implements ToModel, WithHeadingRow, WithValidation
class ImportCustomerProducts implements ToCollection, WithHeadingRow, WithValidation, ToModel
{
    private $rows = 0;
    private $customer_detail_id;

    public function __construct($customer_detail_id)
    {
        $this->customer_detail_id = $customer_detail_id;
    }



    public function collection(Collection $rows)
    {
        $customer_detail_id = $this->customer_detail_id;
        $canImport = true;

        if ($canImport) {
            foreach ($rows as $row) {
                // Find an existing product assignment by product_code and customer_detail_id
                $existingProduct = CustomerProductsAssign::where('customer_detail_id', $customer_detail_id)
                    ->where('product_code', $row['product_code'])
                    ->first();

                if ($existingProduct) {
                    // Update prices for the existing record
                    $existingProduct->update([
                        'unit_price' => $row['unit_price'],
                        'pack_price' => $row['pack_price'],
                    ]);
                } else {
                    $existingProducts = Product::where('product_code', $row['product_code'])->count();
                    if ($existingProducts > 0) {
                        
                    // Create a new entry if it doesn't exist
                        CustomerProductsAssign::create([
                            'customer_detail_id' => $customer_detail_id,
                            'product_code' => $row['product_code'],
                            'products_id' => Product::where('product_code', $row['product_code'])->first()->id,
                            'brand_name' => $row['brand_name'],
                            'nhssc_npc' => $row['nhssc_npc'],
                            'pack_qty' => $row['pack_qty'],
                            'unit_price' => $row['unit_price'],
                            'pack_price' => $row['pack_price'],
                        ]);
                    }

                }
            }

            flash(translate('Imported successfully'))->success();
        }
    }


    public function model(array $row)
    {
        ++$this->rows;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function rules(): array
    {
        return [];
    }
}
