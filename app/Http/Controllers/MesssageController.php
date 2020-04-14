<?php

namespace App\Http\Controllers;

use App\Events\MessagePosted;
use App\Messsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class MesssageController extends Controller
{
    public function index()
    {
        if ($messages = Redis::get('messages.all')) {
            return json_decode($messages);
        }
        $messages = Messsage::with('user')->get();
        Redis::set('messages.all', $messages);

        return view('welcome');
    }

    public function store()
    {
        $user = Auth::user();
        $message = Messsage::create(['message'=> request()->get('message'), 'user_id' => $user->id]);
        broadcast(new MessagePosted($message, $user))->toOthers();

        return $message;
    }
}
