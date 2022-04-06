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

use PDF;

class PdfController extends Controller
{
    public function view($id){
        $row     = Passenger::where('passenger_id',$id)->first();
        return view('passenger')->with('row',$row);
    }
    public function logs($id){
        $row     = Passenger::where('passenger_id',$id)->first();
        return view('logs')->with('row',$row);
    }
}
