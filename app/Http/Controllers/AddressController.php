<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function provinces(Request $request){
    	if($request->ajax())
        {
        	$rules = array(
                'regCode' => ['required']
            );
            
            $error  = Validator::make($request->all(),$rules);        
            if($error->fails())
            {
                return response()->json(['error' => 'You must select a region first.']);
            }


            $regCode 	= $request->input('regCode');

            $result 	= DB::table('provinces')
            					->where('regCode',$regCode)
            					->orderBy('provDesc','ASC')
            					->get();

            if (count($result) > 0) 
            {
            	$html = view('address.provinces')->with([
                    	'result' => $result
                	])->render();
            	return response()->json(['success' => true, 'html' => $html]);
            }
            else
            {
            	return response()->json(['error' => 'INVALID REQUEST! You must select from the options provided.']);
            }
        }
    }
    public function cm(Request $request){
    	if($request->ajax())
        {
        	$rules = array(
                'regCode' => ['required'],
                'provCode' => ['required'],
            );
            
            $error  = Validator::make($request->all(),$rules);        
            if($error->fails())
            {
                return response()->json(['error' => 'You must select a region and province first.']);
            }


            $regCode 	= $request->input('regCode');
            $provCode 	= $request->input('provCode');

            $result 	= DB::table('cm')
            					->where('regDesc',$regCode)
            					->where('provCode',$provCode)
            					->orderBy('citymunDesc','ASC')
            					->get();

            if (count($result) > 0) 
            {
            	$html = view('address.cm')->with([
                    	'result' => $result
                	])->render();
            	return response()->json(['success' => true, 'html' => $html]);
            }
            else
            {
            	return response()->json(['error' => 'INVALID REQUEST! You must select from the options provided.']);
            }
        }
    }

    public function brgy(Request $request){
    	if($request->ajax())
        {
        	$rules = array(
                'regCode' => ['required'],
                'provCode' => ['required'],
                'citymunCode' => ['required'],
            );
            
            $error  = Validator::make($request->all(),$rules);        
            if($error->fails())
            {
                return response()->json(['error' => 'You must select a region, province, and city and municipality first.']);
            }


            $regCode 	= $request->input('regCode');
            $provCode 	= $request->input('provCode');
            $citymunCode 	= $request->input('citymunCode');

            $result 	= DB::table('brgy')
            					->where('regCode',$regCode)
            					->where('provCode',$provCode)
            					->where('citymunCode',$citymunCode)
            					->orderBy('brgyDesc','ASC')
            					->get();

            if (count($result) > 0) 
            {
            	$html = view('address.brgy')->with([
                    	'result' => $result
                	])->render();
            	return response()->json(['success' => true, 'html' => $html]);
            }
            else
            {
            	return response()->json(['error' => 'INVALID REQUEST! You must select from the options provided.']);
            }
        }
    }
}
