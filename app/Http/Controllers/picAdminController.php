<?php

namespace App\Http\Controllers;

use Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\documents;
use App\Models\Rekapdana;
use App\Models\documentrpk;
use Illuminate\Http\Request;
use App\Models\documentberau;
use App\Models\documentJakarta;
use App\Exports\RekapAdminExport;
use App\Models\documentsamarinda;
use Illuminate\Support\Facades\DB;
use App\Models\documentbanjarmasin;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class picAdminController extends Controller
{
    
    //Review Fund Request page for picAdmin
    public function checkform(Request $request){
        $datetime = date('Y-m-d');
        //cabang filter
        $searchresult = $request->search;
        if ($searchresult == 'All') {
            $document = DB::table('documents')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentberau = DB::table('beraudb')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentbanjarmasin = DB::table('banjarmasindb')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentsamarinda = DB::table('samarindadb')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentjakarta = documentJakarta::whereDate('periode_akhir', '>=', $datetime)->latest()->get();
        }
        elseif ($request->filled('search')) {
            $document = DB::table('documents')->where('cabang', $request->search)->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentberau = DB::table('beraudb')->where('cabang', $request->search)->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentbanjarmasin = DB::table('banjarmasindb')->where('cabang', $request->search)->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentsamarinda = DB::table('samarindadb')->where('cabang', $request->search)->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentjakarta = documentJakarta::whereDate('periode_akhir', '>=', $datetime)->where('cabang', $request->search)->latest()->get();
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' ,'documentjakarta'));
        }
        else{{
            $document = DB::table('documents')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentberau = DB::table('beraudb')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentbanjarmasin = DB::table('banjarmasindb')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentsamarinda = DB::table('samarindadb')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentjakarta = documentJakarta::whereDate('periode_akhir', '>=', $datetime)->latest()->get();
        }};

        //Search bar
        //check if search-bar is filled or not
        if ($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            //pakai ->whereDate('periode_akhir', '>=', $datetime)umn agar munculkan data dari pembuatan sampai bulan akhir periode
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->get();

            //berau search bar
            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->get();

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->get();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->get();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->get();
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta'));
        }else{
            $document = documents::whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentberau = documentberau::whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentbanjarmasin = documentbanjarmasin::whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentsamarinda = documentsamarinda::whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            $documentjakarta = documentJakarta::whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta')); 
        }

    }
    
    //review RPK page for picAdmin
    public function checkrpk(Request $request){
        $datetime = date('Y-m-d');
        //filter cabang
        $searchresult = $request->search;
        if ($searchresult == 'All') {
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
        }
        elseif ($request->filled('search')) {
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            return view('picadmin.picAdminRpk' , compact('docrpk'));
        }
        else{
            //jika gk milih cabang
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
        }
        //search bar kapal rpk
        if ($request->filled('search_kapal')) {
            //get DocRPK Data as long as the periode_akhir and search based (column database)
            $docrpk = DB::table('rpkdocuments')
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->get();
            return view('picadmin.picAdminRpk' , compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '>=', $datetime)->latest()->get();
            return view('picadmin.picAdminRpk' , compact('docrpk'));
        }
    }

    //reject for Fund request picAdmin page
    public function reject(Request $request){
        $datetime = date('Y-m-d');
        $request->validate([
            'reasonbox' => 'required|max:180',
        ]);

        if ($request->cabang == 'Babelan'){
            //  dd($request);
            $filename = $request->viewdoc;
            $result = $request->result;
            $kapal_id = $request->kapal_nama;

            documents::where($filename, 'Like', '%' . $result . '%')
            ->where('cabang', $request->cabang)
            ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
            ->whereNotNull($filename)
            ->whereDate('periode_akhir', '>=', $datetime)->update([
                $request->status => 'rejected',
                $request->reason => $request->reasonbox ,
            ]);
        }
        if ($request->cabang == 'Berau'){
            //  dd($request);
            $filename = $request->viewdoc;
            $result = $request->result;
            $kapal_id = $request->kapal_nama;

            documentberau::where($filename, 'Like', '%' . $result . '%')
            ->where('cabang', $request->cabang)
            ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
            ->whereNotNull($filename)
            ->whereDate('periode_akhir', '>=', $datetime)->update([
                $request->status => 'rejected',
                $request->reason => $request->reasonbox ,
            ]);
        }
        if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati'){
            //  dd($request);
            $filename = $request->viewdoc;
            $result = $request->result;
            $kapal_id = $request->kapal_nama;

            documentbanjarmasin::where($filename, 'Like', '%' . $result . '%')
            ->where('cabang', $request->cabang)
            ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
            ->whereNotNull($filename)
            ->whereDate('periode_akhir', '>=', $datetime)->update([
                $request->status => 'rejected',
                $request->reason => $request->reasonbox ,
            ]);
        }
        if ($request->cabang == 'Samarinda' or $request->cabang == 'Kendari' or $request->cabang == 'Morosi'){
            // dd($request);
            $filename = $request->viewdoc;
            $result = $request->result;
            $kapal_id = $request->kapal_nama;

            documentsamarinda::where($filename, 'Like', '%' . $result . '%')
            ->where('cabang', $request->cabang)
            ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
            ->whereNotNull($filename)
            ->whereDate('periode_akhir', '>=', $datetime)->update([
                $request->status => 'rejected',
                $request->reason => $request->reasonbox ,
            ]);
        }
        if ($request->cabang == 'Jakarta'){
            //  dd($request);
            $filename = $request->viewdoc;
            $result = $request->result;
            $kapal_id = $request->kapal_nama;

            documentJakarta::where($filename, 'Like', '%' . $result . '%')
            ->where('cabang', $request->cabang)
            ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
            ->whereNotNull($filename)
            ->whereDate('periode_akhir', '>=', $datetime)->update([
                $request->status => 'rejected',
                $request->reason => $request->reasonbox ,
            ]);
        }
        return redirect('/picadmin/dana');
    }
    
    //approval for Fund request picAdmin page
    public function approve(Request $request){
        $datetime = date('Y-m-d');
        // dd($request);
        //no reason needed for banjarmasin
        if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati'){
            $filename = $request->viewdoc;
            $result = $request->result;
            $kapal_id = $request->kapal_nama;
            $cabang = $request->cabang;
            
            documentbanjarmasin::where($filename, 'Like', '%' . $result . '%')
            ->where('cabang', $request->cabang)
            ->whereNotNull($filename)
            ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
            ->whereDate('periode_akhir', '>=', $datetime)->update([
                $request->status => 'approved',
            ]);
        }else{
            $request->validate([
                'reasonbox' => 'required|max:255',
            ]);
            
            if ($request->cabang == 'Babelan'){
                $filename = $request->viewdoc;
                $result = $request->result;
                $kapal_id = $request->kapal_nama;
                
                documents::where($filename, 'Like', '%' . $result . '%')
                ->whereNotNull($filename)
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->whereDate('periode_akhir', '>=', $datetime)->update([
                    $request->status => 'approved',
                    $request->reason => $request->reasonbox ,
                ]);
            }
            if ($request->cabang == 'Berau'){
                $filename = $request->viewdoc;
                $result = $request->result;
                $kapal_id = $request->kapal_nama;
                
                documentberau::where($filename, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->whereNotNull($filename)
                ->whereDate('periode_akhir', '>=', $datetime)->update([
                    $request->status => 'approved',
                    $request->reason => $request->reasonbox ,
                ]);
            }
            if ($request->cabang == 'Samarinda' or $request->cabang == 'Kendari' or $request->cabang == 'Morosi'){
                $filename = $request->viewdoc;
                $result = $request->result;
                $kapal_id = $request->kapal_nama;
                $cabang = $request->cabang;

                documentsamarinda::where($filename, 'Like', '%' . $result . '%')
                ->where('cabang', $cabang)
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->whereNotNull($filename)
                ->whereDate('periode_akhir', '>=', $datetime)->update([
                    $request->status => 'approved',
                    $request->reason => $request->reasonbox ,
                ]);
            }
            if ($request->cabang == 'Jakarta'){
                $filename = $request->viewdoc;
                $result = $request->result;
                $kapal_id = $request->kapal_nama;

                documentJakarta::where($filename, 'Like', '%' . $result . '%')
                ->where('cabang', $request->cabang)
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->whereNotNull($filename)
                ->whereDate('periode_akhir', '>=', $datetime)->update([
                    $request->status => 'approved',
                    $request->reason => $request->reasonbox ,
                ]);
            }
        }
        return redirect('/picadmin/dana');
    }
    
    //approval for RPK review picAdmin page
    public function approverpk(Request $request){
        $datetime = date('Y-m-d');
        // dd($request);
        //check if cabang is banjarmasin
        if ($request->cabang == 'Banjarmasin') {
            $filename = $request->viewdocrpk;
            $result = $request->result;
            $kapal_id = $request->kapal_nama;

            documentrpk::where($filename, 'Like', '%' . $result . '%')
            ->where('cabang', $request->cabang)
            ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
            ->whereNotNull($filename)
            ->whereDate('periode_akhir', '>=', $datetime)->update([
                $request->status => 'approved',
            ]);
        }else{
            $request->validate([
                'reasonbox' => 'required|max:255',
            ]);

            $filename = $request->viewdocrpk;
            $result = $request->result;
            $kapal_id = $request->kapal_nama;
            
            documentrpk::where($filename, 'Like', '%' . $result . '%')
            ->where('cabang', $request->cabang)
            ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
            ->whereNotNull($filename)
            ->whereDate('periode_akhir', '>=', $datetime)->update([
                $request->status => 'approved',
                $request->reason => $request->reasonbox ,
            ]);
        }
        return redirect('/picadmin/rpk');
    }

    //reject for RPK review picAdmin page
    public function rejectrpk(Request $request){
        $datetime = date('Y-m-d');
        // dd($request);
        $request->validate([
            'reasonbox' => 'required|max:255',
        ]);

        $filename = $request->viewdocrpk;
        $result = $request->result;
        $kapal_id = $request->kapal_nama;

        documentrpk::where($filename, 'Like', '%' . $result . '%')
        ->where('cabang', $request->cabang)
        ->whereNotNull($filename)
        ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
        ->whereDate('periode_akhir', '>=', $datetime)->update([
            $request->status => 'rejected',
            $request->reason => $request->reasonbox ,
        ]);

        return redirect('/picadmin/rpk');
    }
    
    //view for dokumen fund at Admin page 
    public function view(Request $request){
        $datetime = date('Y-m-d');
        $year = date('Y');
        $month = date('m');
        
        if($request->tipefile == 'DANA'){
            if ($request->cabang == 'Babelan'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documents::whereDate('periode_akhir', '>=', $datetime)
                ->whereNotNull ($filename)
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
                $viewer = documentberau::whereDate('periode_akhir', '>=', $datetime)
                ->whereNotNull ($filename)
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
                $viewer = documentbanjarmasin::whereDate('periode_akhir', '>=', $datetime)
                ->whereNotNull ($filename)
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
                $viewer = documentsamarinda::whereDate('periode_akhir', '>=', $datetime)
                ->whereNotNull ($filename)
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
                $viewer = documentJakarta::whereDate('periode_akhir', '>=', $datetime)
                ->whereNotNull ($filename)
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('jakarta/' . $year . "/". $month . "/" . $viewer);
            }
        }
    }

    //view for rpk at Admin page 
    public function viewrpk(Request $request){ 
        $datetime = date('Y-m-d');
        $year = date('Y');
        $month = date('m');

        if($request->tipefile == 'RPK'){
            if ($request->cabang == 'Babelan'){
                $filenameRPK = $request->viewdocrpk;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentrpk::where('cabang' , $request->cabang)
                ->whereNotNull ($filenameRPK)
                ->where($filenameRPK, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->whereDate('periode_akhir', '>=', $datetime)
                ->pluck($filenameRPK)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('babelan/' . $year . "/". $month . "/RPK" . "/" . $viewer);
            }
            if ($request->cabang == 'Berau'){
                $filenameRPK = $request->viewdocrpk;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentrpk::where('cabang' , $request->cabang)
                ->whereNotNull ($filenameRPK)
                ->where($filenameRPK, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->whereDate('periode_akhir', '>=', $datetime)
                ->pluck($filenameRPK)[0]; 
                // dd($viewer);
                return Storage::disk('s3')->response('berau/' . $year . "/". $month . "/RPK" . "/" . $viewer);
            }
            if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati'){
                $filenameRPK = $request->viewdocrpk;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentrpk::where('cabang' , $request->cabang)
                ->whereNotNull ($filenameRPK)
                ->where($filenameRPK, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->whereDate('periode_akhir', '>=', $datetime)
                ->pluck($filenameRPK)[0]; 
                // dd($viewer);
                return Storage::disk('s3')->response('banjarmasin/' . $year . "/". $month . "/RPK" . "/" . $viewer);
            }
            if ($request->cabang == 'Samarinda' or $request->cabang == 'Kendari' or $request->cabang == 'Morosi'){
                $filenameRPK = $request->viewdocrpk;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentrpk::where('cabang' , $request->cabang)
                ->whereNotNull ($filenameRPK)
                ->where($filenameRPK, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->whereDate('periode_akhir', '>=', $datetime)
                ->pluck($filenameRPK)[0]; 
                // dd($viewer);
                return Storage::disk('s3')->response('samarinda/' . $year . "/". $month . "/RPK" . "/" . $viewer);
            }
            if ($request->cabang == 'Jakarta'){
                $filenameRPK = $request->viewdocrpk;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentrpk::where('cabang' , $request->cabang)
                ->whereNotNull ($filenameRPK)
                ->where($filenameRPK, 'Like', '%' . $result . '%')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->whereDate('periode_akhir', '>=', $datetime)
                ->pluck($filenameRPK)[0]; 
                // dd($viewer);
                return Storage::disk('s3')->response('jakarta/' . $year . "/". $month . "/RPK" . "/" . $viewer);
            }
        }
    }
    
    private $excel;
    public function __construct(Excel $excel){
        $this->excel = $excel;
    }
    
    //export Rekap PDF page
    public function exportPDF() {
        $date = Carbon::now();
        $monthName = $date->format('F');

        return (new RekapAdminExport)->download('RekapulasiDanaPicAdmin'. '-' . $monthName . '-' .'.pdf' , \Maatwebsite\Excel\Excel::DOMPDF);
    }

    //export Rekap Excel page
    public function exportEXCEL() {
        $date = Carbon::now();
        $monthName = $date->format('F');
        return Excel::download(new RekapAdminExport, 'RekapulasiDanaPicAdmin'. '-' . $monthName . '-' . '.xlsx');
    }

    // RekapulasiDana page
    public function RekapulasiDana(){
        $datetime = date('Y-m-d');
        $rekapdana= Rekapdana::whereDate('DateNote2', '>=', $datetime)
        ->latest()
        ->get();
        return view('picadmin.picAdminRekapulasiDana', compact('rekapdana'));
    }
    
}
