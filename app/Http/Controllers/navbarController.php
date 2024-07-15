<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Article;
use App\Models\Video;



class navbarController extends Controller
{
    public function index(){
        $articlesCount = Article::where('status','publicado')->count();
        $categories = Category::orderBy('name','asc')->get();
        $videos = Video::orderBy('title','asc')->get();

        return response()->json(['articlesCount'=>$articlesCount,'categories'=>$categories, 'videos'=>$videos]);

    }
}
