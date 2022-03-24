<?php

namespace App\Http\Controllers;

use Storage;
use Carbon\Carbon;
use App\Models\spgrfile;
use App\Models\documents;
use App\Exports\FCIexport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\documentberau;
use App\Models\documentJakarta;
use App\Models\headerformclaim;
use App\Models\documentsamarinda;
use Illuminate\Support\Facades\DB;
use App\Models\documentbanjarmasin;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class InsuranceController extends Controller
{
    //check SPGR UPLOAD,approved,rejected,view
    public function checkspgr(Request $request){
        $uploadspgr = spgrfile::where('cabang', 'Jakarta')->whereMonth('created_at', date('m'))->latest()->get();

        //Search bar
        //check if search-bar is filled or not
        if ($request->filled('search_no_formclaim')) {
            $uploadspgr = spgrfile::where('no_formclaim', 'Like', '%' . $request->search_no_formclaim . '%')
            ->whereMonth('created_at', date('m'))
            ->orderBy('id', 'DESC')
            ->latest()->get();
        }

        return view('insurance.insuranceSpgr', compact('uploadspgr'));
    }

    public function approvespgr(Request $request){

        $claim = $request->no_claim;
        $filename = $request->viewspgrfile;
        $result = $request->result;

        spgrfile::where('cabang', $request->cabang)
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->where('no_formclaim', 'Like', '%' . $claim . '%')
        ->where($filename, 'Like', '%' . $result . '%')
        ->update([
            $request->status => 'approved'
        ]);
        return redirect('/insurance/CheckSpgr');
    }

    public function rejectspgr(Request $request){
        // dd($request);
        $claim = $request->no_claim;
        $filename = $request->viewspgrfile;
        $result = $request->result;
        
        $request->validate([
            'reasonbox' => 'required|max:255',
        ]);

        spgrfile::where('cabang',$request->cabang)
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->where('no_formclaim', 'Like', '%' . $claim . '%')
        ->where($filename, 'Like', '%' . $result . '%')
        ->update([
            $request->status => 'rejected',
            $request->reason => $request->reasonbox ,
        ]);

        return redirect('/insurance/CheckSpgr');
    }

    public function viewspgr(Request $request){
        // view spgr
        if($request->tipefile == 'SPGR'){
            $year = date('Y');
            $month = date('m');

            $cabang = $request->cabang;
            $filename = $request->viewspgrfile;
            $result = $request->result;
            $claim = $request->no_claim;

            $viewer = spgrfile::where('cabang', 'Jakarta')
            ->whereNotNull ($filename)
            ->where('no_formclaim', 'Like', '%' . $claim . '%')
            ->where($filename, 'Like', '%' . $result . '%')
            ->pluck($filename)[0];
            // dd($request);
            // dd($viewer);
            return Storage::disk('s3')->response('spgr/' . $year . "/". $month . "/" . $viewer);
        }
    }
    
    //history note SPGR page
    public function historynotespgr(){
        $UploadNotes = DB::table('note_spgrs')->latest()->paginate(25);
        return view('insurance.insuranceHistoryNotes', compact('UploadNotes'));
    }
    
    //History form claim page
    public function historyFormclaim(){
        $Headclaim = headerformclaim::latest()->paginate(25);
        return view('insurance.insuranceHistoryFormclaim', compact('Headclaim'));
    }

    public function historyFormclaim_approve(Request $request){
        $year = date('Y');
        $month = date('m');

        $request->validate([
            'FCI_file' => 'mimes:pdf,xlsx|max:3072'
        ]);

        if ($request->hasFile('FCI_file')) {
            $file = $request->File('FCI_file');
            $nameForm = $request->file_name;
            $identify = $request->file_id;
            $name_s3 = $nameForm .'-'. Auth::user()->name . '-' . $file->getClientOriginalName();
            $replaced = Str::replace('/', '_', $name_s3);
            $path = $request->file('FCI_file')->storeas('FormClaim/'. $year . "/". $month , $replaced, 's3');

            $up = headerformclaim::where('id', $identify)
            ->where('nama_file', $nameForm)
            ->update([
                'status'=> 'Approved',
                'approved_file'=> basename($path)
            ]);
            // dd($nameForm);
            return redirect()->back()->with('success', 'FormClaim Uploaded Successfully');
        }else{
            return redirect()->back()->with('failed', 'No FormClaim was Uploaded');
        }
        
        
    }
   
    //History form claim download func
    private $excel;
    public function __construct(Excel $excel){
        $this->excel = $excel;
    }
    public function historyFormclaimExport(Request $request) {
        // dd($request);
        $name = $request->file_name;
        $identify = $request->file_id;
        $replaced = Str::replace('/', '_', $name);
        return $this->excel::download(new FCIexport($identify), date("d-m-Y"). ' - ' .'FCI'. ' - ' . $replaced . '.xlsx');
    }
    public function historyFormclaimDownload(Request $request) {
        $year = date('Y');
        $month = date('m');
        // dd($request);
        // $replaced = Str::replace('/', '_', $name);
        $nameForm = $request->file_name;
        $identify = $request->file_id;
        $viewer = headerformclaim:: where('id', $identify)
        ->where('nama_file', $nameForm)
        ->where('status', 'Approved')
        ->pluck('approved_file')[0];
        // dd($viewer);
        return Storage::disk('s3')->response('FormClaim/' . $year . "/". $month . "/" . $viewer);
    }
    

      // Realisasi Dana page
    public function insuranceRealisasiDana_view(Request $request) {
        $year = date('Y');
        $month = date('m');
            // realisasi Fund Request view ----------------------------------------------------------
            if($request->tipefile == 'Fund_Real' && $request->type_upload == 'Fund_Real'){
            if ($request->cabang == 'Babelan'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documents::whereNotNull($filename)
                ->where('upload_type','Fund_Real')
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('babelan/' . $year . "/". $month . "/" . $viewer);
            }
            if ($request->cabang == 'Berau'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentberau::whereNotNull($filename)
                ->where('upload_type','Fund_Real')
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('berau/' . $year . "/". $month . "/" . $viewer);
            }
            if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentbanjarmasin::whereNotNull($filename)
                ->where('upload_type','Fund_Real')
                ->where('cabang' , $request->cabang)
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('banjarmasin/' . $year . "/". $month . "/" . $viewer);
            }
            if ($request->cabang == 'Samarinda' or $request->cabang == 'Kendari' or $request->cabang == 'Morosi'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentsamarinda::whereNotNull($filename)
                ->where('upload_type','Fund_Real')
                ->where('cabang' , $request->cabang)
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('samarinda/' . $year . "/". $month . "/" . $viewer);
            }
            if ($request->cabang == 'Jakarta'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentJakarta::whereNotNull($filename)
                ->where('upload_type','Fund_Real')
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('jakarta/' . $year . "/". $month . "/" . $viewer);
            }
        }
    }
  
    public function insuranceRealisasiDana(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All') {
            $document = documents::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentberau = documentberau::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentjakarta = documentJakarta::where('upload_type','Fund_Real')->latest()->paginate(10);
            return view('insurance.insuranceRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', Auth::user()->cabang)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', Auth::user()->cabang)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            return view('insurance.insuranceRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif ($request->filled('search')){
             $document = documents::where('cabang', $request->search)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10);
 
             $documentberau = documentberau::where('cabang', $request->search)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10);
 
             $documentbanjarmasin = documentbanjarmasin::where('cabang', $request->search)
             ->where('cabang', Auth::user()->cabang)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10);
 
             $documentsamarinda = documentsamarinda::where('cabang', $request->search)
             ->where('cabang', Auth::user()->cabang)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10);
 
             $documentjakarta = documentJakarta::where('cabang', $request->search)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10);
 
             return view('insurance.insuranceRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif ($request->filled('search_kapal') && $request->filled('search')){
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('cabang', Auth::user()->cabang)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('cabang', Auth::user()->cabang)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10);

            return view('insurance.insuranceRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }else{
            $document = documents::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentberau = documentberau::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentjakarta = documentJakarta::where('upload_type','Fund_Real')->latest()->paginate(10);
            return view('insurance.insuranceRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }    
        return view('insurance.insuranceRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }
}
