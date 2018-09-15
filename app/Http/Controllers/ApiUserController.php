<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;



class ApiUserController extends Controller
{
    private $jwtauth;

    public function __construct(JWTAuth $jwtauth)

    {

        $this->jwtauth = $jwtauth;
        $this->middleware('jwt.auth')->except('index','show');
    }

////////////////////    Show All Users Only DESC     ///////////////////////

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

////////////////////    Show User With All Comments    ////////////////////

    public function show($id)
    {
        $user = User::find($id);
        if ($user){
            $photo = base64_decode($user->photo);
            $returnData = array(
                'photo base64_decode' => $photo,
            );
        }

        return response()->json([$user,$returnData]);
    }

////////////////////    Store New User     ////////////////////

    public function create(Request $request)
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


            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'photo' => $file,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);


            return response()->json($user);
        }
    }

////////////////////    Edit User     ////////////////////

    public function update($id, Request $request)
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

            $user = User::find($id);
            if ($user) {
                $user->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'photo' => $file,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);
                $returnData = array(
                    'success' => 'User Updated Successfully!'
                );
                return response()->json([$user,$returnData]);
            }
            else{
                $returnData = array(
                    'error' => 'User Not Found!'
                );
                return response()->json($returnData, 500);
            }
        }
    }

////////////////////    Delete User     ////////////////////

     public function delete($id)
     {
         $user = User::find($id);
         if ($user) {
             $user->delete($id);

             $returnData = array(
                 'success' => 'User Deleted Successfully!'
             );
             return response()->json($returnData, 200);
         }
         else {
             $returnData = array(
                 'error' => 'User Not Found!'
             );
             return response()->json($returnData, 500);
         }
     }
}