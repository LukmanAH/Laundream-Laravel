<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\LaundryStoreRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Laundry;
use App\Models\ShippingRate;
use App\Models\Transaction;
use Illuminate\Http\Request;

class LaundryController extends Controller
{
    public function index()
    {
        if(auth()->user()->tokenCan('customerDo')){
            $transactions = Transaction::query()
                ->with(['laundry', 'catalog', 'parfume', 'user'])
                ->where('user_id', auth()->id())
                ->get();

            return TransactionResource::collection($transactions);
        }

        return response()->json("Permintaan ditolak");
    }

    public function store(LaundryStoreRequest $laundryStoreRequest, Laundry $laundry)
    {
        if(auth()->user()->tokenCan('customerDo')){
            $transaction = Transaction::query()
                ->whereBelongsTo($laundry)
                ->whereDate('created_at', today()->setTimezone('Asia/Jakarta')->toDateTimeString())
                ->get();

            $now = now()->setTimezone('Asia/Jakarta');

            $transaction = Transaction::create(array_merge($laundryStoreRequest->validated(), [
                'laundry_id' => $laundry->id,
                'user_id' => auth()->id(),
                'serial' => "TRX/" . $now->year . $now->month . $now->day . "/" . str_pad($transaction->count(), 3, 0, STR_PAD_LEFT)
            ]));

            if ($laundryStoreRequest->service_type == '2') {
                $distance = $laundry->query()
                    ->nearestTo($laundryStoreRequest->lat, $laundryStoreRequest->lng);

                $shippingRate = ShippingRate::query()
                    ->whereBelongsTo($laundry)
                    ->where('initial_km', '>=', $distance->distance)
                    ->where('final_km', '<=', $distance)
                    ->first();

                if ($shippingRate) {
                    $transaction->update([
                        'delivery_fee' => $shippingRate->price
                    ]);
                }
            }

            return TransactionResource::make($transaction);
        }

        return response()->json("Permintaan ditolak");
    }
}
