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
    // dashboard admin rpk
    public function dashboardAdminRPK(Request $request){
        $datetime = date('Y-m-d');
        $year = date('Y');
        $month = date('m');
        $searchresult = $request->search;

        if ($searchresult == 'All') {
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(5);
            return view('picadmin.picAdminDashboardRPK' , compact('docrpk'));
        }elseif ($request->filled('search')) {
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(5);
            return view('picadmin.picAdminDashboardRPK' , compact('docrpk'));
        }elseif($request->filled('search_kapal')){
             //get DocRPK Data as long as the periode_akhir(column database)
             $docrpk = DB::table('rpkdocuments')
             ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '>=', $datetime)
             ->orderBy('id', 'DESC')
             ->latest()->paginate(5);
             return view('picadmin.picAdminDashboardRPK', compact('docrpk'));
        }elseif ($request->filled('search_kapal') && $request->filled('search')) {
            //get DocRPK Data as long as the periode_akhir(column database) with cabang
            $docrpk = DB::table('rpkdocuments')
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(5);
            return view('picadmin.picAdminDashboardRPK', compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(5);
            return view('picadmin.picAdminDashboardRPK', compact('docrpk'));
        }
    }

    //Review Fund Request page for picAdmin
    public function checkform(Request $request){
        $datetime = date('Y-m-d');
        //cabang filter
        //Search bar
        //check if search-bar is filled or not
        //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
        //pakai umn agar munculkan data dari pembuatan sampai bulan akhir periode
        $searchresult = $request->search;
        if ($searchresult == 'All') {
            $document = DB::table('documents')->latest()->get();
            $documentberau = DB::table('beraudb')->latest()->get();
            $documentbanjarmasin = DB::table('banjarmasindb')->latest()->get();
            $documentsamarinda = DB::table('samarindadb')->latest()->get();
            $documentjakarta = documentJakarta::latest()->get();
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' ,'documentjakarta' , 'searchresult'));
        }
        elseif ($request->filled('search')) {
            $document = DB::table('documents')->where('cabang', $request->search)->latest()->paginate(1);
            $documentberau = DB::table('beraudb')->where('cabang', $request->search)->latest()->paginate(1);
            $documentbanjarmasin = DB::table('banjarmasindb')->where('cabang', $request->search)->latest()->paginate(1);
            $documentsamarinda = DB::table('samarindadb')->where('cabang', $request->search)->latest()->paginate(1);
            $documentjakarta = documentJakarta::where('cabang', $request->search)->latest()->paginate(1);
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' ,'documentjakarta' , 'searchresult'));
        }elseif ($request->filled('search_kapal')) {
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->get();

            //berau search bar
            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->get();

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->get();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->get();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->get();
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta' , 'searchresult'));
        }elseif ($request->filled('search_kapal') && $request->filled('search')){
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(1);

            //berau search bar
            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(1);

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(1);

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(1);

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(1);
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta' , 'searchresult'));
        }else{
            $document = documents::latest()->get();
            $documentberau = documentberau::latest()->get();
            $documentbanjarmasin = documentbanjarmasin::latest()->get();
            $documentsamarinda = documentsamarinda::latest()->get();
            $documentjakarta = documentJakarta::latest()->get();
            
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta' , 'searchresult')); 
        }
    }
    
    //review RPK page for picAdmin
    public function checkrpk(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All'){
            $docrpk = DB::table('rpkdocuments')
            ->latest()->paginate(5);
        }elseif($request->filled('search')){
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)
            ->latest()->paginate(5);
            return view('picadmin.picAdminRpk', compact('docrpk'));
        }elseif($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            //get DocRPK Data as long as the periode_akhir and search based (column database)
            $docrpk = DB::table('rpkdocuments')
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(5);
            return view('picadmin.picAdminRpk', compact('docrpk'));
        }elseif($request->filled('search_kapal') && $request->filled('search')){
            $docrpk = DB::table('rpkdocuments')
            ->where('cabang',$request->search)
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(5);
            return view('picadmin.picAdminRpk', compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->latest()->paginate(5);
        }
        return view('picadmin.picAdminRpk', compact('docrpk'));
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
            ->where('upload_type','Fund_Req')
            ->update([
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
            ->where('upload_type','Fund_Req')
            ->update([
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
            ->where('upload_type','Fund_Req')
            ->update([
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
            ->where('upload_type','Fund_Req')
            ->update([
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
            ->where('upload_type','Fund_Req')
            ->update([
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
        //no approval reason needed for banjarmasin
        if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati'){
            $filename = $request->viewdoc;
            $result = $request->result;
            $kapal_id = $request->kapal_nama;
            $cabang = $request->cabang;
            
            documentbanjarmasin::where($filename, 'Like', '%' . $result . '%')
            ->where('cabang', $request->cabang)
            ->whereNotNull($filename)
            ->where('upload_type','Fund_Req')
            ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
            ->update([
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
                ->where('upload_type','Fund_Req')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->update([
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
                ->where('upload_type','Fund_Req')
                ->update([
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
                ->where('upload_type','Fund_Req')
                ->update([
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
                ->where('upload_type','Fund_Req')
                ->update([
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
            ->update([
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
            ->update([
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
        ->update([
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
                $viewer = documents::whereNotNull ($filename)
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('upload_type','Fund_Req')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('babelan/' . $year . "/". $month . "/" . $viewer);
            }
            if ($request->cabang == 'Berau'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentberau::whereNotNull ($filename)
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('upload_type','Fund_Req')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('berau/' . $year . "/". $month . "/" . $viewer);
            }
            if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentbanjarmasin::whereNotNull ($filename)
                ->where('cabang' , $request->cabang)
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('upload_type','Fund_Req')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('banjarmasin/' . $year . "/". $month . "/" . $viewer);
            }
            if ($request->cabang == 'Samarinda' or $request->cabang == 'Kendari' or $request->cabang == 'Morosi'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentsamarinda::whereNotNull ($filename)
                ->where('cabang' , $request->cabang)
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('upload_type','Fund_Req')
                ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                ->pluck($filename)[0];
                // dd($viewer);
                return Storage::disk('s3')->response('samarinda/' . $year . "/". $month . "/" . $viewer);
            }
            if ($request->cabang == 'Jakarta'){
                $filename = $request->viewdoc;
                $kapal_id = $request->kapal_nama;
                $result = $request->result;
                $viewer = documentJakarta::whereNotNull ($filename)
                ->where($filename, 'Like', '%' . $result . '%')
                ->where('upload_type','Fund_Req')
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
                ->pluck($filenameRPK)[0]; 
                // dd($viewer);
                return Storage::disk('s3')->response('jakarta/' . $year . "/". $month . "/RPK" . "/" . $viewer);
            }
        }
    }
    
    // Realisasi Dana page
    public function AdminRealisasiDana(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All') {
            $document = documents::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentberau = documentberau::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentjakarta = documentJakarta::where('upload_type','Fund_Real')->latest()->paginate(10);
            return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
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

            return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
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
 
             return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
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

            return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }else{
            $document = documents::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentberau = documentberau::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentjakarta = documentJakarta::where('upload_type','Fund_Real')->latest()->paginate(10);
            return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }    
        return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }

    public function AdminRealisasiDana_view(Request $request){
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

    // RecordDocuments page
    public function RecordDocuments(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All'){
            $document = documents::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentberau = documentberau::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentbanjarmasin = documentbanjarmasin::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentsamarinda = documentsamarinda::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentjakarta = documentJakarta::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            return view('picadmin.picAdminRecordDoc', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif($request->filled('search')){
            $document = documents::with('user')->where('cabang',$request->search)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentberau = documentberau::with('user')->where('cabang',$request->search)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentbanjarmasin = documentbanjarmasin::with('user')->where('cabang',$request->search)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentsamarinda = documentsamarinda::with('user')->where('cabang',$request->search)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentjakarta = documentJakarta::with('user')->where('cabang',$request->search)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            return view('picadmin.picAdminRecordDoc', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }else if($request->filled('search_kapal')){
                //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
                $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->orderBy('id', 'DESC')
                ->latest()->paginate(1);
    
                $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->orderBy('id', 'DESC')
                ->latest()->paginate(1);
    
                $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->orderBy('id', 'DESC')
                ->latest()->paginate(1);

                $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->orderBy('id', 'DESC')
                ->latest()->paginate(1);

                $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->orderBy('id', 'DESC')
                ->latest()->paginate(1);
                return view('picadmin.picAdminRecordDoc', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif($request->filled('search_kapal') && $request->filled('search')){
             //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
             $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->orderBy('id', 'DESC')
             ->latest()->paginate(1);
 
             $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->orderBy('id', 'DESC')
             ->latest()->paginate(1);
 
             $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->orderBy('id', 'DESC')
             ->latest()->paginate(1);

             $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->orderBy('id', 'DESC')
             ->latest()->paginate(1);

             $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->orderBy('id', 'DESC')
             ->latest()->paginate(1);
             return view('picadmin.picAdminRecordDoc', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }else{
            $document = documents::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentberau = documentberau::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentbanjarmasin = documentbanjarmasin::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentsamarinda = documentsamarinda::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            $documentjakarta = documentJakarta::with('user')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(1);
            return view('picadmin.picAdminRecordDoc', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }
    }
    public function RecordDocumentsRPK(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All'){
            $docrpk = DB::table('rpkdocuments')
            ->whereDate('periode_akhir', '<', $datetime)
            ->latest()->paginate(1);
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }elseif($request->filled('search')){
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)
            ->whereDate('periode_akhir', '<', $datetime)
            ->latest()->paginate(5);
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }elseif($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            //get DocRPK Data as long as the periode_akhir and search based (column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(5);
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }elseif($request->filled('search_kapal') && $request->filled('search')){
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)
            ->where('cabang',$request->search)
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(5);
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(5);
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }
    }

    public function viewRecordDocuments(Request $request){
        $datetime = date('Y-m-d');
        $year = date('Y');
        $month = date('m');
        // Fund Request view ----------------------------------------------------------
            if($request->tipefile == 'DANA'){
                if ($request->cabang == 'Babelan'){
                    $filename = $request->viewdoc;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documents::whereDate('periode_akhir', '<', $datetime)
                    ->whereNotNull ($filename)
                    ->where('upload_type','Fund_Req')
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
                    $viewer = documentberau::whereDate('periode_akhir', '<', $datetime)
                    ->whereNotNull ($filename)
                    ->where('upload_type','Fund_Req')
                    ->where($filename, 'Like', '%' . $result . '%')
                    ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                    ->pluck($filename)[0];
                    // dd($viewer);
                    return Storage::disk('s3')->response('berau/' . $year . "/". $month . "/" . $viewer);
                }
                if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati' or $request->cabang == 'Batu Licin'){
                    $filename = $request->viewdoc;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documentbanjarmasin::whereDate('periode_akhir', '<', $datetime)
                    ->whereNotNull ($filename)
                    ->where('upload_type','Fund_Req')
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
                    $viewer = documentsamarinda::whereDate('periode_akhir', '<', $datetime)
                    ->whereNotNull ($filename)
                    ->where('upload_type','Fund_Req')
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
                    $viewer = documentJakarta::whereDate('periode_akhir', '<', $datetime)
                    ->whereNotNull ($filename)
                    ->where('upload_type','Fund_Req')
                    ->where($filename, 'Like', '%' . $result . '%')
                    ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                    ->pluck($filename)[0];
                    // dd($viewer);
                    return Storage::disk('s3')->response('jakarta/' . $year . "/". $month . "/" . $viewer);
                }
            }
        // RPK view ----------------------------------------------------------
            if($request->tipefile == 'RPK'){
                if ($request->cabang == 'Babelan'){
                    $filenameRPK = $request->viewdocrpk;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documentrpk::where('cabang' , $request->cabang)
                    ->whereNotNull ($filenameRPK)
                    ->where($filenameRPK, 'Like', '%' . $result . '%')
                    ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                    ->whereDate('periode_akhir', '<', $datetime)
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
                    ->whereDate('periode_akhir', '<', $datetime)
                    ->pluck($filenameRPK)[0]; 
                    // dd($viewer);
                    return Storage::disk('s3')->response('berau/' . $year . "/". $month . "/RPK" . "/" . $viewer);
                }
                if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati' or $request->cabang == 'Batu Licin'){
                    $filenameRPK = $request->viewdocrpk;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documentrpk::where('cabang' , $request->cabang)
                    ->whereNotNull ($filenameRPK)
                    ->where($filenameRPK, 'Like', '%' . $result . '%')
                    ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                    ->whereDate('periode_akhir', '<', $datetime)
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
                    ->whereDate('periode_akhir', '<', $datetime)
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
                    ->whereDate('periode_akhir', '<', $datetime)
                    ->pluck($filenameRPK)[0]; 
                    // dd($viewer);
                    return Storage::disk('s3')->response('jakarta/' . $year . "/". $month . "/RPK" . "/" . $viewer);
                }
            }
    }

}
