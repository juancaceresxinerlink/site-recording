<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Recording;
use \Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

class RecordingController extends Controller
{
    //

    public function index(Request $request)
    {
        // dd($request->query());
        $user = Auth::user();

        \Log::debug($user);

        $recordings = Recording::select('*')->where('organization_id', '=', $user->organization_id);

        $params = collect(json_decode(base64_decode($request->query()[0])));

        $start = $params->has('start') && $params->get('start') ? $params->get('start') : 0;
        $length = $params->has('length') && $params->get('length') ? $params->get('length') : 10;
        $key = $params->has('key') && $params->get('key') ? $params->get('key') : 'id';
        $order = $params->has('order') && $params->get('order') ? $params->get('order') : 'desc';

        $this->validate_params($request, $params);

        $this->filter($params, $recordings);

        $total = $recordings->count();

        $recordings_response = $recordings->skip($start * $length)
            ->take($length)
            ->orderBy($key, $order)
            ->get();

        return response()->json(base64_encode(json_encode([
            'start' => $start,
            'length' => $length,
            'total' => $total,
            'pageCount' => ceil($total / $length),
            'recordings' => $recordings_response,
        ])));
    }  



    public function getQueues2()
    {
        $user = Auth::user();

        \Log::debug("INFO");
        \Log::debug($user);

        $data = Recording::where('organization_id', '=', $user->organization_id)
            ->distinct('queue')
            ->pluck('queue');

        return response()->json(base64_encode($data));
    }

    public function GetAllAgent(Request $request)
    {
        $user = Auth::user();

        \Log::debug($user);

        $data = Recording::where('organization_id', '=', $user->organization_id)
            ->where('agent_account', '!=' , '')
            ->distinct('agent_account')
            ->pluck('agent_account');

        //return response()->json($data);
        return response()->json(base64_encode($data));
        
        //return response()->json(base64_encode($data));
    }

    public function validate_params(Request $request, $params)
    {

        // if ($params->has('agent_account') && $params->get('agent_account')) {
        //     $request->validate([
        //         'agent_account' => 'email',
        //     ]);
        // }
        if ($params->has('agent_account') && $params->get('agent_account')) {
            $this->validate($request, [
                'agent_account' => 'email',
            ]);
            // $request->validate([
            //     'agent_account' => 'email',
            // ]);
        }

        if ($params->has('extension') && $params->get('extension')) {
            // $request->validate([
            //     'extension' => 'alpha_num',
            // ]);

            $this->validate($request, [
                'extension' => 'alpha_num',
            ]);
        }

        if ($params->has('dni') && $params->get('dni')) {
            // $request->validate([
            //     'dni' => 'alpha_num',
            // ]);
            $this->validate($request, [
                'dni' => 'alpha_num',
            ]);
        }

        if ($params->has('from') && $params->get('from')) {
            // $request->validate([
            //     'from' => 'date_format:d/m/Y',
            // ]);
            $this->validate($request, [
                'from' => 'date_format:d/m/Y',
            ]);
            if ($params->has('to') && $params->get('to')) {
                $from = $params->get('from');

                // $request->validate([
                //     'to' => 'date_format:d/m/Y|after:' . $from,
                // ]);
                $this->validate($request, [
                    'to' => 'date_format:d/m/Y|after:' . $from,
                ]);
            }
        }

        if ($params->has('to') && $params->get('to')) {
            // $request->validate([
            //     'to' => 'date_format:d/m/Y',
            // ]);
            $this->validate($request, [
                'to' => 'date_format:d/m/Y',
            ]);
        }

        if ($params->has('queue') && $params->get('queue')) {
            // $request->validate([
            //     'queue' => 'alpha_num',
            // ]);
            $this->validate($request, [
                'queue' => 'alpha_num',
            ]);
        }

        if ($params->has('id_interaction') && $params->get('id_interaction')) {
            // $request->validate([
            //     'id_interaction' => 'alpha_num',
            // ]);
            $this->validate($request, [
                'id_interaction' => 'alpha_num',
            ]);
        }

        if ($params->has('ani') && $params->get('ani')) {
            // $request->validate([
            //     'ani' => 'alpha_num',
            // ]);
            $this->validate($request, [
                'ani' => 'alpha_num',
            ]);
        }

        if ($params->has('dnis') && $params->get('dnis')) {
            // $request->validate([
            //     'dnis' => 'alpha_num',
            // ]);
            $this->validate($request, [
                'dnis' => 'alpha_num',
            ]);
        }

        if ($params->get('duration_min') && $params->get('duration_min')) {
            // $request->validate(['duration_min' => array('regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/')]);
            $this->validate($request, [
                'duration_min' => array('regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/'),
            ]);
        }

        if ($params->has('duration_max') && $params->get('duration_max')) {
            // $request->validate(['duration_max' => array('regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/')]);
            $this->validate($request, [
                'duration_max' => array('regex:/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/')
            ]);
        }

        if ($params->has('duration_min') && $params->get('duration_min')) {
            if ($params->has('duration_max') && $params->get('duration_max')) {
                $duration_min = $params->get('duration_min');
                // $request->validate([
                //     'duration_max' => 'after:' . $duration_min,
                // ]);
                $this->validate($request, [
                    'duration_max' => 'after:' . $duration_min,
                ]);
            }
        }

    }


