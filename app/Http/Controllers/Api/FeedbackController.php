<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Feedbacks;
use App\Models\User;

use App\Http\Traits\CommonMethods;

class FeedbackController extends Controller
{
    use CommonMethods;

    public function userFeedback( Request $request )
    {
        $user = Auth::user();
        $return = [ 'code'   =>  $this->errorCodes("failed") ];
        $return['message']  =   "Request failed";

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $rules = [
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string'
        ];

        $validation = \Validator::make($request->all(), $rules);
        
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        //save new feedback
        $feedback = Feedbacks::create([
            'user_id'   =>  $user->id,
            'rate'      =>  $request->rating, 
            'comment'   =>  $request->comment
        ]);

        $emailBody   =  'Hello Team,';
        $emailBody  .=  '<br/><br/><br/>';
        $emailBody  .=   ' Received a new feedback from the app user : <strong>'. $user->first_name. '</strong>';
        $emailBody  .=  '<br/><br/>';
        $emailBody  .=   ' <strong>Rated us</strong>: '.$feedback->rate.' Star';
        $emailBody  .=  '<br/><br/>';
        $emailBody  .=  ' <strong>Review/Comment</strong>: '.$feedback->comment;
        $emailBody  .=  '<br/><br/><br/>';
        $emailBody  .=  'Thanks';

        $adminEmail =   SELF::$FEEDBACK_EMAIL;
        
        $admin  =   User::where('is_admin', 1)->first();
        if( $admin )
            $adminEmail =   $admin->email;

        //email to admin
        $emailParams = array(
            "to"        =>  $adminEmail,
            "subject"   =>  'User feedback.',
            "content"   =>  $emailBody
        );

        $this->sendEmail($emailParams);

        $return['code'] =   $this->errorCodes("success");
        $return['message'] =   "Success";

        //respond with new recording
        return response()->json($return, $this->errorCodes("success"));

    }
}
