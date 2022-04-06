<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class AccountsController extends Controller
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

    public function index(){
        return view('account.index');
    }

    public function update(Request $request){
        // $rules = array(
        //     'firstname' => ['required', 'string', 'max:255'],
        //     'middlename' => ['required', 'string', 'max:255'],
        //     'lastname' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'email'],
        //     'contact_number' => ['required', 'string', 'min:10','max:10'],
        //     'address' => ['required', 'string'],
        // );

        // $error  = Validator::make($request->all(),$rules);
            
        // if($error->fails())
        // {
        //     return response()->json(['errors' => $error->errors()->all()]);
        // }

        // $firstname          = $request->input('firstname');
        // $middlename         = $request->input('middlename');
        // $lastname           = $request->input('lastname');
        // $suffix             = $request->input('suffix');

        $email              = $request->input('email');
        $contact_number     = $request->input('contact_number');
        //$address            = $request->input('address');
        $old_password       = $request->input('old_password');

        if(!empty($old_password) && Hash::check($old_password, Auth::user()->password))
        {
            $rules = array(
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            );

            $error  = Validator::make($request->all(),$rules);
            
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $id     = Auth::user()->id;
            $users   = User::find($id);
            // $users->firstname       = $firstname;
            // $users->middlename      = $middlename;
            // $users->lastname        = $lastname;   
            // $users->suffix          = $suffix;
            $users->contact_number  = $contact_number;
            $users->email           = $email;
            // $users->address         = $address;
            $users->password        = Hash::make($request->input('password'));
            $users->save();

            return response()->json(['success' => "Your account has been successfully updated!"]);
        }
        elseif(!empty($old_password) && !Hash::check($old_password, Auth::user()->password))
        {
            return response()->json(['error' => "Wrong old password."]);
        }
        else
        {
            $id     = Auth::user()->id;
            $users   = User::find($id);
            // $users->firstname       = $firstname;
            // $users->middlename      = $middlename;
            // $users->lastname        = $lastname;   
            // $users->suffix          = $suffix;
            $users->contact_number  = $contact_number;
            $users->email           = $email;
            // $users->address         = $address;
            $users->save();

            return response()->json(['success' => "Your account has been successfully updated!!!"]);
        }
    }

    public function verify(Request $request){
        $rules = array(
            'password' => ['required'],
        );

        $error  = Validator::make($request->all(),$rules);
            
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $password   = $request->input('password');

        if(!empty($password) && !Hash::check($password, Auth::user()->password))
        {
            return response()->json(['error' => "Wrong old password."]);
        }
        else
        {
            return response()->json(['success' => true]);
        }
    }

    public function upload(Request $request){
        if ($request->ajax()) {
            
            $rules  = array(
                'user_image' => ['image','required','max:1999']
            );

            $error  = Validator::make($request->all(),$rules);        
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            if ($request->hasFile('user_image')) 
            {
                $filenameWithExtension   = $request->file('user_image')->getClientOriginalName();

                $file                    = pathinfo($filenameWithExtension, PATHINFO_FILENAME);

                $extension               = $request->file('user_image')->getClientOriginalExtension();

                $toStore                 = $file.'_'.time().'.'.$extension;
            }
            else
            {
                $toStore    = 'default_photo.png';
            }

            if (Auth::user()->profile_picture !== 'default_photo.png') 
            {
                $filex   = 'public/user_images/'.Auth::user()->profile_picture;
                if (Storage::exists($filex)) 
                {
                    Storage::delete($filex);   
                }
            }

            $id         = Auth::user()->id;
            $upload     = User::find($id);
            $upload->profile_picture  = $toStore;

            try{
                $upload->save();
                $path    = $request->file('user_image')->storeAs('public/user_images',$toStore);
                return response()->json([
                    'success' => 'Image has been successfully uploaded.',
                    'file' => $toStore
                ]);
            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function accounts(){
        return view('account.accounts');
    }

    public function account_save(Request $request){
        $rules = array(
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'user_type' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'min:10','max:10'],
            'location_id' => ['required']
        );

        $error  = Validator::make($request->all(),$rules);
            
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $name               = $request->input('name');
        $position           = $request->input('position');
        $user_type          = $request->input('user_type');
        $contact_number     = $request->input('contact_number');
        $location_id        = $request->input('location_id');

        $email              = 'aics_user'.strtotime(date('Y-m-d H:i:s'));
        $password           = 'aics_'.strtotime(date('Y-m-d H:i:s'));

        $users   = new User;
        $users->name            = $name;  
        $users->position        = $position;
        $users->user_type       = $user_type;
        $users->contact_number  = $contact_number;
        $users->location_id  = $location_id;
        $users->email           = $email;
        $users->password        = Hash::make($password);
        try{
            $users->save();
            return response()->json([
                'success' => 'Account has been registered and ready-to-use.'
            ]);
        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function account_update(Request $request){
        $rules = array(
            'id' => ['required'],
        );

        $error  = Validator::make($request->all(),$rules);
            
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $id     = $request->input('id');
        $user   = User::where('id',$id)->first();

        if(empty($user))
        {
            return response()->json(['errors' => 'User cannot be found.']);
        }

        if($user->is_deactivated == true)
        {
            $user->is_deactivated = false;
            $type = 'activated';
        }
        else
        {
            $user->is_deactivated = true;
            $type = 'deactivated';
        }

        try{
            $user->save();
            return response()->json([
                'success' => 'Account has been '.$type
            ]);
        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }
}
