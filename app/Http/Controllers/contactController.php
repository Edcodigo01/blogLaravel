<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
class contactController extends Controller
{
    public function mail_contact(Request $request){
        // return response()->json(["result"=>"message contact","message"=>"XXXXXXX"]);
      $request->validate([
          'email'=>'required|min:4|email',
          'message'=>'required|min:4'
      ]);

      $data = ['data'=>['email'=>$request->email,'message'=>$request->message]];

      Mail::send('mails.message_contact',$data,function($message){
          $message->subject('Mensaje enviado desde la web Belizabeth Montilla');
          $message->to('eavc53189@gmail.com');

      });

      return response()->json(["result"=>"success","message"=>"Mensaje enviado con éxito."]);
    }

    public function mail_view(Request $request){
        return view("mails.".$request->view);
    }
}
