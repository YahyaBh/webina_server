<?php

namespace App\Http\Controllers;

use App\Models\Website_status;

class WebSiteStatusController extends Controller
{




    public function index()
    {
        $status = Website_status::first();

        return response()->json([
            'status' => $status->status
        ], 200);
    }

    public function setStatus()
    {

        $status = Website_status::get()->first();


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
