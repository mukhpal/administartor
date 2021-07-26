<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Http\Traits\CommonMethods;
use Illuminate\Support\Facades\DB;
// use Mail;

class AuthController extends Controller
{
    use CommonMethods;
    
    public function register(Request $request)
    {

        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        $rules = [
            'email' => 'required|email|unique:users,email', 
            'first_name' => 'required', 
            'password' => 'required|min:6'
        ];

        $validation = \Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        if( !isset($request->last_name) || empty($request->last_name) ){
            $request->last_name = '';
        }

        $otp = mt_rand(1000,9999);

        $user = User::create([
            'first_name' => $request->first_name, 
            'last_name' => $request->last_name, 
            'email' => $request->email, 
            'password' => bcrypt($request->password),
            'otp'   =>  $otp
        ]);

        $emailParams = array(
                "to"=>$request->email, 
                "subject"=>'Account verification otp.', 
                "content"=>' One time password : '.$otp
            );

        $this->sendEmail($emailParams);

        $return = [
            'message' => "User registered successfully",
            "code"  =>  $this->errorCodes("success"),
            "data"  =>  $user
        ];

        return response()->json($return, 200);
    }

    public function sendEmailOTP(Request $request)
    {
        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        $rules = [
            'email' => 'required|email|exists:users,email'
        ];

        $validation = \Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $otp = mt_rand(1000,9999);

        $affected = DB::table('users')
              ->where('email', $request->email)
              ->update(['otp' => $otp]);

        $emailParams = array(
            "to"=>$request->email, 
            "subject"=>'Email verifications otp.', 
            "content"=>' One time password : '.$otp
        );

        $this->sendEmail($emailParams);

        $return = [
            'message' => "OTP sent successfully on email",
            "code"  =>  $this->errorCodes("success"),
        ];

        return response()->json($return, 200);
    }

    public function verifyOTP(Request $request)
    {
        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        $rules = [
            'email' => 'required|email|exists:users,email',
            'otp'   =>  'required|min:4'
        ];

        $validation = \Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $user = User::where(['email'=> $request->email, 'otp' => $request->otp ])->first();
        
        if(!$user){
            $return['message']  =   "Invalid OTP";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $user->otp = null;
        $user->email_verified = 1;
        $user->save();

        $return = [
            'message' => "OTP verified succesfully.",
            "code"  =>  $this->errorCodes("success")
        ];

        return response()->json($return, $this->errorCodes("success"));
    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users,email',
            'password'   =>  'required|min:6'
        ];

        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        $validation = \Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $user = User::where(['email'=> $request->email])->first();

        if(!$user){
            $return['message']  =    "User not found";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $user->password = bcrypt($request->password);
        $user->save();

        $return = [
            "code"  =>  $this->errorCodes("success"),
            'message' => "Password updated succesfully."
        ];

        return response()->json($return, $this->errorCodes("success"));
    }
    
    public function login(Request $request)
    {
        $social_id  =   $social_type    =   null;

        if( isset($request->social_id) && !empty($request->social_id) ){
            $social_id = $request->social_id;

            if( isset($request->social_type) && !empty($request->social_type) ){
                $social_type = $request->social_type;

                $userEmail = User::where(['email'=> $request->email ])->first();

                if($userEmail){
                    if( $social_id  != $userEmail->social_id ){
                        $userSocialId = User::where([ 'social_id' => $social_id ])->first();

                        if($userSocialId){
                            return response()->json([
                                'message'=> 'This social id is already exists with another email',
                                'status'    =>  $this->errorCodes("failed")
                            ], $this->errorCodes("failed"));
                        }else{
                            $userEmail->social_id = $social_id;
                            $userEmail->social_type = $social_type;
                            $userEmail->save();
                        }
                    }
                }else{
                    $userSocialId = User::where([ 'social_id' => $social_id ])->first();

                    $rules = [
                        'email' => 'required|email',
                        'name'  => 'required'
                    ];
            
                    $return = [ 'code'   =>  $this->errorCodes("failed") ];

                    $validation = \Validator::make($request->all(), $rules);
                    if ($validation->fails()) {
                        $return['message']  =   $validation->messages()->all()[0];
                        return response()->json( $return , $this->errorCodes("failed") );
                    }

                    if($userSocialId){
                        $userSocialId->email = $request->email;
                        $userSocialId->save();
                    }else{
                        //create this account
                        $newUser = User::create([
                            'first_name' => $request->name, 
                            'social_id' => $social_id, 
                            'email' => $request->email, 
                            'email_verified' => 1, 
                            'password' => null,
                            'social_type'   =>  $social_type
                        ]);
                    }
                }

                $user = User::where([ 'social_id' => $social_id ])->first();

                if($user){

                    $token = $user->createToken($user->email.'-'.now());

                    return response()->json([
                        'message'   =>  'Succesfully logged in',
                        'code'      =>  $this->errorCodes("success"),
                        'token' => $token->accessToken,
                        'data'    =>  $user
                    ]);

                }else{
                    return response()->json([
                        'message'=> 'Something went wrong',
                        'status'    =>  $this->errorCodes("failed")
                    ], $this->errorCodes("failed"));
                }

            }
        }

        $rules = [
            'email' => 'required|email|exists:users,email', 
            'password' => 'required'
        ];

        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        $validation = \Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $credentials = request(['email', 'password']);

        if( Auth::attempt( $credentials ) ) {
            $user = Auth::user();

            $token = $user->createToken($user->email.'-'.now());
            $userId = $user->id;

            if(!$user->email_verified){
                return response()->json([
                    "code"  =>  $this->errorCodes("not_verified"),
                    'message'=> 'Account email not verified',
                    'status'    =>  $this->errorCodes("not_verified")
                ], $this->errorCodes("failed"),);
            }

            return response()->json([
                'code'      =>  $this->errorCodes("success"),
                'message'   =>  'Succesfully logged in',
                'token' => $token->accessToken,
                'data'    =>  $user
            ]);

        }else{
            return response()->json([
                'code'      =>  $this->errorCodes("failed"),
                'message'=> 'Invalid email or password'
            ], $this->errorCodes("failed"));
        }
    }

    public function logout(Request $request){
        $request->user()->token()->revoke(); 
        return response()->json([
            "message"=>"User logged out successfully"
        ], 200);
    }
}
