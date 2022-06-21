<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InformationResource;
use App\Models\Informations;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
        $info = Informations::query()
            ->where('status', Informations::STATUS_ACTIVE)
            ->get();
        return InformationResource::collection($info);
    }
}