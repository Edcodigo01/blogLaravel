<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Video;
class VideoController extends Controller
{
    public function index(Request $request){
        $videos = Video::orderBy('id','desc')->get();
        return response()->json($videos);
    }

    public function edit($id){
        $video = Video::find($id);
        return response()->json($video);
    }

    public function create(){

    }

    public function store(Request $request){
        $regex = '/^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/';

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:4|max:100|unique:videos',
            'url'=>[ 'required','max:200','regex:'.$regex],
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'errors' => $validator->errors()]);
        }

        $id_url = $request->url;
        if (strpos($id_url, 'www.youtube.com/watch?v=')) {
            $id_url = str_replace('https://www.youtube.com/watch?v=','', $id_url);
        }else{
            $id_url = str_replace('https://youtu.be/','', $id_url);
        }
        // ubica la posicion de & para cortar parametros extras si existen
        $id_urlPos = strpos($id_url,'&list');
        if($id_urlPos){
            $id_url = mb_substr($id_url, 0, $id_urlPos);
        }

        // www.youtube.com/embed/
        $video = new Video;
        $video->title = $request->title;
        $video->url = $request->url;
        $video->id_video = $id_url;
        $video->save();

        return response()->json(['result' => 'success','message'=>'Video agregado con éxito.']);
    }

    public function update(Request $request){
        $regex = '/^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/';

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:4|max:100|unique:videos,id,'.$request->id,
            'url'=>[ 'required','max:200','regex:'.$regex],
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'errors' => $validator->errors()]);
        }

        $id_url = $request->url;
        if (strpos($id_url, 'www.youtube.com/watch?v=')) {
            $id_url = str_replace('https://www.youtube.com/watch?v=','', $id_url);
        }else{
            $id_url = str_replace('https://youtu.be/','', $id_url);
        }
        // ubica la posicion de & para cortar parametros extras si existen
        $id_urlPos = strpos($id_url,'&list');
        if($id_urlPos){
            $id_url = mb_substr($id_url, 0, $id_urlPos);
        }

        // www.youtube.com/embed/
        $video = Video::find($request->id);
        $video->title = $request->title;
        $video->url = $request->url;
        $video->id_video = $id_url;
        $video->save();

        return response()->json(['result' => 'success','message'=>'Video actualizado con éxito']);
    }

    public function delete($id){
        $video = Video::find($id);
        $video->delete();
        return response()->json(['result' => 'success']);
    }
}