    public function filter($params, $recordings)
    {
        $pagination_fields = ['start', 'length', 'key', 'order'];

        foreach ($params as $key => $value) {
            if (isset($value) && !in_array($key, $pagination_fields)) {
                if ($key === "from") {
                    $recordings->where('created_at', '>=', \Carbon\Carbon::createFromFormat('d/m/Y', $value));
                } else if ($key === "to") {
                    $recordings->where('created_at', '<=', \Carbon\Carbon::createFromFormat('d/m/Y', $value));
                } else if ($key === "duration_min") {
                    $recordings->where('duration', '>=', $value);
                } else if ($key === "duration_max") {
                    $recordings->where('duration', '<', $value);
                } else {
                    $recordings->where($key, $value);
                }
            }
        }
    }

    public function getAudio($id)
    {
        $data = Recording::find(base64_decode($id));

        return response()->json($data);
    }



        //Funcion que se conecta a ftp transforma ruta 
    public function getSftpToFile(Request $request){


            $PathFTP = $request->linkRecording;
            $idCall = $request->idCall;
            $urlToReturn = $request->urlToReturn;
            $privateKey = new RSA();

            $mytime = Carbon::now();
            $Fecha = $mytime->toDateTimeString();
            $sftp = new SFTP('sftp-five9.xinerlink.cl');
            $privateKey->loadKey("-----BEGIN RSA PRIVATE KEY-----
            MIIEogIBAAKCAQEAqa56h4YMkw8iWneSgFu2WIBvOiMehZbEqAst/kjS0YkrPpgl
            d8AUXaiYq5wMVwEYPo09qCt2wUJ2sSYORBh30KNXSVddCchwRhYoDo7Jm9UhEkyY
            K4kNNBkzPAAZezaMNdYWBUctEiDYk7yPsNPIkT1gwX3bpbOaH9w95NhIqkP6MSS5
            BNJVxH/YQWaVZRSJnUsek87z/7bMtK548I8rCZV9REuAz5Jrpe6ubdfaIW2YG+/a
            qnTUpz1KwsRoHN1fBe3Ex5yZIAGjLF1hiSrUUOu/A39Qw2aRuAcluZRU9ESpgh9u
            U7VgjDpwsurjfMe5QFKHNijOJGgz5zWa2JdHWwIDAQABAoIBAE9JLwnqci+5L6U0
            ICmIMTEzekWhKV7krymSZ8BdCN1OCf3qm/9TqnCFGil99iolYc3As6HEBW6SfuGR
            1bKt+DZuTJgBkYDDvuqQ6dC7masOMLDbR3N4S8ofSFht93oiZ+u0CIAY3Q+Od4S4
            5C+NFjf/hRmElMDdplPi1qz6/yFHdPFVw3xXToeHArni5sju6bItj6i8+dWp+26T
            3Vd/W2k3VHXK7Byowd5zfzy/BH5apTupgCer7e4Jc7K3Y6u7wtDQjX1G//Dka6CB
            Rpift7ysmIYappWvl+tsagRuSz46ryoJo4JTzhJ/e2E1OWEVAbXo/E8MZNMsiZRD
            VAHtVXECgYEA4EIzDGveGXL6qqloSv68cEfnaOAPUlFU/mvExlGgyPeF9gaI7lWH
            YYH2Mh5HKwxf6mrMJwk7RlVcFeBwjgAhwNq2klkxmKaPxrZrkSZNaGsb6jwCyTZl
            9BF5FPbKbGTFu/hg/ZS4kj+kzbs3blPageyHnT/MxwrdP7j6iQGNoEkCgYEAwbK7
            OpHkTZUpow6hHabKtKGpWQHIBRtAmaOQNR8dChzwV0Fgabwi4IySqIgf/iU8mMPV
            g8COb4O3nFwC04YUHiJ+S0C2FCmW8MrOFWvzB0KPbRiVLTRSJszZG2mYnSSeBdVx
            Ylv2wiiGPS6ej9EFVe7X0+8lxAqo0Rz6HuVQMoMCgYAqj6XMl7RBNDcqqJgok4wD
            60GZ/9wojVMKLj3cPaW8Pm3oMXlPcmANO3MUM/bhzqltffNc/T2Ira6aYEw8Rv4g
            8eFwiQkGpaXn2rszgwdx59IWdGk68t4Koj5Ooj/srntwn0UZG16kMvv+J6fvgm6X
            9eIPEAq3Q/KVo2+5DNhqMQKBgB6/5QEzT+8REv8Tv3gZlmx+jYfXxI0q+mJpmOcV
            /WIxneX8NvYSK+dB6bZfhdSuzKPj0u2LkBEb8/YalUhHLMJr72i66SziPVgUmgrE
            jFxGsMY89NGsUK5gLscvSE8KFRwP+mQG/XFtRYJI+FsUb6hotlKq0HAC0TIBS6PD
            +6tJAoGARpDgQu/m6c40PmIgHgeYw9BIjKKF8liEn5oNMnV44ijIhaLIVwHHtq+O
            r9KySWC53V7MnjBWfZj20Wywb+ctPr9yjyNTflHaLmV19wsXoUcD2Ti3EjOCNrt1
            LupbU+cGfLCUDwl49aYeTT6i4fi04sOMLP9+ScA9tjVGX3NMBfE=
            -----END RSA PRIVATE KEY-----");
    
            if (!$sftp->login('movigoo-five9', $privateKey)) {
                return "ERROR FTP";
                \Log::debug("ERROR FTP, clave o usuario invalido");
            }else{
                \Log::debug("Conexion correcta a FTP!!!");
            

                $stringGrabacion = $Fecha.'_'.$idCall;
                //Return path URL localhost:8000/storage/my_local_filename.wav
                $stringGrabacion = str_replace(" ","_",$stringGrabacion);
                $stringGrabacion = str_replace(":","_",$stringGrabacion);
                //buscar INFO
                $splitPath = explode("/",$PathFTP);
    
                $pathFinal = $splitPath[0]."/".$splitPath[1]."/".$splitPath[2]."/".$splitPath[3]."/".$splitPath[4];
                
                Log::debug($pathFinal);


                //Obtener base url y luego almacenar
                $stringGrabacion = $Fecha.'_'.$idCall;
                //Return path URL localhost:8000/storage/my_local_filename.wav
                $stringGrabacion = str_replace(" ","_",$stringGrabacion);
                $stringGrabacion = str_replace(":","_",$stringGrabacion);            
                Log::debug($pathFinal);
                $files = $sftp->nlist($pathFinal);

                
            $joinPaths = "";
            foreach ($files as $file)
            {
                //Log::debug($file);
                $final = explode("-",$file);
                if ($final[2] == $idCall)
                {
                    //Encuentra grabaciones segmentadas 
                    Log::debug($file);
                    $pathFile = $pathFinal."/".$file;
                    //sirve pero no tanto
                    //$file = $sftp->get($pathFile);
                    //generando nombre del archivo definitivo
                    
                    $nameFinal = $stringGrabacion."_".$final[4];
                    $file = $sftp->get($pathFile);
                    Storage::disk('local')->put('/public//'.$nameFinal.'.wav' ,$file);
                    
                    //by recordring storagePath
                    $storagePath = Storage::disk('local')->path("public/".$nameFinal.".wav");
                    
                    //Generando el ARRAY para luego llamar proceso python y una los archivos

                    $joinPaths = $joinPaths.$storagePath.",";


                    // una vez con el audio completo se retorna este nuevo unido y con esto evitamos problemas

                }


            }
            $joinPaths = '"'.$joinPaths.'"';
            Log::debug($joinPaths);

            $file = $sftp->get($PathFTP);
            Storage::disk('local')->put('/public//'.$stringGrabacion.'.wav' ,$file);
     
            //$urlToReturn = "https://records.xinerlink.cl/storage/".$stringGrabacion.".mp3";

            Log::debug("STRING DE GRABACION");
            Log::debug($stringGrabacion);
            $urlToReturn = "https://records.xinerlink.cl/storage/".$stringGrabacion.".mp3";

            Log::debug("URL TO RETURN");
            Log::debug($urlToReturn);


            $storagePath = Storage::disk('local')->path("public/".$stringGrabacion.".wav");
            Log::debug($storagePath);
            

            //Desarrollo
            //exec("D:\Codigos\Codigos\USS\laravel-crud\docker\ProcesaGrabaciones.py {$joinPaths} {$storagePath}");
            //Produccion
            exec("python3.8 /ProcesaGrabaciones.py {$joinPaths} {$storagePath}");
            //$urlToReturn = "http://54.159.249.200:8080/storage/".$stringGrabacion.".wav";
            //Responder como JSON
            return['url' =>$urlToReturn];
        



            }                
    
            
        }
    
    



}
