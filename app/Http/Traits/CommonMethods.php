<?php
namespace App\Http\Traits;

use Vinkla\Hashids\Facades\Hashids;

use Mail;
use Carbon\Carbon;
use DateTimeZone;
use DateTime;

//use App\Brand;

trait CommonMethods {

  public static $AUDEIO_STORAGE = "/storage/uploads/recordings/";
  public static $PROFILE_PIC = "/storage/uploads/profile_pic/";
  public static $JINGLES_STORAGE = "/storage/uploads/jingles/";
  public static $FEEDBACK_EMAIL = "mps@mailinator.com";

    // common function to send email
    public function sendEmail($dataArry=NULL){
        $to = $dataArry['to']; $subject = $dataArry['subject'];$emailBody = $dataArry['content'];
        $files = ( isset( $dataArry['files'] ) && $dataArry['files'] )?$dataArry['files']:[];

        if(isset($dataArry['file']) && isset($dataArry['mime'])){
            $file = $dataArry['file'];
            $mime = $dataArry['mime'];
        }else{
            $file = "";
            $mime = "";
        }
        Mail::send([], [], function($message) use($to, $subject, $emailBody, $files, $file, $mime) {
          $message->setBody($emailBody, 'text/html');
        //   $message->from(\Config::get('constants.from_email'), \Config::get('constants.from_name'));
          $message->from(config('app.mail_from'));
          $message->to($to);
          $message->subject($subject);
          if( $files ) { 
            foreach( $files as $fl ) { 
                $message->attach( $fl[ 'file' ], array('mime' => $fl[ 'mime' ] ) );
            }
          }
          if($file){
            $message->attach($file, array('mime' => $mime));
          }
        });

    }

    public function errorCodes( $index = "" )
    {  
      $codes = [];

      $codes['success'] = 200;
      $codes['failed'] = 404;
      $codes['invalid_otp'] = 201;
      $codes['not_verified'] = 202;
      $codes['already_exists'] = 203;
      $codes['not_found'] = 204;
      $codes['something_went_wrong'] = 205;
      $codes['validation_failed'] = 205;

      $code = $codes['success'];
      if( isset($codes[$index]) )
        $code = $codes[$index];

      return $code;
    }
}