<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Company;
use App\Models\Licence;
use App\Models\LicenceDocument;
use App\Models\LicenceType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Task;

class LicenceController extends Controller
{
    public function index(Request $request){

        if($request->term && $request->active_status == 'Active'){

            $licences = Licence::with(["company","licence_type"])
                            ->orWhereHas('company', function($query) use($request){
                                $query->where('name', 'like', '%'.$request->term.'%');
                            })->orWhere('trading_name','LIKE','%'.$request->term.'%')
                            ->where('licence_status','1')
                            ->orWhere('licence_number','LIKE','%'.$request->term.'%')
                            ->orWhere('old_licence_number','LIKE','%'.$request->term.'%')
                            ->get();

        }elseif($request->term && $request->active_status == 'Active'){
    
               $licences = Licence::with(["company","licence_type"])
               ->orWhereHas('company', function($query) use($request){
                   $query->where('name', 'like', '%'.$request->term.'%');
               })->orWhere('trading_name','LIKE','%'.$request->term.'%')
               ->where('licence_status','1')
               ->orWhere('licence_number','LIKE','%'.$request->term.'%')
               ->orWhere('old_licence_number','LIKE','%'.$request->term.'%')
               ->get();
               
        }elseif($request->term){
            $licences = Licence::with(["company","licence_type"])
                            ->orWhereHas('company', function($query) use($request){
                                $query->where('name', 'like', '%'.$request->term.'%');
                            })->orWhere('trading_name','LIKE','%'.$request->term.'%')
                            ->orWhere('licence_number','LIKE','%'.$request->term.'%')
                            ->orWhere('old_licence_number','LIKE','%'.$request->term.'%')
                            ->get();
        
        }elseif($request->term  && $request->active_status == 'Inactive'){
                    $licences = Licence::with(["company","licence_type"])
                            ->orWhereHas('company', function($query) use($request){
                                $query->where('name', 'like', '%'.$request->term.'%');
                            })
                            ->orWhere('trading_name','LIKE','%'.$request->term.'%')
                            ->where('licence_status','!=','1')
                            ->orWhere('licence_number','LIKE','%'.$request->term.'%')
                            ->orWhere('old_licence_number','LIKE','%'.$request->term.'%')
                            ->get();

        }else{
            $licences = Licence::with('company','licence_type')->whereNull('trading_name')->get();
        }
        return Inertia::render('Licences/Licence',['licences' => $licences]);
    }

    public function create(){
        $companies = Company::pluck('name','id');
        $licence_dropdowns = LicenceType::get();
        return Inertia::render('Licences/CreateLicence',['licence_dropdowns' => $licence_dropdowns,
        'companies' => $companies]);
    }

    public function store(Request $request){
       
        $request->validate([
            "trading_name" => "required",
            "licence_type" => "required",
            "company" => "required|exists:companies,id",
            "province" => "required",
        ]);
        Licence::create([
            "trading_name" => $request->trading_name,
            "licence_type" => $request->licence_type,
            "licence_date" => $request->licence_date,
            "company_id"   => $request->company,
            "licence_number" => $request->licence_number,
            "old_licence_number" => $request->old_licence_number,
            "address" => $request->address,
            "province" => $request->province,
            "postal_code" => $request->postal_code,
            "is_licence_active" => $request->is_licence_active,
            'slug' => Str::replace(' ','_',$request->trading_name).sha1(time()),
        ]);
        return redirect(route('licences'))->with('success','Licence created successfully.');
    }

    /**
     * Yes i colud have eager loaded licence with('documents')
     * but the way frontend is structured.
     * And also that its multiple
     */
    public function show(Request $request){
        $licence = Licence::with('company','licence_documents')->whereSlug($request->slug)->first();
        $original_lic = LicenceDocument::where('licence_id',$licence->id)->where('document_type','')
        $companies = Company::pluck('name','id');
        $licence_dropdowns = LicenceType::get();
        $tasks = Task::where('model_type','Licence')->where('model_id',$licence->id)->whereUserId(auth()->id())->get();

        return Inertia::render('Licences/ViewLicence',['licence' => $licence,
                                            'licence_dropdowns' => $licence_dropdowns,
                                             'tasks' => $tasks,
                                             'companies' => $companies
                                            ]);
    }

    public function update(Request $request,$slug){
        
        if(empty($request->change_company)){
            $company_var = $request->company_id;
        }else{
            $request->validate(['change_company'=>'required|exists:companies,id']);
            $company_var = $request->change_company;
        }
        $update = Licence::whereSlug($slug)->update([
            "trading_name" => $request->trading_name,
            "licence_type" => $request->licence_type,
            "licence_date" => $request->licence_date,
            "licence_number" => $request->licence_number,
            "old_licence_number" => $request->old_licence_number,
            "address" => $request->address,
            "province" => $request->province,
            "postal_code" => $request->postal_code,
            "is_licence_active" => $request->is_licence_active,
            "company_id" => $company_var
        ]);
        if($update){
            return back()->with('success','Licence updated successfully.');
        }
        return back()->with('error','Error updating licence.');
    }

    public function destroy($slug){
        $licence = Licence::whereSlug($slug)->first();
        if($licence->delete()){
           return to_route('licences')->with('success','Licences deleted successfully.');
        }
        return to_route('licences')->with('error','Error deleting licence.');
    }

    
}
