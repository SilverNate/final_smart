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

class UserController extends Controller
{
    /**
     * have param search : teacher, student, all
     */
    public function teacherView(Request $request)
    {

        $roles  = $request->user('api')->role_id;
        $params = strtolower($request->param);

        if($roles == Config::get('constants.roles_id.teacher')){

            if($params == "all" || $params == ""){
                $getAllUser = ModelsUser::where('role_id', '<>', '1')->get();

                return response()->json([
                    'status' => ['code' => 200, "response" => "Success", "message" => "Successfully get aall data user."],
                    'results' => $getAllUser,
                ]);

            }

            if($params == "teacher"){
                $getTeacher = ModelsUser::where('role', $params)->get();

                if(empty($getTeacher)){
                    return response()
                    ->json(['message' => 'Sorry, data not found.'], 400);
                }

                return response()->json([
                    'status' => ['code' => 200, "response" => "Success", "message" => "Successfully get aall data teacher."],
                    'results' => $getTeacher,
                ]);
            }

            if($params == "student"){

                $getStudent = ModelsUser::where('role', $params)->get();

                if(empty($getStudent)){
                    return response()
                    ->json(['message' => 'Sorry, data not found.'], 400);
                }

                return response()->json([
                    'status' => ['code' => 200, "response" => "Success", "message" => "Successfully get all data teacher."],
                    'results' => $getStudent,
                ]);
            }

        } else {
            return response()
                ->json(['message' => 'Sorry, just teacher can see this.'], 400);
        }
    }

    public function studentView(Request $request)
    {
        $roles  = $request->user('api')->role_id;
        $params = strtolower($request->param);

        if($roles == Config::get('constants.roles_id.student')){

            if($params == "student" || $params == ""){

                $getStudent = ModelsUser::where('role', 'student')->get();

                if(empty($getStudent)){
                    return response()
                    ->json(['message' => 'Sorry, data not found.'], 400);
                }

                return response()->json([
                    'status' => ['code' => 200, "response" => "Success", "message" => "Successfully get all data student."],
                    'results' => $getStudent,
                ]);
            } else {

                return response()
                ->json(['message' => 'Sorry, please check your request.'], 400);
            }
        } else {

            return response()
            ->json(['message' => 'Sorry, just studennt can see this.'], 400);
        }
    }
}
