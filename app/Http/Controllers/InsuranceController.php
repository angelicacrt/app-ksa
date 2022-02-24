<?php

namespace App\Http\Controllers;

use Storage;
use Carbon\Carbon;
use App\Models\spgrfile;
use App\Models\Rekapdana;
use App\Exports\FCIexport;
use App\Exports\RekapAdminExport;
use Illuminate\Http\Request;
use App\Models\headerformclaim;
use Illuminate\Support\Facades\DB;
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
        $UploadNotes = DB::table('note_spgrs')->latest()->get();
        return view('insurance.insuranceHistoryNotes', compact('UploadNotes'));
    }
    
    //History form claim page
    public function historyFormclaim(){
        $Headclaim = headerformclaim::all();
        return view('insurance.insuranceHistoryFormclaim', compact('Headclaim'));
    }
   
    

    //History form claim download func
    private $excel;
    public function __construct(Excel $excel){
        $this->excel = $excel;
    }
    public function historyFormclaimDownload(Request $request) {
        // dd($request);
        $name = $request->file_name;
        $identify = $request->file_id;
        return $this->excel::download(new FCIexport($identify), 'FCI'.$name.'.xlsx');
    }
    
    //export Rekap page
    public function exportPDF(){
        $date = Carbon::now();
        $monthName = $date->format('F');

        return Excel::download(new RekapAdminExport, 'RekapDanaInsuranceManager'. '-' . $monthName . '-' .'.pdf' , \Maatwebsite\Excel\Excel::DOMPDF);
    }

    //export Rekap page
    public function exportEXCEL(){
        $date = Carbon::now();
        $monthName = $date->format('F');
        return Excel::download(new RekapAdminExport, 'RekapDanaInsuranceManager'. '-' . $monthName . '-' . '.xlsx');
    }

    //History Rekapulsi Dana page
    public function historyRekapulasiDana(){
        $datetime = date('Y-m-d');
        $rekapdana= Rekapdana::latest()
        ->paginate(25);
        return view('insurance.insuranceRekapulasiDana', compact('rekapdana'));
    }
}
