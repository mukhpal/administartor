<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Stripe;

use Config;

use App\Http\Traits\CommonMethods;

class StripeController extends Controller
{
    use CommonMethods;

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function createToken( Request $request )
    {
        $user = Auth::user();
        $return = [ 'code'   =>  $this->errorCodes("failed") ];
        $return['message']  =   "Request failed";

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $rules = [
            'card_number' => 'required|integer|digits:16',
            'exp_month' => 'required|integer|between:1,12',
            'exp_year' => 'required|integer|min:2021|digits:4',
            'cvc' => 'required|integer|digits:3',
        ];

        $validation = \Validator::make($request->all(), $rules);
        
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        \Stripe\Stripe::setApiKey( config('services.stripe.secret') );

        try {
            $token = \Stripe\Token::create(array(
                "card" => array(
                    "number"    =>  $request->card_number,
                    "exp_month" =>  $request->exp_month,
                    "exp_year"  =>  $request->exp_year,
                    "cvc"       =>  $request->cvc,
                )
            ));

            $return['code'] =   $this->errorCodes("success");
            $return['message'] =   "Success";
            $return['data'] =   $token;
            return response()->json($return, $this->errorCodes("success"));

        } catch (\Stripe\Exception\CardException $e){
            // Since it's a decline, \Stripe\Exception\CardException will be caught
            $return['status'] = $e->getHttpStatus();
            $return['type'] = $e->getError()->type . '\n';
            $return['code'] = $e->getError()->code . '\n';
            // param is '' in this case
            $return['param'] = $e->getError()->param . '\n';
            $return['message'] = $e->getError()->message . '\n';
        } catch (\Stripe\Exception\RateLimitException $e) {
        // Too many requests made to the API too quickly
        } catch (\Stripe\Exception\InvalidRequestException $e) {
        // Invalid parameters were supplied to Stripe's API
        } catch (\Stripe\Exception\AuthenticationException $e) {
        // Authentication with Stripe's API failed
        // (maybe you changed API keys recently)
        } catch (\Stripe\Exception\ApiConnectionException $e) {
        // Network communication with Stripe failed
        } catch (\Stripe\Exception\ApiErrorException $e) {
        // Display a very generic error to the user, and maybe send
        // yourself an email
        } catch (Exception $e) {
        // Something else happened, completely unrelated to Stripe
        }

        return response()->json($return, $this->errorCodes("failed"));
    }
   
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePayment( Request $request )
    {
        $success = 0;
        $user = Auth::user();
        $return = [ 'code'   =>  $this->errorCodes("failed") ];
        $return['message']  =   "Request failed";

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $rules = [
            'amount' => 'required|numeric|min:1',
            'token' => 'required|string',
        ];

        $validation = \Validator::make($request->all(), $rules);
        
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        try{
            \Stripe\Stripe::setApiKey( config('services.stripe.secret') );
            $payment    =   Stripe\Charge::create ([
                    "amount"        =>  $request->amount * 100,
                    "currency"      =>  "INR",
                    "source"        =>  $request->token,
                    "description"   =>  "This payment is tested purpose Marrsing Yell"
            ]);

            $return['code'] =   $this->errorCodes("success");
            $return['message'] =   "Success";
            $return['data'] =   $payment;
            $success = 1;
            return response()->json($return, $this->errorCodes("success"));

        } catch(\Stripe\Exception\InvalidRequestException $e) {
            $return['message'] = $e->getMessage();
        } catch (\Stripe\Exception\AuthenticationException $e) {
        // Authentication with Stripe's API failed
        // (maybe you changed API keys recently)
        } catch (\Stripe\Exception\ApiConnectionException $e) {
        // Network communication with Stripe failed
        } catch (\Stripe\Exception\ApiErrorException $e) {
        // Display a very generic error to the user, and maybe send
        // yourself an email
        } catch (Exception $e) {
        // Something else happened, completely unrelated to Stripe
        }
        
        return response()->json($return, $this->errorCodes("failed"));
    }
}
