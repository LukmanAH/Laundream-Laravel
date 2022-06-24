<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InformationStoreRequest;
use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
        $info = Information::with('user')
            ->get();

        return view('pages.information.index', compact('info'));
    }


    public function create()
    {
        return view('pages.information.create');
    }


    public function store(InformationStoreRequest $informationStoreRequest)
    {
        $info = Information::create([
            'title' => $informationStoreRequest->title,
            'description' => $informationStoreRequest->description,
            'user_id' =>  auth()->id(),
            'status' => Information::STATUS_ACTIVE
        ]);

        if ($informationStoreRequest->hasFile('picture')) {
            $file = $informationStoreRequest->file('picture')->getClientOriginalName();

            $path = $informationStoreRequest->file('picture')
                ->storeAs(
                    'laundream/info',
                    now()->timestamp . $file,
                    's3'
                );

            $info->update(['picture' => config('filesystems.path') . $path]);
        }

        return redirect()->route('admin.informations.index');
    }

    public function edit(Information $information)
    {
        return view('pages.information.edit', compact('information'));
    }


    public function status(Request $request, Information $information)
    {
        $information->update(['status' => $request->status]);

        return redirect()->route('admin.informations.index');
    }

    public function destroy(Information $information)
    {
        $information->delete();

        return redirect()->route('admin.informations.index');
    }
}
