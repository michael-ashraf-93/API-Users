<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{


    use AuthenticatesUsers;
    private $jwtauth;
    private $user;


    public function __construct(User $user, JWTAuth $jwtauth)
    {
        $this->jwtauth = $jwtauth;
        $this->user = $user;
        $this->middleware('jwt.auth', ['except' => ['login', 'newUserWizard', 'registerInvitedUsers']]);
    }

    public function newUserWizard(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'photo' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }
        else {
            if ($request->hasfile('photo')) {
//            $data = base64_encode($request->input('photo'));
                $array1 = str_replace(' ', '', $request->file('photo')->getClientOriginalName());
                $check = explode('.', $array1);
                if (end($check) == 'png' || end($check) == 'jpg' ||
                    end($check) == 'PNG' || end($check) == 'JPG') {
                    if (end($check) == 'png' || end($check) == 'PNG'){
                    $file = base64_encode($array1) . '.png';
                    }
                    elseif (end($check) == 'jpg' || end($check) == 'JPG'){
                    $file = base64_encode($array1) . '.jpg';
                    }
                    $destination = base_path('/public') . '/uploads/files/';
                    $request->file('photo')->move($destination, $file);
                }
                else {
                    $returnData = array(
                        'error' => 'Only PNG and JPG Photos Are Allowed!',
                    );
                    return response()->json($returnData);
                }
            }

            $newUser = $this->user->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'photo' => $file,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);


            if (!$newUser) {
                return response()->json([trans('api.failed_to_create_new_user')], 500);
            }
            return response()
                ->json([
                    'token' => $this->jwtauth->fromUser($newUser),
                    'user' => $newUser
                ]);
        }
    }

    public function login(Request $request)
    {
        // get user credentials: email, password
        $credentials = $request->only('email', 'password');
        $token = null;
        try {
            $token = $this->jwtauth->attempt($credentials);
            if (!$token) {
                return $this->sendError('Invalid email or password', 400);
            }
        } catch (JWTAuthException $e) {
            return $this->sendError('Failed to create token', 500);

        }
//        return response()->json(compact('token', 'user'));
        return response()->json(compact("token", "user"));

    }


    public function logout()
    {
        $this->jwtauth->invalidate($this->jwtauth->getToken());
        $returnData = array(
            'success' => 'Come back soon :)',
        );
        return response()->json($returnData);
    }


}

