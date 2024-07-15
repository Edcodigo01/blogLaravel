<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
    <br>
    <br>
    <div class="container2">




        <div class="card" style="border:1px rgb(217, 217, 217) solid;box-shadow: 3px 3px 3px rgb(213, 212, 211);padding: 40px;border-radius: 10px;max-width: 800px;">
            <div style="text-align:center">
                {{-- <img style="width: 250px;" src="{{$message->embed(asset('public/images/default/LogoEskaDentalcompleto.png'))}}" class="logo-mail" style="" data-auto-embed="attachment"/> --}}
                {{-- <img class="logo-mail" src="{!! asset('public\images\default\Logo Eska Dental completo.png') !!}" alt=""> --}}
            </div>
            <br>
            <h1 style="color:black">Mensaje enviado desde la web Belizabeth Montilla</h1>
            <p style="color:black;font-size:17px" class="m-0"><span class="text-secondary font-600" style="font-weight: 600;color:#FF2768">Correo del remitente:</span> {{$data['email']}} </p>

            <p style="color:black;font-size:17px" class=""><span class="text-secondary font-600" style="font-weight: 600;color:#FF2768">Mensaje:</span> {{$data['message']}}  </p>
            <br>
            <div class="" style="text-align:center">
                <a href="{{url('/')}}" class="btn btn-primary" style="background:#197BFF;color: white;padding: 10px;border-radius: 5px;margin: auto;font-size: 15px;text-decoration:none">Ir a web Belizabeth Montilla</a>
            </div>

            <br>
            <p class="" style="text-align:center;line-height:1;font-size:13px;color:black">
                No responder este mensaje. Si desea contactar con el remitente verifique su email en la parte superior de este mensaje, y redacte uno nuevo.
            </p>

        </div>

    </div>
</body>
</html>
