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

//Models
use App\Models\Jingles;

class JingleController extends Controller
{
    use CommonMethods;

    public function getJingles( Request $request )
    {
        $user = Auth::user();
        $return = [ 'code'   =>  $this->errorCodes("failed") ];
        $return['message']  =   "Request failed";

        if(!$user){
            $return['message']  =   "User not found!";
            return response()->json( $return , $this->errorCodes("failed") );
        }

        $page = 1;  $recordsPerPage  =   2;
        $search_name =   "";
        $where = [  'status'   =>  1   ];

        if( isset($request->page) && !empty($request->page) )
            $page   =   $request->page;

        if( isset($request->search_name) && !empty($request->search_name) )
            $search_name   =   $request->search_name;

        $totalRecords    =   Jingles::where( $where  )
                            ->where('name', 'LIKE', '%'.$search_name.'%')
                            ->count();

        if(!$totalRecords){
            $return['message']  =   "No records found";
            return response()->json($return, $this->errorCodes("failed"));
        }

        $totalPages =   ceil( $totalRecords / $recordsPerPage );
        $skip   =   ($page   -   1) *   $recordsPerPage;
        $take   =   $recordsPerPage;

        $pageRecords =   Jingles::where(  $where  )
                            ->where('name', 'LIKE', '%'.$search_name.'%')
                            ->skip( $skip   )
                            ->take( $take   )
                            ->get();
        
        $pageCount  =   $pageRecords->count();
        if( !$pageCount ){
            $return['message']  =   "No more recordings found";
            $return['totalJingles'] =   (int)$totalRecords;
            $return['totalPages'] =   (int)$totalPages;
            $return['page'] =   (int)$page;
            $return['pageCount'] =   (int)$pageCount;
            return response()->json($return, $this->errorCodes("failed"));
        }

        foreach( $pageRecords as $key  =>  $value ){
            $pageRecords[$key]->jingle    =   url($value->folder.$value->jingle);
        }

        $return = [
            "code"  =>  $this->errorCodes("success"),
            'message' => 'Success!',
            'totalJingles'  =>  (int)$totalRecords,
            'totalPages'    =>  (int)$totalPages,
            'page'          =>  (int)$page,
            'pageCount'     =>  (int)$pageCount,
            'data'  =>  $pageRecords
        ];
        return response()->json($return);
    }
}
