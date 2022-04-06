<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Beneficiary;
use App\Dependent;
use App\Upload;
use App\Service;
use App\Requirement;
use App\Application;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

use Codedge\Fpdf\Fpdf\Fpdf;

use Twilio\Rest\Client;

use Illuminate\Support\Facades\DB;

class RequestsController extends Controller
{
    private $fpdf;
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
        return view('requests.index');
    }

    public function submit(){
        return view('requests.submit');
    }

    public function archived(){
        return view('requests.archived');
    }

    public function new(){
        return view('requests.new');
    }

    public function details(Request $request){
        if($request->ajax())
        {
            $beneficiary_id      = $request->input('beneficiary_id');
            $services_id         = $request->input('services_id');
            $with_requirements   = $request->input('with_requirements');

            if($with_requirements === 'yes')
            {
                $services_id      = $request->input('services_id');

                $result         = DB::table('uploads')
                                    ->join('requirements','uploads.requirement_id','=','requirements.requirement_id')
                                    ->where('beneficiary_id',$beneficiary_id)
                                    ->where('uploads.services_id',$services_id)
                                    ->get();
                                    
                $request        = Service::where('services_id',$services_id)->first();

                $row            = DB::table('beneficiaries')
                                ->join('brgy','beneficiaries.barangay','=','brgy.brgyCode')
                                ->join('cm','beneficiaries.city_municipality','=','cm.citymunCode')
                                ->join('provinces','beneficiaries.province','=','provinces.provCode')
                                ->join('regions','beneficiaries.region','=','regions.regCode')
                                ->where('beneficiaries.beneficiary_id',$beneficiary_id)
                                ->first();
            }
            else
            {
                $result         = "";
                $request         = "";
                $row            = DB::table('beneficiaries')
                                ->join('brgy','beneficiaries.barangay','=','brgy.brgyCode')
                                ->join('cm','beneficiaries.city_municipality','=','cm.citymunCode')
                                ->join('provinces','beneficiaries.province','=','provinces.provCode')
                                ->join('regions','beneficiaries.region','=','regions.regCode')
                                ->where('beneficiaries.beneficiary_id',$beneficiary_id)
                                ->first();
            }
            $html    = view('requests.details')->with(['row' => $row, 'result' => $result, 'request' => $request,  'with_requirements' => $with_requirements])->render();
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        }
    }


    public function load(Request $request){
        if($request->ajax())
        {
           $beneficiary_id  = $request->input('beneficiary_id');
           
           if(empty($beneficiary_id))
           {
                return response()->json(['error' => 'Improper usage detected. Please reload page.']);
           }

           $result  = Dependent::where('beneficiary_id',$beneficiary_id)->get();
           
           $html = "";
           if(count($result) > 0)
           {
               foreach($result as $row)
               {
                    $html .= '<tr class="records bold" id="record_'.$row->dependent_id.'">
                                <td>'.strtoupper($row->member_name).'</td>
                                <td>'.strtoupper($row->member_relation).'</td>
                                <td>'.strtoupper($row->date_of_birth).'</td>
                                <td>'.strtoupper($row->sex).'</td>
                                <td>'.strtoupper($row->member_occupation).'</td>
                                <td>'.strtoupper($row->member_sector).'</td>
                                <td>'.strtoupper($row->member_health_condition).'</td>
                                <td class="px-2 text-center">
                                    <a href="javascript:void(0)"
                                        class="member_edit w3-medium m-0 green"
                                        data-dependent_id="'.$row->dependent_id.'"
                                        data-member_name="'.$row->member_name.'"
                                        data-member_relation="'.$row->member_relation.'"
                                        data-date_of_birth="'.$row->date_of_birth.'"
                                        data-sex="'.$row->sex.'"
                                        data-member_occupation="'.$row->member_occupation.'"
                                        data-member_sector="'.$row->member_sector.'"
                                        data-member_health_condition="'.$row->member_health_condition.'">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td class="px-2">
                                    <a href="javascript:void(0)"
                                        class="member_delete w3-medium m-0 green"
                                        data-dependent_id="'.$row->dependent_id.'"
                                        data-beneficiary_id="'.$row->beneficiary_id.'">
                                        <i class="fa fa-remove"></i>
                                    </a>
                                </td>
                            </tr>';
               }
                
           }
           else
           {
                $html .= '';
           }

           return response()->json(['success' => true, 'html' => $html]);
        }
    }

    public function store(Request $request){
        if($request->ajax())
        {
            $rules = array(
                'firstname' => ['required', 'string', 'max:255'],
                'middlename' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'gender' => ['required'],
                'region' => ['required'],
                'province' => ['required'],
                'city_municipality' => ['required'],
                'barangay' => ['required'],
                'type_of_id' => ['required'],
                'id_number' => ['required'],
                'birthdate' => ['required'],
                'occupation' => ['required'],
                'monthly_income' => ['required'],
                'contact_number' => ['required', 'digits:10']
            );

            $error  = Validator::make($request->all(),$rules);
                
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $beneficiary_id     = $request->input('beneficiary_id');    

            $firstname          = strtolower($request->input('firstname'));
            $middlename         = strtolower($request->input('middlename'));
            $lastname           = strtolower($request->input('lastname'));
            $suffix             = $request->input('suffix');
            $gender             = $request->input('gender');

            $region                = $request->input('region');
            $province              = $request->input('province');
            $city_municipality     = $request->input('city_municipality');
            $barangay              = $request->input('barangay');
            $street                = $request->input('street');
            $address_line          = $request->input('address_line');

            $type_of_id             = $request->input('type_of_id');
            $id_number              = $request->input('id_number');
            $birthdate              = $request->input('birthdate');
            $occupation             = $request->input('occupation');
            $monthly_income         = $request->input('monthly_income');
            $contact_number         = $request->input('contact_number');
            $workplace_and_address  = $request->input('workplace_and_address');
            $sector                 = $request->input('sector');
            $health_condition       = $request->input('health_condition');
            $beneficiary_type       = $request->input('beneficiary_type');
            $ip_group               = $request->input('ip_group');
            $beneficiary_type_others= $request->input('beneficiary_type_others');
            $id                     = Auth::user()->id;

            
            

            if(!empty($beneficiary_id))
            {
                $check  = Beneficiary::where('beneficiary_id',$beneficiary_id)->first();
            }

            if(!empty($check))
            {
                $users   = Beneficiary::where('beneficiary_id',$beneficiary_id)->first();
            }
            else
            {
                $users   = new Beneficiary;
            }

            $users->firstname       = $firstname;
            $users->middlename      = $middlename;
            $users->lastname        = $lastname;   
            $users->suffix          = $suffix;
            $users->gender          = $gender;
            $users->region                  = $region;
            $users->province                = $province;
            $users->city_municipality       = $city_municipality;
            $users->barangay                = $barangay;
            $users->street                  = $street;
            $users->address_line            = $address_line;
            $users->type_of_id              = $type_of_id;
            $users->id_number               = $id_number;
            $users->birthdate               = $birthdate;
            $users->occupation              = $occupation;
            $users->monthly_income          = $monthly_income;
            $users->contact_number          = $contact_number;
            $users->workplace_and_address   = $workplace_and_address;
            $users->sector                  = $sector;
            $users->health_condition        = $health_condition;
            $users->beneficiary_type        = $beneficiary_type;
            $users->ip_group                = $ip_group;
            $users->beneficiary_type_others = $beneficiary_type_others;
            $users->id                      = $id;

            try{
                $users->save();
                $pk         = $users->beneficiary_id;
                return response()->json([
                    'success' => 'Beneficiary has been successfully saved!',
                    'beneficiary_id' => $pk
                ]);

            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function save(Request $request){
        if($request->ajax())
        {
            $rules = array(
                'member_name' => ['required', 'string', 'max:255'],
                'member_relation' => ['required', 'string', 'max:255'],
                'date_of_birth' => ['required'],
                'sex' => ['required'],
                'member_occupation' => ['required'],
                'member_sector' => ['required'],
                'member_health_condition' => ['required'],
                'beneficiary' => ['required'],
            );

            $error  = Validator::make($request->all(),$rules);
                
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $dependent_id           = $request->input('dependent_id');
            $member_name            = strtolower($request->input('member_name'));
            $member_relation        = strtolower($request->input('member_relation'));
            $date_of_birth          = strtolower($request->input('date_of_birth'));
            $sex                    = $request->input('sex');
            $member_occupation      = $request->input('member_occupation');
            $member_sector          = $request->input('member_sector');
            $member_health_condition= $request->input('member_health_condition');
            $beneficiary_id         = $request->input('beneficiary');
            $id                     = Auth::user()->id;
            
            if(!empty($dependent_id))
            {
                $check  = Dependent::where('dependent_id',$dependent_id)->first();
            }

            if(!empty($check))
            {
                $users   = Dependent::where('dependent_id',$dependent_id)->first();
            }
            else
            {
                $users   = new Dependent;
            }

            $users->member_name             = $member_name;
            $users->member_relation         = $member_relation;
            $users->date_of_birth           = $date_of_birth;   
            $users->sex                     = $sex;
            $users->member_occupation       = $member_occupation;
            $users->member_sector           = $member_sector;
            $users->member_health_condition = $member_health_condition;
            $users->beneficiary_id          = $beneficiary_id;
            $users->id                      = $id;

            try{
                $users->save();
                return response()->json([
                    'success' => 'Family member has been successfully added!',
                    'beneficiary_id' => $beneficiary_id
                ]);

            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function delete(Request $request){
        $beneficiary_id     = $request->input('beneficiary_id');

        if(empty($beneficiary_id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $data  = Beneficiary::where('beneficiary_id',$beneficiary_id)->first();
        

        if(empty($data))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }
        
        Dependent::where('beneficiary_id',$beneficiary_id)->delete();
        Application::where('beneficiary_id',$beneficiary_id)->delete();
        Upload::where('beneficiary_id',$beneficiary_id)->delete();

        try{
            $data->delete();
            return response()->json([
                'success' => 'Record has been permanently deleted!',
            ]);

        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function archive(Request $request){
        $beneficiary_id     = $request->input('beneficiary_id');

        if(empty($beneficiary_id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $data  = Beneficiary::where('beneficiary_id',$beneficiary_id)->first();
        

        if(empty($data))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }

        if($data->is_archived == true)
        {
            $data->is_archived  = false;
            $message    = 'Record has been restored.';
        }
        else
        {
            $data->is_archived  = true;
            $message    = 'Record has been added to archives.';
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

    public function archives(Request $request){
        $application_id     = $request->input('application_id');

        if(empty($application_id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $data  = Application::where('application_id',$application_id)->first();
        

        if(empty($data))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }

        if($data->is_archived == true)
        {
            $data->is_archived  = false;
            $message    = 'Record has been restored.';
        }
        else
        {
            $data->is_archived  = true;
            $message    = 'Record has been added to archives.';
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

    public function trash(Request $request){
        $application_id     = $request->input('application_id');

        if(empty($application_id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $data  = Application::where('application_id',$application_id)->first();
        

        if(empty($data))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }

        try{
            $data->delete();
            return response()->json([
                'success' => 'Record has been permanently deleted!',
            ]);

        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function remove(Request $request){
        $dependent_id     = $request->input('dependent_id');

        if(empty($dependent_id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $data  = Dependent::where('dependent_id',$dependent_id)->first();

        if(empty($data))
        {
            return response()->json(['error' => "Invalid request detected!"]);
        }

        try{
            $data->delete();
            return response()->json([
                'success' => 'Record has been successfully deleted!',
            ]);

        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function check(Request $request){
        $beneficiary_id     = $request->input('beneficiary_id');

        if(empty($beneficiary_id))
        {
            return response()->json(['error' => "Submitted data is empty!"]);
        }

        $html = '';
        
        $results = Service::orderBy('services_id')->get();
        $html .= '<center>
                    <h3><b>Select from AICS Services</b></h3>
                </center>
                
                <p class="p-0 my-0"><b class="w3-white px-3 w3-border"></b> &nbsp;: Requirements must be added.</p>
                <p class="p-0 my-0"><b class="w3-pale-red px-3 w3-border"></b> &nbsp;: No requirement needed. Click to directly add to applications.</p>
                
                <ul class="list-group">'; 
        foreach($results as $rows)
        {
            $requirements = Requirement::where('services_id',$rows->services_id)->get();
            if(count($requirements) > 0)
            {
                $action = 'pointer w3-hover-green select_service';
            }
            else
            {
                $action = 'add_to_applications pointer w3-pale-red';
            }

            $html .= '<li class="list-group-item text-center '.$action.'"
                        data-services_id="'.$rows->services_id.'"
                        data-beneficiary_id="'.$beneficiary_id.'"
                        data-aics_services="'.ucwords($rows->aics_services).'">
                        <b class="w3-large">'.ucwords($rows->aics_services).'</b><br />
                        <p class="p-0 m-0"><small>'.count($requirements).' requirement/s</small></p>
                    </li>';
        }
        $html .= '</ul>';

        try{
            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    public function verify(Request $request){
        $beneficiary_id     = $request->input('beneficiary_id');
        $services_id        = $request->input('services_id');
        $aics_services        = $request->input('aics_services');

        $html = '';

        $req    = Requirement::where('services_id',$services_id)->get();
        
        $result = DB::table('uploads')
                        ->join('requirements','uploads.requirement_id','=','requirements.requirement_id')
                        ->where('uploads.services_id',$services_id)
                        ->where('uploads.beneficiary_id',$beneficiary_id)->get();

        if(count($result) > 0)
        {
            $status = 'existing';

            $html .= '<center><h3 class="my-0"><b>'.$aics_services.'</b></h3></center>
                    <table class="table table-sm table-condensed">
                        <thead>
                            <tr>
                                <th>REQUIREMENT</th>
                                <th>TYPE</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center">UPLOAD</th>
                            </tr>
                        </thead>'; 
            foreach($result as $row)
            {
                $html .= '<tr>
                            <td>'.strtoupper($row->requirement_description).'</td>
                            <td>'.strtoupper($row->requirement_type).'</td>
                            <td class="text-center">';
                                if(empty($row->uploaded_file))
                                {
                                    $html .= '<i class="fa fa-remove w3-text-red"></i>';
                                }
                                else
                                {
                                    $html .= '<i class="fa fa-check w3-text-green"></i>';
                                }
                    $html .='</td>
                            <td class="text-center">
                                <a href="javascript:void(0)"
                                    class="upload_file"
                                    data-beneficiary_id="'.$beneficiary_id.'"
                                    data-services_id="'.$services_id.'"
                                    data-aics_services="'.$aics_services.'"
                                    data-upload_id="'.$row->upload_id.'"
                                    data-requirement_type="'.strtolower($row->requirement_type).'">
                                    <i class="fa fa-file green"><i class="fa fa-plus w3-tiny ml-1"></i></i>
                                </a>
                            </td>
                        </tr>';
            }
            $html .= '</table>
            <div class="row">
                <div class="col-lg-6">
                    <a href="javascript:void(0)"
                        class="btn w3-border green w3-large upload"
                        data-beneficiary_id="'.$beneficiary_id.'">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-lg-6">
                    <a href="javascript:void(0)"
                        class="btn btn-success px-4 add_to_applications"
                        data-beneficiary_id="'.$beneficiary_id.'"
                        data-services_id="'.$services_id.'">
                        <b><i class="fa fa-save"></i> ADD TO APPLICATIONS</b>
                    </a>
                </div>     
            </div>';

        }
        elseif(count($req) > 0)
        {
            $status = 'new';
            foreach($req as $value)
            {
                $add = new Upload;
                $add->beneficiary_id    = $beneficiary_id;
                $add->services_id       = $services_id;
                $add->requirement_id    = $value->requirement_id;
                $add->id                = Auth::user()->id;
                $add->save();
            }
        }
        else
        {
            $status = 'n/a';
            return response()->json(['status' => 'No requirements.']);
        }

        try{
            return response()->json([
                'success' => true,
                'html' => $html,
                'status' => $status
            ]);

        }catch(QueryException $e){
            return response()->json(['error' => $e->errorInfo[2]]);
        }
    }

    

    public function upload(Request $request){
        if ($request->ajax()) {
            $requirement_type   = $request->input('requirement_type');

            if($requirement_type === 'image')
            {
                $rules  = array(
                    'uploaded_file' => ['image','required','max:1999']
                );
            }
            else
            {
                $rules  = array(
                    'uploaded_file' => ['mimes:'.$requirement_type,'required','max:1999']
                );
            }
            

            $error  = Validator::make($request->all(),$rules);        
            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }

            if ($request->hasFile('uploaded_file')) 
            {
                $filenameWithExtension   = $request->file('uploaded_file')->getClientOriginalName();

                $file                    = pathinfo($filenameWithExtension, PATHINFO_FILENAME);

                $extension               = $request->file('uploaded_file')->getClientOriginalExtension();

                $toStore                 = $file.'_'.time().'.'.$extension;
            }
            else
            {
                return response()->json(['error' => 'No file was added.']);
            }

            $upload_id   = $request->input('upload_id');
            $check  = Upload::where('upload_id',$upload_id)->first();

            if (!empty($check->uploaded_file)) 
            {
                $filex   = 'public/requirements/'.$check->uploaded_file;
                if (Storage::exists($filex)) 
                {
                    Storage::delete($filex);   
                }
            }

            $id         = Auth::user()->id;
            $upload     = Upload::where('upload_id',$upload_id)->first();
            $upload->uploaded_file  = $toStore;

            try{
                $upload->save();
                $path    = $request->file('uploaded_file')->storeAs('public/requirements',$toStore);
                return response()->json([
                    'success' => 'File has been successfully uploaded.'
                ]);
            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function application_save(Request $request){
        if($request->ajax())
        {
            $rules = array(
                'beneficiary_id' => ['required', 'integer'],
                'services_id' => ['required', 'integer']
            );

            $error  = Validator::make($request->all(),$rules);
                
            if($error->fails())
            {
                return response()->json(['error' => 'Invalid server request!']);
            }

            $beneficiary_id      = $request->input('beneficiary_id');
            $services_id         = $request->input('services_id');
            $id                  = Auth::user()->id;
            
            $check      = Upload::where('beneficiary_id',$beneficiary_id)
                                ->where('services_id',$services_id)
                                ->where('uploaded_file','')
                                ->get();

            if(count($check) > 0)
            {
                return response()->json(['error' => 'Incomplete requirement detected. Please complete requirements first.']);
            }

            $verify     = Application::where('beneficiary_id',$beneficiary_id)
                                ->where('services_id',$services_id)
                                ->first();
            if(!empty($verify))
            {
                return response()->json(['error' => 'An existing application found. Duplicate applications are not allowed.']);
            }

            $data   = new Application;
            $data->beneficiary_id   = $beneficiary_id;
            $data->services_id      = $services_id;
            $data->id               = Auth::user()->id;
            try{
                $data->save();
                return response()->json([
                    'success' => 'Application has been successfully added to list.',
                ]);

            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function submit_application(Request $request){
        if($request->ajax())
        {
            $application_id      = $request->input('application_id');
            
            $check      = Application::where('application_id',$application_id)->first();

            if(empty($check))
            {
                return response()->json(['error' => 'Incomplete requirement detected. Please complete requirements first.']);
            }

            $update     = Application::where('application_id',$application_id)->first();

            if($update->is_submitted == false)
            {
                $update->is_submitted = true;
                $update->date_submitted = date('Y-m-d');
            }


            try{
                $update->save();
                return response()->json([
                    'success' => true,
                ]);

            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function update(Request $request){
        if($request->ajax())
        {
            $application_id      = $request->input('application_id');
            $action              = $request->input('action');
            
            $check      = Application::where('application_id',$application_id)->first();

            if(empty($check))
            {
                return response()->json(['error' => 'Incomplete requirement detected. Please complete requirements first.']);
            }

            $update     = Application::where('application_id',$application_id)->first();

            if($action === 'approve')
            {
                if($check->is_approved == true)
                {
                    return response()->json(['error' => 'This request was already approved.']);
                }

                $update->is_approved = true;
                $message    = 'approved.';
            }
            else
            {
                if($check->is_approved == false)
                {
                    return response()->json(['error' => 'This request was already disapproved.']);
                }

                $update->is_approved = false;
                $message    = 'disapproved.';
            }


            try{
                $update->is_completed = true;
                $update->save();
                return response()->json([
                    'success' => "Request has been ".$message,
                ]);

            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function send(Request $request){
        if($request->ajax())
        {
            $beneficiary_id      = $request->input('beneficiary_id');
            $services_id         = $request->input('services_id');
            $action              = $request->input('action');
            $aics_services       = $request->input('aics_services');
            
            $data       = Beneficiary::where('beneficiary_id',$beneficiary_id)->first();

            try{
                $account_sid = env("TWILIO_SID");
                $auth_token = env("TWILIO_AUTH_TOKEN");
                $twilio_number = env("TWILIO_NUMBER");
                $recipient  = "+63".$data->contact_number;
                $message    = "Your request for ".$aics_services." has been ".$action.". \n\n Thank you for your requesting in our services. \n\n -MSWD";

                $client = new Client($account_sid, $auth_token);

                $client->messages->create($recipient, 
                ['from' => $twilio_number, 'body' => $message]);


                return response()->json([
                    'success' => "Message has been sent!",
                ]);

            }catch(QueryException $e){
                return response()->json(['error' => $e->errorInfo[2]]);
            }
        }
    }

    public function admin(){
        return view('requests.archived-admin');
    }


    public function view_details($beneficiary_id){

        $row            = DB::table('beneficiaries')
                                ->join('brgy','beneficiaries.barangay','=','brgy.brgyCode')
                                ->join('cm','beneficiaries.city_municipality','=','cm.citymunCode')
                                ->join('provinces','beneficiaries.province','=','provinces.provCode')
                                ->join('regions','beneficiaries.region','=','regions.regCode')
                                ->where('beneficiaries.beneficiary_id',$beneficiary_id)
                                ->first();

        $this->fpdf = new Fpdf;
        $this->fpdf->AddPage();
        $this->fpdf->Image(asset('images/sac.jpg'), 0,0,210,300);
        $this->fpdf->Link(0,0,220,40, '/beneficiaries');
        $this->fpdf->AddLink();
        $this->fpdf->SetTitle('DSWD');
        $this->fpdf->SetFont("Arial", "B", "8");

        $this->fpdf->Cell(195,42,"",0,1);
        
        $this->fpdf->Cell(163,5,"",0,0);
        if(strtolower($row->gender) === 'male')
        {
            $this->fpdf->Cell(30,5,"X",0,1);
        }
        else
        {
            $this->fpdf->Cell(30,5,"",0,1);
        }
        
        
        $this->fpdf->SetFont("Arial", "B", "10");
        $this->fpdf->Cell(15,5,"",0,0);
        $this->fpdf->Cell(148,5,strtoupper($row->lastname.', '.$row->firstname.' '.$row->middlename.' '.$row->suffix),0,0);
        if(strtolower($row->gender) === 'female')
        {
            $this->fpdf->Cell(30,5,"X",0,1);
        }
        else
        {
            $this->fpdf->Cell(30,5,"",0,1);
        }
        $this->fpdf->Cell(190,1,"",0,1);

        $this->fpdf->Cell(10,5,"",0,0);
        $this->fpdf->Cell(70,5,strtoupper($row->address_line),0,0);
        $this->fpdf->Cell(70,5,strtoupper($row->street),0,0);
        $this->fpdf->Cell(40,5,strtoupper($row->type_of_id),0,1);

        $this->fpdf->Cell(190,2,"",0,1);
        $this->fpdf->Cell(10,5,"",0,0);
        $this->fpdf->Cell(80,5,strtoupper($row->brgyDesc),0,0);
        $this->fpdf->Cell(65,5,strtoupper($row->citymunDesc),0,0);
        $this->fpdf->Cell(40,5,strtoupper($row->id_number),0,1);

        $this->fpdf->Cell(190,1,"",0,1);

        $this->fpdf->Cell(10,5,"",0,0);
        $this->fpdf->Cell(73,5,strtoupper($row->provDesc),0,0);
        $this->fpdf->Cell(87,5,strtoupper($row->regDesc),0,0);
        $this->fpdf->Cell(25,5,date('m-d-Y',strtotime($row->birthdate)),0,1);

        $this->fpdf->Cell(190,3,"",0,1);

        $this->fpdf->Cell(10,5,"",0,0);
        $this->fpdf->Cell(82,5,strtoupper($row->occupation),0,0);
        $this->fpdf->Cell(69,5,strtoupper($row->monthly_income),0,0);
        $this->fpdf->Cell(35,5,'+63'.$row->contact_number,0,1);

        $this->fpdf->Cell(190,2,"",0,1);

        $this->fpdf->Cell(40,5,"",0,0);
        $this->fpdf->Cell(72,5,strtoupper($row->workplace_and_address),0,0);
        $this->fpdf->Cell(50,5,strtoupper($row->sector),0,0);
        $this->fpdf->Cell(35,5,strtoupper($row->health_condition),0,1);

        $this->fpdf->Cell(190,3,"",0,1);

        if($row->beneficiary_type === 'UCT BENEFICIARY')
        {
            $this->fpdf->Cell(40,5,"X",0,0);
            $this->fpdf->Cell(42,5,"",0,0);
            $this->fpdf->Cell(55,5,"",0,0);
            $this->fpdf->Cell(60,5,"",0,1);
        }
        elseif($row->beneficiary_type === '4PS BENEFICIARY')
        {
            $this->fpdf->Cell(40,5,"",0,0);
            $this->fpdf->Cell(42,5,"X",0,0);
            $this->fpdf->Cell(55,5,"",0,0);
            $this->fpdf->Cell(60,5,"",0,1);
        }
        elseif($row->beneficiary_type === 'INDIGENOUS PEOPLE')
        {
            $this->fpdf->Cell(40,5,"",0,0);
            $this->fpdf->Cell(42,5,"",0,0);
            $this->fpdf->Cell(55,5,"X --- ".strtoupper($row->ip_group),0,0);
            $this->fpdf->Cell(60,5,"",0,1);
        }
        else
        {
            $this->fpdf->Cell(40,5,"",0,0);
            $this->fpdf->Cell(42,5,"",0,0);
            $this->fpdf->Cell(55,5,"",0,0);
            $this->fpdf->Cell(60,5,"X --- ".strtoupper($row->beneficiary_type_others),0,1);
        }

        $this->fpdf->Cell(190,26,"",0,1);
        $this->fpdf->SetFont("Arial", "B", "7");


        $result     = Dependent::where('beneficiary_id',$beneficiary_id)->get();
        if(count($result) > 0)
        {
            foreach($result as $rows)
            {
                if(strtolower($rows->sex) === 'male')
                {
                    $sex    = 'M';
                }
                else
                {
                    $sex    = 'F';
                }
                $sect     = $rows->member_sector;

                if($sect === 'SENIOR CITIZEN')
                {
                    $sector = 'A';
                }
                elseif($sect === 'PREGNANT WOMAN')
                {
                    $sector = 'B';
                }
                elseif($sect === 'BREASTFEEDING MOTHER')
                {
                    $sector = 'C';
                }
                elseif($sect === 'PWD')
                {
                    $sector = 'D';
                }
                elseif($sect === 'SOLO PARENT')
                {
                    $sector = 'E';
                }
                elseif($sect === 'INDIGENT')
                {
                    $sector = 'F';
                }
                else
                {
                    $sector = 'N/A';
                }

                $health     = $rows->member_health_condition;

                if($health === 'HEART PROBLEM')
                {
                    $condition = '1';
                }
                elseif($health === 'HYPERTENSION')
                {
                    $condition = '2';
                }
                elseif($health === 'LUNG PROBLEM')
                {
                    $condition = '3';
                }
                elseif($health === 'DIABETES')
                {
                    $condition = '4';
                }
                elseif($health === 'CANCER')
                {
                    $condition = '5';
                }
                else
                {
                    $condition = 'N/A';
                }

                $this->fpdf->Cell(48,7.5,strtoupper($rows->member_name),0,0);
                $this->fpdf->Cell(20,7.5,strtoupper($rows->member_relation),0,0);
                $this->fpdf->Cell(32,7.5,date('m/d/Y', strtotime($rows->date_of_birth)),0,0,"C");
                $this->fpdf->Cell(16,7.5,strtoupper($sex),0,0,"C");
                $this->fpdf->Cell(32,7.5,strtoupper($rows->member_occupation),0,0,'C');
                $this->fpdf->Cell(16,7.5,$sector,0,0,'C');
                $this->fpdf->Cell(26,7.5,$condition,0,1,'C');
            }
        }
        else
        {

        }
        $this->fpdf->Output();
        exit;
    }
    
}
