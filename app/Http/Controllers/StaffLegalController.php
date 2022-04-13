<?php

namespace App\Http\Controllers;

use Storage;
use Carbon\Carbon;
use App\Models\Tug;
use App\Models\User;
use App\Models\Barge;
use App\Models\documents;
use App\Models\documentrpk;
use Illuminate\Http\Request;
use App\Models\documentberau;
use App\Models\documentJakarta;
use App\Models\documentsamarinda;
use Illuminate\Support\Facades\DB;
use App\Models\documentbanjarmasin;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Export_RekapDocument;

class StaffLegalController extends Controller
{
    // upload fund & RPK page , Realisasi Dana page
    public function staffrpk_page(){
        $tug=Tug::latest()->get();
        $barge=Barge::latest()->get();
        return view('StaffLegal.StaffLegalRpk' , compact('tug','barge'));
    }
    public function stafffund_page(){
        $tug=Tug::latest()->get();
        $barge=Barge::latest()->get();
        return view('StaffLegal.StaffLegalUpload' , compact('tug','barge'));
    }
    public function staffReal_page(){
        $tug=Tug::latest()->get();
        $barge=Barge::latest()->get();
        return view('StaffLegal.StaffLegalRealisasi' , compact('tug','barge'));
    }

    // dashboard pages
    public function Dashboard_fund_Real_page(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All') {
            $document = documents::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentberau = documentberau::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Real')->latest()->paginate(10);
            $documentjakarta = documentJakarta::where('upload_type','Fund_Real')->latest()->paginate(10);
            return view('StaffLegal.StaffLegalDashboardReal', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
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
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            return view('StaffLegal.StaffLegalDashboardReal', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
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
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             $documentsamarinda = documentsamarinda::where('cabang', $request->search)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             $documentjakarta = documentJakarta::where('cabang', $request->search)
             ->where('upload_type','Fund_Real')
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
 
             return view('StaffLegal.StaffLegalDashboardReal', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
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
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Real')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();

            return view('StaffLegal.StaffLegalDashboardReal', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }else{
            $document = documents::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            $documentberau = documentberau::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            // dd($documentbanjarmasin);
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Real')->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalDashboardReal', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }    
    }
    public function Dashboard_fund_Real_view(Request $request){
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
    public function Dashboard_staffrpk_page(Request $request){
        $datetime = date('Y-m-d');
        $year = date('Y');
        $month = date('m');
        $searchresult = $request->search;

        if ($searchresult == 'All') {
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalDashboardRpk' , compact('docrpk'));
        }elseif ($request->filled('search')) {
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalDashboardRpk' , compact('docrpk'));
        }elseif($request->filled('search_kapal')){
             //get DocRPK Data as long as the periode_akhir(column database)
             $docrpk = DB::table('rpkdocuments')
             ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
             ->whereDate('periode_akhir', '>=', $datetime)
             ->orderBy('id', 'DESC')
             ->latest()->paginate(10)->withQueryString();
             return view('StaffLegal.StaffLegalDashboardRpk' , compact('docrpk'));
        }elseif ($request->filled('search_kapal') && $request->filled('search')) {
            //get DocRPK Data as long as the periode_akhir(column database) with cabang
            $docrpk = DB::table('rpkdocuments')
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->whereDate('periode_akhir', '>=', $datetime)
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalDashboardRpk' , compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalDashboardRpk' , compact('docrpk'));
        }
    }
    public function Dashboard_staffrpk_view(Request $request){
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
            if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati' or $request->cabang == 'Batu Licin'){
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
            return view('StaffLegal.StaffLegalDoc_Review' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' ,'documentjakarta' , 'searchresult'));
        }
        elseif ($request->filled('search')) {
            $document = DB::table('documents')->where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            $documentberau = DB::table('beraudb')->where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            $documentbanjarmasin = DB::table('banjarmasindb')->where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            $documentsamarinda = DB::table('samarindadb')->where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->where('cabang', $request->search)->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalDoc_Review' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' ,'documentjakarta' , 'searchresult'));
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
            return view('StaffLegal.StaffLegalDoc_Review' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta' , 'searchresult'));
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
            return view('StaffLegal.StaffLegalDoc_Review' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta' , 'searchresult'));
        }else{
            $document = documents::where('upload_type','Fund_Req')->latest()->get();
            $documentberau = documentberau::where('upload_type','Fund_Req')->latest()->get();
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Req')->latest()->get();
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Req')->latest()->get();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->latest()->get();
            
            return view('StaffLegal.StaffLegalDoc_Review' , compact('document', 'documentberau' , 'documentbanjarmasin', 'documentsamarinda' , 'documentjakarta' , 'searchresult')); 
        }
    }
    
    //review RPK page for picAdmin
    public function checkrpk(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All'){
            $docrpk = DB::table('rpkdocuments')
            ->latest()->get();
        }elseif($request->filled('search')){
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)
            ->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRPK_Review', compact('docrpk','searchresult'));
        }elseif($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            //get DocRPK Data as long as the periode_akhir and search based (column database)
            $docrpk = DB::table('rpkdocuments')
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRPK_Review', compact('docrpk','searchresult'));
        }elseif($request->filled('search_kapal') && $request->filled('search')){
            $docrpk = DB::table('rpkdocuments')
            ->where('cabang',$request->search)
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRPK_Review', compact('docrpk','searchresult'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->latest()->get();
        }
        return view('StaffLegal.StaffLegalRPK_Review', compact('docrpk','searchresult'));
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
        return redirect()->back();
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
        return redirect()->back();
    }
    //approval for RPK review picAdmin page
    public function approverpk(Request $request){
        $datetime = date('Y-m-d');
        // dd($request);
        //check if cabang is banjarmasin
        if ($request->cabang == 'Banjarmasin' or $request->cabang == 'Bunati') {
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
        return redirect()->back();
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

        return redirect()->back();
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
        return view('StaffLegal.StaffLegalRecordDoc', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
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
        return view('StaffLegal.StaffLegalRecordDoc', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }
    public function RecordDocumentsRPK(Request $request){
        $datetime = date('Y-m-d');
        //get DocRPK Data as long as the periode_akhir(column database)
        $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
        return view('StaffLegal.StaffLegalRecordDocRPK', compact('docrpk'));
    }
    public function RecordDocumentsRPK_search(Request $request){
        $datetime = date('Y-m-d');
        $searchresult = $request->search;
        if ($searchresult == 'All'){
            $docrpk = DB::table('rpkdocuments')
            ->whereDate('periode_akhir', '<', $datetime)
            ->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRecordDocRPK', compact('docrpk'));
        }elseif($request->filled('search')){
            $docrpk = DB::table('rpkdocuments')->where('cabang', $request->search)
            ->whereDate('periode_akhir', '<', $datetime)
            ->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRecordDocRPK', compact('docrpk'));
        }elseif($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            //get DocRPK Data as long as the periode_akhir and search based (column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRecordDocRPK', compact('docrpk'));
        }elseif($request->filled('search_kapal') && $request->filled('search')){
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)
            ->where('cabang',$request->search)
            ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->orderBy('id', 'DESC')
            ->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRecordDocRPK', compact('docrpk'));
        }else{
            //get DocRPK Data as long as the periode_akhir(column database)
            $docrpk = DB::table('rpkdocuments')->whereDate('periode_akhir', '<', $datetime)->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRecordDocRPK', compact('docrpk'));
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

    // Rekapitulasi document page
    public function Rekap_staff_page(Request $request){
        $year = date('Y');
        $searchresult = $request->search;
        $document = documents::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
        $documentberau = documentberau::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
        $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
        $documentsamarinda = documentsamarinda::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
        $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
        return view('StaffLegal.StaffLegalRekapitulasi', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }
    public function Rekap_staff_search(Request $request) {
        $searchresult = $request->search;
        $year = date('Y');
        if ($searchresult == 'All') {
            $document = documents::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            $documentberau = documentberau::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRekapitulasi', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif($request->filled('search_kapal')) {
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            return view('StaffLegal.StaffLegalRekapitulasi', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif ($request->filled('search')){
             $document = documents::where('cabang', $request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
 
             $documentberau = documentberau::where('cabang', $request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
 
             $documentbanjarmasin = documentbanjarmasin::where('cabang', $request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
 
             $documentsamarinda = documentsamarinda::where('cabang', $request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
 
             $documentjakarta = documentJakarta::where('cabang', $request->search)
             ->where('upload_type','Fund_Req')
             ->orderBy('id', 'DESC')
             ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
 
             return view('StaffLegal.StaffLegalRekapitulasi', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }elseif ($request->filled('search_kapal') && $request->filled('search')){
            $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
            ->where('cabang', $request->search)
            ->where('upload_type','Fund_Req')
            ->orderBy('id', 'DESC')
            ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();

            return view('StaffLegal.StaffLegalRekapitulasi', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }else{
            $document = documents::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            $documentberau = documentberau::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            $documentbanjarmasin = documentbanjarmasin::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            // dd($documentbanjarmasin);
            $documentsamarinda = documentsamarinda::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            $documentjakarta = documentJakarta::where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->paginate(10)->withQueryString();
            return view('StaffLegal.StaffLegalRekapitulasi', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
        }    
        
        return view('StaffLegal.StaffLegalRekapitulasi', compact('searchresult','document','documentberau','documentbanjarmasin','documentsamarinda','documentjakarta'));
    }

    private $excel;
    public function __construct(Excel $excel){
        $this->excel = $excel;
    }

    //export Rekap Excel page
    public function exportEXCEL(Request $request) {
        $searchresult = $request->search;
        $date = Carbon::now();
        $monthName = $date->format('F');
        $cabang_download = $request->select_download;
        // dd($cabang_download);
        return Excel::download(new Export_RekapDocument($cabang_download), 'Rekapitulasi-Document ' . $cabang_download . '-' . $monthName . '.xlsx');
    }

    // upload RPK file
    public function staff_upload_RPK(Request $request){
        if(Auth::user()->cabang == 'Jakarta' && Auth::user()->hasRole('StaffLegal')){
            $request->validate([
                'Staff_file1' => 'mimes:pdf|max:1024' ,
                'Staff_file2' => 'mimes:pdf|max:1024' ,
                'Staff_file3' => 'mimes:pdf|max:1024' ,
                'nama_kapal' => 'nullable|string',
                'Nama_Barge' => 'nullable|string',
            ]);

            // dd($request);
            $year = date('Y');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;
            $month = date('m');
            if ($request->hasFile('Staff_file1')) {
                //dd($request);
                $file = $request->file('Staff_file1');
                $name1 = 'staff_Legal-' . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/RPK';
                $path = $request->file('Staff_file1')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status8' => 'on review',
                        'RPK' => basename($path),
                        'time_upload8' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status8' => 'on review',
                        'RPK' => basename($path),
                        'time_upload8' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status8' => 'on review',
                        'RPK' => basename($path),
                        'time_upload8' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('Staff_file2')){
                $file = $request->file('Staff_file2');
                $name1 = 'staff_Legal-' . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/Penambahan_pelabuhan_singgah';
                $path = $request->file('Staff_file2')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                       
                        'status9' => 'on review',
                        'Penambahan_pelabuhan_singgah'=> basename($path) ,
                        'time_upload9' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status9' => 'on review',
                        'Penambahan_pelabuhan_singgah'=> basename($path) ,
                        'time_upload9' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status9' => 'on review',
                        'Penambahan_pelabuhan_singgah'=> basename($path) ,
                        'time_upload9' => date("Y-m-d h:i:s"),
                    ]);
                }
            }      
            if ($request->hasFile('Staff_file3')){
                $file = $request->file('Staff_file3');
                $name1 = 'staff_Legal-' . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/Penambahan_urgensi_muatan';
                $path = $request->file('Staff_file3')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status10' => 'on review',
                        'Penambahan_urgensi_muatan' => basename($path),
                        'time_upload10' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', $mergenama_kapal)->where('cabang' , 'Jakarta')->where('periode_awal', $request->tgl_awal)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status10' => 'on review',
                        'Penambahan_urgensi_muatan' => basename($path),
                        'time_upload10' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status10' => 'on review',
                        'Penambahan_urgensi_muatan' => basename($path),
                        'time_upload10' => date("Y-m-d h:i:s"),
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Upload Success! Silahkan di cek  ke bagian "DASHBOARD !');
        }
    }

    // upload fund Request & Realisasi file
    public function staff_upload_fund(Request $request){
        if (Auth::user()->cabang == "Jakarta") {
            //jakarta
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
            ]);
    
            $year = date('Y');
            $month = date('m');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;
          
            if ($request->hasFile('jktfile1')) {
                $file1 = $request->file('jktfile1');
                $name1 = Auth::user()->cabang . '-'. $file1->getClientOriginalName();
                $path = $request->file('jktfile1')->storeas('jakarta/'. $year . "/". $month , $name1, 's3');
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana1' => $request->dana1,
                        'status1' => 'on review',
                        'time_upload1' => date("Y-m-d h:i:s"),
                        'pnbp_rpt' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana2' => $request->dana2,
                        'status2' => 'on review',
                        'time_upload2' => date("Y-m-d h:i:s"),
                        'pps' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana3' => $request->dana3,  
                        'status3' => 'on review',
                        'time_upload3' => date("Y-m-d h:i:s"),
                        'pnbp_spesifikasi_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana4' => $request->dana4,  
                        'status4' => 'on review',
                        'time_upload4' => date("Y-m-d h:i:s"),
                        'anti_fauling_permanen' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana5' => $request->dana5,  
                        'status5' => 'on review',
                        'time_upload5' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_anti_fauling' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana6' => $request->dana6,  
                        'status6' => 'on review',
                        'time_upload6' => date("Y-m-d h:i:s"),
                        'snpp_permanen' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana7' => $request->dana7,  
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'pengesahan_gambar' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana8' => $request->dana8,  
                        'status8' => 'on review',
                        'time_upload8' => date("Y-m-d h:i:s"),
                        'surat_laut_permanen' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'dana9' => $request->dana9,  
                        'status9' => 'on review',
                        'time_upload9' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status10' => 'on review',
                        'dana10' => $request->dana10,
                        'time_upload10' => date("Y-m-d h:i:s"),
                        'pnbp_surat_laut_(ubah_pemilik)' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status11' => 'on review',
                        'dana11' => $request->dana11,
                        'time_upload11' => date("Y-m-d h:i:s"),
                        'clc_bunker' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                    if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                            'status12' => 'on review',
                            'dana12' => $request->dana12,
                            'time_upload12' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_i' => basename($path),]);
                    }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                    else{
                        documentJakarta::create([
                            'upload_type' => 'Fund_Req',
                            'no_PR' => $request->no_PR,
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
                    if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                            'status13' => 'on review',
                            'dana13' => $request->dana13,
                            'time_upload13' => date("Y-m-d h:i:s"),
                            'nota_dinas_penundaan_dok_ii' => basename($path),]);
                    }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                    else{
                        documentJakarta::create([
                            'upload_type' => 'Fund_Req',
                            'no_PR' => $request->no_PR,
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
                    if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                            'status14' => 'on review',
                            'dana14' => $request->dana14,
                            'time_upload14' => date("Y-m-d h:i:s"),
                            'nota_dinas_perubahan_kawasan' => basename($path),]);
                    }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                        documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                    else{
                        documentJakarta::create([
                            'upload_type' => 'Fund_Req',
                            'no_PR' => $request->no_PR,
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
                    if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status15' => 'on review',
                        'dana15' => $request->dana15,
                        'time_upload15' => date("Y-m-d h:i:s"),
                        'call_sign' => basename($path),]);
                    }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                    else{
                        documentJakarta::create([
                            'upload_type' => 'Fund_Req',
                            'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status16' => 'on review',
                        'dana16' => $request->dana16,
                        'time_upload16' => date("Y-m-d h:i:s"),
                        'perubahan_kepemilikan_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status17' => 'on review',
                        'dana17' => $request->dana17,
                        'time_upload17' => date("Y-m-d h:i:s"),
                        'nota_dinas_bendera_(baru)' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status18' => 'on review',
                        'dana18' => $request->dana18,
                        'time_upload18' => date("Y-m-d h:i:s"),
                        'pup_safe_manning' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status19' => 'on review',
                        'dana19' => $request->dana19,
                        'time_upload19' => date("Y-m-d h:i:s"),
                        'corporate' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status20' => 'on review',
                        'dana20' => $request->dana20,
                        'time_upload20' => date("Y-m-d h:i:s"),
                        'dokumen_kapal_asing_(baru)' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                }else{
                documentJakarta::create([
                    'upload_type' => 'Fund_Req',
                    'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status21' => 'on review',
                        'dana21' => $request->dana21,
                        'time_upload21' => date("Y-m-d h:i:s"),
                        'rekomendasi_radio_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status22' => 'on review',
                        'dana22' => $request->dana22,
                        'time_upload22' => date("Y-m-d h:i:s"),
                        'izin_stasiun_radio_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status23' => 'on review',
                        'dana23' => $request->dana23,
                        'time_upload23' => date("Y-m-d h:i:s"),
                        'mmsi' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status24' => 'on review',
                        'dana24' => $request->dana24,
                        'time_upload24' => date("Y-m-d h:i:s"),
                        'pnbp_pemeriksaan_konstruksi' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status25' => 'on review',
                        'dana25' => $request->dana25,
                        'time_upload25' => date("Y-m-d h:i:s"),
                        'ok_1_skb' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status26' => 'on review',
                        'dana26' => $request->dana26,
                        'time_upload26' => date("Y-m-d h:i:s"),
                        'ok_1_skp' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status27' => 'on review',
                        'dana27' => $request->dana27,
                        'time_upload27' => date("Y-m-d h:i:s"),
                        'ok_1_skr' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status28' => 'on review',
                        'dana28' => $request->dana28,
                        'time_upload28' => date("Y-m-d h:i:s"),
                        'status_hukum_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status29' => 'on review',
                        'dana29' => $request->dana29,
                        'time_upload29' => date("Y-m-d h:i:s"),
                        'autorization_garis_muat' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                    'status30' => 'on review',
                    'dana30' => $request->dana30,
                    'time_upload30' => date("Y-m-d h:i:s"),
                    'otorisasi_klas' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status31' => 'on review',
                        'dana31' => $request->dana31,
                        'time_upload31' => date("Y-m-d h:i:s"),
                        'pnbp_otorisasi(all)' => basename($path),]);   
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status32' => 'on review',
                        'dana32' => $request->dana32,
                        'time_upload32' => date("Y-m-d h:i:s"),
                        'halaman_tambah_grosse_akta' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status33' => 'on review',
                        'dana33' => $request->dana33,
                        'time_upload33' => date("Y-m-d h:i:s"),
                        'pnbp_surat_ukur' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status34' => 'on review',
                        'dana34' => $request->dana34,
                        'time_upload34' => date("Y-m-d h:i:s"),
                        'nota_dinas_penundaan_klas_bki_ss' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                            'status35' => 'on review',
                            'dana35' => $request->dana35,
                            'time_upload35' => date("Y-m-d h:i:s"),
                            'uwild_pengganti_doking' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status36' => 'on review',
                        'dana36' => $request->dana36,
                        'time_upload36' => date("Y-m-d h:i:s"),
                        'update_nomor_call_sign' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status37' => 'on review',
                        'dana37' => $request->dana37,
                        'time_upload37' => date("Y-m-d h:i:s"),
                        'clc_badan_kapal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status38' => 'on review',
                        'dana38' => $request->dana38,
                        'time_upload38' => date("Y-m-d h:i:s"),
                        'wreck_removal' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status39' => 'on review',
                        'dana39' => $request->dana39,
                        'time_upload39' => date("Y-m-d h:i:s"),
                        'biaya_percepatan_proses' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status40' => 'on review',
                        'dana40' => $request->dana40,
                        'time_upload40' => date("Y-m-d h:i:s"),
                        'BKI_Lambung' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status41' => 'on review',
                        'dana41' => $request->dana41,
                        'time_upload41' => date("Y-m-d h:i:s"),
                        'BKI_Mesin' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status42' => 'on review',
                        'dana42' => $request->dana42,
                        'time_upload42' => date("Y-m-d h:i:s"),
                        'BKI_Garis_Muat' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status43' => 'on review',
                        'dana43' => $request->dana43,
                        'time_upload43' => date("Y-m-d h:i:s"),
                        'Lain_Lain1' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status44' => 'on review',
                        'dana44' => $request->dana44,
                        'time_upload44' => date("Y-m-d h:i:s"),
                        'Lain_Lain2' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
        
       
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status45' => 'on review',
                        'dana45' => $request->dana45,
                        'time_upload45' => date("Y-m-d h:i:s"),
                        'Lain_Lain3' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status46' => 'on review',
                        'dana46' => $request->dana46,
                        'time_upload46' => date("Y-m-d h:i:s"),
                        'Lain_Lain4' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
                if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([  
                        'status47' => 'on review',
                        'dana47' => $request->dana47,
                        'time_upload47' => date("Y-m-d h:i:s"),
                        'Lain_Lain5' => basename($path),]);
                }else if(documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                documentJakarta::where('upload_type','Fund_Req')->where('nama_kapal', 'Like',  $mergenama_kapal )->where('periode_awal', $request->tgl_awal)->where('periode_akhir', $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
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
                else{
                    documentJakarta::create([
                        'upload_type' => 'Fund_Req',
                        'no_PR' => $request->no_PR,
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
    }
}
