<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\queue;
use App\Models\queue_user;
use DB;
use Hash;
use Illuminate\Support\Arr;
    
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


  

        \Log::debug("INFO on LOAD");
        $data = User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
        
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {

    \Log::debug("Request");
      if($request->ajax())
      {

          //$data=DB::table('users')->where('name','LIKE','%'.$request->search."%")->paginate(5);

          \Log::debug($request->full_text_search_query);
          $data = User::where('name','LIKE','%'.$request->full_text_search_query."%")
                        ->orwhere('email','LIKE','%'.$request->full_text_search_query."%")
                        ->orderBy('id','DESC')->paginate(5);
        
          \Log::debug("INFO ENVIADA");
          \Log::debug($data);
          //$data = User::search($request->get('full_text_search_query'))->get()->paginate(5);

          $view = view('users.resultados', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render();

          //$view = view('users.resultados', compact('data'))->render();


          return response()->json($view);

          
          
  
      }else{

        \Log::debug("NO AJAX");
        $data = User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);

      }
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();

        $queues = queue::pluck('queueName','id')->all();

        return view('users.create')
            ->with(compact('roles'))
            ->with(compact('queues'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['organization_id'] = 1;
    
        $user = User::create($input);
        
        $user->assignRole($request->input('roles'));

        

        $user->queues()->attach($request->input('queues'));

    
        return redirect()->route('users.index')
                        ->with('Usuario creado de forma correcta');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        $queues = queue::pluck('queueName','id')->all();
        $Userqueues = $user->queues->pluck('queueName','id')->all();

        return view('users.edit',compact('user','roles','userRole','queues','Userqueues'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
    
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        DB::table('queue_users')->where('user_id',$id)->delete();

        
        $user->queues()->attach($request->input('queues'));
    
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','Usuario actualizado de forma correcta');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','Se ha eliminado usuario de forma correcta');
    }
}