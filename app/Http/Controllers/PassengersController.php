<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Location;
use App\Passenger;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class PassengersController extends Controller
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
        $result     = Passenger::where('location_id',$location_id)
                        ->orderBy('passenger_id','DESC')
                        ->get();
        return view('passenger.index')->with('result',$result);
    }

    public function view($id){
        $row     = Passenger::where('passenger_id',$id)->first();
        return view('passenger.view')->with('row',$row);
    }

    public function logs($id){
        $row     = Passenger::where('passenger_id',$id)->first();
        return view('passenger.logs')->with('row',$row);
    }

    public function edit($id){
        $row     = Passenger::where('passenger_id',$id)->first();
        return view('passenger.edit')->with('row',$row);
    }

    public function add(){
        $row    = "";
        return view('passenger.add')->with("row",$row);
    }

    public function store(Request $request){
        if($request->ajax())
        {
            $rules = array(
                'firstname' => ['required', 'string', 'max:255'],
                'middlename' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'sex' => ['required'],
                'region' => ['required'],
                'province' => ['required'],
                'city_municipality' => ['required'],
                'barangay' => ['required'],
                'guardian_name' => ['required'],
                'guardian_number' => ['required','digits:10'],
                'guardian_address' => ['required'],
            );

            $error  = Validator::make($request->all(),$rules);
                
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $student_id         = $request->input('student_id');
            $firstname          = strtolower($request->input('firstname'));
            $middlename         = strtolower($request->input('middlename'));
            $lastname           = strtolower($request->input('lastname'));
            $suffix             = $request->input('suffix');
            $contact_number     = $request->input('contact_number');
            $email              = $request->input('email');
            $region             = $request->input('region');
            $province           = $request->input('province');
            $city_municipality  = $request->input('city_municipality');
            $barangay           = $request->input('barangay');
            $address_line       = $request->input('address_line');
            $zip_code           = $request->input('zip_code');

            $guardian_name      = $request->input('guardian_name');
            $guardian_number    = $request->input('guardian_number');
            $guardian_address   = $request->input('guardian_address');
            $profile_picture    = $request->input('profile_picture');
            $student_code       = strtotime(date('Y-m-d H:i:s'));
            $location_id        = Auth::user()->location_id;
            $id                 = Auth::user()->id;
            $sex                = $request->input('sex');

            if ($request->hasFile('profile_picture')) 
            {
                $rules2 = array(
                    'profile_picture' => ['image','required','max:1999'],
                );

                $error2  = Validator::make($request->all(),$rules2);
                
                if($error2->fails())
                {
                    return response()->json(['errors' => $error2->errors()->all()]);
                }

                $filenameWithExtension   = $request->file('profile_picture')->getClientOriginalName();
                $file                    = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                $extension               = $request->file('profile_picture')->getClientOriginalExtension();
                $toStore                 = $file.'_'.time().'.'.$extension;
            }
            else
            {
                $toStore    = 'default_photo.png';
            }

            $users   = new Passenger;
            $users->student_id      = $student_id;
            $users->firstname       = $firstname;
            $users->middlename      = $middlename;
            $users->lastname        = $lastname;   
            $users->suffix          = $suffix;
            $users->contact_number  = $contact_number;
            $users->email           = $email;
            $users->region    = $region;
            $users->province  = $province;
            $users->city_municipality  = $city_municipality;
            $users->barangay      = $barangay;
            $users->address_line  = $address_line;
            $users->zip_code      = $zip_code;
            $users->guardian_name     = $guardian_name;
            $users->guardian_number   = $guardian_number;
            $users->guardian_address  = $guardian_address;

            if($request->hasFile('profile_picture'))
            {
                $path    = $request->file('profile_picture')->storeAs('public/user_images',$toStore);
                $users->profile_picture   = $toStore;
            }
            else
            {
                $users->profile_picture   = 'default_photo.png';
            }

            $users->student_code        = $student_code;
            $users->location_id         = $location_id;
            $users->id                  = $id;
            $users->sex                 = $sex;

            try{
                $users->save();
                return response()->json([
                    'success' => 'New passenger record has been successfully saved!',
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
                'firstname' => ['required', 'string', 'max:255'],
                'middlename' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'sex' => ['required'],
                'region' => ['required'],
                'province' => ['required'],
                'city_municipality' => ['required'],
                'barangay' => ['required'],
                'guardian_name' => ['required'],
                'guardian_number' => ['required','digits:10'],
                'guardian_address' => ['required'],
            );

            $error  = Validator::make($request->all(),$rules);
                
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $passenger_id       = $request->input('passenger_id');
            $student_id         = $request->input('student_id');
            $firstname          = strtolower($request->input('firstname'));
            $middlename         = strtolower($request->input('middlename'));
            $lastname           = strtolower($request->input('lastname'));
            $suffix             = $request->input('suffix');
            $contact_number     = $request->input('contact_number');
            $email              = $request->input('email');
            $region             = $request->input('region');
            $province           = $request->input('province');
            $city_municipality  = $request->input('city_municipality');
            $barangay           = $request->input('barangay');
            $address_line       = $request->input('address_line');
            $zip_code           = $request->input('zip_code');

            $guardian_name      = $request->input('guardian_name');
            $guardian_number    = $request->input('guardian_number');
            $guardian_address   = $request->input('guardian_address');
            $profile_picture    = $request->input('profile_picture');
            $sex                = $request->input('sex');

            $users   = Passenger::where('passenger_id',$passenger_id)->first();

            if(empty($users))
            {
                return response()->json([
                    'error' => 'Invalid request! Reload page.',
                ]);
            }

            $users->student_id      = $student_id;
            $users->firstname       = $firstname;
            $users->middlename      = $middlename;
            $users->lastname        = $lastname;   
            $users->suffix          = $suffix;
            $users->contact_number  = $contact_number;
            $users->email           = $email;
            $users->region    = $region;
            $users->province  = $province;
            $users->city_municipality  = $city_municipality;
            $users->barangay      = $barangay;
            $users->address_line  = $address_line;
            $users->zip_code      = $zip_code;
            $users->guardian_name     = $guardian_name;
            $users->guardian_number   = $guardian_number;
            $users->guardian_address  = $guardian_address;
            $users->sex                 = $sex;

            try{
                $users->save();
                return response()->json([
                    'success' => 'Record has been successfully updated!',
                ]);

            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function delete(Request $request){
        $passenger_id     = $request->input('passenger_id');

        if(empty($passenger_id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $users  = Passenger::where('passenger_id',$passenger_id)->first();

        if(empty($users))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }

        try{
            $users->delete();
            return response()->json([
                'success' => 'Record has been successfully deleted!',
            ]);

        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function change(Request $request){
        if ($request->ajax()) {
            
            $rules  = array(
                'profile_picture' => ['image','required','max:1999']
            );

            $error  = Validator::make($request->all(),$rules);        
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            if ($request->hasFile('profile_picture')) 
            {
                $filenameWithExtension   = $request->file('profile_picture')->getClientOriginalName();

                $file                    = pathinfo($filenameWithExtension, PATHINFO_FILENAME);

                $extension               = $request->file('profile_picture')->getClientOriginalExtension();

                $toStore                 = $file.'_'.time().'.'.$extension;
            }
            else
            {
                $toStore    = 'default_photo.png';
            }

            $passenger_id   = $request->input('passenger_id');
            $user           = Passenger::where('passenger_id',$passenger_id)->first();

            if(empty($user))
            {
                return response()->json(['errors' => "Record not found!"]);
            }
            else
            {
                $filex   = 'public/user_images/'.$user->profile_picture;
                if (Storage::exists($filex)) 
                {
                    Storage::delete($filex);   
                }
            }

            $user->profile_picture  = $toStore;

            try{
                $user->save();

                $path    = $request->file('profile_picture')->storeAs('public/user_images',$toStore);

                return response()->json([
                    'success' => 'Profile picture has been successfully uploaded.'
                ]);
                
            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }
}
