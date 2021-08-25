<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Notification;
use App\Notifications\MailNotification;

class AuthController extends Controller
{

    public static function isAdmin(Request $request): bool
    {

        try {
            // Validate the value..
            $roles = $request->user('api')->role_id;

            if($roles == Config::get('constants.roles_id.school_admin')){
                return true;
            }
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    public static function isTeacher(Request $request):bool
    {
        try {

            $roles = $request->user('api')->role_id;

            if($roles == Config::get('constants.roles_id.teacher')){
                return true;
            }
        }catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    public static function isStudent(Request $request):bool
    {
        try {

            $roles = $request->user('api')->role_id;

            if($roles == Config::get('constants.roles_id.student')){
                return true;
            }
        }catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|string|email', 'password' => 'required|string']);

        $validator = Validator::make(
            ['password' => $request->password], ['password' => 'required|string'], ['email' => $request->email], ['email' => 'required|string|email|unique:users']);

            if ($validator->fails())
            {
                return response()
                    ->json(['errors' => $validator->errors() ], 406);
            }

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) return response()->json(['message' => 'Unauthorized'], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) $token->expires_at = Carbon::now()
            ->addWeeks(1);
        $token->save();

        return response()
            ->json(['access_token' => $tokenResult->accessToken, 'token_type' => 'Bearer', 'expires_at' => Carbon::parse($tokenResult
            ->token
            ->expires_at)
            ->toDateTimeString(),
            'email' => $user->email,
            'id' => $user->id,
            'role' => $user->role ]);
    }

    public function registerAdmin(Request $request)
    {
        $validator = Validator::make(
        ['school_name' => $request->school_name], ['school_name' => 'required|string'], ['email' => $request->email], ['email' => 'required|string|email|unique:users'], ['password' => $request->password], ['password' => 'required|string']);

        if ($validator->fails())
        {
            return response()
                ->json(['errors' => $validator->errors() ], 406);
        }

        $check = ModelsUser::where('email', $request->email)->first();

        if(!empty($check)){
            return response()
            ->json(['message' => 'E-mails already used.'], 400);
        }

        $user = new ModelsUser();
        $user->username = "School Admin";
        $user->school_name = $request->school_name;
        $user->school_id = mt_rand(1,9999);
        $user->email = $request->email;
        $user->role_id = Config::get('constants.roles_id.school_admin');
        $user->role = Config::get('constants.roles.1');
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            'status' => ['code' => 201, "response" => "Success", "message" => "Successfully created user admin."],
            'results' => $user,
        ]);
    }

    public function registerUser(Request $request)
    {
        $roles = $request->user('api')->role_id;
        $schoolIds = $request->user('api')->school_id;
        $schoolName =  $request->user('api')->school_name;

        // echo $schoolIds;die;

        if($roles == Config::get('constants.roles_id.school_admin')){

            $validator = Validator::make(
                ['username' => $request->username], ['username' => 'required|string'],
                ['email' => $request->email], ['email' => 'required|string|email|unique:users'],
                ['role' => $request->role], ['role' => 'required|string']);

                if ($validator->fails())
                {
                    return response()
                        ->json(['errors' => $validator->errors() ], 406);
                }

            $check = ModelsUser::where('email', $request->email)->first();

            if(!empty($check)){
                    return response()
                    ->json(['message' => 'E-mails student/teacher already used.'], 400);
            }

            //create random str for pass
            $password = bin2hex(openssl_random_pseudo_bytes(4));

            $user = new ModelsUser();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->role_id = $request->role;
            $user->role = Config::get("constants.roles.{$request->role}");
            $user->school_id = $schoolIds;
            $user->school_name = $schoolName;
            $user->password = bcrypt($password);
            $user->save();

            /**
             * if success we should sent email data to user
             *
             **/

            $user = ModelsUser::where('email', $user->email)->get();

            $details = [
                'greeting' => 'Hi guys',
                'body' => "This is E-mail Notification just because you already invited to smart school system",
                'body1'=> "Here your credential for login:",
                'body2'=> "username : $request->username",
                'body3'=> "password : $password",
                'body4' => "school_id : $schoolIds",
                'body5' => "Please keep this credentials just for you",
                'thanks' => 'Thank you!',
                'actionText' => 'View My Site',
                'actionURL' => env('APP_URL'),
                'order_id' => $request->username,
            ];

            Notification::send($user, new MailNotification($details));

            return response()->json([
                'status' => ['code' => 201, "response" => "Success", "message" => "Successfully created user teacher or student."],
                'results' => $user,
            ]);
        }

        return response()
                ->json(['message' => 'Please contact your school admin to register.'], 400);
    }

    public function logout(Request $request)
    {
        $request->user()
            ->token()
            ->revoke();
        return response()
            ->json(['message' => 'Successfully logged out', ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function loginUser(Request $request)
    {

        $request->validate(['school_id' => 'required|string', 'username' => 'required|string', 'password' => 'required|string']);

        $validator = Validator::make(
            ['password' => $request->password], ['password' => 'required|string'],
            ['school_id' => $request->school_id], ['email' => 'required|string'],
            ['username' => $request->username], ['username' => 'required|string'] );

            if ($validator->fails())
            {
                return response()
                    ->json(['errors' => $validator->errors() ], 406);
            }

        //find email
        $getEmails = ModelsUser::where([
                                    'username'=> $request->username,
                                    'school_id' =>$request->school_id])
                                    ->get();

        // echo json_encode($getEmails);die;
        $setEmails = [];
        if(count($getEmails) > 0){
            foreach($getEmails as $k=>$v){
                $testAttempt = ['email' => $v['email'], 'password' => $request->password];
                if(Auth::attempt($testAttempt) == true){
                        array_push($setEmails, $v['email']);
                }
            }
        }

        if(count($setEmails) < 1 && empty($setEmails))
        {
            return response()->json([
                'status' => ['code' => 400, "response" => "Error", "message" =>"credential wrongs"],
            ]);
        }

        $credentials = ['email' => $setEmails[0], 'password' => $request->password];

        if (!Auth::attempt($credentials)) return response()->json(['message' => 'Unauthorized user'], 401);

        if (Auth::check()) {
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;

            if ($request->remember_me) $token->expires_at = Carbon::now()
                ->addWeeks(1);

            $token->save();

            return response()
                ->json(['access_token' => $tokenResult->accessToken, 'token_type' => 'Bearer', 'expires_at' => Carbon::parse($tokenResult
                ->token
                ->expires_at)
                ->toDateTimeString(),
                'email' => $user->email,
                'id' => $user->id,
                'role' => $user->role ]);
        }
    }
}

