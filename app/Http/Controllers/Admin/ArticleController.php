<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Article;
use App\Models\Image;
use Illuminate\Support\Facades\File;
use ImageIntervention;
use Auth;
use App\Models\Category;


class ArticleController extends Controller
{

    private function deleteImage($local_path){
        if(File::exists($local_path)) {
            $local_path = str_replace("\\","/",$local_path);
            $positionExt = strripos($local_path, '.');
            $ext = substr($local_path,$positionExt);
            $path_xs = str_replace($ext,'-xs'.$ext,$local_path);
            $path_sm = str_replace($ext,'-sm'.$ext,$local_path);
            File::delete($path_xs);
            File::delete($path_sm);
            File::delete($local_path);
        }
    }

    public function index(){

        $imagesNotSaved = Image::where('saved',0)->where('created_at','>', \Carbon\Carbon::now()->addHours(4))->get();

        foreach($imagesNotSaved as $img){
            $this->deleteImage( $img->local_path );
            $img->delete();
        }

        $articles = Article::all();
        $images = Image::where('saved',1)->where('type','principal')->get();
        $categories = Category::orderBy('name','asc')->get();
        foreach ($articles as $art) {
            foreach ($images as $img) {
                if($img->ideditor == $art->ideditor){
                    $art->image_p = $img->url_path;
                }
            }
            foreach($categories as $cate){
                if($cate->id == $art->category_id){
                    $art->category = $cate->name;
                    $art->categorySlug = $cate->slug;

                }
            }
        }

        return response()->json(['articles'=>$articles,'categories'=>$categories]);
    }


    public function uploadImageEditor(Request $request){

        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'ideditor'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'errors' => $validator->errors()]);
        }

        $width_min = 350;
        $width_max = 1200;

        if ($request->hasFile('file')) {

            $file = ImageIntervention::make($request->file('file')->getRealPath());

            if ($file->width() < $width_min) {
                return response()->json(["result"=>"error","message"=> "Por favor ingrese imágenes con tamaño superior a los ".$width_min." pixeles."]);
            }

            $extension = $request->file('file')->getClientOriginalExtension();
            $fileName   = \Carbon\Carbon::now()->format('dmYHms');
            $url_path = asset('public/images/articles/'.$request->ideditor).'/'.$fileName.'.'.$extension;
            $local_path = public_path('images/articles/'.$request->ideditor).'/'.$fileName.'.'.$extension;
            $image = new Image;
            $image->url_path = $url_path;
            $image->local_path = $local_path;
            $image->ideditor = $request->ideditor;
            if($request->type == 'principal'){
                $imageAnt = Image::where('type','principal')->where('ideditor',$request->ideditor)->first();
                if($imageAnt){
                    $this->deleteImage($imageAnt->local_path);
                    $imageAnt->delete();
                }
                $image->type = $request->type;
            }
            $image->save();

            // make dir
            if(!File::exists('public/images')) {
                File::makeDirectory('public/images');
            }

            if (!File::exists('public/images/articles')) {
                File::makeDirectory('public/images/articles');
            }

            if(!File::exists('public/images/articles/'.$request->ideditor)) {
                File::makeDirectory('public/images/articles/'.$request->ideditor);
            }
            //move image to public/img folder
            if ($file->width() > $width_max) {
                $img = $file->resize($width_max, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save('public/images/articles/'.$request->ideditor.'/'.$fileName.'.'.$extension);
            }else{
                $file->save('public/images/articles/'.$request->ideditor.'/'.$fileName.'.'.$extension);
            }
            $img = $file->resize($width_max, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            // crea thumb

            $img = $file->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save('public/images/articles/'.$request->ideditor.'/'.$fileName.'-sm.'.$extension);

            $img = $file->resize(100, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save('public/images/articles/'.$request->ideditor.'/'.$fileName.'-xs.'.$extension);
            return response()->json(["result"=>"success","message" => "Imagen subida con éxito.","location"=>$url_path]);
        } else {
            return response()->json("La imagen no pudo subirse.");
        }
    }

    public function create(){
        $categories = Category::orderBy('name','asc')->get();
        return response()->json(['categories' => $categories]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:120|unique:articles',
            'status'=>'required|in:publicado,no-publicado',
            'category_id'=>'required|integer',

            'date'=>'required|date',
            'hour'=>'required|date_format:H:i:s',

            'author'=>'required',
            'description' => 'required|max:20000',
            'ideditor'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'errors' => $validator->errors()]);
        }

        $article = new Article;
        $imagesEditor = Image::where('ideditor',$request->ideditor)->get();
        foreach($imagesEditor as $image){
            if(!strpos($request->description,$image->url_path) and $image->type !== 'principal' ){
                $this->deleteImage($image->local_path);
                $image->delete();
            }else{
                $image->saved = 1;
                $image->save();
            }
        }

        $article->title = $request->title;
        $article->category_id = $request->category_id;
        $article->status = $request->status;
        $article->author = $request->author;
        $article->description = $request->description;
        $article->ideditor = $request->ideditor;
        $article->date = $request->date;
        $article->hour = $request->hour;
        $article->datetime = $request->date.' '.$request->hour;


        $article->save();

        return response()->json(['result'=>'success','message'=>'éxito']);
    }

    public function edit(Request $request){
        $article = Article::where('id',$request->slug)->first();
        $image = Image::where('ideditor',$article->ideditor)->where('type','principal')->first();
        if($image){
            $imageP = $image->url_path;
        }else{
            $imageP = '';
        }
        $categories = Category::orderBy('name','asc')->get();

        return response()->json(['article'=>$article,'imageP'=>$imageP,'categories' => $categories]);
    }

    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'id'=>'required',
            'ideditor'=>'required',
            'title' => 'required|max:120|unique:articles,id,'.$id,
            'status'=>'required|in:publicado,no-publicado',
            'category_id'=>'required|integer',
            'date'=>'required|date',
            'hour'=>'required|date_format:H:i:s',
            'author'=>'required',
            'description' => 'required|max:20000',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'errors' => $validator->errors()]);
        }

        $article = Article::find($id);
        // verificar si se borraron imágenes
        $imagesEditor = Image::where('ideditor',$request->ideditor)->get();
        foreach($imagesEditor as $image){
            if(!strpos($request->description,$image->url_path) and $image->type !== 'principal' ){
                $this->deleteImage($image->local_path);
                $image->delete();
            }else{
                $image->saved = 1;
                $image->save();
            }
        }

        $article->title = $request->title;
        $article->category_id = $request->category_id;
        $article->status = $request->status;
        $article->description = $request->description;
        $article->author = $request->author;
        $article->date = $request->date;
        $article->hour = $request->hour;
        $article->datetime = $request->date.' '.$request->hour;
        $article->save();

        return response()->json(['result'=>'success','message'=>$imagesEditor->count()]);
    }

    public function disable($id){
        $article = Article::find($id);
        $article->status = 'eliminado';
        $article->save();

        return response()->json(['result'=>'success']);
    }
    public function enable(Request $request, $id){
        $article = Article::find($id);
        if( $request->status == 'yes publish'){
            $article->status = 'publicado';
        }else{
            $article->status = 'no-publicado';
        }

        $article->save();

        return response()->json(['result'=>'success']);
    }
    public function delete($id){
        $article = Article::find($id);
        $article->delete();

        return response()->json(['result'=>'success']);
    }
}
