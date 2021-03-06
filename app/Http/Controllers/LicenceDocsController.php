<?php

namespace App\Http\Controllers;

use App\Models\LicenceDocument;
use Illuminate\Http\Request;

class LicenceDocsController extends Controller
{
    public function store(Request $request){
        $request->validate([
            "original_licence_doc"=> "required|mimes:pdf"
            ]);
            $get_file_name = explode(".",$request->original_licence_doc->getClientOriginalName());
        // foreach ($request->original_licence_doc as $doc) {
            $store_file = $request->original_licence_doc->store('licenceDocuments','public'); 
           LicenceDocument::create([
                "licence_id" => $request->licence_id,
                "document_name" => $get_file_name[0],
                "document_file" => $store_file,
                "document_type" => $request->doc_type
               ]);
       
      return back()->with('success','Document uploaded successfully.');
    
    }

    public function destroy($id){
        $model = LicenceDocument::find($id);
        if(!is_null($model->document_file)){
            // unlink(storage_path('app/folder/'.$model->document_file));
            unlink(public_path('storage/app/public/'.$model->document_file));
            $model->delete();
            return back()->with('success','Document removed successfully.');
        }
    }
}
