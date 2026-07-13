<?php

namespace App\Http\Controllers\Api\V2;

class BannarController extends Controller
{
    public function getAllBanners()
    {
        return (new HomePageController())->getAllBanners();
    }
}
