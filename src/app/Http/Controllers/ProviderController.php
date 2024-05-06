<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function providers()
    {
        $providers = Provider::all();

        return response()->json($providers, 200);
    }
}
