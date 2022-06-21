<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InformationStoreRequest;
use App\Models\Informations;
use App\Models\User;
use App\Models\Parfume;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
      $info = Informations::with('user')
      ->get();

     return view('pages.information.index', compact('info'));
    }


    public function create()
    {
        return view('pages.information.create');
    }


    public function store(InformationStoreRequest $InformationStoreRequest)
    {
        $info = Informations::create([
            'title' => $InformationStoreRequest->title,
            'description' => $InformationStoreRequest->description,
            'user_id' =>  auth()->id(),
            'status' => Informations::STATUS_ACTIVE
        ]);

        return redirect()->route('admin.informations.index');
    }

    
    public function status(Request $request, Informations $information)
    {
        $information->update(['status' => $request->status]);

        return redirect()->route('admin.informations.index');
    }

    public function destroy(Informations $information)
    {
        Informations::find($information->id)->delete();

        return redirect()->route('admin.informations.index');
    }

}
