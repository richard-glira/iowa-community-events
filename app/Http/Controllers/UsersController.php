<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;

class UsersController extends Controller {
    
    public function fetchUserInfo($id) {
        $status = 400;
        $message = 'Bad Request';

        $user = User::find($id);

        if ($user !== null) {
            $status = 200;
            $message = 'OK';
        } else {
            $status = 404;
            $message = 'Not Found';
        }
        
        return response()->json([
            'status' => $status,
            'message' => $message,
            'payload' => $user
        ]);

        // return $user;    
    }
}
