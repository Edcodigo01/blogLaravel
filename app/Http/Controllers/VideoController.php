<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
class VideoController extends Controller
{
    public function index(Request $request){
        if($request->limit != null and $request->limit != 0){
            $videos = Video::orderBy('id','desc')->take($request->limit)->get();
        }else{
            $videos = Video::orderBy('id','desc')->get();
        }

        return response()->json(['videos'=>$videos]);
    }


}
