<?php

namespace App\Http\Controllers\Core;

use App\Models\Province;
use App\Models\City;
use Illuminate\Http\Request;

class LocationController extends BaseController
{
    public function provinces($countryId)
    {
        $provinces = Province::where('country_id', $countryId)->orderBy('name')->get();
        return response()->json($provinces);
    }


}