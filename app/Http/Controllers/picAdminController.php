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
use App\Models\documentsamarinda;
use Illuminate\Support\Facades\DB;
use App\Models\documentbanjarmasin;
use Illuminate\Support\Facades\Auth;


class picAdminController extends Controller
{
    // dashboard admin rpk
    public function dashboardAdminRPK(Request $request){
        $datetime = date('Y-m-d');
        $year = date('Y');
        $month = date('m');
        $searchresult = $request->search;

        if ($searchresult == 'All') {
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminDashboardRPK' , compact('docrpk'));
        }elseif ($request->filled('search')) {
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminDashboardRPK' , compact('docrpk'));
        }elseif($request->filled('search_kapal')){
             //get DocRPK Data as long as the periode_akhir(column database)
             $docrpk = DB::table('rpkdocuments')
             ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '>=', $datetime)
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
             return view('picadmin.picAdminDashboardRPK', compact('docrpk'));
        }elseif ($request->filled('search_kapal') && $request->filled('search')) {
            //get DocRPK Data as long as the periode_akhir(column database) with cabang
            $docrpk = DB::table('rpkdocuments')
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminDashboardRPK', compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(10)->withQueryString();
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
            $document = DB::table('documents')->where('upload_type','Fund_Req')->latest()->get();
            $documentberau = DB::table('beraudb')->where('upload_type','Fund_Req')->latest()->get();
            $documentbanjarmasin = DB::table('banjarmasindb')->where('upload_type','Fund_Req')->latest()->get();
            $documentsamarinda = DB::table('samarindadb')->where('upload_type','Fund_Req')->latest()->get();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->latest()->get();
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' ,'documentjakarta' , 'searchresult'));
        }
        elseif ($request->filled('search')) {
            $document = DB::table('documents')->where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            $documentberau = DB::table('beraudb')->where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            $documentbanjarmasin = DB::table('banjarmasindb')->where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            $documentsamarinda = DB::table('samarindadb')->where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' ,'documentjakarta' , 'searchresult'));
        }elseif ($request->filled('search_kapal')) {
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            //berau search bar
            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta' , 'searchresult'));
        }elseif ($request->filled('search_kapal') && $request->filled('search')){
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            //berau search bar
            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta' , 'searchresult'));
        }else{
            $document = documents::where('upload_type','Fund_Req')->latest()->get();
            $documentberau = documentberau::where('upload_type','Fund_Req')->latest()->get();
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Req')->latest()->get();
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Req')->latest()->get();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->latest()->get();
            
            return view('picadmin.picAdminDoc' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta' , 'searchresult')); 
        }
    }
    
    //review RPK page for picAdmin
    public function checkrpk(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All'){
            $docrpk = DB::table('rpkdocuments')
            ->latest()->paginate(10)->withQueryString();
        }elseif($request->filled('search')){
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminRpk', compact('docrpk'));
        }elseif($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            //get DocRPK Data as long as the periode_akhir and search based (column database)
            $docrpk = DB::table('rpkdocuments')
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminRpk', compact('docrpk'));
        }elseif($request->filled('search_kapal') && $request->filled('search')){
            $docrpk = DB::table('rpkdocuments')
            ->where('cabang',$request->search)
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminRpk', compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->latest()->paginate(10)->withQueryString();
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
                'approved_by' => Auth::user()->name,
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
                    'approved_by' => Auth::user()->name,
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
                    'approved_by' => Auth::user()->name,
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
                    'approved_by' => Auth::user()->name,
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
                    'approved_by' => Auth::user()->name,
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
                'approved_by' => Auth::user()->name,
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
                'approved_by' => Auth::user()->name,
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
            'approved_by' => Auth::user()->name ,
            $request->reason => $request->reasonbox ,
        ]);

        return redirect('/picadmin/rpk');
    }
    
    //view for dokumen fund at Admin page 
    public function view(Request $request){
        $datetime = date('Y-m-d');
        $year = $request->created_at_Year;
        $month = $request->created_at_month;
        
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
        $year = $request->created_at_Year;
        $month = $request->created_at_month;

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
            ->latest()->paginate(10)->withQueryString();

            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', Auth::user()->cabang)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', Auth::user()->cabang)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif ($request->filled('search')){
             $document = documents::where('cabang', $request->search)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             $documentberau = documentberau::where('cabang', $request->search)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             $documentbanjarmasin = documentbanjarmasin::where('cabang', $request->search)
             ->where('cabang', Auth::user()->cabang)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             $documentsamarinda = documentsamarinda::where('cabang', $request->search)
             ->where('cabang', Auth::user()->cabang)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             $documentjakarta = documentJakarta::where('cabang', $request->search)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif ($request->filled('search_kapal') && $request->filled('search')){
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('cabang', Auth::user()->cabang)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('cabang', Auth::user()->cabang)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }else{
            $document = documents::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            $documentberau = documentberau::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            // dd($documentbanjarmasin);
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }    
        return view('picadmin.picAdminRealisasiDana', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }

    public function AdminRealisasiDana_view(Request $request){
        $year = $request->created_at_Year;
        $month = $request->created_at_month;
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
        $document = documents::where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        $documentberau = documentberau::where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        $documentbanjarmasin = DB::table('banjarmasindb')->whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
        $documentsamarinda = documentsamarinda::where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        // dd($documentbanjarmasin);
        return view('picadmin.picAdminRecordDoc', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }
    public function RecordDocumentsRPK(Request $request){
        $datetime = date('Y-m-d');
        //get DocRPK Data as long as the periode_akhir(column database)
        $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
    }
    public function RecordDocuments_search(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All'){
            $document = documents::with('user')->whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
            $documentberau = documentberau::with('user')->whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
            $documentbanjarmasin = DB::table('banjarmasindb')->whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->get();
            $documentsamarinda = documentsamarinda::with('user')->whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
            $documentjakarta = documentJakarta::with('user')->whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
        }elseif($request->filled('search')){
            $document = documents::with('user')->where('cabang',$request->search)->where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            $documentberau = documentberau::with('user')->where('cabang',$request->search)->where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            $documentbanjarmasin = documentbanjarmasin::with('user')->where('cabang',$request->search)->where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            $documentsamarinda = documentsamarinda::with('user')->where('cabang',$request->search)->where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            $documentjakarta = documentJakarta::with('user')->where('cabang',$request->search)->where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        }else if($request->filled('search_kapal')){
                //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
                $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();
    
                $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();
    
                $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();

                $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();

                $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();
        }elseif($request->filled('search_kapal') && $request->filled('search')){
             //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
             $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();

             $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();

             $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '<', $datetime)
             ->where('cabang',$request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
        }else{
            $document = documents::where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            $documentberau = documentberau::where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            $documentbanjarmasin = DB::table('banjarmasindb')->whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        }
        // dd($documentbanjarmasin);
        return view('picadmin.picAdminRecordDoc', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }
    public function RecordDocumentsRPK_search(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All'){
            $docrpk = DB::table('rpkdocuments')
            ->whereDate('periode_akhir', '<', $datetime)
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }elseif($request->filled('search')){
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)
            ->whereDate('periode_akhir', '<', $datetime)
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }elseif($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            //get DocRPK Data as long as the periode_akhir and search based (column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }elseif($request->filled('search_kapal') && $request->filled('search')){
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)
            ->where('cabang',$request->search)
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            return view('picadmin.picAdminRecordDocRPK', compact('docrpk'));
        }
    }

    public function viewRecordDocuments(Request $request){
        $datetime = date('Y-m-d');
        $year = $request->created_at_Year;
        $month = $request->created_at_month;
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
