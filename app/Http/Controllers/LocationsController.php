<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Location;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class LocationsController extends Controller
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
        $row            = Location::where('location_id',$location_id)->first();
        return view('location.index')->with('row',$row);
    }

    public function offices(){
        $result            = Location::where('location_id','<>',1)
                                ->orderBy('location_id','DESC')
                                ->get();
        return view('location.offices')->with('result',$result);
    }

    public function save(Request $request){
        $location    = Auth::user()->location_id;

        $rules = array(
            'name' => ['required', 'string', 'max:255'],
            'company_address' => ['required', 'string', 'max:255'],
            'contact_info' => ['required', 'string', 'max:255'],
        );

        $error  = Validator::make($request->all(),$rules);
            
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $name               = $request->input('name');
        $company_address    = $request->input('company_address');
        $contact_info       = $request->input('contact_info');

        $check               = Location::where('location_id',$location)->first();

        if(!empty($check))
        {
            $data   = Location::where('location_id',$location)->first();
            $data->name             = $name;
            $data->company_address  = $company_address;
            $data->contact_info     = $contact_info;
        }
        else
        {
            $data   = new Location;
            $data->name             = $name;
            $data->company_address  = $company_address;
            $data->contact_info     = $contact_info;
        }
        
        try{
            $data->save();
            return response()->json([
                'success' => 'Location settings has been updated!',
            ]);
        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function store(Request $request){

        $rules = array(
            'name' => ['required', 'string', 'max:255'],
            'company_address' => ['required', 'string', 'max:255'],
            'contact_info' => ['required', 'string', 'max:255'],
        );

        $error  = Validator::make($request->all(),$rules);
            
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $location_id        = $request->input('location_id');
        $name               = $request->input('name');
        $company_address    = $request->input('company_address');
        $contact_info       = $request->input('contact_info');

        if(!empty($location_id))
        {
            $check  = Location::where('location_id',$location_id)->first();
        }
        else
        {
            $check  = "";
        }

        if(!empty($check))
        {
            $data   = Location::where('location_id',$location_id)->first();
            $data->name             = $name;
            $data->company_address  = $company_address;
            $data->contact_info     = $contact_info;
            $message                = "Office record has been updated.";
        }
        else
        {
            $data   = new Location;
            $data->name             = $name;
            $data->company_address  = $company_address;
            $data->contact_info     = $contact_info;
            $message                = "Office record has been successfully saved.";
        }
        
        try{
            $data->save();
            return response()->json([
                'success' => $message,
            ]);
        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function delete(Request $request){
        $location_id     = $request->input('location_id');

        if(empty($location_id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $location  = Location::where('location_id',$location_id)->first();

        if(empty($location))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }

        try{
            $location->delete();
            return response()->json([
                'success' => 'Record has been successfully deleted!',
            ]);

        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }
}
 