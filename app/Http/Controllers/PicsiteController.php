<?php

namespace App\Http\Controllers;

use Storage;
use Response;
use validator;
use Carbon\Carbon;
use App\Mail\Gmail;
use App\Models\Tug;
use App\Models\User;
use App\Models\Barge;
use App\Models\documents;
use App\Models\Rekapdana;
use App\Models\documentrpk;
use App\Exports\RekapExport;

use Illuminate\Http\Request;

use App\Models\documentberau;
use App\Models\documentJakarta;
use App\Models\documentsamarinda;
use Illuminate\Support\Facades\DB;
use App\Models\documentbanjarmasin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Google\Cloud\Storage\StorageClient;


class PicsiteController extends Controller
{
    // upload dana page
    public function uploadform(Request $request){
        $tug=Tug::latest()->get();
        $barge=Barge::latest()->get();
        return view('picsite.upload', compact('tug' , 'barge'));
    }

    // record page
    public function DocRecord(Request $request){
        $datetime = date('Y-m-d');
        $document = documents::with('user')->where('upload_type','Fund_Req')->where('cabang',Auth::user()->cabang)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        $documentberau = documentberau::with('user')->where('upload_type','Fund_Req')->where('cabang',Auth::user()->cabang)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        $documentbanjarmasin = documentbanjarmasin::with('user')->where('upload_type','Fund_Req')->where('cabang',Auth::user()->cabang)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        $documentsamarinda = documentsamarinda::with('user')->where('upload_type','Fund_Req')->where('cabang',Auth::user()->cabang)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        $documentjakarta = documentJakarta::with('user')->where('upload_type','Fund_Req')->where('cabang',Auth::user()->cabang)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        return view('picsite.picSiteDocRecord', compact('document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }
    public function DocRecordRPK(Request $request){
        $datetime = date('Y-m-d');
        $docrpk = DB::table('rpkdocuments')->where('cabang', Auth::user()->cabang)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        return view('picsite.picSiteDocRecordRPK', compact('docrpk'));
    }
    public function searchDocRecordRPK(Request $request){
        $datetime = date('Y-m-d');
        if($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            //get DocRPK Data as long as the periode_akhir and search based (column database)
            $docrpk = DB::table('rpkdocuments')->where('cabang', Auth::user()->cabang)
            ->where('nama_kapal', 'Like',  $request->search_kapal . '%')
            ->whereDate('periode_akhir', '<', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            
            return view('picsite.picSiteDocRecordRPK', compact('docrpk'));
         }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->where('cabang', Auth::user()->cabang)->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            return view('picsite.picSiteDocRecordRPK', compact('docrpk'));
        }
        return view('picsite.picSiteDocRecordRPK', compact('docrpk'));
    }
    public function searchDocRecord(Request $request){
        $datetime = date('Y-m-d');
        if(Auth::user()->cabang == "Babelan"){
            if(Auth::user()->cabang == "Babelan" and $request->filled('search_kapal')) {
                //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
                $document = documents::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();
    
                return view('picsite.picSiteDocRecord', compact('document'));
            }else{
                $document = documents::whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                return view('picsite.picSiteDocRecord', compact('document'));
            }
        }
        else if(Auth::user()->cabang == "Berau"){
            if (Auth::user()->cabang == "Berau" and $request->filled('search_kapal')) {
                //berau search bar
                $documentberau = documentberau::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();

                return view('picsite.picSiteDocRecord', compact('documentberau'));
            }else{
                $documentberau = documentberau::whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                return view('picsite.picSiteDocRecord', compact('documentberau'));
            }
        }
        else if(Auth::user()->cabang == "Banjarmasin" or Auth::user()->cabang == "Bunati"){
            if ($request->filled('search_kapal')) {
                //banjarmasin search bar
                $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)->where('cabang', Auth::user()->cabang)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();

                return view('picsite.picSiteDocRecord', compact('documentbanjarmasin'));
            }else{
                $documentbanjarmasin = documentbanjarmasin::whereDate('periode_akhir', '<', $datetime)->where('cabang', Auth::user()->cabang)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                return view('picsite.picSiteDocRecord', compact('documentbanjarmasin'));
            }
        }
        else if(Auth::user()->cabang == "Samarinda" or Auth::user()->cabang == "Kendari" or Auth::user()->cabang == "Morosi"){
            if ($request->filled('search_kapal')) {
                //samarinda search bar
                $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)->where('cabang', Auth::user()->cabang)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();

                return view('picsite.picSiteDocRecord', compact('documentsamarinda'));
            }else{
                $documentsamarinda = documentsamarinda::whereDate('periode_akhir', '<', $datetime)->where('cabang', Auth::user()->cabang)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                return view('picsite.picSiteDocRecord', compact('documentsamarinda'));
            }
        }
        else if(Auth::user()->cabang == "Jakarta"){
            if (Auth::user()->cabang == "Jakarta" and $request->filled('search_kapal')) {
                $documentjakarta = documentJakarta::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                ->whereDate('periode_akhir', '<', $datetime)
                ->where('upload_type','Fund_Req')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10)->withQueryString();
                
                return view('picsite.picSiteDocRecord', compact('documentjakarta'));
            }else{
                $documentjakarta = documentJakarta::whereDate('periode_akhir', '<', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                return view('picsite.picSiteDocRecord', compact('documentjakarta'));
            }
        }
        return view('picsite.picSiteDocRecord', compact('document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }
    public function viewRecord(Request $request){
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
                    ->where('id', $request->identity)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
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
                    ->where('id', $request->identity)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
                    ->pluck($filename)[0];
                    // dd($viewer);
                    return Storage::disk('s3')->response('berau/' . $year . "/". $month . "/" . $viewer);
                }
                if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati'){
                    $filename = $request->viewdoc;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documentbanjarmasin::whereDate('periode_akhir', '<', $datetime)
                    ->whereNotNull ($filename)
                    ->where('id', $request->identity)
                    ->where('cabang' , $request->cabang)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
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
                    ->where('id', $request->identity)
                    ->where('cabang' , $request->cabang)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
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
                    ->where('id', $request->identity)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
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
                    ->where('id', $request->identity)
                    ->where($filenameRPK, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
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
                    ->where('id', $request->identity)
                    ->where($filenameRPK, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
                    ->whereDate('periode_akhir', '<', $datetime)
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
                    ->where('id', $request->identity)
                    ->where($filenameRPK, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
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
                    ->where('id', $request->identity)
                    ->where($filenameRPK, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
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
                    ->where('id', $request->identity)
                    ->where($filenameRPK, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
                    ->whereDate('periode_akhir', '<', $datetime)
                    ->pluck($filenameRPK)[0]; 
                    // dd($viewer);
                    return Storage::disk('s3')->response('jakarta/' . $year . "/". $month . "/RPK" . "/" . $viewer);
                }
            }
    }

    // RPK dashboard
    public function DashboardRPK(Request $request){
        $datetime = date('Y-m-d');
        $docrpk = DB::table('rpkdocuments')->where('cabang', Auth::user()->cabang)->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(10)->withQueryString();
        return view('picsite.picDashboardRPK', compact('docrpk'));
    }
    public function DashboardRPKsearch(Request $request){
        $datetime = date('Y-m-d');

        if($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            $docrpk = DB::table('rpkdocuments')->where('cabang', Auth::user()->cabang)
            ->where('nama_kapal', 'Like',  $request->search_kapal . '%')
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            
            return view('picsite.picDashboardRPK', compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->where('cabang', Auth::user()->cabang)->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(10)->withQueryString();
            return view('picsite.picDashboardRPK', compact('docrpk'));
        }
        return view('picsite.picDashboardRPK', compact('docrpk'));
    }

    //Realisasi Dana page
        public function realisasiDana(){
            $tug=Tug::latest()->get();
            $barge=Barge::latest()->get();
        
            return view('picsite.picsiteRealisasiDana', compact('tug' , 'barge'));
        }
    //dashboard Realisasi Dana page and search func
        public function Dashboard_Realisasisearch(Request $request){
            if(Auth::user()->cabang == "Babelan" and $request->filled('search_kapal')) {
                //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
                $document = documents::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                ->where('upload_type','Fund_Real')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10);
    
                return view('picsite.picsiteDashboardReal', compact('document'));
            }
            elseif(Auth::user()->cabang == "Berau" and $request->filled('search_kapal')) {
                //berau search bar
                $documentberau = documentberau::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                ->where('upload_type','Fund_Real')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10);

                return view('picsite.picsiteDashboardReal', compact('documentberau'));
            }
            elseif(Auth::user()->cabang == "Banjarmasin" or Auth::user()->cabang == "Bunati"){
                if ($request->filled('search_kapal')) {
                    //banjarmasin search bar
                    $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                    ->where('cabang', Auth::user()->cabang)
                    ->where('upload_type','Fund_Real')
                    ->orderBy('id', 'DESC')
                    ->latest()->paginate(10);
    
                    return view('picsite.picsiteDashboardReal', compact('documentbanjarmasin'));
                }
            }
            elseif(Auth::user()->cabang == "Samarinda" or Auth::user()->cabang == "Kendari" or Auth::user()->cabang == "Morosi"){
                if ($request->filled('search_kapal')) {
                    //samarinda search bar
                    $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                    ->where('cabang', Auth::user()->cabang)
                    ->where('upload_type','Fund_Real')
                    ->orderBy('id', 'DESC')
                    ->latest()->paginate(10);
    
                    return view('picsite.picsiteDashboardReal', compact('documentsamarinda'));
                }
            }
            elseif (Auth::user()->cabang == "Jakarta" and $request->filled('search_kapal')) {
                $documentjakarta = documentJakarta::where('nama_kapal', 'Like',  $request->search_kapal . '%')
                ->where('upload_type','Fund_Real')
                ->orderBy('id', 'DESC')
                ->latest()->paginate(10);
                
                return view('picsite.picsiteDashboardReal', compact('documentjakarta'));
            }
            else{
                $document = documents::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
                $documentberau = documentberau::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
                $documentbanjarmasin = documentbanjarmasin::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
                $documentsamarinda = documentsamarinda::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
                $documentjakarta = documentJakarta::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
                return view('picsite.picsiteDashboardReal', compact('document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
            }
            $document = documents::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            $documentberau = documentberau::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            $documentbanjarmasin = documentbanjarmasin::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            $documentsamarinda = documentsamarinda::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            $documentjakarta = documentJakarta::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            return view('picsite.picsiteDashboardReal', compact('document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }
        public function Dashboard_Realisasi(Request $request){
            $datetime = date('Y-m-d');
            $document = documents::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            $documentberau = documentberau::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            $documentbanjarmasin = documentbanjarmasin::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            $documentsamarinda = documentsamarinda::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            $documentjakarta = documentJakarta::with('user')->where('upload_type','Fund_Real')->where('cabang',Auth::user()->cabang)->latest()->paginate(10);
            return view('picsite.picsiteDashboardReal', compact('document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }
        public function view_Realisasi(Request $request){
            $year = $request->created_at_Year;
            $month = $request->created_at_month;
                // realisasi Fund Request view ----------------------------------------------------------
                if($request->tipefile == 'Fund_Real' && $request->type_upload == 'Fund_Real'){
                if ($request->cabang == 'Babelan'){
                    $filename = $request->viewdoc;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documents::whereNotNull ($filename)
                    ->where('upload_type','Fund_Real')
                    ->where('id', $request->identity)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
                    ->pluck($filename)[0];
                    // dd($viewer);
                    return Storage::disk('s3')->response('babelan/' . $year . "/". $month . "/" . $viewer);
                }
                elseif ($request->cabang == 'Berau'){
                    $filename = $request->viewdoc;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documentberau::whereNotNull ($filename)
                    ->where('upload_type','Fund_Real')
                    ->where('id', $request->identity)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
                    ->pluck($filename)[0];
                    // dd($viewer);
                    return Storage::disk('s3')->response('berau/' . $year . "/". $month . "/" . $viewer);
                }
                elseif ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati'){
                    $filename = $request->viewdoc;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documentbanjarmasin::whereNotNull ($filename)
                    ->where('upload_type','Fund_Real')
                    ->where('id', $request->identity)
                    ->where('cabang' , $request->cabang)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
                    ->pluck($filename)[0];
                    // dd($viewer);
                    return Storage::disk('s3')->response('banjarmasin/' . $year . "/". $month . "/" . $viewer);
                }
                elseif ($request->cabang == 'Samarinda' or $request->cabang == 'Kendari' or $request->cabang == 'Morosi'){
                    $filename = $request->viewdoc;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documentsamarinda::whereNotNull ($filename)
                    ->where('upload_type','Fund_Real')
                    ->where('id', $request->identity)
                    ->where('cabang' , $request->cabang)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
                    ->pluck($filename)[0];
                    // dd($viewer);
                    return Storage::disk('s3')->response('samarinda/' . $year . "/". $month . "/" . $viewer);
                }
                elseif ($request->cabang == 'Jakarta'){
                    $filename = $request->viewdoc;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documentJakarta::whereNotNull ($filename)
                    ->where('upload_type','Fund_Real')
                    ->where('id', $request->identity)
                    ->where($filename, 'Like',  $result . '%')
                    ->where('nama_kapal', 'Like',  $kapal_id . '%')
                    ->pluck($filename)[0];
                    // dd($viewer);
                    return Storage::disk('s3')->response('jakarta/' . $year . "/". $month . "/" . $viewer);
                }
            }
        }
    

    //upload files
    public function uploadfile(Request $request){

        $document = documents::with('user')->where('cabang',Auth::user()->cabang)->latest()->get();
        $documentberau = documentberau::with('user')->where('cabang',Auth::user()->cabang)->latest()->get();
        $documentbanjarmasin = documentbanjarmasin::with('user')->where('cabang',Auth::user()->cabang)->latest()->get();
        $documentsamarinda = documentsamarinda::with('user')->where('cabang',Auth::user()->cabang)->latest()->get();
       
        if (Auth::user()->cabang == 'Babelan') {
            if($request->type_upload == 'Fund_Req'){
                $request->validate([
                'ufile1' => 'mimes:pdf|max:3072' ,
                    'ufile2' => 'mimes:pdf|max:3072' ,
                    'ufile3' => 'mimes:pdf|max:3072' ,
                    'ufile4' => 'mimes:pdf|max:3072' ,
                    'ufile5' => 'mimes:pdf|max:3072' , 
                    'ufile6' => 'mimes:pdf|max:3072' ,
                    'ufile7' => 'mimes:pdf|max:3072' ,
                    'ufile8' => 'mimes:pdf|max:3072' ,
                    'ufile9' => 'mimes:pdf|max:3072' ,
                    'ufile10' => 'mimes:pdf|max:3072' ,
                    'ufile11' => 'mimes:pdf|max:3072' ,
                    'ufile12' => 'mimes:pdf|max:3072' ,
                    'ufile13' => 'mimes:pdf|max:3072' ,
                    'ufile14' => 'mimes:pdf|max:3072' ,
                    'ufile15' => 'mimes:pdf|max:3072' ,
                    'ufile16' => 'mimes:pdf|max:3072' ,
                    'ufile17' => 'mimes:pdf|max:3072' ,
                    'ufile18' => 'mimes:pdf|max:3072' ,
                    'ufile19' => 'mimes:pdf|max:3072' ,
                    'ufile20' => 'mimes:pdf|max:3072' ,
                    'ufile21' => 'mimes:pdf|max:3072' ,
                    'ufile22' => 'mimes:pdf|max:3072' ,
                    'ufile23' => 'mimes:pdf|max:3072' ,
                    'ufile24' => 'mimes:pdf|max:3072' ,
                'dana1' => 'nullable|string|min:4|max:15' ,
                    'dana2' => 'nullable|string|min:4|max:15' ,
                    'dana3' => 'nullable|string|min:4|max:15' ,
                    'dana4' => 'nullable|string|min:4|max:15' ,
                    'dana5' => 'nullable|string|min:4|max:15' ,
                    'dana6' => 'nullable|string|min:4|max:15' ,
                    'dana7' => 'nullable|string|min:4|max:15' ,
                    'dana8' => 'nullable|string|min:4|max:15' ,
                    'dana9' => 'nullable|string|min:4|max:15' ,
                    'dana10' => 'nullable|string|min:4|max:15' ,
                    'dana11' => 'nullable|string|min:4|max:15' ,
                    'dana12' => 'nullable|string|min:4|max:15' ,
                    'dana13' => 'nullable|string|min:4|max:15' ,
                    'dana14' => 'nullable|string|min:4|max:15' ,
                    'dana15' => 'nullable|string|min:4|max:15' ,
                    'dana16' => 'nullable|string|min:4|max:15' ,
                    'dana17' => 'nullable|string|min:4|max:15' ,
                    'dana18' => 'nullable|string|min:4|max:15' ,
                    'dana19' => 'nullable|string|min:4|max:15' ,
                    'dana20' => 'nullable|string|min:4|max:15' ,
                    'dana21' => 'nullable|string|min:4|max:15' ,
                    'dana22' => 'nullable|string|min:4|max:15' ,
                    'dana23' => 'nullable|string|min:4|max:15' ,
                    'dana24' => 'nullable|string|min:4|max:15' ,
                'nama_kapal' => 'nullable|string',
                'Nama_Barge' => 'nullable|string',
                'no_mohon' => 'required|string',
                'no_PR' => 'required|string',
                ]);
                
            }elseif($request->type_upload == 'Fund_Real'){
                $request->validate([
                'ufile1' => 'mimes:pdf|max:3072' ,
                    'ufile2' => 'mimes:pdf|max:3072' ,
                    'ufile3' => 'mimes:pdf|max:3072' ,
                    'ufile4' => 'mimes:pdf|max:3072' ,
                    'ufile5' => 'mimes:pdf|max:3072' , 
                    'ufile6' => 'mimes:pdf|max:3072' ,
                    'ufile7' => 'mimes:pdf|max:3072' ,
                    'ufile8' => 'mimes:pdf|max:3072' ,
                    'ufile9' => 'mimes:pdf|max:3072' ,
                    'ufile10' => 'mimes:pdf|max:3072' ,
                    'ufile11' => 'mimes:pdf|max:3072' ,
                    'ufile12' => 'mimes:pdf|max:3072' ,
                    'ufile13' => 'mimes:pdf|max:3072' ,
                    'ufile14' => 'mimes:pdf|max:3072' ,
                    'ufile15' => 'mimes:pdf|max:3072' ,
                    'ufile16' => 'mimes:pdf|max:3072' ,
                    'ufile17' => 'mimes:pdf|max:3072' ,
                    'ufile18' => 'mimes:pdf|max:3072' ,
                    'ufile19' => 'mimes:pdf|max:3072' ,
                    'ufile20' => 'mimes:pdf|max:3072' ,
                    'ufile21' => 'mimes:pdf|max:3072' ,
                    'ufile22' => 'mimes:pdf|max:3072' ,
                    'ufile23' => 'mimes:pdf|max:3072' ,
                    'ufile24' => 'mimes:pdf|max:3072' ,
                'dana1' => 'nullable|string|min:4|max:15' ,
                    'dana2' => 'nullable|string|min:4|max:15' ,
                    'dana3' => 'nullable|string|min:4|max:15' ,
                    'dana4' => 'nullable|string|min:4|max:15' ,
                    'dana5' => 'nullable|string|min:4|max:15' ,
                    'dana6' => 'nullable|string|min:4|max:15' ,
                    'dana7' => 'nullable|string|min:4|max:15' ,
                    'dana8' => 'nullable|string|min:4|max:15' ,
                    'dana9' => 'nullable|string|min:4|max:15' ,
                    'dana10' => 'nullable|string|min:4|max:15' ,
                    'dana11' => 'nullable|string|min:4|max:15' ,
                    'dana12' => 'nullable|string|min:4|max:15' ,
                    'dana13' => 'nullable|string|min:4|max:15' ,
                    'dana14' => 'nullable|string|min:4|max:15' ,
                    'dana15' => 'nullable|string|min:4|max:15' ,
                    'dana16' => 'nullable|string|min:4|max:15' ,
                    'dana17' => 'nullable|string|min:4|max:15' ,
                    'dana18' => 'nullable|string|min:4|max:15' ,
                    'dana19' => 'nullable|string|min:4|max:15' ,
                    'dana20' => 'nullable|string|min:4|max:15' ,
                    'dana21' => 'nullable|string|min:4|max:15' ,
                    'dana22' => 'nullable|string|min:4|max:15' ,
                    'dana23' => 'nullable|string|min:4|max:15' ,
                    'dana24' => 'nullable|string|min:4|max:15' ,
                'nama_kapal' => 'nullable|string',
                'Nama_Barge' => 'nullable|string',
                'no_mohon' => 'nullable|string',
                'no_PR' => 'nullable|string',
                ]);
            }
            
            $year = date('Y');
            $month = date('m');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;
            
            if ($request->hasFile('ufile1')) {
                $file1 = $request->file('ufile1');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'babelan/sertifikat_keselamatan';
                $path = $request->file('ufile1')->storeas('babelan/'. $year . "/". $month , $name1, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                    'dana1' => $request->dana1,
                    'status1' => 'on review',
                    'time_upload1' => date("Y-m-d h:i:s"),
                    'sertifikat_keselamatan' => basename($path),
                ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                    'dana1' => $request->dana1,
                    'status1' => 'on review',
                    'time_upload1' => date("Y-m-d h:i:s"),
                    'sertifikat_keselamatan' => basename($path),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                    'upload_type' => 'Fund_Real',
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,   
                        
                    'dana1' => $request->dana1,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    
                    'status1' => 'on review',
                    'time_upload1' => date("Y-m-d h:i:s"),
                    'sertifikat_keselamatan' => basename($path)
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,   
                        
                    'dana1' => $request->dana1,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    
                    'status1' => 'on review',
                    'time_upload1' => date("Y-m-d h:i:s"),
                    'sertifikat_keselamatan' => basename($path)
                    ]);
                }
            }
            if ($request->hasFile('ufile2')) {
                $file2 = $request->file('ufile2');
                $name2 =  Auth::user()->cabang .'-'. $file2->getClientOriginalName();
                $tujuan_upload = 'babelan/sertifikat_garis_muat';     

                $path = $request->file('ufile2')->storeas('babelan/'. $year . "/". $month , $name2, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,                                        
                    'sertifikat_garis_muat' => basename($path),
                    'status2' => 'on review',
                    'time_upload2' => date("Y-m-d h:i:s"),
                ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,
                        'sertifikat_garis_muat' => basename($path),
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana2' => $request->dana2,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'sertifikat_garis_muat' => basename($path),
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana2' => $request->dana2,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'sertifikat_garis_muat' => basename($path),
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('ufile3')) {
                $file3 = $request->file('ufile3');
                $name3 = Auth::user()->cabang . '-' . $file3->getClientOriginalName();
                $tujuan_upload = 'babelan/penerbitan_sekali_jalan';

                $path = $request->file('ufile3')->storeas('babelan/'. $year . "/". $month , $name3, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                    'dana3' => $request->dana3,
                    'penerbitan_sekali_jalan' => basename($path),
                    'status3' => 'on review',
                    'time_upload3' => date("Y-m-d h:i:s"),
                ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,
                        'penerbitan_sekali_jalan' => basename($path),
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana3' => $request->dana3,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'penerbitan_sekali_jalan' => basename($path),
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }
                else {
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana3' => $request->dana3,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'penerbitan_sekali_jalan' => basename($path),
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]); 
                }
            }            
            if ($request->hasFile('ufile4')) {
                $file4 = $request->file('ufile4');
                $name4 = Auth::user()->cabang . '-' . $file4->getClientOriginalName();
                $tujuan_upload = 'babelan/sertifikat_safe_manning';
               
                $path = $request->file('ufile4')->storeas('babelan/'. $year . "/". $month , $name4, 's3');
               if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,
                    'sertifikat_safe_manning'=> basename($path),
                    'status4' => 'on review',
                    'time_upload4' => date("Y-m-d h:i:s"),
                ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,
                        'sertifikat_safe_manning'=> basename($path),
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana4' => $request->dana4,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'sertifikat_safe_manning'=> basename($path),
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana4' => $request->dana4,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'sertifikat_safe_manning'=> basename($path),
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }
            }           
            if ($request->hasFile('ufile5')) {
                $file5 = $request->file('ufile5');
                $name5 = Auth::user()->cabang . '-' . $file5->getClientOriginalName();
                $tujuan_upload = 'babelan/endorse_surat_laut';
                
                $path = $request->file('ufile5')->storeas('babelan/'. $year . "/". $month , $name5, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,
                        'endorse_surat_laut'=> basename($path),
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);   
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,
                        'endorse_surat_laut'=> basename($path),
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana5' => $request->dana5,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'endorse_surat_laut'=> basename($path),
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana5' => $request->dana5,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'endorse_surat_laut'=> basename($path),
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }
            }          
            if ($request->hasFile('ufile6')) {
                $file6 = $request->file('ufile6');
                $name6 = Auth::user()->cabang . '-' . $file6->getClientOriginalName();
                $tujuan_upload = 'babelan/perpanjangan_sertifikat_sscec';
               
                $path = $request->file('ufile6')->storeas('babelan/'. $year . "/". $month , $name6, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,
                    'perpanjangan_sertifikat_sscec'=> basename($path),
                    'status6' => 'on review',
                    'time_upload6' => date("Y-m-d h:i:s"),
                ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,
                    'perpanjangan_sertifikat_sscec'=> basename($path),
                    'status6' => 'on review',
                    'time_upload6' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana6' => $request->dana6,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'perpanjangan_sertifikat_sscec'=> basename($path),
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana6' => $request->dana6,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'perpanjangan_sertifikat_sscec'=> basename($path),
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }
            }            
            if ($request->hasFile('ufile7')) {
                $file7 = $request->file('ufile7');
                $name7 =Auth::user()->cabang . '-' . $file7->getClientOriginalName();
                $tujuan_upload = 'babelan/perpanjangan_sertifikat_p3k';
                
                $path = $request->file('ufile7')->storeas('babelan/'. $year . "/". $month , $name7, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,
                    'perpanjangan_sertifikat_p3k'=> basename($path),
                    'status7' => 'on review',
                    'time_upload7' => date("Y-m-d h:i:s"),
                ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,
                    'perpanjangan_sertifikat_p3k'=> basename($path),
                    'status7' => 'on review',
                    'time_upload7' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana7' => $request->dana7,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'perpanjangan_sertifikat_p3k'=> basename($path),
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana7' => $request->dana7,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'perpanjangan_sertifikat_p3k'=> basename($path),
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                    ]);
                }
                
            }           
            if ($request->hasFile('ufile8')) {
                $file8 = $request->file('ufile8');
                $name8 = Auth::user()->cabang . '-' . $file8->getClientOriginalName();
                $tujuan_upload = 'babelan/biaya_laporan_dok';

                $path = $request->file('ufile8')->storeas('babelan/'. $year . "/". $month , $name8, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,
                        'biaya_laporan_dok'=> basename($path),
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                    ]);     
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                    'dana8' => $request->dana8,
                    'biaya_laporan_dok'=> basename($path),
                    'status8' => 'on review',
                    'time_upload8' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana8' => $request->dana8,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'biaya_laporan_dok'=> basename($path),
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana8' => $request->dana8,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'biaya_laporan_dok'=> basename($path),
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                    ]); 
                }  
            }           
            if ($request->hasFile('ufile9')) {
                $file9 = $request->file('ufile9');
                $name9 = Auth::user()->cabang . '-' . $file9->getClientOriginalName();
                $tujuan_upload = 'babelan/pnpb_sertifikat_keselamatan';
               
                $path = $request->file('ufile9')->storeas('babelan/'. $year . "/". $month , $name9, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana9' => $request->dana9,
                        'pnpb_sertifikat_keselamatan'=> basename($path),
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                    ]);                   
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana9' => $request->dana9,
                        'pnpb_sertifikat_keselamatan'=> basename($path),
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana9' => $request->dana9,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnpb_sertifikat_keselamatan'=> basename($path),
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana9' => $request->dana9,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnpb_sertifikat_keselamatan'=> basename($path),
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                    ]);
                }
            }         
            if ($request->hasFile('ufile10')) {
                $file10 = $request->file('ufile10');
                $name10 = Auth::user()->cabang . '-' . $file10->getClientOriginalName();
                $tujuan_upload = 'babelan/pnpb_sertifikat_garis_muat';
               
                $path = $request->file('ufile10')->storeas('babelan/'. $year . "/". $month , $name10, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana10' => $request->dana10,
                        'pnpb_sertifikat_garis_muat'=> basename($path),
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                    ]);          
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana10' => $request->dana10,
                        'pnpb_sertifikat_garis_muat'=> basename($path),
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana10' => $request->dana10,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnpb_sertifikat_garis_muat'=> basename($path),
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana10' => $request->dana10,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnpb_sertifikat_garis_muat'=> basename($path),
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                    ]);
                }
            }           
            if ($request->hasFile('ufile11')) {
                $file11 = $request->file('ufile11');
                $name11 = Auth::user()->cabang . '-' . $file11->getClientOriginalName();
                $tujuan_upload = 'babelan/pnpb_surat_laut';
               
                $path = $request->file('ufile11')->storeas('babelan/'. $year . "/". $month , $name11, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana11' => $request->dana11,
                        'pnpb_surat_laut'=> basename($path),
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana11' => $request->dana11,
                        'pnpb_surat_laut'=> basename($path),
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana11' => $request->dana11,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnpb_surat_laut'=> basename($path),
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana11' => $request->dana11,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnpb_surat_laut'=> basename($path),
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                    ]);
                }
            }         
            if ($request->hasFile('ufile12')) {
                $file12 = $request->file('ufile12');
                $name12 = Auth::user()->cabang . '-' . $file12->getClientOriginalName();
                $tujuan_upload = 'babelan/sertifikat_snpp';
               
                $path = $request->file('ufile12')->storeas('babelan/'. $year . "/". $month , $name12, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana12' => $request->dana12,
                        'sertifikat_snpp'=> basename($path),
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana12' => $request->dana12,
                        'sertifikat_snpp'=> basename($path),
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana12' => $request->dana12,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'sertifikat_snpp'=> basename($path),
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana12' => $request->dana12,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'sertifikat_snpp'=> basename($path),
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                    ]);
                }
            }           
            if ($request->hasFile('ufile13')) {
                $file13 = $request->file('ufile13');
                $name13 = Auth::user()->cabang . '-' . $file13->getClientOriginalName();
                $tujuan_upload = 'babelan/sertifikat_anti_teritip';
                
                $path = $request->file('ufile13')->storeas('babelan/'. $year . "/". $month , $name13, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana13' => $request->dana13,
                        'sertifikat_anti_teritip'=> basename($path),
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana13' => $request->dana13,
                        'sertifikat_anti_teritip'=> basename($path),
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana13' => $request->dana13,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'sertifikat_anti_teritip'=> basename($path),
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana13' => $request->dana13,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'sertifikat_anti_teritip'=> basename($path),
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                    ]);
                }
            }         
            if ($request->hasFile('ufile14')) {
                $file14 = $request->file('ufile14');
                $name14 = Auth::user()->cabang . '-' . $file14->getClientOriginalName();
                $tujuan_upload = 'babelan/pnbp_snpp&snat';
                
                $path = $request->file('ufile14')->storeas('babelan/'. $year . "/". $month , $name14, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana14' => $request->dana14,
                        'pnbp_snpp&snat'=> basename($path),
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana14' => $request->dana14,
                        'pnbp_snpp&snat'=> basename($path),
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana14' => $request->dana14,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnbp_snpp&snat'=> basename($path),
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                    ]);
                }
                else {
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana14' => $request->dana14,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnbp_snpp&snat'=> basename($path),
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                    ]);
                }
            }  
            if ($request->hasFile('ufile15')) {
                $file15 = $request->file('ufile15');
                $name15 = Auth::user()->cabang . '-' . $file15->getClientOriginalName();
                $tujuan_upload = 'babelan/biaya_survey';
               
                $path = $request->file('ufile15')->storeas('babelan/'. $year . "/". $month , $name15, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana15' => $request->dana15,
                        'biaya_survey'=> basename($path),
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana15' => $request->dana15,
                        'biaya_survey'=> basename($path),
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana15' => $request->dana15,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'biaya_survey'=> basename($path),
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana15' => $request->dana15,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'biaya_survey'=> basename($path),
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                    ]);                    
                }
            }
            if ($request->hasFile('ufile16')) {
                $file16 = $request->file('ufile16');
                $name16 = Auth::user()->cabang . '-' . $file16->getClientOriginalName();
                $tujuan_upload = 'babelan/pnpb_sscec';
              
                $path = $request->file('ufile16')->storeas('babelan/'. $year . "/". $month , $name16 , 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana16' => $request->dana16,
                        'pnpb_sscec'=> basename($path),
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana16' => $request->dana16,
                        'pnpb_sscec'=> basename($path),
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana16' => $request->dana16,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnpb_sscec'=> basename($path),
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana16' => $request->dana16,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'pnpb_sscec'=> basename($path),
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('ufile17')) {
                $file17 = $request->file('ufile17');
                $name17 = Auth::user()->cabang . '-' . $file17->getClientOriginalName();
                $tujuan_upload = 'babelan/BKI';
                $path = $request->file('ufile17')->storeas('babelan/'. $year . "/". $month , $name17 , 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana17' => $request->dana17,
                        'BKI_Lambung'=> basename($path),
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana17' => $request->dana17,
                        'BKI_Lambung'=> basename($path),
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana17' => $request->dana17,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'BKI_Lambung'=> basename($path),
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana17' => $request->dana17,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'BKI_Lambung'=> basename($path),
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('ufile18')) {
                $file17 = $request->file('ufile18');
                $name17 = Auth::user()->cabang . '-' . $file17->getClientOriginalName();
                $tujuan_upload = 'babelan/BKI';
                $path = $request->file('ufile18')->storeas('babelan/'. $year . "/". $month , $name17 , 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana18' => $request->dana18,
                        'BKI_Mesin'=> basename($path),
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana18' => $request->dana18,
                        'BKI_Mesin'=> basename($path),
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana18' => $request->dana18,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'BKI_Mesin'=> basename($path),
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana18' => $request->dana18,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'BKI_Mesin'=> basename($path),
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('ufile19')) {
                $file17 = $request->file('ufile19');
                $name17 = Auth::user()->cabang . '-' . $file17->getClientOriginalName();
                $tujuan_upload = 'babelan/BKI';
                $path = $request->file('ufile19')->storeas('babelan/'. $year . "/". $month , $name17 , 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana19' => $request->dana19,
                        'BKI_Garis_Muat'=> basename($path),
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana19' => $request->dana19,
                        'BKI_Garis_Muat'=> basename($path),
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana19' => $request->dana19,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'BKI_Garis_Muat'=> basename($path),
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana19' => $request->dana19,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'BKI_Garis_Muat'=> basename($path),
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('ufile20')){
                $file1 = $request->file('ufile20');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('ufile20')->storeas('babelan/'. $year . "/". $month , $name1, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana20' => $request->dana20,  
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana20' => $request->dana20,
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana20' => $request->dana20,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana20' => $request->dana20,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('ufile21')) {
                $file1 = $request->file('ufile21');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('ufile21')->storeas('babelan/'. $year . "/". $month , $name1, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana21' => $request->dana21,  
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana21' => $request->dana21,
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana21' => $request->dana21,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana21' => $request->dana21,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('ufile22')) {
                $file1 = $request->file('ufile22');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('ufile22')->storeas('babelan/'. $year . "/". $month , $name1, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana22' => $request->dana22,  
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana22' => $request->dana22,
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana22' => $request->dana22,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana22' => $request->dana22,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('ufile23')) {
                $file1 = $request->file('ufile23');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('ufile23')->storeas('babelan/'. $year . "/". $month , $name1, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana23' => $request->dana23,  
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana23' => $request->dana23,
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana23' => $request->dana23,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana23' => $request->dana23,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('ufile24')) {
                $file1 = $request->file('ufile24');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('ufile24')->storeas('babelan/'. $year . "/". $month , $name1, 's3');
                if (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana24' => $request->dana24,  
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }elseif (documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documents::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana24' => $request->dana24,
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documents::create([
                        'upload_type' => 'Fund_Real',
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana24' => $request->dana24,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documents::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana24' => $request->dana24,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),
                    ]);
                }
            }
            
            return redirect()->back()->with('success', 'Upload Success! Silahkan di cek ke DASHBOARD !');
        }

        if (Auth::user()->cabang == 'Berau') {
            // dd($request);
            if($request->type_upload == 'Fund_Req'){
                $request->validate([
                    'beraufile1'=> 'mimes:pdf|max:3072' ,
                        'beraufile2'=> 'mimes:pdf|max:3072' ,
                        'beraufile3'=> 'mimes:pdf|max:3072' ,
                        'beraufile4'=> 'mimes:pdf|max:3072' ,
                        'beraufile5'=> 'mimes:pdf|max:3072' ,
                        'beraufile6'=> 'mimes:pdf|max:3072' ,
                        'beraufile7'=> 'mimes:pdf|max:3072' ,
                        'beraufile8'=> 'mimes:pdf|max:3072' ,
                        'beraufile9'=> 'mimes:pdf|max:3072' ,
                        'beraufile10'=> 'mimes:pdf|max:3072' ,
                        'beraufile11'=> 'mimes:pdf|max:3072' ,
                        'beraufile12'=> 'mimes:pdf|max:3072' ,
                        'beraufile13'=> 'mimes:pdf|max:3072' ,
                        'beraufile14'=> 'mimes:pdf|max:3072' ,
                        'beraufile15'=> 'mimes:pdf|max:3072' ,
                        'beraufile16'=> 'mimes:pdf|max:3072' ,
                        'beraufile17'=> 'mimes:pdf|max:3072' ,
                        'beraufile18'=> 'mimes:pdf|max:3072' ,
                        'beraufile19'=> 'mimes:pdf|max:3072' ,
                        'beraufile20'=> 'mimes:pdf|max:3072' , 
                        'beraufile21'=> 'mimes:pdf|max:3072' ,
                        'beraufile22'=> 'mimes:pdf|max:3072' ,
                        'beraufile23'=> 'mimes:pdf|max:3072' ,
                        'beraufile24'=> 'mimes:pdf|max:3072' , 
                        'beraufile25'=> 'mimes:pdf|max:3072' ,
                        'beraufile26'=> 'mimes:pdf|max:3072' ,
                        'beraufile27'=> 'mimes:pdf|max:3072' ,
                        'beraufile28'=> 'mimes:pdf|max:3072' ,
                        'beraufile29'=> 'mimes:pdf|max:3072' ,
                        'beraufile30'=> 'mimes:pdf|max:3072' ,
                        'beraufile31'=> 'mimes:pdf|max:3072' ,
                        'beraufile32'=> 'mimes:pdf|max:3072' ,
                        'beraufile33'=> 'mimes:pdf|max:3072' ,
                        'beraufile34'=> 'mimes:pdf|max:3072' ,
                    'dana1' => 'nullable|string|min:4|max:15' ,
                        'dana2' => 'nullable|string|min:4|max:15' ,
                        'dana3' => 'nullable|string|min:4|max:15' ,
                        'dana4' => 'nullable|string|min:4|max:15' ,
                        'dana5' => 'nullable|string|min:4|max:15' , 
                        'dana6' => 'nullable|string|min:4|max:15' ,
                        'dana7' => 'nullable|string|min:4|max:15' ,
                        'dana8' => 'nullable|string|min:4|max:15' ,
                        'dana9' => 'nullable|string|min:4|max:15' ,
                        'dana10' => 'nullable|string|min:4|max:15' ,
                        'dana11' => 'nullable|string|min:4|max:15' ,
                        'dana12' => 'nullable|string|min:4|max:15' ,
                        'dana13' => 'nullable|string|min:4|max:15' ,
                        'dana14' => 'nullable|string|min:4|max:15' ,
                        'dana15' => 'nullable|string|min:4|max:15' ,
                        'dana16' => 'nullable|string|min:4|max:15' ,
                        'dana17' => 'nullable|string|min:4|max:15' ,
                        'dana18' => 'nullable|string|min:4|max:15' ,
                        'dana19' => 'nullable|string|min:4|max:15' ,
                        'dana20' => 'nullable|string|min:4|max:15' ,
                        'dana21' => 'nullable|string|min:4|max:15' ,
                        'dana22' => 'nullable|string|min:4|max:15' ,
                        'dana23' => 'nullable|string|min:4|max:15' ,
                        'dana24' => 'nullable|string|min:4|max:15' ,
                        'dana25' => 'nullable|string|min:4|max:15' ,
                        'dana26' => 'nullable|string|min:4|max:15' ,
                        'dana27' => 'nullable|string|min:4|max:15' ,
                        'dana28' => 'nullable|string|min:4|max:15' ,
                        'dana29' => 'nullable|string|min:4|max:15' , 
                        'dana30' => 'nullable|string|min:4|max:15' ,
                        'dana31' => 'nullable|string|min:4|max:15' ,
                        'dana32' => 'nullable|string|min:4|max:15' ,
                        'dana33' => 'nullable|string|min:4|max:15' ,
                        'dana34' => 'nullable|string|min:4|max:15' ,
                    'nama_kapal' => 'nullable|string',
                    'Nama_Barge' => 'nullable|string',
                    'no_mohon' => 'required|string',
                    'no_PR' => 'required|string',
                ]);
                
            }elseif($request->type_upload == 'Fund_Real'){
                $request->validate([
                    'beraufile1'=> 'mimes:pdf|max:3072' ,
                        'beraufile2'=> 'mimes:pdf|max:3072' ,
                        'beraufile3'=> 'mimes:pdf|max:3072' ,
                        'beraufile4'=> 'mimes:pdf|max:3072' ,
                        'beraufile5'=> 'mimes:pdf|max:3072' ,
                        'beraufile6'=> 'mimes:pdf|max:3072' ,
                        'beraufile7'=> 'mimes:pdf|max:3072' ,
                        'beraufile8'=> 'mimes:pdf|max:3072' ,
                        'beraufile9'=> 'mimes:pdf|max:3072' ,
                        'beraufile10'=> 'mimes:pdf|max:3072' ,
                        'beraufile11'=> 'mimes:pdf|max:3072' ,
                        'beraufile12'=> 'mimes:pdf|max:3072' ,
                        'beraufile13'=> 'mimes:pdf|max:3072' ,
                        'beraufile14'=> 'mimes:pdf|max:3072' ,
                        'beraufile15'=> 'mimes:pdf|max:3072' ,
                        'beraufile16'=> 'mimes:pdf|max:3072' ,
                        'beraufile17'=> 'mimes:pdf|max:3072' ,
                        'beraufile18'=> 'mimes:pdf|max:3072' ,
                        'beraufile19'=> 'mimes:pdf|max:3072' ,
                        'beraufile20'=> 'mimes:pdf|max:3072' , 
                        'beraufile21'=> 'mimes:pdf|max:3072' ,
                        'beraufile22'=> 'mimes:pdf|max:3072' ,
                        'beraufile23'=> 'mimes:pdf|max:3072' ,
                        'beraufile24'=> 'mimes:pdf|max:3072' , 
                        'beraufile25'=> 'mimes:pdf|max:3072' ,
                        'beraufile26'=> 'mimes:pdf|max:3072' ,
                        'beraufile27'=> 'mimes:pdf|max:3072' ,
                        'beraufile28'=> 'mimes:pdf|max:3072' ,
                        'beraufile29'=> 'mimes:pdf|max:3072' ,
                        'beraufile30'=> 'mimes:pdf|max:3072' ,
                        'beraufile31'=> 'mimes:pdf|max:3072' ,
                        'beraufile32'=> 'mimes:pdf|max:3072' ,
                        'beraufile33'=> 'mimes:pdf|max:3072' ,
                        'beraufile34'=> 'mimes:pdf|max:3072' ,
                    'dana1' => 'nullable|string|min:4|max:15' ,
                        'dana2' => 'nullable|string|min:4|max:15' ,
                        'dana3' => 'nullable|string|min:4|max:15' ,
                        'dana4' => 'nullable|string|min:4|max:15' ,
                        'dana5' => 'nullable|string|min:4|max:15' , 
                        'dana6' => 'nullable|string|min:4|max:15' ,
                        'dana7' => 'nullable|string|min:4|max:15' ,
                        'dana8' => 'nullable|string|min:4|max:15' ,
                        'dana9' => 'nullable|string|min:4|max:15' ,
                        'dana10' => 'nullable|string|min:4|max:15' ,
                        'dana11' => 'nullable|string|min:4|max:15' ,
                        'dana12' => 'nullable|string|min:4|max:15' ,
                        'dana13' => 'nullable|string|min:4|max:15' ,
                        'dana14' => 'nullable|string|min:4|max:15' ,
                        'dana15' => 'nullable|string|min:4|max:15' ,
                        'dana16' => 'nullable|string|min:4|max:15' ,
                        'dana17' => 'nullable|string|min:4|max:15' ,
                        'dana18' => 'nullable|string|min:4|max:15' ,
                        'dana19' => 'nullable|string|min:4|max:15' ,
                        'dana20' => 'nullable|string|min:4|max:15' ,
                        'dana21' => 'nullable|string|min:4|max:15' ,
                        'dana22' => 'nullable|string|min:4|max:15' ,
                        'dana23' => 'nullable|string|min:4|max:15' ,
                        'dana24' => 'nullable|string|min:4|max:15' ,
                        'dana25' => 'nullable|string|min:4|max:15' ,
                        'dana26' => 'nullable|string|min:4|max:15' ,
                        'dana27' => 'nullable|string|min:4|max:15' ,
                        'dana28' => 'nullable|string|min:4|max:15' ,
                        'dana29' => 'nullable|string|min:4|max:15' , 
                        'dana30' => 'nullable|string|min:4|max:15' ,
                        'dana31' => 'nullable|string|min:4|max:15' ,
                        'dana32' => 'nullable|string|min:4|max:15' ,
                        'dana33' => 'nullable|string|min:4|max:15' ,
                        'dana34' => 'nullable|string|min:4|max:15' ,
                    'nama_kapal' => 'nullable|string',
                    'Nama_Barge' => 'nullable|string',
                    'no_mohon' => 'nullable|string',
                    'no_PR' => 'nullable|string',
                ]);
            }

            $year = date('Y');
            $month = date('m');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;
            
            if ($request->hasFile('beraufile1')) {
                $file1 = $request->file('beraufile1');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_sertifikat_konstruksi';
                $path = $request->file('beraufile1')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana1' => $request->dana1,
                        'pnbp_sertifikat_konstruksi' => basename($path),
                        'cabang' => Auth::user()->cabang ,
                        'status1' => 'on review',
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana1' => $request->dana1,
                        'pnbp_sertifikat_konstruksi' => basename($path),
                        'cabang' => Auth::user()->cabang ,
                        'status1' => 'on review',
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                    'upload_type' => 'Fund_Real',
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
    
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,
                    
                    'dana1' => $request->dana1,
                    'status1' => 'on review',
                    'time_upload1' => date("Y-m-d h:i:s"),
                    'pnbp_sertifikat_konstruksi' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,
                    
                    'dana1' => $request->dana1,
                    'status1' => 'on review',
                    'time_upload1' => date("Y-m-d h:i:s"),
                    'pnbp_sertifikat_konstruksi' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('beraufile2')) {
                $file1 = $request->file('beraufile2');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/jasa_urus_sertifikat';
                $path = $request->file('beraufile2')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,                   
                        'jasa_urus_sertifikat' => basename($path),
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,
                        'jasa_urus_sertifikat' => basename($path),
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'jasa_urus_sertifikat' => basename($path),
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'jasa_urus_sertifikat' => basename($path),
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile3')) {
                $file1 = $request->file('beraufile3');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_sertifikat_perlengkapan';
                $path = $request->file('beraufile3')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,                   
                        'pnbp_sertifikat_perlengkapan' => basename($path),
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,
                        'pnbp_sertifikat_perlengkapan' => basename($path),
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'pnbp_sertifikat_perlengkapan' => basename($path),
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'dana3' => $request->dana3,
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'pnbp_sertifikat_perlengkapan' => basename($path),
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'dana3' => $request->dana3,
                    ]);
                }
            }
            if ($request->hasFile('beraufile4')) {
                $file1 = $request->file('beraufile4');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_sertifikat_radio';
                $path = $request->file('beraufile4')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,                   
                        'pnbp_sertifikat_radio' => basename($path),
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,
                        'pnbp_sertifikat_radio' => basename($path),
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'pnbp_sertifikat_radio' => basename($path),
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'dana4' => $request->dana4,
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'pnbp_sertifikat_radio' => basename($path),
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'dana4' => $request->dana4,
                    ]);
                }
            }
            if ($request->hasFile('beraufile5')) {
                $file1 = $request->file('beraufile5');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_sertifikat_ows';
                $path = $request->file('beraufile5')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,                   
                        'pnbp_sertifikat_ows' => basename($path),
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,
                        'pnbp_sertifikat_ows' => basename($path),
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'pnbp_sertifikat_ows' => basename($path),
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'dana5' => $request->dana5,
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'pnbp_sertifikat_ows' => basename($path),
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'dana5' => $request->dana5,
                    ]);
                }
            }
            if ($request->hasFile('beraufile6')) {
                $file1 = $request->file('beraufile6');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_garis_muat';
                $path = $request->file('beraufile6')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,                   
                        'pnbp_garis_muat' => basename($path),
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,
                        'pnbp_garis_muat' => basename($path),
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'pnbp_garis_muat' => basename($path),
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'dana6' => $request->dana6,
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'pnbp_garis_muat' => basename($path),
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'dana6' => $request->dana6,
                    ]);
                }
            }
            if ($request->hasFile('beraufile7')) {
                $file1 = $request->file('beraufile7');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_pemeriksaan_endorse_sl';
                $path = $request->file('beraufile7')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,                   
                        'pnbp_pemeriksaan_endorse_sl' => basename($path),
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,
                        'pnbp_pemeriksaan_endorse_sl' => basename($path),
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'pnbp_pemeriksaan_endorse_sl' => basename($path),
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'dana7' => $request->dana7,
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'pnbp_pemeriksaan_endorse_sl' => basename($path),
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'dana7' => $request->dana7,
                    ]);
                }
            }
            if ($request->hasFile('beraufile8')) {
                $file1 = $request->file('beraufile8');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pemeriksaan_sertifikat';
                $path = $request->file('beraufile8')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,                   
                        'pemeriksaan_sertifikat' => basename($path),
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,
                        'pemeriksaan_sertifikat' => basename($path),
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'pemeriksaan_sertifikat' => basename($path),
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'dana8' => $request->dana8,
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'pemeriksaan_sertifikat' => basename($path),
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'dana8' => $request->dana8,
                    ]);
                }
            }
            if ($request->hasFile('beraufile9')) {
                $file1 = $request->file('beraufile9');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/marine_inspektor';
                $path = $request->file('beraufile9')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana9' => $request->dana9,                   
                        'marine_inspektor' => basename($path),
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana9' => $request->dana9,
                        'marine_inspektor' => basename($path),
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'marine_inspektor' => basename($path),
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                        'dana9' => $request->dana9,
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'marine_inspektor' => basename($path),
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                        'dana9' => $request->dana9,
                    ]);
                }
            }
            if ($request->hasFile('beraufile10')) {
                $file1 = $request->file('beraufile10');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/biaya_clearance';
                $path = $request->file('beraufile10')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana10' => $request->dana10,                   
                        'biaya_clearance' => basename($path),
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana10' => $request->dana10,
                        'biaya_clearance' => basename($path),
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'biaya_clearance' => basename($path),
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'dana10' => $request->dana10,
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'biaya_clearance' => basename($path),
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'dana10' => $request->dana10,
                    ]);
                }
            }          
            if ($request->hasFile('beraufile11')) {
                $file1 = $request->file('beraufile11');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_master_cable';
                $path = $request->file('beraufile11')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana11' => $request->dana11,                   
                        'pnbp_master_cable' => basename($path),
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana11' => $request->dana11,
                        'pnbp_master_cable' => basename($path),
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana11' => $request->dana11,
                        'pnbp_master_cable' => basename($path),
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana11' => $request->dana11,
                        'pnbp_master_cable' => basename($path),
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile12')) {
                $file1 = $request->file('beraufile12');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/cover_deck_logbook';
                $path = $request->file('beraufile12')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana12' => $request->dana12,                   
                        'cover_deck_logbook' => basename($path),
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana12' => $request->dana12,
                        'cover_deck_logbook' => basename($path),
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana12' => $request->dana12,
                        'cover_deck_logbook' => basename($path),
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana12' => $request->dana12,
                        'cover_deck_logbook' => basename($path),
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile13')) {
                $file1 = $request->file('beraufile13');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/cover_engine_logbook';
                $path = $request->file('beraufile13')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana13' => $request->dana13,                   
                        'cover_engine_logbook' => basename($path),
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana13' => $request->dana13,
                        'cover_engine_logbook' => basename($path),
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana13' => $request->dana13,
                        'cover_engine_logbook' => basename($path),
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana13' => $request->dana13,
                        'cover_engine_logbook' => basename($path),
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile14')) {
                $file1 = $request->file('beraufile14');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/exibitum_dect_logbook';
                $path = $request->file('beraufile14')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana14' => $request->dana14,                   
                        'exibitum_dect_logbook' => basename($path),
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana14' => $request->dana14,
                        'exibitum_dect_logbook' => basename($path),
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana14' => $request->dana14,
                        'exibitum_dect_logbook' => basename($path),
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana14' => $request->dana14,
                        'exibitum_dect_logbook' => basename($path),
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile15')) {
                $file1 = $request->file('beraufile15');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/exibitum_engine_logbook';
                $path = $request->file('beraufile15')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana15' => $request->dana15,                   
                        'exibitum_engine_logbook' => basename($path),
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana15' => $request->dana15,
                        'exibitum_engine_logbook' => basename($path),
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana15' => $request->dana15,
                        'exibitum_engine_logbook' => basename($path),
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana15' => $request->dana15,
                        'exibitum_engine_logbook' => basename($path),
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile16')) {
                $file1 = $request->file('beraufile16');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_deck_logbook';
                $path = $request->file('beraufile16')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana16' => $request->dana16,                   
                        'pnbp_deck_logbook' => basename($path),
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana16' => $request->dana16,
                        'pnbp_deck_logbook' => basename($path),
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana16' => $request->dana16,
                        'pnbp_deck_logbook' => basename($path),
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana16' => $request->dana16,
                        'pnbp_deck_logbook' => basename($path),
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile17')) {
                $file1 = $request->file('beraufile17');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_engine_logbook';             
                $path = $request->file('beraufile17')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana17' => $request->dana17,
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'pnbp_engine_logbook' => basename($path),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana17' => $request->dana17,
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'pnbp_engine_logbook' => basename($path),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana17' => $request->dana17,
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'pnbp_engine_logbook' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana17' => $request->dana17,
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'pnbp_engine_logbook' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('beraufile18')) {
                $file1 = $request->file('beraufile18');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/biaya_docking';
                $path = $request->file('beraufile18')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana18' => $request->dana18,                   
                        'biaya_docking' => basename($path),
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                    ]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana18' => $request->dana18,
                        'biaya_docking' => basename($path),
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                    ]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana18' => $request->dana18,
                        'biaya_docking' => basename($path),
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana18' => $request->dana18,
                        'biaya_docking' => basename($path),
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile19')) {
                $file1 = $request->file('beraufile19');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/lain-lain';
                $path = $request->file('beraufile19')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana19' => $request->dana19,                   
                        'lain-lain' => basename($path),
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),]); 
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana19' => $request->dana19,
                        'lain-lain' => basename($path),
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana19' => $request->dana19,
                        'lain-lain' => basename($path),
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana19' => $request->dana19,
                        'lain-lain' => basename($path),
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile20')) {
                $file1 = $request->file('beraufile20');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/biaya_labuh_tambat';
                $path = $request->file('beraufile20')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana20' => $request->dana20,                   
                        'biaya_labuh_tambat' => basename($path),
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana20' => $request->dana20,
                        'biaya_labuh_tambat' => basename($path),
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana20' => $request->dana20,
                        'biaya_labuh_tambat' => basename($path),
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana20' => $request->dana20,
                        'biaya_labuh_tambat' => basename($path),
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile21')) {
                $file1 = $request->file('beraufile21');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/biaya_rambu';
                $path = $request->file('beraufile21')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana21' => $request->dana21,                   
                        'biaya_rambu' => basename($path),
                        'status21' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana21' => $request->dana21,
                        'biaya_rambu' => basename($path),
                        'status21' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana21' => $request->dana21,
                        'biaya_rambu' => basename($path),
                        'status21' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana21' => $request->dana21,
                        'biaya_rambu' => basename($path),
                        'status21' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile22')) {
                $file1 = $request->file('beraufile22');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnbp_pemeriksaan';
                $path = $request->file('beraufile22')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana22' => $request->dana22,                   
                        'pnbp_pemeriksaan' => basename($path),
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana22' => $request->dana22,
                        'pnbp_pemeriksaan' => basename($path),
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana22' => $request->dana22,
                        'pnbp_pemeriksaan' => basename($path),
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana22' => $request->dana22,
                        'pnbp_pemeriksaan' => basename($path),
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile23')) {
                $file1 = $request->file('beraufile23');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/sertifikat_bebas_sanitasi&p3k';
                $path = $request->file('beraufile23')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana23' => $request->dana23,                   
                        'sertifikat_bebas_sanitasi&p3k' => basename($path),
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana23' => $request->dana23,
                        'sertifikat_bebas_sanitasi&p3k' => basename($path),
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana23' => $request->dana23,
                        'sertifikat_bebas_sanitasi&p3k' => basename($path),
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana23' => $request->dana23,
                        'sertifikat_bebas_sanitasi&p3k' => basename($path),
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile24')) {
                $file1 = $request->file('beraufile24');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/sertifikat_garis_muat';
                $path = $request->file('beraufile25')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana24' => $request->dana24,                   
                        'sertifikat_garis_muat' => basename($path),
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana24' => $request->dana24,
                        'sertifikat_garis_muat' => basename($path),
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana24' => $request->dana24,
                        'sertifikat_garis_muat' => basename($path),
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana24' => $request->dana24,
                        'sertifikat_garis_muat' => basename($path),
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile25')) {
                $file1 = $request->file('beraufile25');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/pnpb_sscec';
                $path = $request->file('beraufile25')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana25' => $request->dana25,                   
                        'pnpb_sscec' => basename($path),
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana25' => $request->dana25,
                        'pnpb_sscec' => basename($path),
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana25' => $request->dana25,
                        'pnpb_sscec' => basename($path),
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana25' => $request->dana25,
                        'pnpb_sscec' => basename($path),
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile26')) {
                $file1 = $request->file('beraufile26');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/ijin_sekali_jalan';
                $path = $request->file('beraufile26')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana26' => $request->dana26,                   
                        'ijin_sekali_jalan' => basename($path),
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana26' => $request->dana26,
                        'ijin_sekali_jalan' => basename($path),
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana26' => $request->dana26,
                        'ijin_sekali_jalan' => basename($path),
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana26' => $request->dana26,
                        'ijin_sekali_jalan' => basename($path),
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile27')) {
                $file1 = $request->file('beraufile27');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/BKI';
                $path = $request->file('beraufile27')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana27' => $request->dana27,                   
                        'BKI_Lambung' => basename($path),
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana27' => $request->dana27,
                        'BKI_Lambung' => basename($path),
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana27' => $request->dana27,
                        'BKI_Lambung' => basename($path),
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana27' => $request->dana27,
                        'BKI_Lambung' => basename($path),
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile28')) {
                $file1 = $request->file('beraufile28');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/BKI';
                $path = $request->file('beraufile28')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana28' => $request->dana28,                   
                        'BKI_Mesin' => basename($path),
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana28' => $request->dana28,
                        'BKI_Mesin' => basename($path),
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana28' => $request->dana28,
                        'BKI_Mesin' => basename($path),
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana28' => $request->dana28,
                        'BKI_Mesin' => basename($path),
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile29')) {
                $file1 = $request->file('beraufile29');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'berau/BKI';
                $path = $request->file('beraufile29')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana29' => $request->dana29,                   
                        'BKI_Garis_Muat' => basename($path),
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana29' => $request->dana29,
                        'BKI_Garis_Muat' => basename($path),
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana29' => $request->dana29,
                        'BKI_Garis_Muat' => basename($path),
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana29' => $request->dana29,
                        'BKI_Garis_Muat' => basename($path),
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('beraufile30')) {
                $file1 = $request->file('beraufile30');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('beraufile30')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana30' => $request->dana30,  
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana30' => $request->dana30,
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana30' => $request->dana30,
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana30' => $request->dana30,
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('beraufile31')) {
                $file1 = $request->file('beraufile31');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('beraufile31')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana31' => $request->dana31,  
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana31' => $request->dana31,
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana31' => $request->dana31,
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana31' => $request->dana31,
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('beraufile32')) {
                $file1 = $request->file('beraufile32');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('beraufile32')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana32' => $request->dana32,  
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana32' => $request->dana32,
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana32' => $request->dana32,
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana32' => $request->dana32,
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('beraufile33')) {
                $file1 = $request->file('beraufile33');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('beraufile33')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana33' => $request->dana33,  
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana33' => $request->dana33,
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana33' => $request->dana33,
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana33' => $request->dana33,
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('beraufile34')) {
                $file1 = $request->file('beraufile34');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('beraufile34')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana34' => $request->dana34,  
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }elseif (documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentberau::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana34' => $request->dana34,
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentberau::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana34' => $request->dana34,
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),
                    ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentberau::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana34' => $request->dana34,
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),
                    ]);
                }
            }
            
            return redirect()->back()->with('success', 'Upload Success! Silahkan di cek ke DASHBOARD !');
        }
            
        if (Auth::user()->cabang == 'Banjarmasin') {
            if($request->type_upload == 'Fund_Req'){
                $request->validate([
                    //Banjarmasin
                    'banjarmasinfile1'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile2'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile3'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile4'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile5'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile6'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile7'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile8'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile9'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile10'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile11'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile12'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile13'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile14'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile15'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile16'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile17'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile18'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile19'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile20'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile21'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile22'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile23'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile24'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile25'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile26'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile27'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile28'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile29'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile30'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile31'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile32'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile33'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile34'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile35'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile36'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile37'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile38'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile39'=> 'mimes:pdf|max:3072' ,
                    'dana1' => 'nullable|string|min:4|max:15' ,
                        'dana2' => 'nullable|string|min:4|max:15' ,
                        'dana3' => 'nullable|string|min:4|max:15' ,
                        'dana4' => 'nullable|string|min:4|max:15' ,
                        'dana5' => 'nullable|string|min:4|max:15' ,
                        'dana6' => 'nullable|string|min:4|max:15' ,
                        'dana7' => 'nullable|string|min:4|max:15' ,
                        'dana8' => 'nullable|string|min:4|max:15' ,
                        'dana9' => 'nullable|string|min:4|max:15' ,
                        'dana10' => 'nullable|string|min:4|max:15' ,
                        'dana11' => 'nullable|string|min:4|max:15' ,
                        'dana12' => 'nullable|string|min:4|max:15' ,
                        'dana13' => 'nullable|string|min:4|max:15' ,
                        'dana14' => 'nullable|string|min:4|max:15' ,
                        'dana15' => 'nullable|string|min:4|max:15' ,
                        'dana16' => 'nullable|string|min:4|max:15' ,
                        'dana17' => 'nullable|string|min:4|max:15' ,
                        'dana18' => 'nullable|string|min:4|max:15' ,
                        'dana19' => 'nullable|string|min:4|max:15' ,
                        'dana20' => 'nullable|string|min:4|max:15' ,
                        'dana21' => 'nullable|string|min:4|max:15' ,
                        'dana22' => 'nullable|string|min:4|max:15' ,
                        'dana23' => 'nullable|string|min:4|max:15' ,
                        'dana24' => 'nullable|string|min:4|max:15' ,
                        'dana25' => 'nullable|string|min:4|max:15' ,
                        'dana26' => 'nullable|string|min:4|max:15' ,
                        'dana27' => 'nullable|string|min:4|max:15' ,
                        'dana28' => 'nullable|string|min:4|max:15' ,
                        'dana29' => 'nullable|string|min:4|max:15' ,
                        'dana30' => 'nullable|string|min:4|max:15' ,
                        'dana31' => 'nullable|string|min:4|max:15' ,
                        'dana32' => 'nullable|string|min:4|max:15' ,
                        'dana33' => 'nullable|string|min:4|max:15' ,
                        'dana34' => 'nullable|string|min:4|max:15' ,
                        'dana35' => 'nullable|string|min:4|max:15' ,
                        'dana36' => 'nullable|string|min:4|max:15' ,
                        'dana37' => 'nullable|string|min:4|max:15' ,
                        'dana38' => 'nullable|string|min:4|max:15' ,
                        'dana39' => 'nullable|string|min:4|max:15' ,
                    'nama_kapal' => 'nullable|string',
                    'Nama_Barge' => 'nullable|string',
                    'no_mohon' => 'required|string',
                    'no_PR' => 'required|string',
                ]);
                
            }elseif($request->type_upload == 'Fund_Real'){
                $request->validate([
                    //Banjarmasin
                    'banjarmasinfile1'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile2'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile3'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile4'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile5'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile6'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile7'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile8'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile9'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile10'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile11'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile12'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile13'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile14'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile15'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile16'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile17'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile18'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile19'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile20'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile21'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile22'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile23'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile24'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile25'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile26'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile27'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile28'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile29'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile30'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile31'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile32'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile33'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile34'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile35'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile36'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile37'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile38'=> 'mimes:pdf|max:3072' ,
                        'banjarmasinfile39'=> 'mimes:pdf|max:3072' ,
                    'dana1' => 'nullable|string|min:4|max:15' ,
                        'dana2' => 'nullable|string|min:4|max:15' ,
                        'dana3' => 'nullable|string|min:4|max:15' ,
                        'dana4' => 'nullable|string|min:4|max:15' ,
                        'dana5' => 'nullable|string|min:4|max:15' ,
                        'dana6' => 'nullable|string|min:4|max:15' ,
                        'dana7' => 'nullable|string|min:4|max:15' ,
                        'dana8' => 'nullable|string|min:4|max:15' ,
                        'dana9' => 'nullable|string|min:4|max:15' ,
                        'dana10' => 'nullable|string|min:4|max:15' ,
                        'dana11' => 'nullable|string|min:4|max:15' ,
                        'dana12' => 'nullable|string|min:4|max:15' ,
                        'dana13' => 'nullable|string|min:4|max:15' ,
                        'dana14' => 'nullable|string|min:4|max:15' ,
                        'dana15' => 'nullable|string|min:4|max:15' ,
                        'dana16' => 'nullable|string|min:4|max:15' ,
                        'dana17' => 'nullable|string|min:4|max:15' ,
                        'dana18' => 'nullable|string|min:4|max:15' ,
                        'dana19' => 'nullable|string|min:4|max:15' ,
                        'dana20' => 'nullable|string|min:4|max:15' ,
                        'dana21' => 'nullable|string|min:4|max:15' ,
                        'dana22' => 'nullable|string|min:4|max:15' ,
                        'dana23' => 'nullable|string|min:4|max:15' ,
                        'dana24' => 'nullable|string|min:4|max:15' ,
                        'dana25' => 'nullable|string|min:4|max:15' ,
                        'dana26' => 'nullable|string|min:4|max:15' ,
                        'dana27' => 'nullable|string|min:4|max:15' ,
                        'dana28' => 'nullable|string|min:4|max:15' ,
                        'dana29' => 'nullable|string|min:4|max:15' ,
                        'dana30' => 'nullable|string|min:4|max:15' ,
                        'dana31' => 'nullable|string|min:4|max:15' ,
                        'dana32' => 'nullable|string|min:4|max:15' ,
                        'dana33' => 'nullable|string|min:4|max:15' ,
                        'dana34' => 'nullable|string|min:4|max:15' ,
                        'dana35' => 'nullable|string|min:4|max:15' ,
                        'dana36' => 'nullable|string|min:4|max:15' ,
                        'dana37' => 'nullable|string|min:4|max:15' ,
                        'dana38' => 'nullable|string|min:4|max:15' ,
                        'dana39' => 'nullable|string|min:4|max:15' ,
                    'nama_kapal' => 'nullable|string',
                    'Nama_Barge' => 'nullable|string',
                    'no_mohon' => 'nullable|string',
                    'no_PR' => 'nullable|string',
                ]);
            }
            $year = date('Y');
            $month = date('m');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;

            if ($request->hasFile('banjarmasinfile1')) {
                $file1 = $request->file('banjarmasinfile1');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/perjalanan';
                $path = $request->file('banjarmasinfile1')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana1' => $request->dana1,
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'perjalanan' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana1' => $request->dana1,
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'perjalanan' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'perjalanan' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana1' => $request->dana1,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'perjalanan' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile2')) {
                $file1 = $request->file('banjarmasinfile2');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/sertifikat_keselamatan';
                $path = $request->file('banjarmasinfile2')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
               if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'sertifikat_keselamatan' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'sertifikat_keselamatan' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'sertifikat_keselamatan' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana2' => $request->dana2,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'sertifikat_keselamatan' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile3')) {
                $file1 = $request->file('banjarmasinfile3');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/sertifikat_anti_fauling';
                $path = $request->file('banjarmasinfile3')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
               if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'sertifikat_anti_fauling' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'sertifikat_anti_fauling' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'sertifikat_anti_fauling' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana3' => $request->dana3,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'sertifikat_anti_fauling' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile4')) {
                $file1 = $request->file('banjarmasinfile4');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/surveyor';
                $path = $request->file('banjarmasinfile4')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
               if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),      
                        'surveyor' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),      
                        'surveyor' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),      
                        'surveyor' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana4' => $request->dana4,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),      
                        'surveyor' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile5')) {
                $file1 = $request->file('banjarmasinfile5');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/drawing&stability';
                $path = $request->file('banjarmasinfile5')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
               if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),        
                        'drawing&stability' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),        
                        'drawing&stability' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),        
                        'drawing&stability' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana5' => $request->dana5,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),        
                        'drawing&stability' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile6')) {
                $file1 = $request->file('banjarmasinfile6');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/laporan_pengeringan';
                $path = $request->file('banjarmasinfile6')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
               if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),       
                        'laporan_pengeringan' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),       
                        'laporan_pengeringan' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),       
                        'laporan_pengeringan' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana6' => $request->dana6,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),       
                        'laporan_pengeringan' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile7')) {
                $file1 = $request->file('banjarmasinfile7');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/berita_acara_lambung';
                $path = $request->file('banjarmasinfile7')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
               if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),   
                        'berita_acara_lambung' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),   
                        'berita_acara_lambung' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),   
                        'berita_acara_lambung' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana7' => $request->dana7,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),   
                        'laporan_pemeriksaan_nautis' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile8')) {
                $file1 = $request->file('banjarmasinfile8');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/laporan_pemeriksaan_anti_faulin';
                $path = $request->file('banjarmasinfile8')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
               if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),      
                        'laporan_pemeriksaan_anti_faulin' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),      
                        'laporan_pemeriksaan_anti_faulin' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),      
                        'laporan_pemeriksaan_anti_faulin' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana8' => $request->dana8,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                       'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),      
                        'laporan_pemeriksaan_anti_faulin' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile9')) {
                $file1 = $request->file('banjarmasinfile9');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/laporan_pemeriksaan_anti_faulin';
                $path = $request->file('banjarmasinfile9')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
               if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana9' => $request->dana9,
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),       
                        'laporan_pemeriksaan_anti_faulin' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana9' => $request->dana9,
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),       
                        'laporan_pemeriksaan_anti_faulin' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),       
                        'laporan_pemeriksaan_anti_faulin' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana9' => $request->dana9,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),       
                        'laporan_pemeriksaan_anti_faulin' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile10')) {
                $file1 = $request->file('banjarmasinfile10');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/laporan_pemeriksaan_radio';
                $path = $request->file('banjarmasinfile10')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana10' => $request->dana10,
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_radio' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana10' => $request->dana10,
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_radio' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_radio' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana10' => $request->dana10,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_radio' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile11')) {
                $file1 = $request->file('banjarmasinfile11');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/laporan_pemeriksaan_snpp';
                $path = $request->file('banjarmasinfile11')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana11' => $request->dana11,
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_snpp' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana11' => $request->dana11,
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_snpp' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_snpp' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana11' => $request->dana11,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_snpp' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile12')) {
                $file1 = $request->file('banjarmasinfile12');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/bki';
                $path = $request->file('banjarmasinfile12')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana12' => $request->dana12,
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                        'bki' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana12' => $request->dana12,
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                        'bki' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                        'bki' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana12' => $request->dana12,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                        'bki' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile13')) {
                $file1 = $request->file('banjarmasinfile13');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/snpp_permanen';
                $path = $request->file('banjarmasinfile13')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana13' => $request->dana13,
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana13' => $request->dana13,
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana13' => $request->dana13,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile14')) {
                $file1 = $request->file('banjarmasinfile14');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/snpp_endorse';
                $path = $request->file('banjarmasinfile14')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana14' => $request->dana14,
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                        'snpp_endorse' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana14' => $request->dana14,
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                        'snpp_endorse' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                        'snpp_endorse' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana14' => $request->dana14,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                        'snpp_endorse' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile15')) {
                $file1 = $request->file('banjarmasinfile15');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/surat_laut_endorse';
                $path = $request->file('banjarmasinfile15')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana15' => $request->dana15,
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                        'surat_laut_endorse' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana15' => $request->dana15,
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                        'surat_laut_endorse' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                        'surat_laut_endorse' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana15' => $request->dana15,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                        'surat_laut_endorse' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile16')) {
                $file1 = $request->file('banjarmasinfile16');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/surat_laut_permanen';
                $path = $request->file('banjarmasinfile16')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana16' => $request->dana16,
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana16' => $request->dana16,
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana16' => $request->dana16,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile17')) {
                $file1 = $request->file('banjarmasinfile17');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/compas_seren';
                $path = $request->file('banjarmasinfile17')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana17' => $request->dana17,
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'compas_seren' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana17' => $request->dana17,
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'compas_seren' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'compas_seren' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana17' => $request->dana17,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'compas_seren' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile18')) {
                $file1 = $request->file('banjarmasinfile18');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/keselamatan_(tahunan)';
                $path = $request->file('banjarmasinfile18')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana18' => $request->dana18,
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'keselamatan_(tahunan)' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana18' => $request->dana18,
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'keselamatan_(tahunan)' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'keselamatan_(tahunan)' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana18' => $request->dana18,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'keselamatan_(tahunan)' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile19')) {
                $file1 = $request->file('banjarmasinfile19');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/keselamatan_(pengaturan_dok)';
                $path = $request->file('banjarmasinfile19')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana19' => $request->dana19,
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'keselamatan_(pengaturan_dok)' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana19' => $request->dana19,
                            'status19' => 'on review',
                            'time_upload19' => date("Y-m-d h:i:s"),
                            'keselamatan_(pengaturan_dok)' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'keselamatan_(pengaturan_dok)' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana19' => $request->dana19,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'keselamatan_(pengaturan_dok)' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile20')) {
                $file1 = $request->file('banjarmasinfile20');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/keselamatan_(dok)';
                $path = $request->file('banjarmasinfile20')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana20' => $request->dana20,
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'keselamatan_(dok)' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana20' => $request->dana20,
                            'status20' => 'on review',
                            'time_upload20' => date("Y-m-d h:i:s"),
                            'keselamatan_(dok)' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'keselamatan_(dok)' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana20' => $request->dana20,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'keselamatan_(dok)' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile21')) {
                $file1 = $request->file('banjarmasinfile21');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/garis_muat';
                $path = $request->file('banjarmasinfile21')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana21' => $request->dana21,
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'garis_muat' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana21' => $request->dana21,
                            'status21' => 'on review',
                            'time_upload21' => date("Y-m-d h:i:s"),
                            'garis_muat' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'garis_muat' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana21' => $request->dana21,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'garis_muat' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile22')) {
                $file1 = $request->file('banjarmasinfile22');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/dispensasi_isr';
                $path = $request->file('banjarmasinfile22')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana22' => $request->dana22,
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'dispensasi_isr' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana22' => $request->dana22,
                            'status22' => 'on review',
                            'time_upload22' => date("Y-m-d h:i:s"),
                            'dispensasi_isr' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'dispensasi_isr' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana22' => $request->dana22,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'dispensasi_isr' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile23')) {
                $file1 = $request->file('banjarmasinfile23');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/life_raft_1_2_pemadam';
                $path = $request->file('banjarmasinfile23')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana23' => $request->dana23,
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'life_raft_1_2_pemadam' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana23' => $request->dana23,
                            'status23' => 'on review',
                            'time_upload23' => date("Y-m-d h:i:s"),
                            'life_raft_1_2_pemadam' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'life_raft_1_2_pemadam' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana23' => $request->dana23,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'life_raft_1_2_pemadam' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile24')) {
                $file1 = $request->file('banjarmasinfile24');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/sscec';
                $path = $request->file('banjarmasinfile24')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana24' => $request->dana24,
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'sscec' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana24' => $request->dana24,
                            'status24' => 'on review',
                            'time_upload24' => date("Y-m-d h:i:s"),
                            'sscec' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'sscec' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana24' => $request->dana24,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'sscec' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile25')) {
                $file1 = $request->file('banjarmasinfile25');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/seatrail';
                $path = $request->file('banjarmasinfile25')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana25' => $request->dana25,
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'seatrail' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana25' => $request->dana25,
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'seatrail' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'seatrail' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana25' => $request->dana25,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'seatrail' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile26')) {
                $file1 = $request->file('banjarmasinfile26');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/laporan_pemeriksaan_umum';
                $path = $request->file('banjarmasinfile26')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana26' => $request->dana26,
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_umum' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana26' => $request->dana26,
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_umum' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_umum' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana26' => $request->dana26,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_umum' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile27')) {
                $file1 = $request->file('banjarmasinfile27');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/laporan_pemeriksaan_mesin';
                $path = $request->file('banjarmasinfile27')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana27' => $request->dana27,
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_mesin' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana27' => $request->dana27,
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_mesin' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_mesin' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana27' => $request->dana27,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_mesin' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile28')) {
                $file1 = $request->file('banjarmasinfile28');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/nota_dinas_perubahan_kawasan';
                $path = $request->file('banjarmasinfile28')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana28' => $request->dana28,
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'nota_dinas_perubahan_kawasan' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana28' => $request->dana28,
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'nota_dinas_perubahan_kawasan' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'nota_dinas_perubahan_kawasan' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana28' => $request->dana28,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'nota_dinas_perubahan_kawasan' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile29')) {
                $file1 = $request->file('banjarmasinfile29');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/PAS';
                $path = $request->file('banjarmasinfile29')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana29' => $request->dana29,
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'PAS' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana29' => $request->dana29,
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'PAS' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'PAS' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana29' => $request->dana29,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'PAS' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile30')) {
                $file1 = $request->file('banjarmasinfile30');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/invoice_bki';
                $path = $request->file('banjarmasinfile30')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana30' => $request->dana30,
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'invoice_bki' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana30' => $request->dana30,
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'invoice_bki' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'invoice_bki' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana30' => $request->dana30,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'invoice_bki' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile31')) {
                $file1 = $request->file('banjarmasinfile31');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/safe_manning';
                $path = $request->file('banjarmasinfile31')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana31' => $request->dana31,
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'safe_manning' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana31' => $request->dana31,
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'safe_manning' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'safe_manning' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana31' => $request->dana31,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'safe_manning' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile32')) {
                $file1 = $request->file('banjarmasinfile32');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/safe_manning';
                $path = $request->file('banjarmasinfile32')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana32' => $request->dana32,
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'bki_lambung' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana32' => $request->dana32,
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'bki_lambung' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'bki_lambung' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana32' => $request->dana32,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'bki_lambung' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile33')) {
                $file1 = $request->file('banjarmasinfile33');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/safe_manning';
                $path = $request->file('banjarmasinfile33')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana33' => $request->dana33,
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'bki_mesin' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana33' => $request->dana33,
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'bki_mesin' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'bki_mesin' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana33' => $request->dana33,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'bki_mesin' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile34')) {
                $file1 = $request->file('banjarmasinfile34');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'banjarmasin/bki_Garis_muat';
                $path = $request->file('banjarmasinfile34')->storeas('banjarmasin/'. $year . "/". $month , $name1, 's3');
                if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana34' => $request->dana34,
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'bki_Garis_muat' => basename($path),]);
                    }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana34' => $request->dana34,
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'bki_Garis_muat' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'bki_Garis_muat' => basename($path),
                        ]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'dana34' => $request->dana34,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'bki_Garis_muat' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile35')) {
                $file1 = $request->file('banjarmasinfile35');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('banjarmasinfile35')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if(documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana35' => $request->dana35,  
                        'status35' => 'on review',
                        'time_upload35' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' , date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana35' => $request->dana35,
                        'status35' => 'on review',
                        'time_upload35' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status35' => 'on review',
                        'time_upload35' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),
                        ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'dana35' => $request->dana35,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status35' => 'on review',
                        'time_upload35' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile36')) {
                $file1 = $request->file('banjarmasinfile36');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('banjarmasinfile36')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if(documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana36' => $request->dana36,  
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' , date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana36' => $request->dana36,
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),
                        ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'dana36' => $request->dana36,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile37')) {
                $file1 = $request->file('banjarmasinfile37');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('banjarmasinfile37')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if(documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana37' => $request->dana37,  
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' , date('Y-m-d'))->exists()){
                        documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana37' => $request->dana37,
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),
                        ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'dana37' => $request->dana37,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile38')) {
                $file1 = $request->file('banjarmasinfile38');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('banjarmasinfile38')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if(documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                    'dana38' => $request->dana38,  
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' , date('Y-m-d'))->exists()){
                documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                'dana38' => $request->dana38,
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),
                        ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'dana38' => $request->dana38,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),
                    ]);
                }
            }
            if ($request->hasFile('banjarmasinfile39')) {
                $file1 = $request->file('banjarmasinfile39');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('banjarmasinfile39')->storeas('berau/'. $year . "/". $month , $name1, 's3');
                if(documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                    'dana39' => $request->dana39,  
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }else if (documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' , date('Y-m-d'))->exists()){
                documentbanjarmasin::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                'dana39' => $request->dana39,
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),
                        ]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentbanjarmasin::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'dana39' => $request->dana39,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),
                    ]);
                }
            }
            
            return redirect()->back()->with('success', 'Upload Success! Silahkan di cek ke DASHBOARD !');
        }
            
        if (Auth::user()->cabang == 'Samarinda' or Auth::user()->cabang == 'Kendari' or Auth::user()->cabang == 'Morosi') {
            //Samarinda
            if($request->type_upload == 'Fund_Req'){
                $request->validate([
                    'samarindafile1' => 'mimes:pdf|max:3072' , 
                        'samarindafile2' => 'mimes:pdf|max:3072' ,
                        'samarindafile3' => 'mimes:pdf|max:3072' ,
                        'samarindafile4' => 'mimes:pdf|max:3072' ,
                        'samarindafile5' => 'mimes:pdf|max:3072' ,
                        'samarindafile6' => 'mimes:pdf|max:3072' ,
                        'samarindafile7' => 'mimes:pdf|max:3072' ,
                        'samarindafile8' => 'mimes:pdf|max:3072' ,
                        'samarindafile9' => 'mimes:pdf|max:3072' ,
                        'samarindafile10'=> 'mimes:pdf|max:3072' ,
                        'samarindafile11'=> 'mimes:pdf|max:3072' ,
                        'samarindafile12'=> 'mimes:pdf|max:3072' ,
                        'samarindafile13'=> 'mimes:pdf|max:3072' ,
                        'samarindafile14'=> 'mimes:pdf|max:3072' ,
                        'samarindafile15'=> 'mimes:pdf|max:3072' ,
                        'samarindafile16'=> 'mimes:pdf|max:3072' ,
                        'samarindafile17'=> 'mimes:pdf|max:3072' ,
                        'samarindafile18'=> 'mimes:pdf|max:3072' ,
                        'samarindafile19'=> 'mimes:pdf|max:3072' ,
                        'samarindafile20'=> 'mimes:pdf|max:3072' ,
                        'samarindafile21'=> 'mimes:pdf|max:3072' ,
                        'samarindafile22'=> 'mimes:pdf|max:3072' ,
                        'samarindafile23'=> 'mimes:pdf|max:3072' ,
                        'samarindafile24'=> 'mimes:pdf|max:3072' ,
                        'samarindafile25'=> 'mimes:pdf|max:3072' ,
                        'samarindafile26'=> 'mimes:pdf|max:3072' ,
                        'samarindafile27'=> 'mimes:pdf|max:3072' ,
                        'samarindafile28'=> 'mimes:pdf|max:3072' ,
                        'samarindafile29'=> 'mimes:pdf|max:3072' ,
                        'samarindafile30'=> 'mimes:pdf|max:3072' ,
                        'samarindafile31'=> 'mimes:pdf|max:3072' ,
                        'samarindafile32'=> 'mimes:pdf|max:3072' ,
                        'samarindafile33'=> 'mimes:pdf|max:3072' ,
                        'samarindafile34'=> 'mimes:pdf|max:3072' ,
                        'samarindafile35'=> 'mimes:pdf|max:3072' ,
                        'samarindafile36'=> 'mimes:pdf|max:3072' ,
                        'samarindafile37'=> 'mimes:pdf|max:3072' ,
                        'samarindafile38'=> 'mimes:pdf|max:3072' ,
                        'samarindafile39'=> 'mimes:pdf|max:3072' ,
                        'samarindafile40'=> 'mimes:pdf|max:3072' ,
                        'samarindafile41'=> 'mimes:pdf|max:3072' ,
                        'samarindafile42'=> 'mimes:pdf|max:3072' ,
                        'samarindafile43'=> 'mimes:pdf|max:3072' ,
                        'samarindafile44'=> 'mimes:pdf|max:3072' ,
                        'samarindafile45'=> 'mimes:pdf|max:3072' ,
                        'samarindafile46'=> 'mimes:pdf|max:3072' ,
                        'samarindafile47'=> 'mimes:pdf|max:3072' ,
                        'samarindafile48'=> 'mimes:pdf|max:3072' ,
                    'dana1' => 'nullable|string|min:4|max:15' ,
                        'dana2' => 'nullable|string|min:4|max:15' ,
                        'dana3' => 'nullable|string|min:4|max:15' ,
                        'dana4' => 'nullable|string|min:4|max:15' ,
                        'dana5' => 'nullable|string|min:4|max:15' , 
                        'dana6' => 'nullable|string|min:4|max:15' ,
                        'dana7' => 'nullable|string|min:4|max:15' ,
                        'dana8' => 'nullable|string|min:4|max:15' ,
                        'dana9' => 'nullable|string|min:4|max:15' ,
                        'dana10' => 'nullable|string|min:4|max:15' ,
                        'dana11' => 'nullable|string|min:4|max:15' ,
                        'dana12' => 'nullable|string|min:4|max:15' ,
                        'dana13' => 'nullable|string|min:4|max:15' ,
                        'dana14' => 'nullable|string|min:4|max:15' ,
                        'dana15' => 'nullable|string|min:4|max:15' ,
                        'dana16' => 'nullable|string|min:4|max:15' ,
                        'dana17' => 'nullable|string|min:4|max:15' ,
                        'dana18' => 'nullable|string|min:4|max:15' ,
                        'dana19' => 'nullable|string|min:4|max:15' ,
                        'dana20' => 'nullable|string|min:4|max:15' ,
                        'dana21' => 'nullable|string|min:4|max:15' ,
                        'dana22' => 'nullable|string|min:4|max:15' ,
                        'dana23' => 'nullable|string|min:4|max:15' ,
                        'dana24' => 'nullable|string|min:4|max:15' ,
                        'dana25' => 'nullable|string|min:4|max:15' ,
                        'dana26' => 'nullable|string|min:4|max:15' ,
                        'dana27' => 'nullable|string|min:4|max:15' ,
                        'dana28' => 'nullable|string|min:4|max:15' ,
                        'dana29' => 'nullable|string|min:4|max:15' , 
                        'dana30' => 'nullable|string|min:4|max:15' ,
                        'dana31' => 'nullable|string|min:4|max:15' ,
                        'dana32' => 'nullable|string|min:4|max:15' ,
                        'dana33' => 'nullable|string|min:4|max:15' ,
                        'dana34' => 'nullable|string|min:4|max:15' ,
                        'dana35' => 'nullable|string|min:4|max:15' ,
                        'dana36' => 'nullable|string|min:4|max:15' ,
                        'dana37' => 'nullable|string|min:4|max:15' ,
                        'dana38' => 'nullable|string|min:4|max:15' ,
                        'dana39' => 'nullable|string|min:4|max:15' , 
                        'dana40' => 'nullable|string|min:4|max:15' ,
                        'dana41' => 'nullable|string|min:4|max:15' ,
                        'dana42' => 'nullable|string|min:4|max:15' ,
                        'dana43' => 'nullable|string|min:4|max:15' ,
                        'dana44' => 'nullable|string|min:4|max:15' ,
                        'dana45' => 'nullable|string|min:4|max:15' ,
                        'dana46' => 'nullable|string|min:4|max:15' ,
                        'dana47' => 'nullable|string|min:4|max:15' ,
                        'dana48' => 'nullable|string|min:4|max:15' ,
                    'nama_kapal' => 'nullable|string',
                    'Nama_Barge' => 'nullable|string',
                    'no_mohon' => 'required|string',
                    'no_PR' => 'required|string',
                ]);
            }elseif($request->type_upload == 'Fund_Real'){
                $request->validate([
                    'samarindafile1' => 'mimes:pdf|max:3072' , 
                        'samarindafile2' => 'mimes:pdf|max:3072' ,
                        'samarindafile3' => 'mimes:pdf|max:3072' ,
                        'samarindafile4' => 'mimes:pdf|max:3072' ,
                        'samarindafile5' => 'mimes:pdf|max:3072' ,
                        'samarindafile6' => 'mimes:pdf|max:3072' ,
                        'samarindafile7' => 'mimes:pdf|max:3072' ,
                        'samarindafile8' => 'mimes:pdf|max:3072' ,
                        'samarindafile9' => 'mimes:pdf|max:3072' ,
                        'samarindafile10'=> 'mimes:pdf|max:3072' ,
                        'samarindafile11'=> 'mimes:pdf|max:3072' ,
                        'samarindafile12'=> 'mimes:pdf|max:3072' ,
                        'samarindafile13'=> 'mimes:pdf|max:3072' ,
                        'samarindafile14'=> 'mimes:pdf|max:3072' ,
                        'samarindafile15'=> 'mimes:pdf|max:3072' ,
                        'samarindafile16'=> 'mimes:pdf|max:3072' ,
                        'samarindafile17'=> 'mimes:pdf|max:3072' ,
                        'samarindafile18'=> 'mimes:pdf|max:3072' ,
                        'samarindafile19'=> 'mimes:pdf|max:3072' ,
                        'samarindafile20'=> 'mimes:pdf|max:3072' ,
                        'samarindafile21'=> 'mimes:pdf|max:3072' ,
                        'samarindafile22'=> 'mimes:pdf|max:3072' ,
                        'samarindafile23'=> 'mimes:pdf|max:3072' ,
                        'samarindafile24'=> 'mimes:pdf|max:3072' ,
                        'samarindafile25'=> 'mimes:pdf|max:3072' ,
                        'samarindafile26'=> 'mimes:pdf|max:3072' ,
                        'samarindafile27'=> 'mimes:pdf|max:3072' ,
                        'samarindafile28'=> 'mimes:pdf|max:3072' ,
                        'samarindafile29'=> 'mimes:pdf|max:3072' ,
                        'samarindafile30'=> 'mimes:pdf|max:3072' ,
                        'samarindafile31'=> 'mimes:pdf|max:3072' ,
                        'samarindafile32'=> 'mimes:pdf|max:3072' ,
                        'samarindafile33'=> 'mimes:pdf|max:3072' ,
                        'samarindafile34'=> 'mimes:pdf|max:3072' ,
                        'samarindafile35'=> 'mimes:pdf|max:3072' ,
                        'samarindafile36'=> 'mimes:pdf|max:3072' ,
                        'samarindafile37'=> 'mimes:pdf|max:3072' ,
                        'samarindafile38'=> 'mimes:pdf|max:3072' ,
                        'samarindafile39'=> 'mimes:pdf|max:3072' ,
                        'samarindafile40'=> 'mimes:pdf|max:3072' ,
                        'samarindafile41'=> 'mimes:pdf|max:3072' ,
                        'samarindafile42'=> 'mimes:pdf|max:3072' ,
                        'samarindafile43'=> 'mimes:pdf|max:3072' ,
                        'samarindafile44'=> 'mimes:pdf|max:3072' ,
                        'samarindafile45'=> 'mimes:pdf|max:3072' ,
                        'samarindafile46'=> 'mimes:pdf|max:3072' ,
                        'samarindafile47'=> 'mimes:pdf|max:3072' ,
                        'samarindafile48'=> 'mimes:pdf|max:3072' ,
                    'dana1' => 'nullable|string|min:4|max:15' ,
                        'dana2' => 'nullable|string|min:4|max:15' ,
                        'dana3' => 'nullable|string|min:4|max:15' ,
                        'dana4' => 'nullable|string|min:4|max:15' ,
                        'dana5' => 'nullable|string|min:4|max:15' , 
                        'dana6' => 'nullable|string|min:4|max:15' ,
                        'dana7' => 'nullable|string|min:4|max:15' ,
                        'dana8' => 'nullable|string|min:4|max:15' ,
                        'dana9' => 'nullable|string|min:4|max:15' ,
                        'dana10' => 'nullable|string|min:4|max:15' ,
                        'dana11' => 'nullable|string|min:4|max:15' ,
                        'dana12' => 'nullable|string|min:4|max:15' ,
                        'dana13' => 'nullable|string|min:4|max:15' ,
                        'dana14' => 'nullable|string|min:4|max:15' ,
                        'dana15' => 'nullable|string|min:4|max:15' ,
                        'dana16' => 'nullable|string|min:4|max:15' ,
                        'dana17' => 'nullable|string|min:4|max:15' ,
                        'dana18' => 'nullable|string|min:4|max:15' ,
                        'dana19' => 'nullable|string|min:4|max:15' ,
                        'dana20' => 'nullable|string|min:4|max:15' ,
                        'dana21' => 'nullable|string|min:4|max:15' ,
                        'dana22' => 'nullable|string|min:4|max:15' ,
                        'dana23' => 'nullable|string|min:4|max:15' ,
                        'dana24' => 'nullable|string|min:4|max:15' ,
                        'dana25' => 'nullable|string|min:4|max:15' ,
                        'dana26' => 'nullable|string|min:4|max:15' ,
                        'dana27' => 'nullable|string|min:4|max:15' ,
                        'dana28' => 'nullable|string|min:4|max:15' ,
                        'dana29' => 'nullable|string|min:4|max:15' , 
                        'dana30' => 'nullable|string|min:4|max:15' ,
                        'dana31' => 'nullable|string|min:4|max:15' ,
                        'dana32' => 'nullable|string|min:4|max:15' ,
                        'dana33' => 'nullable|string|min:4|max:15' ,
                        'dana34' => 'nullable|string|min:4|max:15' ,
                        'dana35' => 'nullable|string|min:4|max:15' ,
                        'dana36' => 'nullable|string|min:4|max:15' ,
                        'dana37' => 'nullable|string|min:4|max:15' ,
                        'dana38' => 'nullable|string|min:4|max:15' ,
                        'dana39' => 'nullable|string|min:4|max:15' , 
                        'dana40' => 'nullable|string|min:4|max:15' ,
                        'dana41' => 'nullable|string|min:4|max:15' ,
                        'dana42' => 'nullable|string|min:4|max:15' ,
                        'dana43' => 'nullable|string|min:4|max:15' ,
                        'dana44' => 'nullable|string|min:4|max:15' ,
                        'dana45' => 'nullable|string|min:4|max:15' ,
                        'dana46' => 'nullable|string|min:4|max:15' ,
                        'dana47' => 'nullable|string|min:4|max:15' ,
                        'dana48' => 'nullable|string|min:4|max:15' ,
                    'nama_kapal' => 'nullable|string',
                    'Nama_Barge' => 'nullable|string',
                    'no_mohon' => 'nullable|string',
                    'no_PR' => 'nullable|string',
                ]);
            }
            $year = date('Y');
            $month = date('m');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;

            if ($request->hasFile('samarindafile1')) {
                $file1 = $request->file('samarindafile1');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/sertifikat_keselamatan(perpanjangan)';
                $path = $request->file('samarindafile1')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()) {
                    documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana1' => $request->dana1,
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'sertifikat_keselamatan(perpanjangan)' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()) {
                    documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana1' => $request->dana1,
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'sertifikat_keselamatan(perpanjangan)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana1' => $request->dana1,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'sertifikat_keselamatan(perpanjangan)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana1' => $request->dana1,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'sertifikat_keselamatan(perpanjangan)' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile2')) {
                $file1 = $request->file('samarindafile2');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/perubahan_ok_13_ke_ok_1';
                $path = $request->file('samarindafile2')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'perubahan_ok_13_ke_ok_1' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'perubahan_ok_13_ke_ok_1' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana2' => $request->dana2,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                                
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'perubahan_ok_13_ke_ok_1' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana2' => $request->dana2,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                                
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'perubahan_ok_13_ke_ok_1' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile3')) {
                $file1 = $request->file('samarindafile3');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/keselamatan_(tahunan)';
                $path = $request->file('samarindafile3')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,  
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'keselamatan_(tahunan)' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,  
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'keselamatan_(tahunan)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana3' => $request->dana3,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'keselamatan_(tahunan)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana3' => $request->dana3,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'keselamatan_(tahunan)' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile4')) {
                $file1 = $request->file('samarindafile4');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/keselamatan_(dok)';
                $path = $request->file('samarindafile4')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,  
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'keselamatan_(dok)' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,  
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'keselamatan_(dok)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana4' => $request->dana4,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'keselamatan_(dok)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana4' => $request->dana4,
                        
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'keselamatan_(dok)' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile5')) {
                $file1 = $request->file('samarindafile5');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/keselamatan_(pengaturan_dok)';
                $path = $request->file('samarindafile5')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,  
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'keselamatan_(pengaturan_dok)' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,  
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'keselamatan_(pengaturan_dok)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana5' => $request->dana5,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'keselamatan_(pengaturan_dok)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana5' => $request->dana5,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'keselamatan_(pengaturan_dok)' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile6')) {
                $file1 = $request->file('samarindafile6');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/keselamatan_(penundaan_dok)';
                $path = $request->file('samarindafile6')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,  
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'keselamatan_(penundaan_dok)' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,  
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'keselamatan_(penundaan_dok)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana6' => $request->dana6,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'keselamatan_(penundaan_dok)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana6' => $request->dana6,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'keselamatan_(penundaan_dok)' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile7')) {
                $file1 = $request->file('samarindafile7');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/sertifikat_garis_muat';
                $path = $request->file('samarindafile7')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,  
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'sertifikat_garis_muat' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,  
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'sertifikat_garis_muat' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana7' => $request->dana7,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'sertifikat_garis_muat' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana7' => $request->dana7,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'sertifikat_garis_muat' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile8')) {
                $file1 = $request->file('samarindafile8');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/laporan_pemeriksaan_garis_muat';
                $path = $request->file('samarindafile8')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,  
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_garis_muat' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,  
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_garis_muat' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana8' => $request->dana8,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_garis_muat' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana8' => $request->dana8,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                            
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_garis_muat' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile9')) {
                    $file1 = $request->file('samarindafile9');
                    $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                    $tujuan_upload = 'samarinda/sertifikat_anti_fauling';
                        $path = $request->file('samarindafile9')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                    if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana9' => $request->dana9,  
                            'status9' => 'on review',
                            'time_upload9' => date("Y-m-d h:i:s"),
                            'sertifikat_anti_fauling' => basename($path),]);
                    }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                            'dana9' => $request->dana9,  
                            'status9' => 'on review',
                            'time_upload9' => date("Y-m-d h:i:s"),
                            'sertifikat_anti_fauling' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentsamarinda::create([
                            'upload_type' => 'Fund_Real',
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
                            'dana9' => $request->dana9,
    
                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,
    
                            'status9' => 'on review',
                            'time_upload9' => date("Y-m-d h:i:s"),
                            'sertifikat_anti_fauling' => basename($path),]);
                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentsamarinda::create([
                            'upload_type' => 'Fund_Req',
                            'no_PR' => $request->no_PR ,
                            'no_mohon' => $request->no_mohon,
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
                            'dana9' => $request->dana9,

                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,

                            'status9' => 'on review',
                            'time_upload9' => date("Y-m-d h:i:s"),
                            'sertifikat_anti_fauling' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile10')) {
                $file1 = $request->file('samarindafile10');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/surat_laut_permanen';
                $path = $request->file('samarindafile10')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana10' => $request->dana10,  
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana10' => $request->dana10,  
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana10' => $request->dana10,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana10' => $request->dana10,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile11')) {
                $file1 = $request->file('samarindafile11');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/surat_laut_endorse';
                $path = $request->file('samarindafile11')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana11' => $request->dana11,  
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'surat_laut_endorse' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana11' => $request->dana11,  
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'surat_laut_endorse' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana11' => $request->dana11,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'surat_laut_endorse' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana11' => $request->dana11,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status11' => 'on review',
                    'time_upload11' => date("Y-m-d h:i:s"),
                    'surat_laut_endorse' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile12')) {
                $file1 = $request->file('samarindafile12');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/call_sign';
                $path = $request->file('samarindafile12')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana12' => $request->dana12,  
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                        'call_sign' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana12' => $request->dana12,  
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                        'call_sign' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana12' => $request->dana12,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                        'call_sign' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana12' => $request->dana12,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'status12' => 'on review',
                        'time_upload12' => date("Y-m-d h:i:s"),
                        'call_sign' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile13')) {
                $file1 = $request->file('samarindafile13');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/perubahan_sertifikat_keselamatan';
                $path = $request->file('samarindafile13')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana13' => $request->dana13,  
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                        'perubahan_sertifikat_keselamatan' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana13' => $request->dana13,  
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                        'perubahan_sertifikat_keselamatan' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana13' => $request->dana13,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                        'perubahan_sertifikat_keselamatan' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana13' => $request->dana13,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status13' => 'on review',
                        'time_upload13' => date("Y-m-d h:i:s"),
                        'perubahan_sertifikat_keselamatan' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile14')) {
                $file1 = $request->file('samarindafile14');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/perubahan_kawasan_tanpa_notadin';
                $path = $request->file('samarindafile14')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana14' => $request->dana14,  
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                        'perubahan_kawasan_tanpa_notadin' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana14' => $request->dana14,  
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                        'perubahan_kawasan_tanpa_notadin' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana14' => $request->dana14,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                        'perubahan_kawasan_tanpa_notadin' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana14' => $request->dana14,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status14' => 'on review',
                        'time_upload14' => date("Y-m-d h:i:s"),
                        'perubahan_kawasan_tanpa_notadin' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile15')) {
                $file1 = $request->file('samarindafile15');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/snpp_permanen';
                $path = $request->file('samarindafile15')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana15' => $request->dana15,  
                    'status15' => 'on review',
                    'time_upload15' => date("Y-m-d h:i:s"),
                    'snpp_permanen' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana15' => $request->dana15,  
                    'status15' => 'on review',
                    'time_upload15' => date("Y-m-d h:i:s"),
                    'snpp_permanen' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana15' => $request->dana15,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana15' => $request->dana15,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile16')) {
                $file1 = $request->file('samarindafile16');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/snpp_endorse';
                $path = $request->file('samarindafile16')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana16' => $request->dana16,  
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'snpp_endorse' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana16' => $request->dana16,  
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'snpp_endorse' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana16' => $request->dana16,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'snpp_endorse' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana16' => $request->dana16,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status16' => 'on review',
                    'time_upload16' => date("Y-m-d h:i:s"),
                    'snpp_endorse' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile17')) {
                $file1 = $request->file('samarindafile17');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/laporan_pemeriksaan_snpp';
                $path = $request->file('samarindafile17')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana17' => $request->dana17,  
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_snpp' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana17' => $request->dana17,  
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_snpp' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana17' => $request->dana17,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_snpp' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana17' => $request->dana17,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status17' => 'on review',
                    'time_upload17' => date("Y-m-d h:i:s"),
                    'laporan_pemeriksaan_snpp' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile18')) {
                $file1 = $request->file('samarindafile18');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/laporan_pemeriksaan_keselamatan';
                $path = $request->file('samarindafile18')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana18' => $request->dana18,  
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_keselamatan' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana18' => $request->dana18,  
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_keselamatan' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana18' => $request->dana18,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_keselamatan' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana18' => $request->dana18,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status18' => 'on review',
                    'time_upload18' => date("Y-m-d h:i:s"),
                    'laporan_pemeriksaan_keselamatan' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile19')) {
                $file1 = $request->file('samarindafile19');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/buku_kesehatan';
                $path = $request->file('samarindafile19')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana19' => $request->dana19,  
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'buku_kesehatan' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana19' => $request->dana19,  
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'buku_kesehatan' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana19' => $request->dana19,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'buku_kesehatan' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana19' => $request->dana19,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status19' => 'on review',
                    'time_upload19' => date("Y-m-d h:i:s"),
                    'buku_kesehatan' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile20')) {
                $file1 = $request->file('samarindafile20');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/sertifikat_sanitasi_water&p3k';
                $path = $request->file('samarindafile20')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana20' => $request->dana20,  
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'sertifikat_sanitasi_water&p3k' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana20' => $request->dana20,  
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'sertifikat_sanitasi_water&p3k' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana20' => $request->dana20,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'sertifikat_sanitasi_water&p3k' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana20' => $request->dana20,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status20' => 'on review',
                    'time_upload20' => date("Y-m-d h:i:s"),
                    'sertifikat_sanitasi_water&p3k' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile21')) {
                $file1 = $request->file('samarindafile21');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/pengaturan_non_ke_klas_bki';
                $path = $request->file('samarindafile21')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                            'dana21' => $request->dana21,  
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'pengaturan_non_ke_klas_bki' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                            'dana21' => $request->dana21,  
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'pengaturan_non_ke_klas_bki' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana21' => $request->dana21,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'pengaturan_non_ke_klas_bki' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana21' => $request->dana21,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'pengaturan_non_ke_klas_bki' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile22')) {
                $file1 = $request->file('samarindafile22');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/pengaturan_klas_bki_(dok_ss)';
                $path = $request->file('samarindafile22')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana22' => $request->dana22,  
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'pengaturan_klas_bki_(dok_ss)' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana22' => $request->dana22,  
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'pengaturan_klas_bki_(dok_ss)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana22' => $request->dana22,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'pengaturan_klas_bki_(dok_ss)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana22' => $request->dana22,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status22' => 'on review',
                    'time_upload22' => date("Y-m-d h:i:s"),
                    'pengaturan_klas_bki_(dok_ss)' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile23')) {
                $file1 = $request->file('samarindafile23');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/surveyor_endorse_tahunan_bki';
                $path = $request->file('samarindafile23')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana23' => $request->dana23,  
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'surveyor_endorse_tahunan_bki' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana23' => $request->dana23,  
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'surveyor_endorse_tahunan_bki' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana23' => $request->dana23,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'surveyor_endorse_tahunan_bki' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana23' => $request->dana23,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status23' => 'on review',
                    'time_upload23' => date("Y-m-d h:i:s"),
                    'surveyor_endorse_tahunan_bki' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile24')) {
                $file1 = $request->file('samarindafile24');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/pr_supplier_bki';
                $path = $request->file('samarindafile24')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana24' => $request->dana24,  
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'pr_supplier_bki' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana24' => $request->dana24,  
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'pr_supplier_bki' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana24' => $request->dana24,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'pr_supplier_bki' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana24' => $request->dana24,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status24' => 'on review',
                    'time_upload24' => date("Y-m-d h:i:s"),
                    'pr_supplier_bki' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile25')) {
                $file1 = $request->file('samarindafile25');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/balik_nama_grosse';
                $path = $request->file('samarindafile25')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana25' => $request->dana25,  
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'balik_nama_grosse' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana25' => $request->dana25,  
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'balik_nama_grosse' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana25' => $request->dana25,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'balik_nama_grosse' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana25' => $request->dana25,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status25' => 'on review',
                    'time_upload25' => date("Y-m-d h:i:s"),
                    'balik_nama_grosse' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile26')) {
                $file1 = $request->file('samarindafile26');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/kapal_baru_body_(set_dokumen)';
                $path = $request->file('samarindafile26')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana26' => $request->dana26,  
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'kapal_baru_body_(set_dokumen)' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana26' => $request->dana26,  
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'kapal_baru_body_(set_dokumen)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana26' => $request->dana26,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'kapal_baru_body_(set_dokumen)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana26' => $request->dana26,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status26' => 'on review',
                    'time_upload26' => date("Y-m-d h:i:s"),
                    'kapal_baru_body_(set_dokumen)' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile27')) {
                $file1 = $request->file('samarindafile27');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/halaman_tambahan_grosse';
                $path = $request->file('samarindafile27')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana27' => $request->dana27,  
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'halaman_tambahan_grosse' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana27' => $request->dana27,  
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'halaman_tambahan_grosse' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana27' => $request->dana27,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'halaman_tambahan_grosse' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana27' => $request->dana27,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'halaman_tambahan_grosse' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile28')) {
                $file1 = $request->file('samarindafile28');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/pnbp&pup';
                $path = $request->file('samarindafile28')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana28' => $request->dana28,  
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'pnbp&pup' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana28' => $request->dana28,  
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'pnbp&pup' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana28' => $request->dana28,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'pnbp&pup' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana28' => $request->dana28,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status28' => 'on review',
                    'time_upload28' => date("Y-m-d h:i:s"),
                    'pnbp&pup' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile29')) {
                $file1 = $request->file('samarindafile29');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/laporan_pemeriksaan_anti_teriti';
                $path = $request->file('samarindafile29')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana29' => $request->dana29,  
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_anti_teriti' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana29' => $request->dana29,  
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_anti_teriti' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana29' => $request->dana29,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'laporan_pemeriksaan_anti_teriti' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana29' => $request->dana29,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status29' => 'on review',
                    'time_upload29' => date("Y-m-d h:i:s"),
                    'laporan_pemeriksaan_anti_teriti' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile30')) {
                $file1 = $request->file('samarindafile30');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/surveyor_pengedokan';
                $path = $request->file('samarindafile30')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana30' => $request->dana30,  
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'surveyor_pengedokan' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana30' => $request->dana30,  
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'surveyor_pengedokan' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana30' => $request->dana30,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'surveyor_pengedokan' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                documentsamarinda::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR ,
                    'no_mohon' => $request->no_mohon,
                    'nama_kapal' => $mergenama_kapal,
                    'periode_awal' => $request->tgl_awal,
                    'periode_akhir' => $request->tgl_akhir,
                    'dana30' => $request->dana30,

                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,
                    
                    'status30' => 'on review',
                    'time_upload30' => date("Y-m-d h:i:s"),
                    'surveyor_pengedokan' => basename($path),]);
                }
            }
            if ($request->hasFile('samarindafile31')) {
                $file1 = $request->file('samarindafile31');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/surveyor_penerimaan_klas_bki';
                $path = $request->file('samarindafile31')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana31' => $request->dana31,  
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'surveyor_penerimaan_klas_bki' => basename($path),]);   
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana31' => $request->dana31,  
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'surveyor_penerimaan_klas_bki' => basename($path),]);   
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana31' => $request->dana31,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'surveyor_penerimaan_klas_bki' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana31' => $request->dana31,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'surveyor_penerimaan_klas_bki' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile32')) {
                $file1 = $request->file('samarindafile32');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/nota_tagihan_jasa_perkapalan';
                $path = $request->file('samarindafile32')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana32' => $request->dana32,  
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'nota_tagihan_jasa_perkapalan' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana32' => $request->dana32,  
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'nota_tagihan_jasa_perkapalan' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana32' => $request->dana32,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'nota_tagihan_jasa_perkapalan' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana32' => $request->dana32,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'nota_tagihan_jasa_perkapalan' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile33')) {
                $file1 = $request->file('samarindafile33');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/gambar_kapal_baru_(bki)';
                $path = $request->file('samarindafile33')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana33' => $request->dana33,  
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'gambar_kapal_baru_(bki)' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana33' => $request->dana33,  
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'gambar_kapal_baru_(bki)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana33' => $request->dana33,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'gambar_kapal_baru_(bki)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana33' => $request->dana33,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'gambar_kapal_baru_(bki)' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile34')) {
                $file1 = $request->file('samarindafile34');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/samarinda_jam1nan_(clc)';
                $path = $request->file('samarindafile34')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana34' => $request->dana34,  
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'samarinda_jam1nan_(clc)' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana34' => $request->dana34,  
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'samarinda_jam1nan_(clc)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana34' => $request->dana34,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'samarinda_jam1nan_(clc)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana34' => $request->dana34,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'samarinda_jam1nan_(clc)' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile35')) {
                $file1 = $request->file('samarindafile35');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/surat_ukur_dalam_negeri';
                $path = $request->file('samarindafile35')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana35' => $request->dana35,  
                            'status35' => 'on review',
                            'time_upload35' => date("Y-m-d h:i:s"),
                            'surat_ukur_dalam_negeri' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana35' => $request->dana35,  
                            'status35' => 'on review',
                            'time_upload35' => date("Y-m-d h:i:s"),
                            'surat_ukur_dalam_negeri' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana35' => $request->dana35,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status35' => 'on review',
                        'time_upload35' => date("Y-m-d h:i:s"),
                        'surat_ukur_dalam_negeri' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana35' => $request->dana35,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status35' => 'on review',
                        'time_upload35' => date("Y-m-d h:i:s"),
                        'surat_ukur_dalam_negeri' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile36')) {
                $file1 = $request->file('samarindafile36');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/penerbitan_sertifikat_kapal_baru';
                $path = $request->file('samarindafile36')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana36' => $request->dana36,  
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'penerbitan_sertifikat_kapal_baru' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana36' => $request->dana36,  
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'penerbitan_sertifikat_kapal_baru' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana36' => $request->dana36,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'penerbitan_sertifikat_kapal_baru' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana36' => $request->dana36,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'penerbitan_sertifikat_kapal_baru' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile37')) {
                $file1 = $request->file('samarindafile37');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/buku_stabilitas';
                $path = $request->file('samarindafile37')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana37' => $request->dana37,  
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'buku_stabilitas' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana37' => $request->dana37,  
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'buku_stabilitas' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana37' => $request->dana37,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'buku_stabilitas' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana37' => $request->dana37,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'buku_stabilitas' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile38')) {
                $file1 = $request->file('samarindafile38');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $tujuan_upload = 'samarinda/grosse_akta';
                $path = $request->file('samarindafile38')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana38' => $request->dana38,  
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'grosse_akta' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana38' => $request->dana38,  
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'grosse_akta' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana38' => $request->dana38,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'grosse_akta' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana38' => $request->dana38,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'grosse_akta' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile39')) {
                $file1 = $request->file('samarindafile39');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile39')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana39' => $request->dana39,  
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'penerbitan_nota_dinas_pertama' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana39' => $request->dana39,  
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'penerbitan_nota_dinas_pertama' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana39' => $request->dana39,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'penerbitan_nota_dinas_pertama' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana39' => $request->dana39,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'penerbitan_nota_dinas_pertama' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile40')) {
                $file1 = $request->file('samarindafile40');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile40')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana40' => $request->dana40,  
                        'status40' => 'on review',
                        'time_upload40' => date("Y-m-d h:i:s"),
                        'penerbitan_nota_dinas_kedua' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana40' => $request->dana40,  
                        'status40' => 'on review',
                        'time_upload40' => date("Y-m-d h:i:s"),
                        'penerbitan_nota_dinas_kedua' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana40' => $request->dana40,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status40' => 'on review',
                        'time_upload40' => date("Y-m-d h:i:s"),
                        'penerbitan_nota_dinas_kedua' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana40' => $request->dana40,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status40' => 'on review',
                        'time_upload40' => date("Y-m-d h:i:s"),
                        'penerbitan_nota_dinas_kedua' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile41')) {
                $file1 = $request->file('samarindafile41');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile41')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana41' => $request->dana41,  
                        'status41' => 'on review',
                        'time_upload41' => date("Y-m-d h:i:s"),
                        'BKI_Lambung' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana41' => $request->dana41,  
                        'status41' => 'on review',
                        'time_upload41' => date("Y-m-d h:i:s"),
                        'BKI_Lambung' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana41' => $request->dana41,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status41' => 'on review',
                        'time_upload41' => date("Y-m-d h:i:s"),
                        'BKI_Lambung' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana41' => $request->dana41,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status41' => 'on review',
                        'time_upload41' => date("Y-m-d h:i:s"),
                        'BKI_Lambung' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile42')) {
                $file1 = $request->file('samarindafile42');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile42')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana42' => $request->dana42,  
                        'status42' => 'on review',
                        'time_upload42' => date("Y-m-d h:i:s"),
                        'BKI_Mesin' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana42' => $request->dana42,  
                        'status42' => 'on review',
                        'time_upload42' => date("Y-m-d h:i:s"),
                        'BKI_Mesin' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana42' => $request->dana42,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status42' => 'on review',
                        'time_upload42' => date("Y-m-d h:i:s"),
                        'BKI_Mesin' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana42' => $request->dana42,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status42' => 'on review',
                        'time_upload42' => date("Y-m-d h:i:s"),
                        'BKI_Mesin' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile43')) {
                $file1 = $request->file('samarindafile43');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile43')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana43' => $request->dana43,  
                        'status43' => 'on review',
                        'time_upload43' => date("Y-m-d h:i:s"),
                        'BKI_Garis_Muat' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana43' => $request->dana43,  
                        'status43' => 'on review',
                        'time_upload43' => date("Y-m-d h:i:s"),
                        'BKI_Garis_Muat' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana43' => $request->dana43,
    
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'status43' => 'on review',
                        'time_upload43' => date("Y-m-d h:i:s"),
                        'BKI_Garis_Muat' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana43' => $request->dana43,

                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'status43' => 'on review',
                        'time_upload43' => date("Y-m-d h:i:s"),
                        'BKI_Garis_Muat' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile44')) {
                $file1 = $request->file('samarindafile44');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile44')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana44' => $request->dana44,  
                        'status44' => 'on review',
                        'time_upload44' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana44' => $request->dana44,  
                        'status44' => 'on review',
                        'time_upload44' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana44' => $request->dana44,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status44' => 'on review',
                        'time_upload44' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana44' => $request->dana44,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status44' => 'on review',
                        'time_upload44' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile45')) {
                $file1 = $request->file('samarindafile45');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile45')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana45' => $request->dana45,  
                        'status45' => 'on review',
                        'time_upload45' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana45' => $request->dana45,  
                        'status45' => 'on review',
                        'time_upload45' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana45' => $request->dana45,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status45' => 'on review',
                        'time_upload45' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana45' => $request->dana45,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status45' => 'on review',
                        'time_upload45' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile46')) {
                $file1 = $request->file('samarindafile46');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile46')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana46' => $request->dana46,  
                        'status46' => 'on review',
                        'time_upload46' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana46' => $request->dana46,  
                        'status46' => 'on review',
                        'time_upload46' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana46' => $request->dana46,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status46' => 'on review',
                        'time_upload46' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana46' => $request->dana46,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status46' => 'on review',
                        'time_upload46' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile47')) {
                $file1 = $request->file('samarindafile47');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile47')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana47' => $request->dana47,  
                        'status47' => 'on review',
                        'time_upload47' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana47' => $request->dana47,  
                        'status47' => 'on review',
                        'time_upload47' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana47' => $request->dana47,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status47' => 'on review',
                        'time_upload47' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana47' => $request->dana47,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status47' => 'on review',
                        'time_upload47' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                    }
            }
            if ($request->hasFile('samarindafile48')) {
                $file1 = $request->file('samarindafile48');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('samarindafile48')->storeas('samarinda/'. $year . "/". $month , $name1, 's3');
                if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana48' => $request->dana48,  
                        'status48' => 'on review',
                        'time_upload48' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }else if(documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentsamarinda::where('upload_type','Fund_Req')->where('cabang' , Auth::user()->cabang)->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana48' => $request->dana48,  
                        'status48' => 'on review',
                        'time_upload48' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana48' => $request->dana48,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status48' => 'on review',
                        'time_upload48' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentsamarinda::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        'dana48' => $request->dana48,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
            
                        'status48' => 'on review',
                        'time_upload48' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                    }
            }
            
            return redirect()->back()->with('success', 'Upload Success! Silahkan di cek ke DASHBOARD !');
        }

        if (Auth::user()->cabang == "Jakarta") {
            //jakarta
            if($request->type_upload == 'Fund_Req'){
                $request->validate([
                    'jktfile1' => 'mimes:pdf|max:3072' , 
                        'jktfile2' => 'mimes:pdf|max:3072' ,
                        'jktfile3' => 'mimes:pdf|max:3072' ,
                        'jktfile4' => 'mimes:pdf|max:3072' ,
                        'jktfile5' => 'mimes:pdf|max:3072' ,
                        'jktfile6' => 'mimes:pdf|max:3072' ,
                        'jktfile7' => 'mimes:pdf|max:3072' ,
                        'jktfile8' => 'mimes:pdf|max:3072' ,
                        'jktfile9' => 'mimes:pdf|max:3072' ,
                        'jktfile10'=> 'mimes:pdf|max:3072' ,
                        'jktfile11'=> 'mimes:pdf|max:3072' ,
                        'jktfile12'=> 'mimes:pdf|max:3072' ,
                        'jktfile13'=> 'mimes:pdf|max:3072' ,
                        'jktfile14'=> 'mimes:pdf|max:3072' ,
                        'jktfile15'=> 'mimes:pdf|max:3072' ,
                        'jktfile16'=> 'mimes:pdf|max:3072' ,
                        'jktfile17'=> 'mimes:pdf|max:3072' ,
                        'jktfile18'=> 'mimes:pdf|max:3072' ,
                        'jktfile19'=> 'mimes:pdf|max:3072' ,
                        'jktfile20'=> 'mimes:pdf|max:3072' ,
                        'jktfile21'=> 'mimes:pdf|max:3072' ,
                        'jktfile22'=> 'mimes:pdf|max:3072' ,
                        'jktfile23'=> 'mimes:pdf|max:3072' ,
                        'jktfile24'=> 'mimes:pdf|max:3072' ,
                        'jktfile25'=> 'mimes:pdf|max:3072' ,
                        'jktfile26'=> 'mimes:pdf|max:3072' ,
                        'jktfile27'=> 'mimes:pdf|max:3072' ,
                        'jktfile28'=> 'mimes:pdf|max:3072' ,
                        'jktfile29'=> 'mimes:pdf|max:3072' ,
                        'jktfile30'=> 'mimes:pdf|max:3072' ,
                        'jktfile31'=> 'mimes:pdf|max:3072' ,
                        'jktfile32'=> 'mimes:pdf|max:3072' ,
                        'jktfile33'=> 'mimes:pdf|max:3072' ,
                        'jktfile34'=> 'mimes:pdf|max:3072' ,
                        'jktfile35'=> 'mimes:pdf|max:3072' ,
                        'jktfile36'=> 'mimes:pdf|max:3072' ,
                        'jktfile37'=> 'mimes:pdf|max:3072' ,
                        'jktfile38'=> 'mimes:pdf|max:3072' ,
                        'jktfile39'=> 'mimes:pdf|max:3072' ,
                        'jktfile40'=> 'mimes:pdf|max:3072' ,
                        'jktfile41'=> 'mimes:pdf|max:3072' ,
                        'jktfile42'=> 'mimes:pdf|max:3072' ,
                        'jktfile43'=> 'mimes:pdf|max:3072' ,
                        'jktfile44'=> 'mimes:pdf|max:3072' ,
                        'jktfile45'=> 'mimes:pdf|max:3072' ,
                        'jktfile46'=> 'mimes:pdf|max:3072' ,
                        'jktfile47'=> 'mimes:pdf|max:3072' ,
                    'dana1' => 'nullable|string|min:4|max:15' ,
                        'dana2' => 'nullable|string|min:4|max:15' ,
                        'dana3' => 'nullable|string|min:4|max:15' ,
                        'dana4' => 'nullable|string|min:4|max:15' ,
                        'dana5' => 'nullable|string|min:4|max:15' , 
                        'dana6' => 'nullable|string|min:4|max:15' ,
                        'dana7' => 'nullable|string|min:4|max:15' ,
                        'dana8' => 'nullable|string|min:4|max:15' ,
                        'dana9' => 'nullable|string|min:4|max:15' ,
                        'dana10' => 'nullable|string|min:4|max:15' ,
                        'dana11' => 'nullable|string|min:4|max:15' ,
                        'dana12' => 'nullable|string|min:4|max:15' ,
                        'dana13' => 'nullable|string|min:4|max:15' ,
                        'dana14' => 'nullable|string|min:4|max:15' ,
                        'dana15' => 'nullable|string|min:4|max:15' ,
                        'dana16' => 'nullable|string|min:4|max:15' ,
                        'dana17' => 'nullable|string|min:4|max:15' ,
                        'dana18' => 'nullable|string|min:4|max:15' ,
                        'dana19' => 'nullable|string|min:4|max:15' ,
                        'dana20' => 'nullable|string|min:4|max:15' ,
                        'dana21' => 'nullable|string|min:4|max:15' ,
                        'dana22' => 'nullable|string|min:4|max:15' ,
                        'dana23' => 'nullable|string|min:4|max:15' ,
                        'dana24' => 'nullable|string|min:4|max:15' ,
                        'dana25' => 'nullable|string|min:4|max:15' ,
                        'dana26' => 'nullable|string|min:4|max:15' ,
                        'dana27' => 'nullable|string|min:4|max:15' ,
                        'dana28' => 'nullable|string|min:4|max:15' ,
                        'dana29' => 'nullable|string|min:4|max:15' , 
                        'dana30' => 'nullable|string|min:4|max:15' ,
                        'dana31' => 'nullable|string|min:4|max:15' ,
                        'dana32' => 'nullable|string|min:4|max:15' ,
                        'dana33' => 'nullable|string|min:4|max:15' ,
                        'dana34' => 'nullable|string|min:4|max:15' ,
                        'dana35' => 'nullable|string|min:4|max:15' ,
                        'dana36' => 'nullable|string|min:4|max:15' ,
                        'dana37' => 'nullable|string|min:4|max:15' ,
                        'dana38' => 'nullable|string|min:4|max:15' ,
                        'dana39' => 'nullable|string|min:4|max:15' , 
                        'dana40' => 'nullable|string|min:4|max:15' ,
                        'dana41' => 'nullable|string|min:4|max:15' ,
                        'dana42' => 'nullable|string|min:4|max:15' ,
                        'dana43' => 'nullable|string|min:4|max:15' ,
                        'dana44' => 'nullable|string|min:4|max:15' ,
                        'dana45' => 'nullable|string|min:4|max:15' ,
                        'dana46' => 'nullable|string|min:4|max:15' ,
                        'dana47' => 'nullable|string|min:4|max:15' ,
                    'nama_kapal' => 'nullable|string',
                    'Nama_Barge' => 'nullable|string',
                    'no_mohon' => 'required|string',
                    'no_PR' => 'required|string',
                ]);
            }elseif($request->type_upload == 'Fund_Real'){
                $request->validate([
                    'jktfile1' => 'mimes:pdf|max:3072' , 
                        'jktfile2' => 'mimes:pdf|max:3072' ,
                        'jktfile3' => 'mimes:pdf|max:3072' ,
                        'jktfile4' => 'mimes:pdf|max:3072' ,
                        'jktfile5' => 'mimes:pdf|max:3072' ,
                        'jktfile6' => 'mimes:pdf|max:3072' ,
                        'jktfile7' => 'mimes:pdf|max:3072' ,
                        'jktfile8' => 'mimes:pdf|max:3072' ,
                        'jktfile9' => 'mimes:pdf|max:3072' ,
                        'jktfile10'=> 'mimes:pdf|max:3072' ,
                        'jktfile11'=> 'mimes:pdf|max:3072' ,
                        'jktfile12'=> 'mimes:pdf|max:3072' ,
                        'jktfile13'=> 'mimes:pdf|max:3072' ,
                        'jktfile14'=> 'mimes:pdf|max:3072' ,
                        'jktfile15'=> 'mimes:pdf|max:3072' ,
                        'jktfile16'=> 'mimes:pdf|max:3072' ,
                        'jktfile17'=> 'mimes:pdf|max:3072' ,
                        'jktfile18'=> 'mimes:pdf|max:3072' ,
                        'jktfile19'=> 'mimes:pdf|max:3072' ,
                        'jktfile20'=> 'mimes:pdf|max:3072' ,
                        'jktfile21'=> 'mimes:pdf|max:3072' ,
                        'jktfile22'=> 'mimes:pdf|max:3072' ,
                        'jktfile23'=> 'mimes:pdf|max:3072' ,
                        'jktfile24'=> 'mimes:pdf|max:3072' ,
                        'jktfile25'=> 'mimes:pdf|max:3072' ,
                        'jktfile26'=> 'mimes:pdf|max:3072' ,
                        'jktfile27'=> 'mimes:pdf|max:3072' ,
                        'jktfile28'=> 'mimes:pdf|max:3072' ,
                        'jktfile29'=> 'mimes:pdf|max:3072' ,
                        'jktfile30'=> 'mimes:pdf|max:3072' ,
                        'jktfile31'=> 'mimes:pdf|max:3072' ,
                        'jktfile32'=> 'mimes:pdf|max:3072' ,
                        'jktfile33'=> 'mimes:pdf|max:3072' ,
                        'jktfile34'=> 'mimes:pdf|max:3072' ,
                        'jktfile35'=> 'mimes:pdf|max:3072' ,
                        'jktfile36'=> 'mimes:pdf|max:3072' ,
                        'jktfile37'=> 'mimes:pdf|max:3072' ,
                        'jktfile38'=> 'mimes:pdf|max:3072' ,
                        'jktfile39'=> 'mimes:pdf|max:3072' ,
                        'jktfile40'=> 'mimes:pdf|max:3072' ,
                        'jktfile41'=> 'mimes:pdf|max:3072' ,
                        'jktfile42'=> 'mimes:pdf|max:3072' ,
                        'jktfile43'=> 'mimes:pdf|max:3072' ,
                        'jktfile44'=> 'mimes:pdf|max:3072' ,
                        'jktfile45'=> 'mimes:pdf|max:3072' ,
                        'jktfile46'=> 'mimes:pdf|max:3072' ,
                        'jktfile47'=> 'mimes:pdf|max:3072' ,
                    'dana1' => 'nullable|string|min:4|max:15' ,
                        'dana2' => 'nullable|string|min:4|max:15' ,
                        'dana3' => 'nullable|string|min:4|max:15' ,
                        'dana4' => 'nullable|string|min:4|max:15' ,
                        'dana5' => 'nullable|string|min:4|max:15' , 
                        'dana6' => 'nullable|string|min:4|max:15' ,
                        'dana7' => 'nullable|string|min:4|max:15' ,
                        'dana8' => 'nullable|string|min:4|max:15' ,
                        'dana9' => 'nullable|string|min:4|max:15' ,
                        'dana10' => 'nullable|string|min:4|max:15' ,
                        'dana11' => 'nullable|string|min:4|max:15' ,
                        'dana12' => 'nullable|string|min:4|max:15' ,
                        'dana13' => 'nullable|string|min:4|max:15' ,
                        'dana14' => 'nullable|string|min:4|max:15' ,
                        'dana15' => 'nullable|string|min:4|max:15' ,
                        'dana16' => 'nullable|string|min:4|max:15' ,
                        'dana17' => 'nullable|string|min:4|max:15' ,
                        'dana18' => 'nullable|string|min:4|max:15' ,
                        'dana19' => 'nullable|string|min:4|max:15' ,
                        'dana20' => 'nullable|string|min:4|max:15' ,
                        'dana21' => 'nullable|string|min:4|max:15' ,
                        'dana22' => 'nullable|string|min:4|max:15' ,
                        'dana23' => 'nullable|string|min:4|max:15' ,
                        'dana24' => 'nullable|string|min:4|max:15' ,
                        'dana25' => 'nullable|string|min:4|max:15' ,
                        'dana26' => 'nullable|string|min:4|max:15' ,
                        'dana27' => 'nullable|string|min:4|max:15' ,
                        'dana28' => 'nullable|string|min:4|max:15' ,
                        'dana29' => 'nullable|string|min:4|max:15' , 
                        'dana30' => 'nullable|string|min:4|max:15' ,
                        'dana31' => 'nullable|string|min:4|max:15' ,
                        'dana32' => 'nullable|string|min:4|max:15' ,
                        'dana33' => 'nullable|string|min:4|max:15' ,
                        'dana34' => 'nullable|string|min:4|max:15' ,
                        'dana35' => 'nullable|string|min:4|max:15' ,
                        'dana36' => 'nullable|string|min:4|max:15' ,
                        'dana37' => 'nullable|string|min:4|max:15' ,
                        'dana38' => 'nullable|string|min:4|max:15' ,
                        'dana39' => 'nullable|string|min:4|max:15' , 
                        'dana40' => 'nullable|string|min:4|max:15' ,
                        'dana41' => 'nullable|string|min:4|max:15' ,
                        'dana42' => 'nullable|string|min:4|max:15' ,
                        'dana43' => 'nullable|string|min:4|max:15' ,
                        'dana44' => 'nullable|string|min:4|max:15' ,
                        'dana45' => 'nullable|string|min:4|max:15' ,
                        'dana46' => 'nullable|string|min:4|max:15' ,
                        'dana47' => 'nullable|string|min:4|max:15' ,
                    'nama_kapal' => 'nullable|string',
                    'Nama_Barge' => 'nullable|string',
                    'no_mohon' => 'nullable|string',
                    'no_PR' => 'nullable|string',
                ]);
            }

            $year = date('Y');
            $month = date('m');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;
          

            if ($request->hasFile('jktfile1')) {
                $file1 = $request->file('jktfile1');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile1')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana1' => $request->dana1,
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'pnbp_rpt' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'dana1' => $request->dana1,
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'pnbp_rpt' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana1' => $request->dana1,
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'pnbp_rpt' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana1' => $request->dana1,
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'pnbp_rpt' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile2')) {
                $file1 = $request->file('jktfile2');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile2')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'pps' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'pps' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'pps' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'pps' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile3')) {
                $file1 = $request->file('jktfile3');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile3')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,  
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'pnbp_spesifikasi_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,  
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'pnbp_spesifikasi_kapal' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana3' => $request->dana3,
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'pnbp_spesifikasi_kapal' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana3' => $request->dana3,
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'pnbp_spesifikasi_kapal' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile4')) {
                $file1 = $request->file('jktfile4');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile4')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,  
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'anti_fauling_permanen' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,  
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'anti_fauling_permanen' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana4' => $request->dana4,
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'anti_fauling_permanen' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana4' => $request->dana4,
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'anti_fauling_permanen' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile5')) {
                $file1 = $request->file('jktfile5');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile5')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,  
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_anti_fauling' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,  
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_anti_fauling' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana5' => $request->dana5,
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_anti_fauling' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana5' => $request->dana5,
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_anti_fauling' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile6')) {
                $file1 = $request->file('jktfile6');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile6')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,  
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,  
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana6' => $request->dana6,
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana6' => $request->dana6,
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile7')) {
                $file1 = $request->file('jktfile7');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile7')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,  
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'pengesahan_gambar' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,  
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'pengesahan_gambar' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana7' => $request->dana7,
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'pengesahan_gambar' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana7' => $request->dana7,
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'pengesahan_gambar' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile8')) {
                $file1 = $request->file('jktfile8');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile8')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,  
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,  
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana8' => $request->dana8,
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana8' => $request->dana8,
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile9')) {
                $file1 = $request->file('jktfile9');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile9')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana9' => $request->dana9,  
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana9' => $request->dana9,  
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana9' => $request->dana9,
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
                        
                        'dana9' => $request->dana9,
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile10')) {
                $file1 = $request->file('jktfile10');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile10')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status10' => 'on review',
                        'dana10' => $request->dana10,
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut_(ubah_pemilik)' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana10' => $request->dana10,  
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut_(ubah_pemilik)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,
    
                        'dana10' => $request->dana10,
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut_(ubah_pemilik)' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana10' => $request->dana10,
                        'status10' => 'on review',
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut_(ubah_pemilik)' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile11')) {
                $file1 = $request->file('jktfile11');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile11')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status11' => 'on review',
                        'dana11' => $request->dana11,
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'clc_bunker' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana11' => $request->dana11,  
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'clc_bunker' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana11' => $request->dana11,
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'clc_bunker' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana11' => $request->dana11,
                        'status11' => 'on review',
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'clc_bunker' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile12')) {
                $file1 = $request->file('jktfile12');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile12')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                    if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                            'status12' => 'on review',
                            'dana12' => $request->dana12,
                            'time_upload12' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_i' => basename($path),]);
                    }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                            'dana12' => $request->dana12,  
                            'status12' => 'on review',
                            'time_upload12' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_i' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentJakarta::create([
                            'upload_type' => 'Fund_Real',
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
            
                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,

                            'dana12' => $request->dana12,
                            'status12' => 'on review',
                            'time_upload12' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_i' => basename($path),]);

                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentJakarta::create([
                            'upload_type' => 'Fund_Req',
                            'no_PR' => $request->no_PR ,
                            'no_mohon' => $request->no_mohon,
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
            
                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,

                            'dana12' => $request->dana12,
                            'status12' => 'on review',
                            'time_upload12' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_i' => basename($path),]);
                        }
            }
            if ($request->hasFile('jktfile13')) {
                $file1 = $request->file('jktfile13');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile13')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                    if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                            'status13' => 'on review',
                            'dana13' => $request->dana13,
                            'time_upload13' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_ii' => basename($path),]);
                    }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                            'dana13' => $request->dana13,  
                            'status13' => 'on review',
                            'time_upload13' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_ii' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentJakarta::create([
                            'upload_type' => 'Fund_Real',
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
            
                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,

                            'dana13' => $request->dana13,
                            'status13' => 'on review',
                            'time_upload13' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_ii' => basename($path),]);

                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentJakarta::create([
                            'upload_type' => 'Fund_Req',
                            'no_PR' => $request->no_PR ,
                            'no_mohon' => $request->no_mohon,
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
            
                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,

                            'dana13' => $request->dana13,
                            'status13' => 'on review',
                            'time_upload13' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_ii' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile14')) {
                $file1 = $request->file('jktfile14');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile14')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                    if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                            'status14' => 'on review',
                            'dana14' => $request->dana14,
                            'time_upload14' => date("Y-m-d h:i:s"),
                            'nota_dinas_perubahan_kawasan' => basename($path),]);
                    }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                            'dana14' => $request->dana14,  
                            'status14' => 'on review',
                            'time_upload14' => date("Y-m-d h:i:s"),
                            'nota_dinas_perubahan_kawasan' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentJakarta::create([
                            'upload_type' => 'Fund_Real',
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
            
                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,

                            'dana14' => $request->dana14,
                            'status14' => 'on review',
                            'time_upload14' => date("Y-m-d h:i:s"),
                            'nota_dinas_perubahan_kawasan' => basename($path),]);

                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentJakarta::create([
                            'upload_type' => 'Fund_Req',
                            'no_PR' => $request->no_PR ,
                            'no_mohon' => $request->no_mohon,
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
            
                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,

                            'dana14' => $request->dana14,
                            'status14' => 'on review',
                            'time_upload14' => date("Y-m-d h:i:s"),
                            'nota_dinas_perubahan_kawasan' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile15')) {
                $file1 = $request->file('jktfile15');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile15')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                    if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status15' => 'on review',
                        'dana15' => $request->dana15,
                        'time_upload15' => date("Y-m-d h:i:s"),
                        'call_sign' => basename($path),]);
                    }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana15' => $request->dana15,  
                        'status15' => 'on review',
                        'time_upload15' => date("Y-m-d h:i:s"),
                        'call_sign' => basename($path),]);
                    }elseif($request->type_upload == 'Fund_Real'){
                        documentJakarta::create([
                            'upload_type' => 'Fund_Real',
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
            
                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,

                            'dana15' => $request->dana15,
                            'status15' => 'on review',
                            'time_upload15' => date("Y-m-d h:i:s"),
                            'call_sign' => basename($path),]);

                    }
                    elseif($request->type_upload == 'Fund_Req'){
                        documentJakarta::create([
                            'upload_type' => 'Fund_Req',
                            'no_PR' => $request->no_PR ,
                            'no_mohon' => $request->no_mohon,
                            'nama_kapal' => $mergenama_kapal,
                            'periode_awal' => $request->tgl_awal,
                            'periode_akhir' => $request->tgl_akhir,
            
                            'cabang' => Auth::user()->cabang ,
                            'user_id' => Auth::user()->id,

                            'dana15' => $request->dana15,
                            'status15' => 'on review',
                            'time_upload15' => date("Y-m-d h:i:s"),
                            'call_sign' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile16')) {
                $file1 = $request->file('jktfile16');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile16')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status16' => 'on review',
                        'dana16' => $request->dana16,
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'perubahan_kepemilikan_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana16' => $request->dana16,  
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'perubahan_kepemilikan_kapal' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana16' => $request->dana16,
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'perubahan_kepemilikan_kapal' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana16' => $request->dana16,
                        'status16' => 'on review',
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'perubahan_kepemilikan_kapal' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile17')) {
                $file1 = $request->file('jktfile17');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile17')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status17' => 'on review',
                        'dana17' => $request->dana17,
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'nota_dinas_bendera_(baru)' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana17' => $request->dana17,  
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'nota_dinas_bendera_(baru)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana17' => $request->dana17,
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'nota_dinas_bendera_(baru)' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana17' => $request->dana17,
                        'status17' => 'on review',
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'nota_dinas_bendera_(baru)' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile18')) {
                $file1 = $request->file('jktfile18');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile18')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status18' => 'on review',
                        'dana18' => $request->dana18,
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'pup_safe_manning' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana18' => $request->dana18,  
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'pup_safe_manning' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana18' => $request->dana18,
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'pup_safe_manning' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana18' => $request->dana18,
                        'status18' => 'on review',
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'pup_safe_manning' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile19')) {
                $file1 = $request->file('jktfile19');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile19')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status19' => 'on review',
                        'dana19' => $request->dana19,
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'corporate' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana19' => $request->dana19,  
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'corporate' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana19' => $request->dana19,
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'corporate' => basename($path),]);

                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana19' => $request->dana19,
                        'status19' => 'on review',
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'corporate' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile20')) {
                $file1 = $request->file('jktfile20');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile20')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status20' => 'on review',
                        'dana20' => $request->dana20,
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'dokumen_kapal_asing_(baru)' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana20' => $request->dana20,  
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'dokumen_kapal_asing_(baru)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana20' => $request->dana20,
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'dokumen_kapal_asing_(baru)' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana20' => $request->dana20,
                        'status20' => 'on review',
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'dokumen_kapal_asing_(baru)' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile21')) {
                $file1 = $request->file('jktfile21');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile21')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status21' => 'on review',
                        'dana21' => $request->dana21,
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'rekomendasi_radio_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana21' => $request->dana21,  
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'rekomendasi_radio_kapal' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana21' => $request->dana21,
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'rekomendasi_radio_kapal' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana21' => $request->dana21,
                        'status21' => 'on review',
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'rekomendasi_radio_kapal' => basename($path),]);
                }
            }
            if ($request->hasFile('jktfile22')) {
                $file1 = $request->file('jktfile22');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile22')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status22' => 'on review',
                        'dana22' => $request->dana22,
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'izin_stasiun_radio_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana22' => $request->dana22,  
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'izin_stasiun_radio_kapal' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana22' => $request->dana22,
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'izin_stasiun_radio_kapal' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana22' => $request->dana22,
                        'status22' => 'on review',
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'izin_stasiun_radio_kapal' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile23')) {
                $file1 = $request->file('jktfile23');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile23')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status23' => 'on review',
                        'dana23' => $request->dana23,
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'mmsi' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana23' => $request->dana23,  
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'mmsi' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana23' => $request->dana23,
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'mmsi' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana23' => $request->dana23,
                        'status23' => 'on review',
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'mmsi' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile24')) {
                $file1 = $request->file('jktfile24');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile24')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status24' => 'on review',
                        'dana24' => $request->dana24,
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_konstruksi' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana24' => $request->dana24,  
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_konstruksi' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana24' => $request->dana24,
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_konstruksi' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana24' => $request->dana24,
                        'status24' => 'on review',
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_konstruksi' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile25')) {
                $file1 = $request->file('jktfile25');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile25')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status25' => 'on review',
                        'dana25' => $request->dana25,
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'ok_1_skb' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana25' => $request->dana25,  
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'ok_1_skb' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana25' => $request->dana25,
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'ok_1_skb' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana25' => $request->dana25,
                        'status25' => 'on review',
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'ok_1_skb' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile26')) {
                $file1 = $request->file('jktfile26');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile26')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status26' => 'on review',
                        'dana26' => $request->dana26,
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'ok_1_skp' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana26' => $request->dana26,  
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'ok_1_skp' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana26' => $request->dana26,
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'ok_1_skp' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana26' => $request->dana26,
                        'status26' => 'on review',
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'ok_1_skp' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile27')) {
                $file1 = $request->file('jktfile27');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile27')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status27' => 'on review',
                        'dana27' => $request->dana27,
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'ok_1_skr' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana27' => $request->dana27,  
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'ok_1_skr' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana27' => $request->dana27,
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'ok_1_skr' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana27' => $request->dana27,
                        'status27' => 'on review',
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'ok_1_skr' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile28')) {
                $file1 = $request->file('jktfile28');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile28')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status28' => 'on review',
                        'dana28' => $request->dana28,
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'status_hukum_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana28' => $request->dana28,  
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'status_hukum_kapal' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana28' => $request->dana28,
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'status_hukum_kapal' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana28' => $request->dana28,
                        'status28' => 'on review',
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'status_hukum_kapal' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile29')) {
                $file1 = $request->file('jktfile29');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile29')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status29' => 'on review',
                        'dana29' => $request->dana29,
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'autorization_garis_muat' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana29' => $request->dana29,  
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'autorization_garis_muat' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana29' => $request->dana29,
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'autorization_garis_muat' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana29' => $request->dana29,
                        'status29' => 'on review',
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'autorization_garis_muat' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile30')) {
                $file1 = $request->file('jktfile30');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile30')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                    'status30' => 'on review',
                    'dana30' => $request->dana30,
                    'time_upload30' => date("Y-m-d h:i:s"),
                    'otorisasi_klas' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                    'dana30' => $request->dana30,  
                    'status30' => 'on review',
                    'time_upload30' => date("Y-m-d h:i:s"),
                    'otorisasi_klas' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana30' => $request->dana30,
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'otorisasi_klas' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana30' => $request->dana30,
                        'status30' => 'on review',
                        'time_upload30' => date("Y-m-d h:i:s"),
                        'otorisasi_klas' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile31')) {
                $file1 = $request->file('jktfile31');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile31')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status31' => 'on review',
                        'dana31' => $request->dana31,
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'pnbp_otorisasi(all)' => basename($path),]);   
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana31' => $request->dana31,  
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'pnbp_otorisasi(all)' => basename($path),]);   
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana31' => $request->dana31,
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'pnbp_otorisasi(all)' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana31' => $request->dana31,
                        'status31' => 'on review',
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'pnbp_otorisasi(all)' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile32')) {
                $file1 = $request->file('jktfile32');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile32')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status32' => 'on review',
                        'dana32' => $request->dana32,
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'halaman_tambah_grosse_akta' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana32' => $request->dana32,  
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'halaman_tambah_grosse_akta' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana32' => $request->dana32,
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'halaman_tambah_grosse_akta' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana32' => $request->dana32,
                        'status32' => 'on review',
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'halaman_tambah_grosse_akta' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile33')) {
                $file1 = $request->file('jktfile33');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile33')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status33' => 'on review',
                        'dana33' => $request->dana33,
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'pnbp_surat_ukur' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana33' => $request->dana33,  
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'pnbp_surat_ukur' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana33' => $request->dana33,
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'pnbp_surat_ukur' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana33' => $request->dana33,
                        'status33' => 'on review',
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'pnbp_surat_ukur' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile34')) {
                $file1 = $request->file('jktfile34');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile34')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status34' => 'on review',
                        'dana34' => $request->dana34,
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'nota_dinas_penundaan_klas_bki_ss' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana34' => $request->dana34,  
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'nota_dinas_penundaan_klas_bki_ss' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana34' => $request->dana34,
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'nota_dinas_penundaan_klas_bki_ss' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana34' => $request->dana34,
                        'status34' => 'on review',
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'nota_dinas_penundaan_klas_bki_ss' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile35')) {
                $file1 = $request->file('jktfile35');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile35')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                            'status35' => 'on review',
                            'dana35' => $request->dana35,
                            'time_upload35' => date("Y-m-d h:i:s"),
                            'uwild_pengganti_doking' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                            'dana35' => $request->dana35,  
                            'status35' => 'on review',
                            'time_upload35' => date("Y-m-d h:i:s"),
                            'uwild_pengganti_doking' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana35' => $request->dana35,
                        'status35' => 'on review',
                        'time_upload35' => date("Y-m-d h:i:s"),
                        'uwild_pengganti_doking' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana35' => $request->dana35,
                        'status35' => 'on review',
                        'time_upload35' => date("Y-m-d h:i:s"),
                        'uwild_pengganti_doking' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile36')) {
                $file1 = $request->file('jktfile36');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile36')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status36' => 'on review',
                        'dana36' => $request->dana36,
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'update_nomor_call_sign' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana36' => $request->dana36,  
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'update_nomor_call_sign' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana36' => $request->dana36,
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'update_nomor_call_sign' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana36' => $request->dana36,
                        'status36' => 'on review',
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'update_nomor_call_sign' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile37')) {
                $file1 = $request->file('jktfile37');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile37')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status37' => 'on review',
                        'dana37' => $request->dana37,
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'clc_badan_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana37' => $request->dana37,  
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'clc_badan_kapal' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana37' => $request->dana37,
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'clc_badan_kapal' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana37' => $request->dana37,
                        'status37' => 'on review',
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'clc_badan_kapal' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile38')) {
                $file1 = $request->file('jktfile38');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile38')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status38' => 'on review',
                        'dana38' => $request->dana38,
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'wreck_removal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana38' => $request->dana38,  
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'wreck_removal' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana38' => $request->dana38,
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'wreck_removal' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana38' => $request->dana38,
                        'status38' => 'on review',
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'wreck_removal' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile39')) {
                $file1 = $request->file('jktfile39');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile39')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status39' => 'on review',
                        'dana39' => $request->dana39,
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'biaya_percepatan_proses' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana39' => $request->dana39,  
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'biaya_percepatan_proses' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana39' => $request->dana39,
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'biaya_percepatan_proses' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana39' => $request->dana39,
                        'status39' => 'on review',
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'biaya_percepatan_proses' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile40')) {
                $file1 = $request->file('jktfile40');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile40')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status40' => 'on review',
                        'dana40' => $request->dana40,
                        'time_upload40' => date("Y-m-d h:i:s"),
                        'BKI_Lambung' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana40' => $request->dana40,  
                        'status40' => 'on review',
                        'time_upload40' => date("Y-m-d h:i:s"),
                        'BKI_Lambung' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana40' => $request->dana40,
                        'status40' => 'on review',
                        'time_upload40' => date("Y-m-d h:i:s"),
                        'BKI_Lambung' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana40' => $request->dana40,
                        'status40' => 'on review',
                        'time_upload40' => date("Y-m-d h:i:s"),
                        'BKI_Lambung' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile41')) {
                $file1 = $request->file('jktfile41');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile41')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status41' => 'on review',
                        'dana41' => $request->dana41,
                        'time_upload41' => date("Y-m-d h:i:s"),
                        'BKI_Mesin' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana41' => $request->dana41,  
                        'status41' => 'on review',
                        'time_upload41' => date("Y-m-d h:i:s"),
                        'BKI_Mesin' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana41' => $request->dana41,
                        'status41' => 'on review',
                        'time_upload41' => date("Y-m-d h:i:s"),
                        'BKI_Mesin' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana41' => $request->dana41,
                        'status41' => 'on review',
                        'time_upload41' => date("Y-m-d h:i:s"),
                        'BKI_Mesin' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile42')) {
                $file1 = $request->file('jktfile42');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile42')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status42' => 'on review',
                        'dana42' => $request->dana42,
                        'time_upload42' => date("Y-m-d h:i:s"),
                        'BKI_Garis_Muat' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana42' => $request->dana42,  
                        'status42' => 'on review',
                        'time_upload42' => date("Y-m-d h:i:s"),
                        'BKI_Garis_Muat' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana42' => $request->dana42,
                        'status42' => 'on review',
                        'time_upload42' => date("Y-m-d h:i:s"),
                        'BKI_Garis_Muat' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana42' => $request->dana42,
                        'status42' => 'on review',
                        'time_upload42' => date("Y-m-d h:i:s"),
                        'BKI_Garis_Muat' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile43')) {
                $file1 = $request->file('jktfile43');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile43')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status43' => 'on review',
                        'dana43' => $request->dana43,
                        'time_upload43' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana43' => $request->dana43,  
                        'status43' => 'on review',
                        'time_upload43' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana43' => $request->dana43,
                        'status43' => 'on review',
                        'time_upload43' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana43' => $request->dana43,
                        'status43' => 'on review',
                        'time_upload43' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile44')) {
                $file1 = $request->file('jktfile44');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile44')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status44' => 'on review',
                        'dana44' => $request->dana44,
                        'time_upload44' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana44' => $request->dana44,  
                        'status44' => 'on review',
                        'time_upload44' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana44' => $request->dana44,
                        'status44' => 'on review',
                        'time_upload44' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana44' => $request->dana44,
                        'status44' => 'on review',
                        'time_upload44' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile45')) {
                $file1 = $request->file('jktfile45');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile45')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status45' => 'on review',
                        'dana45' => $request->dana45,
                        'time_upload45' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana45' => $request->dana45,  
                        'status45' => 'on review',
                        'time_upload45' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana45' => $request->dana45,
                        'status45' => 'on review',
                        'time_upload45' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana45' => $request->dana45,
                        'status45' => 'on review',
                        'time_upload45' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile46')) {
                $file1 = $request->file('jktfile46');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile46')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status46' => 'on review',
                        'dana46' => $request->dana46,
                        'time_upload46' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana46' => $request->dana46,  
                        'status46' => 'on review',
                        'time_upload46' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana46' => $request->dana46,
                        'status46' => 'on review',
                        'time_upload46' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana46' => $request->dana46,
                        'status46' => 'on review',
                        'time_upload46' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                    }
            }
            if ($request->hasFile('jktfile47')) {
                $file1 = $request->file('jktfile47');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile47')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status47' => 'on review',
                        'dana47' => $request->dana47,
                        'time_upload47' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('no_mohon', 'Like' , $request->no_mohon )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'dana47' => $request->dana47,  
                        'status47' => 'on review',
                        'time_upload47' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }elseif($request->type_upload == 'Fund_Real'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Real',
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana47' => $request->dana47,
                        'status47' => 'on review',
                        'time_upload47' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }
                elseif($request->type_upload == 'Fund_Req'){
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR ,
                        'no_mohon' => $request->no_mohon,
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
            
                        'cabang' => Auth::user()->cabang ,
                        'user_id' => Auth::user()->id,

                        'dana47' => $request->dana47,
                        'status47' => 'on review',
                        'time_upload47' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                    }
            }
            return redirect()->back()->with('success', 'Upload Success! Silahkan di cek ke DASHBOARD !');
        }
        
//email to user
    // $details = [
    //         'title' => 'Thank you for receiving this email', 
    //         'body' => 'you are a test subject for the project hehe'
    //     ];
        
    //     Mail::to('stanlytong@gmail.com')->send(new Gmail($details));

        return view('picsite.upload');
    }
    
}