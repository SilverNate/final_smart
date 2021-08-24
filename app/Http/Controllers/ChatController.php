<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher;

class ChatController extends Controller
{

    public function chat(){
        return view('chat');
    }

    public function send(Request $request)
    {
        $user = User::find(Auth::id());

        event(new ChatEvent($request->message, $user));
    }
}
