<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contingencia;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /*
        $data = \DB::select('select * from contingencia');
        \Log::info(print_r($data, true));
        */
        return view('home'); 
    }



    public function update(Request $request)
    {   
        \Log::info("Volviendo");
        \Log::info($request);
        
        \DB::table('contingencia')->where('id',$request->cola)->update(array(
            'estado'=>$request->estado,
            'contingencia1'=>$request->text
        ));

        return redirect()->route('Home.index')
                        ->with('success','User updated successfully');
    }
}
