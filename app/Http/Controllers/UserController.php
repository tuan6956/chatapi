<?php

namespace App\Http\Controllers;

use App\User;
use App\Chatkit;
use Illuminate\Http\Request;
use Image;

class UserController extends Controller
{
    /**
     * Create a new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chatkit $chatkit
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, Chatkit $chatkit)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $data['chatkit_id'] = str_slug($data['email'], '_');

        $userData = [
            'id' => $data['chatkit_id'],
            'name' => $data['name']
        ];

        $response = $chatkit->createUser($userData);

        if ($response['status'] !== 201) {
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(User::create($data));
    }
	public function updateInfo(Request $request, Chatkit $chatkit){
        $user = request()->user();
        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(300, 300)->save(public_path('uploads/avatars/'.$filename ) );
   
            $user->avatar_url = 'http://103.207.38.142/chatapi/public/uploads/avatars/'.$filename;
            $user->save();
            //$response = $chatkit->updateUser($userData);
            return response()->json($user);
  
        }
        else{
            return response()->json(['status' => 'error'], 400);
        }

    }
}
