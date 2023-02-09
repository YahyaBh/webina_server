<?php

namespace App\Http\Controllers;

use App\Mail\MarketingMail as MailMarketingMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MarketingMail extends Controller
{
    public function sendEmail(Request $request)

    {

        $users = User::all();



        foreach ($users as $user) {

            Mail::to($user->email)->send(new MailMarketingMail($user, $request->subject, $request->content));
        }



        return response()->json(['success' => 'Send emails successfully.']);
    }
}
