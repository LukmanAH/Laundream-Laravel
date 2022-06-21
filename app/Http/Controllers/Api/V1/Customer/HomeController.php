<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\LaundryResource;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Auth;

use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    public function index()
    {
        if(auth()->user()->tokenCan('customerDo')){
            $laundries = Laundry::query()
            ->with(['catalogs', 'parfumes', 'operationalHour', 'shippingRates'])
            ->where('status', Laundry::STATUS_ACTIVE)
            ->nearestTo(request('lat'), request('lng'))
            ->take(5)
            ->get();

            return LaundryResource::collection($laundries);
        }

        return response()->json("Permintaan ditolak");
       
    }
}
