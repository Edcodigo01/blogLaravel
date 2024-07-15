<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Image;


class BlogController extends Controller
{

    public function lastArticles(){
        $articles = Article::where('status','publicado')->orderBy('datetime','desc')->take(6)->get();

        $images = Image::where('saved',1)->where('type','principal')->get();
        $categories = Category::orderBy('name','asc')->get();
        foreach ($articles as $art) {
            foreach ($images as $img) {
                if($img->ideditor == $art->ideditor){
                    $art->imageP = $img->url_path;
                }
            }
            foreach($categories as $cate){
                if($cate->id == $art->category_id){
                    $art->categoryName = $cate->name;
                    $art->categorySlug = $cate->slug;
                }
            }

            $summary = strip_tags($art->description);
            $summary = html_entity_decode($summary);
            $art->summary = $summary;

        }

        return response()->json(['articles'=>$articles ]);
    }

    public function index(){
        $articles = Article::where('status','publicado')->orderBy('datetime','asc')->get();
        $images = Image::where('saved',1)->where('type','principal')->get();
        $categories = Category::orderBy('name','asc')->get();
        foreach ($articles as $art) {
            foreach ($images as $img) {
                if($img->ideditor == $art->ideditor){
                    $art->imageP = $img->url_path;
                }
            }
            foreach($categories as $cate){
                if($cate->id == $art->category_id){
                    $art->categoryName = $cate->name;
                    $art->categorySlug = $cate->slug;
                }
            }

            $summary = strip_tags($art->description);
            $summary = html_entity_decode($summary);
            $art->summary = $summary;

        }
        return response()->json(['articles'=>$articles,'categories'=>$categories ]);
    }


    public function article(Request $requerst){
        $article = Article::where('slug',$requerst->article)->first();
        $categories = Category::orderBy('name','asc')->get();
        $category = Category::find($article->category_id);
        $article->categoryName = $category->name;

        if($article == null){
            return response()->json(['result'=>'no encontrado' ]);
        }
        $imageP = Image::where('ideditor',$article->ideditor)->where('type','principal')->first();
        if($imageP){
            $imageP = $imageP->url_path;
        }

        $articleAnt = Article::where('id','>',$article->id)->orderBy('id','asc')->first();
        $articleSig = Article::where('id','<',$article->id)->orderBy('id','desc')->first();
        if($articleAnt == Null){
            $articleAnt = 'disabled';

        }else{
            $category = Category::find($articleAnt->category_id);
            $articleAnt->slugCategory = $category->slug;
        }
        if($articleSig == Null){
            $articleSig = 'disabled';
        }else{
            $category2 = Category::find($articleSig->category_id);
            $articleSig->slugCategory = $category2->slug;
        }
        return response()->json(['article'=>$article,'categories'=>$categories,'imageP'=>$imageP, 'articleAnt'=>$articleAnt,'articleSig'=>$articleSig]);
    }
}
