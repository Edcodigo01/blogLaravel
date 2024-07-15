<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use ImageIntervention;
use Models\Image;

class UserController extends Controller
{

    public function uploadimage(Request $request)
    {
        //check file
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $picture   = date('His') . '-' . $filename;
            //move image to public/img folder
            $file->move(public_path('img'), $picture);
            return response()->json(["message" => "Image Uploaded Succesfully"]);
        } else {
            return response()->json(["message" => "Select image first."]);
        }
    }


    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'errors' => $validator->errors()]);
        }

        $file      = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $filename  = date('His').rand(2,50).'.'. $extension;

        //move image to public/img folder
        $file->move(public_path('images'), $filename);
        return response()->json(["message" => "Image Uploaded Succesfully","location"=>asset('public/images').'/'.$filename]);
    }


    public function index()
    {
        return 'Ejecutandose';
    }

    public function create()
    {
        return 'FUNCIONA 2';
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'errors' => $validator->errors()]);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;

        try {
            $user->save();
        } catch (\Illuminate\Database\QueryException $exception) {
            $errorInfo = $exception->errorInfo;

            return response()->json(['result' => 'error', 'message' => $errorInfo]);
        }

        return response()->json(['result' => 'ok', 'message' => 'guardado']);
    }
}
