<?php

namespace App\Http\Controllers;

use App\Models\WebSiteStatus;
use Illuminate\Http\Request;

class WebSiteStatusController extends Controller
{




    public function index()
    {

        $status = WebSiteStatus::et('status')->first();


        return response()->json([
            'status' => $status->status
        ], 200);
    }

    public function setStatus()
    {

        $status = WebSiteStatus::get()->first();


        if ($status->status === 'active') {

            $status->update([
                'status' => 'inactive'
            ]);
        } else {
            $status->update([
                'status' => 'inactive'
            ]);
        }
    }
}
