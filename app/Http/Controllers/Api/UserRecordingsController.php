<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\CommonMethods;

use Illuminate\Support\Facades\Storage;
use App\File;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

use App\Models\Recordings;

class UserRecordingsController extends Controller
{
    use CommonMethods;

    public function saveRecording(Request $request)
    {
        $user = $request->user();
        $user = Auth::user();
        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        //validation on request
        $rules = [
            'name' => 'required', 
            'length' => 'required', 
            'recording' => 'required|file|mimes:audio/mpeg,mpga,mp3,wav,aac'
        ];

        $validation = \Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            $return['message']  =   $validation->messages()->all()[0];
            return response()->json( $return , $this->errorCodes("failed") );
        }

        //save new recording
        $recording = Recordings::create([
            'name' => $request->name, 
            'local_name' => $request->local_name, 
            'length' => $request->length, 
            'user_id' => $user->id,
            'recording' => $request->recording
        ]);

        if($request->hasFile('recording'))
        {
            // $file = $request->file;
            $uniqueid=uniqid();
            $original_name=$request->file('recording')->getClientOriginalName();
            $size=$request->file('recording')->getSize();
            $extension=$request->file('recording')->getClientOriginalExtension();
            $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
            // $audiopath=url(SELF::$AUDEIO_STORAGE.$filename);
            $path=$request->file('recording')->storeAs('public/uploads/recordings/',$filename);
            // $all_audios=$audiopath;

            $recording->recording = $filename;
            $recording->folder = SELF::$AUDEIO_STORAGE;
            $recording->save();
        }

        $return['code'] =   $this->errorCodes("success");
        $return['message'] =   "Recording saved successfully";
        // $return['data'] =   $recording;

        //respond with new recording
        return response()->json($return, $this->errorCodes("success"));
    }

    public function fetchRecordings(Request $request)
    {
        $page = 1;  $recordingsPerPage  =   10;
        $local_name =   $search    =   "";
        $user = $request->user();
        $user = Auth::user();
        $return = [ 'code'   =>  $this->errorCodes("failed") ];

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $where = [  'user_id'   =>  $user->id   ];

        if( isset($request->page) && !empty($request->page) )
            $page   =   $request->page;

        if( isset($request->search) && !empty($request->search) )
            $search   =   $request->search;

        if( isset($request->local_name) && !empty($request->local_name) )
            $local_name   =   $request->local_name;

        //Get the recordings from the DB with filter if any in request
        $recordings =   Recordings::where(  $where  )
                        ->where('local_name', 'LIKE', '%'.$local_name.'%')
                        ->where('name', 'LIKE', '%'.$search.'%')
                        ->get();
        $totalRecordings  =   $recordings->count();
        
        if(!$totalRecordings){
            $return['message']  =   "No records found";
            return response()->json($return, $this->errorCodes("failed"));
        }

        $totalPages =   ceil( $totalRecordings / $recordingsPerPage );
        $skip   =   ($page   -   1) *   $recordingsPerPage;
        $take   =   $recordingsPerPage;

        $pageRecordings =   Recordings::where(  $where  )
                            ->where('local_name', 'LIKE', '%'.$local_name.'%')
                            ->where('name', 'LIKE', '%'.$search.'%')
                            ->skip( $skip   )
                            ->take( $take   )
                            ->get();

        $pageCount  =   $pageRecordings->count();
        if( !$pageCount ){
            $return['message']  =   "No more recordings found";
            return response()->json($return, $this->errorCodes("failed"));
        }

        foreach( $pageRecordings as $key  =>  $value ){
            $pageRecordings[$key]->recording    =   url($value->folder.$value->recording);
        }

        $return['message']  =   "Success";
        $return['code'] =   $this->errorCodes("success");
        $return['totalRecordings'] =   (int)$totalRecordings;
        $return['totalPages'] =   (int)$totalPages;
        $return['page'] =   (int)$page;
        $return['pageCount'] =   (int)$pageCount;
        $return['data'] =   $pageRecordings;
        //respond with the list of recordings
        return response()->json($return, $this->errorCodes("success"));
    }
}
