<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Inertia\Inertia;
use App\Models\People;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CompanyDocument;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CompanyValidateRequest;

class CompanyController extends Controller
{
    
    public function index(Request $request){

      if(!empty($request->term) && $request->active_status == 'Active'){

        $companies = Company::when($request->term,function($query,$term){
         $query->where('name','LIKE','%'.$term.'%')
               ->whereNotNull('active');
        })->get();

        // $companies = Company::with(["company","licence_type"])
        //          ->whereNotNull('is_licence_active')
        //         ->where(function($query) use($request){
        //         $query->where('trading_name','LIKE','%'.$request->term.'%')
        //         ->orWhere('licence_number','LIKE','%'.$request->term.'%')
        //         ->orWhere('old_licence_number','LIKE','%'.$request->term.'%');
        //         })->get();
    }elseif($request->active_status == "All" && empty($request->term)){
        $companies = Company::get();
    }elseif(!empty($request->term) && empty($request->active_status) ){
            $companies = Company::when($request->term,function($query,$term){
                $query->where('name','LIKE','%'.$term.'%');
               })->get();

    }elseif(!empty($request->term) && $request->active_status == 'Inactive'){

        $companies = Company::when($request->term,function($query,$term){
            $query->where('name','LIKE','%'.$term.'%')
                  ->whereNull('active');
           })->get();
    
    }else{
        $companies = Company::whereNull('name')->get();
    }
        return Inertia::render('Company',['companies'=> $companies]);
    }

    public function create() {
        return Inertia::render('CreateNewCompany');
    }

    public function store(CompanyValidateRequest $request)
    {       
            $company = Company::create([
                'name' => $request->company_name,
                'company_type' => $request->company_type,
                'reg_number' => $request->reg_number,
                'vat_number' => $request->vat_number,
                'email' => $request->email_address_1,
                'email1' => $request->email_address_2,
                'email2' => $request->email_address_3,
                'tel_number' => $request->telephone_number_1,
                'tel_number1' => $request->telephone_number_2,
                'website' => $request->website,
                'business_address' => $request->business_address,
                'business_address2' => $request->business_address2,
                'business_address3' => $request->business_address3,
                'business_province' => $request->business_province,
                'business_address_postal_code' => $request->business_address_postal_code,
                'postal_address' => $request->postal_address,
                'postal_code2' => $request->postal_address2,
                'postal_code3' => $request->postal_address3,
                'postal_province' => $request->postal_province,
                'postal_code' => $request->postal_code,
                'active' => $request->active,
                'slug' => Str::replace(' ','_',$request->company_name).sha1(time()),
            ]);

            if($company ){
                return redirect('/companies')->with('success', 'Company successfully created!');
            }
            return back()->with('error', 'Ooops!!Error creating company!');
        
    }


    public function show($slug){
        $company = Company::with('users','licences','people')->whereSlug($slug)->first();
        $contrib_cert = CompanyDocument::where('company_id',$company->id)->where('document_type','Contribution-Certificate')->get();
        $bee_cert = CompanyDocument::where('company_id',$company->id)->where('document_type','BEE-Certificate')->get();
        $cipc_cert = CompanyDocument::where('company_id',$company->id)->where('document_type','CIPC-Certificate')->get();
        $lta_cert = CompanyDocument::where('company_id',$company->id)->where('document_type','LTA-Certificate')->get();
        $company_doc = CompanyDocument::where('company_id',$company->id)->where('document_type','Company-Document')->get();
        $tasks = Task::where('model_type','Company')->where('model_id',$company->id)->whereUserId(auth()->id())->get();
        $people = People::pluck('full_name','id');
        
        return Inertia::render('ViewCompany',[
            'company'=> $company,
            'people' => $people,
             'tasks' => $tasks,
             'contrib_cert' => $contrib_cert,
             'bee_cert' => $bee_cert,
             'cipc_cert' => $cipc_cert,
             'lta_cert' => $lta_cert,
             'company_doc' => $company_doc
            ]);
    }

    public function update(Request $request){
        $company = Company::whereId($request->company_id)->first();
        $company->update([
            'name' => $request->company_name,
            'company_type' => $request->company_type,
            'reg_number' => $request->reg_number,
            'vat_number' => $request->vat_number,
            'email' => $request->email_address_1,
            'email1' => $request->email_address_2,
            'email2' => $request->email_address_3,
            'tel_number' => $request->telephone_number_1,
            'tel_number1' => $request->telephone_number_2,            
            'website' => $request->website,            
            'business_address' => $request->business_address,
            'business_address2' => $request->business_address2,
             'business_address3' => $request->business_address3,
            'business_province' => $request->business_province,
            'business_address_postal_code' => $request->business_address_postal_code,
            'postal_address' => $request->postal_address,
            'postal_code2' => $request->postal_address2,
            'postal_code3' => $request->postal_address3,
            'postal_province' => $request->postal_province,
            'postal_code' => $request->postal_code,
            'active' => $request->active,
        ]);
        if($company){
            return to_route('view_company',['slug'=> $company->slug])->with('success','Company updated successfully.');
        }
        return to_route('view_company',['slug'=> $company->slug])->with('error','Error occured while updating company.');


    }

    public function attachPeopleToCompany(Request $request,$company_id){
        $company = Company::find($company_id);
        foreach ($request->people as $person) {
            $exist = DB::table('company_people')
                         ->where('company_id',$company->id)
                         ->where('people_id',$person)
                         ->first();
            if(!is_null($exist)){
               continue;
            }
            $company->people()->attach($person);
        }
        return back()->with('message','People selected successfully.');            
        
    }

   
public function updatePeople(Request $request,$pivot_id){
    $update = DB::table('company_people')
    ->whereId($pivot_id)
    ->update(['position' => $request->position]);
    if($update){
        return back()->with('message', $request->full_name.' updated successfully.'); 
    }
}
  /**
     * Unlink person from company.
     */
    public function unlinkPerson($id){
       $unlink = DB::table('company_people')->where('id',$id)->delete();
       if($unlink){
        return back()->with('success','Person removed successfully.');            
        }
        return back()->with('error','Error..Something went wrong.');
    }

}
