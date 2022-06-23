<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InformationStoreRequest;
use App\Http\Resources\InformationResource;
use App\Http\Resources\InformationResourceAdmin;
use App\Models\Informations;
use Validator;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
        if(auth()->user()->tokenCan('adminDo')){
            $info = Informations::with('user')
            ->get();

            return InformationResourceAdmin::collection($info);
        }
        return response()->json("Permintaan ditolak");
    }

    public function store(Request $informationStoreRequest)
    {
        if(auth()->user()->tokenCan('adminDo')){
            $validator = Validator::make($informationStoreRequest->all(),[
                'title' => 'required|string|max:255',
                'description' => 'required|string|',
                'picture' => 'mimes:jpeg,jpg,png|max:5000|nullable',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }


            $info = Informations::create([
                'title' => $informationStoreRequest->title,
                'description' => $informationStoreRequest->description,
                'user_id' =>  auth()->id(),
                'status' => Informations::STATUS_ACTIVE
            ]);

            if ($informationStoreRequest->hasFile('picture')) {
                $path = $informationStoreRequest->file('picture')->store('image', 's3');

                $info->update([
                    'picture' => $path
                ]);
            }

            return InformationResourceAdmin::make($info);
        }
        return response()->json("Permintaan ditolak");
    }

    
    public function status(Request $request, Informations $information)
    {
        if(auth()->user()->tokenCan('adminDo')){
            $validator = Validator::make($request->all(),[
                'title' => 'required|string|max:255',
                'description' => 'required|string|',
                'status' => 'required',
                'picture' => 'mimes:jpeg,jpg,png|max:5000|nullable',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors());
            }

            $information->update(['status' => $request->status, 'title'=> $request->title, 'description' =>$request->description]);


          return response()->json($information);;
          }
        return response()->json("Permintaan ditolak");
    }

    public function destroy(Informations $information)
    {
        if(auth()->user()->tokenCan('adminDo')){
            Informations::find($information->id)->delete();
            return response()->json("Berhasil Dihapus");
        }
        return response()->json("Permintaan ditolak");
    }

}
