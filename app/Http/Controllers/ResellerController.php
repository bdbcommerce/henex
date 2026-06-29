<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Reseller;
use Illuminate\View\View;

class ResellerController extends Controller
{
    public function index(): View
    {
        $resellerCount = cache()->remember('reseller_count', 3600, fn () =>
            Reseller::where('is_active', true)->count()
        );

        $serviceCenterCount = cache()->remember('service_center_count', 3600, fn () =>
            Reseller::where('is_active', true)
                ->whereIn('type', ['service_center', 'both'])
                ->count()
        );

        return view('pages.resellers', compact('resellerCount', 'serviceCenterCount'));
    }
}
