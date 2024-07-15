<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Article;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::orderBy('name','asc')->get();
        return response()->json($categories);
    }

    public function edit($id){
        $category = Category::find($id);
        return response()->json($category);
    }

    public function create(){

        return response()->json('success');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'errors' => $validator->errors()]);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->save();

        return response()->json(['result' => 'success', 'message' => 'Categoría agregada con éxito.']);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,id,'.$id,
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'errors' => $validator->errors()]);
        }

        $category = Category::find($id);
        $category->name = $request->name;
        $category->save();

        return response()->json(['result' => 'success', 'message' => 'Categoría actualizada con éxito.']);
    }

    public function delete($id){
         $category = Category::find($id);

         $articles = Article::where('category_id',$id)->count();

         if($articles != 0){
            return response()->json(['result'=>'error','message'=>'Existen algunos artículos relacionados a esta categoría, para eliminarlos, deberá borrar cada artículo relacionado por completo.']);
         }
         $category->delete();
        return response()->json(['result'=>'success','message'=>'']);
    }
}
