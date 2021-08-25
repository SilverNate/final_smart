<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Http\Controllers\Auth\AuthController;

class ManageController extends Controller
{
    public function adminView(Request $request)
    {

        $roles = $request->user('api')->role_id;
        $emails = $request->user('api')->email;
        $schoolId = $request->user('api')->school_id;
        $role = Config::get("constants.roles.$roles");

        if($role == "school_admin"){

            $user = ModelsUser::where('email', '<>', $emails)
                                ->where('school_id', '=',$schoolId)->get();

            if(empty($user)){
                return response()->json([
                    'status' => ['code' => 400, "response" => "Failed", "message" => "Failed to get all data user."]
                ]);
            }

            return response()->json([
                'status' => ['code' => 200, "response" => "Success", "message" => "Success get all user."],
                'results' => $user,
            ]);

        } else if($role == "teacher"){

            $user = ModelsUser::where('role','<>', 'school_admin')
                                ->where('school_id', '=',$schoolId)->get();

            if(empty($user)){
                return response()->json([
                    'status' => ['code' => 400, "response" => "Failed", "message" => "Failed to get all data user."]
                ]);
            }

            return response()->json([
                'status' => ['code' => 200, "response" => "Success", "message" => "Success get all user."],
                'results' => $user,
            ]);

        } else if($role == "student"){
            $user = ModelsUser::where('role','=', 'student')
                                ->where('school_id', '=',$schoolId)->get();

            if(empty($user)){
                return response()->json([
                    'status' => ['code' => 400, "response" => "Failed", "message" => "Failed to get all data user."]
                ]);
            }

            return response()->json([
                'status' => ['code' => 200, "response" => "Success", "message" => "Success get all user."],
                'results' => $user,
            ]);

        } else {

            return response()->json([
                'status' => ['code' => 400, "response" => "Failed", "message" => "Failed you are not school admin."]
            ]);
        }

    }

    public function adminAdd(Request $request)
    {
        if(AuthController::isAdmin($request) == true){

            $schoolIds  = $request->user('api')->school_id;
            $schoolName = $request->user('api')->school_name;
            $password   = bcrypt("password");


            $validator = Validator::make(
                ['username' => $request->username], ['username' => 'required|string'],
                ['email' => $request->email], ['email' => 'required|string|email|unique:users'],
                ['role' => $request->role], ['role' => 'required|string']);

                if ($validator->fails())
                {
                    return response()
                        ->json(['errors' => $validator->errors() ], 406);
                }

            $checkUser = ModelsUser::where('email', $request->email)->first();

            if(!empty($checkUser)){
                return response()
                ->json(['message' => 'E-mails student/teacher already used.'], 400);
            }

            $user = new ModelsUser();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->role_id = $request->role;
            $user->role = Config::get("constants.roles.{$request->role}");
            $user->school_id = $schoolIds;
            $user->school_name = $schoolName;
            $user->password = $password;
            $user->save();

            return response()->json([
                'status' => ['code' => 201, "response" => "Success", "message" => "Successfully created user teacher or student."],
                'results' => $user,
            ]);


        } else {
            return response()->json([
                'status' => ['code' => 400, "response" => "Failed", "message" => "Failed you are not school admin."]
            ]);
        }
    }

    public function adminDelete(Request $request)
    {
        if(AuthController::isAdmin($request) == true){

            //get param id user to delete
            $deletedIds = $request->id;

            $checkIds = ModelsUser::find($deletedIds);

            if(empty($checkIds)){
                return response()->json([
                    'status' => ['code' => 400, "response" => "Error", "message" => "No such id."]
                ]);
            }

            $checkIds->delete();

            return response()->json([
                'status' => ['code' => 200, "response" => "Success", "message" => "Successfully deleted user."],
                'results' => $checkIds,
            ]);

        } else {
            return response()->json([
                'status' => ['code' => 400, "response" => "Failed", "message" => "Failed you are not school admin."]
            ]);
        }
    }
}
