<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Models\Laundry;
use App\Models\OperationalHour;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\ValidationException;

class OperationalHourController extends Controller
{
    public function index(Laundry $laundry)
    {
        // throw_if(
        //     auth()->id() != $laundry->user_id || !auth()->user()->tokenCan('ophour.list'),
        //     ValidationException::withMessages(['op_hour' => 'Tidak dapat melihat jam operasional!'])
        // );

        if(auth()->id() == $laundry->user_id && auth()->user()->tokenCan('ownerDo')){
            $operationalHour = OperationalHour::query()
                ->whereBelongsTo($laundry)->get();

            return response()->json($operationalHour);
        }
        return response()->json("Permintaan ditolak");
    }

    public function update(Laundry $laundry, OperationalHour $operationalHour)
    {
        // throw_if(
        //     auth()->id() != $laundry->user_id
        //         || $laundry->id != $employee->laundry_id,
        //     ValidationException::withMessages(['employee' => 'Tidak dapat mengubah karyawan!'])
        // );

        if(auth()->user()->tokenCan('ownerDo') && auth()->id() == $laundry->user_id && $laundry->id == $operationalHour->laundry_id){
            $validator = Validator::make(request()->all(),[
                'open' => 'required',
                'close' => 'required',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }

            $operationalHour->update(request()->all());
          

            return $operationalHour;
        }
        return response()->json("Permintaan ditolak");
    }
}
