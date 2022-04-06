<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Location;
use App\Address;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class RegistrationsController extends Controller
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

        $location_id    = Auth::user()->location_id;
        $result     = User::where('user_type','<>','ADMIN')
                        ->where('location_id',$location_id)
                        ->orderBy('id','DESC')
                        ->get();
        return view('users.index')->with('result',$result);

    }

    public function view($id){
        $row     = User::where('id',$id)->first();
        return view('users.view')->with('row',$row);
    }

    public function logs($id){
        $row     = User::where('id',$id)->first();
        return view('users.logs')->with(['row' => $row, 'pk' => $id]);
    }
    
    public function print($id){
        $row     = User::where('id',$id)->first();
        return view('users.print')->with('row',$row);
    }

    public function edit($id){
        $row     = User::where('id',$id)->first();
        return view('users.edit')->with('row',$row);
    }

    public function add(){
        $row    = "";
        return view('users.add')->with("row",$row);
    }

    public function store(Request $request){
        if($request->ajax())
        {
            $rules = array(
                'firstname' => ['required', 'string', 'max:255'],
                'middlename' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email'],
                'contact_number' => ['required', 'digits:10'],
                'plate_number' => ['required'],
                'contact_person_name' => ['required'],
                'contact_person_number' => ['required'],
                'contact_person_address' => ['required'],
                'license' => ['image','required','max:1999'],
            );

            $error  = Validator::make($request->all(),$rules);
                
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $firstname          = strtolower($request->input('firstname'));
            $middlename         = strtolower($request->input('middlename'));
            $lastname           = strtolower($request->input('lastname'));
            $suffix             = $request->input('suffix');
            $email              = $request->input('email');
            $contact_number     = $request->input('contact_number');
            $plate_number           = $request->input('plate_number');
            $contact_person_name    = $request->input('contact_person_name');
            $contact_person_number  = $request->input('contact_person_number');
            $contact_person_address = $request->input('contact_person_address');
            $license                = $request->input('license');
            $password               = Hash::make($request->input('lastname').date('Y'));
            
            $location_id            = Auth::user()->location_id;
            $user_type              = $request->input('user_type');

            $region                = $request->input('region');
            $province              = $request->input('province');
            $city_municipality     = $request->input('city_municipality');
            $barangay              = $request->input('barangay');
            $address_line          = $request->input('address_line');
            $zip_code               = $request->input('zip_code');

            if ($request->hasFile('license')) 
            {
                $filenameWithExtension   = $request->file('license')->getClientOriginalName();

                $file                    = pathinfo($filenameWithExtension, PATHINFO_FILENAME);

                $extension               = $request->file('license')->getClientOriginalExtension();

                $toStore                 = $file.'_'.time().'.'.$extension;
            }
            else
            {
                $toStore    = 'no_license';
            }

            $users   = new User;
            $users->firstname       = $firstname;
            $users->middlename      = $middlename;
            $users->lastname        = $lastname;   
            $users->suffix          = $suffix;
            $users->contact_number  = $contact_number;
            $users->email           = $email;
            $users->plate_number    = $plate_number;
            $users->contact_person_name     = $contact_person_name;
            $users->contact_person_number   = $contact_person_number;
            $users->contact_person_address  = $contact_person_address;
            $users->license                 = $toStore;
            $users->password                = $password;
            $users->location_id             = $location_id;
            $users->user_type             = $user_type;

            try{
                $users->save();

                $last_id    = $users->id;
                $address    = new Address;
                $address->region    = $region;
                $address->province  = $province;
                $address->city_municipality  = $city_municipality;
                $address->barangay      = $barangay;
                $address->address_line  = $address_line;
                $address->zip_code      = $zip_code;
                $address->id            = $last_id;
                $address->save();

                $address_id   = $address->address_id;

                $update     = User::find($last_id);
                $update->address = $address_id;
                $update->save();
                
                $path    = $request->file('license')->storeAs('public/user_images',$toStore);

                return response()->json([
                    'success' => 'Account has been successfully saved!',
                ]);

            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function update(Request $request){
        if($request->ajax())
        {
            $rules = array(
                'id' => ['required', 'integer'],
                'firstname' => ['required', 'string', 'max:255'],
                'middlename' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email'],
                'contact_number' => ['required','digits:10'],
                'plate_number' => ['required'],
                'contact_person_name' => ['required'],
                'contact_person_number' => ['required'],
                'contact_person_address' => ['required'],
            );

            $error  = Validator::make($request->all(),$rules);
                
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $id     = $request->input('id');

            $users  = User::where('id',$id)->first();

            if(empty($users))
            {
                return response()->json(['error' => "Invalid request detected!"]);
            }

            $firstname          = strtolower($request->input('firstname'));
            $middlename         = strtolower($request->input('middlename'));
            $lastname           = strtolower($request->input('lastname'));
            $suffix             = $request->input('suffix');
            $email              = $request->input('email');
            $contact_number     = $request->input('contact_number');
            $plate_number           = $request->input('plate_number');
            $contact_person_name    = $request->input('contact_person_name');
            $contact_person_number  = $request->input('contact_person_number');
            $contact_person_address = $request->input('contact_person_address');

            $user_type              = $request->input('user_type');

            $region                = $request->input('region');
            $province              = $request->input('province');
            $city_municipality     = $request->input('city_municipality');
            $barangay              = $request->input('barangay');
            $address_line          = $request->input('address_line');
            $zip_code               = $request->input('zip_code');

            $users->firstname       = $firstname;
            $users->middlename      = $middlename;
            $users->lastname        = $lastname;   
            $users->suffix          = $suffix;
            $users->contact_number  = $contact_number;
            $users->email           = $email;
            $users->plate_number    = $plate_number;
            $users->contact_person_name     = $contact_person_name;
            $users->contact_person_number   = $contact_person_number;
            $users->contact_person_address  = $contact_person_address;

            $users->user_type             = $user_type;

            try{
                $users->save();

                $last_id    = $id;

                $address    = Address::where('address_id',$users->address)->first();
                $address->region    = $region;
                $address->province  = $province;
                $address->city_municipality  = $city_municipality;
                $address->barangay      = $barangay;
                $address->address_line  = $address_line;
                $address->zip_code      = $zip_code;
                $address->save();

                $update     = User::find($last_id);
                $update->address = $users->address;
                $update->save();

                return response()->json([
                    'success' => 'Account has been successfully updated!',
                ]);

            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function delete(Request $request){
        $id     = $request->input('id');

        if(empty($id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $users  = User::where('id',$id)->first();

        if(empty($users))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }

        $address_id     = $users->address;

        $delete     = Address::find($address_id);

        try{
            $users->delete();
            $delete->delete();

            return response()->json([
                'success' => 'Account has been successfully deleted!',
            ]);

        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }

    }

    public function change(Request $request){
        if ($request->ajax()) {
            
            $rules  = array(
                'license' => ['image','required','max:1999']
            );

            $error  = Validator::make($request->all(),$rules);        
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            if ($request->hasFile('license')) 
            {
                $filenameWithExtension   = $request->file('license')->getClientOriginalName();

                $file                    = pathinfo($filenameWithExtension, PATHINFO_FILENAME);

                $extension               = $request->file('license')->getClientOriginalExtension();

                $toStore                 = $file.'_'.time().'.'.$extension;
            }
            else
            {
                $toStore    = 'no_license';
            }

            $id     = $request->input('id');
            $user   = User::where('id',$id)->first();

            if(empty($user))
            {
                return response()->json(['errors' => "Record not found!"]);
            }
            else
            {
                $filex   = 'public/user_images/'.$user->license;
                if (Storage::exists($filex)) 
                {
                    Storage::delete($filex);   
                }
            }

            $user->license  = $toStore;

            try{
                $user->save();
                $path    = $request->file('license')->storeAs('public/user_images',$toStore);

                return response()->json([
                    'success' => 'License image has been successfully uploaded.'
                ]);
                
            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }
}
