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
use App\Models\documentrpk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Google\Cloud\Storage\StorageClient;

class PicRpkController extends Controller
{
    public function rpk(){
        // $date = date('m');
        // $docrpk = documentrpk::with('user')->where('cabang',Auth::user()->cabang)->whereMonth('created_at', date('m'))->latest()->get();
        // return view('picsite.rpk' , compact('docrpk'));
        $tug=Tug::latest()->get();
        $barge=Barge::latest()->get();
        return view('picsite.rpk' , compact('tug','barge'));
    }

    public function uploadrpk(Request $request){

        if(Auth::user()->cabang == 'Babelan'){ 
            $request->validate([
                'rfile1' => 'mimes:pdf|max:1024' ,
                'rfile2' => 'mimes:pdf|max:1024' ,
                'rfile3' => 'mimes:pdf|max:1024' ,
                'rfile4' => 'mimes:pdf|max:1024' ,
                'rfile5' => 'mimes:pdf|max:1024' ,
                'rfile6' => 'mimes:pdf|max:1024' ,
                'rfile7' => 'mimes:pdf|max:1024' ,
                'nama_kapal' => 'nullable|string',
                'Nama_Barge' => 'nullable|string',
            ]);

            $year = date('Y');
            $month = date('m');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;

            if ($request->hasFile('rfile1')) {
                //dd($request);
                $file = $request->file('rfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('rfile1')->storeas('babelan/'. $year . "/". $month . '/RPK', $name1, 's3');
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('rfile2')){
                $file = $request->file('rfile2');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/cargo_manifest';
                $path = $request->file('rfile2')->storeas('babelan/'. $year . "/". $month . '/RPK', $name1, 's3');
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([      
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }
            }      
            if ($request->hasFile('rfile3')){
                $file = $request->file('rfile3');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/voyage';
                $path = $request->file('rfile3')->storeas('babelan/'. $year . "/". $month . '/RPK', $name1, 's3');
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('rfile4')){
                $file = $request->file('rfile4');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/bill_lading';
                $path = $request->file('rfile4')->storeas('babelan/'. $year . "/". $month . '/RPK', $name1, 's3');
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('rfile5')){
                $file = $request->file('rfile5');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/gerak_kapal';
                $path = $request->file('rfile5')->storeas('babelan/'. $year . "/". $month . '/RPK', $name1, 's3');
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([                   
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('rfile6')){
                $file = $request->file('rfile6');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/docking';
                $path = $request->file('rfile6')->storeas('babelan/'. $year . "/". $month . '/RPK', $name1, 's3');
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                  
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([      
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('rfile7')){
                $file = $request->file('rfile7');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_kapal';
                $path = $request->file('rfile7')->storeas('babelan/'. $year . "/". $month . '/RPK', $name1, 's3');
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([              
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);  
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                         'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }
            }
            return redirect('picsite/rpk')->with('success', 'Upload Success! Silahkan di cek  ke bagian "DASHBOARD !');
        }

        
        if(Auth::user()->cabang == 'Berau'){
            $request->validate([
                'brfile1' => 'mimes:pdf|max:1024' ,
                'brfile2' => 'mimes:pdf|max:1024' ,
                'brfile3' => 'mimes:pdf|max:1024' ,
                'brfile4' => 'mimes:pdf|max:1024' ,
                'brfile5' => 'mimes:pdf|max:1024' ,
                'brfile6' => 'mimes:pdf|max:1024' ,
                'brfile7' => 'mimes:pdf|max:1024' ,
                'nama_kapal' => 'nullable|string',
                'Nama_Barge' => 'nullable|string',
            ]);
            
            $year = date('Y');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;
            $month = date('m');
            if ($request->hasFile('brfile1')) {
                //dd($request);
                $file = $request->file('brfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('brfile1')->storeas('berau/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('brfile2')){
                $file = $request->file('brfile2');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/cargo_manifest';
                $path = $request->file('brfile2')->storeas('berau/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }
            }      
            if ($request->hasFile('brfile3')){
                $file = $request->file('brfile3');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/voyage';
                $path = $request->file('brfile3')->storeas('berau/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('brfile4')){
                $file = $request->file('brfile4');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/bill_lading';
                $path = $request->file('brfile4')->storeas('berau/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('brfile5')){
                $file = $request->file('brfile5');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/gerak_kapal';
                $path = $request->file('brfile5')->storeas('berau/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('brfile6')){
                $file = $request->file('brfile6');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/docking';
                $path = $request->file('brfile6')->storeas('berau/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('brfile7')){
                $file = $request->file('brfile7');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_kapal';
                $path = $request->file('brfile7')->storeas('berau/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }  
            }
            return redirect('picsite/rpk')->with('success', 'Upload Success! Silahkan di cek  ke bagian "DASHBOARD !');
        }

        
        if(Auth::user()->cabang == 'Banjarmasin' or Auth::user()->cabang == 'Bunati' or Auth::user()->cabang == 'Batu Licin'){
            $request->validate([
                'bjrfile1' => 'mimes:pdf|max:1024' ,
                'bjrfile2' => 'mimes:pdf|max:1024' ,
                'bjrfile3' => 'mimes:pdf|max:1024' ,
                'bjrfile4' => 'mimes:pdf|max:1024' ,
                'bjrfile5' => 'mimes:pdf|max:1024' ,
                'bjrfile6' => 'mimes:pdf|max:1024' ,
                'bjrfile7' => 'mimes:pdf|max:1024' ,
                'nama_kapal' => 'nullable|string',
                'Nama_Barge' => 'nullable|string',
            ]);
            $year = date('Y');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;
            $month = date('m');
            if ($request->hasFile('bjrfile1')) {
                //dd($request);
                $file = $request->file('bjrfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('bjrfile1')->storeas('banjarmasin/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('bjrfile2')){
                $file = $request->file('bjrfile2');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/cargo_manifest';
                $path = $request->file('bjrfile2')->storeas('banjarmasin/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }
            }      
            if ($request->hasFile('bjrfile3')){
                $file = $request->file('bjrfile3');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/voyage';
                $path = $request->file('bjrfile3')->storeas('banjarmasin/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('bjrfile4')){
                $file = $request->file('bjrfile4');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/bill_lading';
                $path = $request->file('bjrfile4')->storeas('banjarmasin/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }

            }
            if ($request->hasFile('bjrfile5')){
                $file = $request->file('bjrfile5');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/gerak_kapal';
                $path = $request->file('bjrfile5')->storeas('banjarmasin/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([       
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('bjrfile6')){
                $file = $request->file('bjrfile6');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/docking';
                $path = $request->file('bjrfile6')->storeas('banjarmasin/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([         
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('bjrfile7')){
                $file = $request->file('bjrfile7');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_kapal';
                $path = $request->file('bjrfile7')->storeas('banjarmasin/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }else {
                    documentrpk::create([       
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }
            }
            return redirect('picsite/rpk')->with('success', 'Upload Success! Silahkan di cek  ke bagian "DASHBOARD !');
        }

        
        if(Auth::user()->cabang == 'Samarinda' or Auth::user()->cabang == 'Kendari'  or Auth::user()->cabang == 'Morosi'){
            $request->validate([
                'smrfile1' => 'mimes:pdf|max:1024' ,
                'smrfile2' => 'mimes:pdf|max:1024' ,
                'smrfile3' => 'mimes:pdf|max:1024' ,
                'smrfile4' => 'mimes:pdf|max:1024' ,
                'smrfile5' => 'mimes:pdf|max:1024' ,
                'smrfile6' => 'mimes:pdf|max:1024' ,
                'smrfile7' => 'mimes:pdf|max:1024' ,
                'nama_kapal' => 'nullable|string',
                'Nama_Barge' => 'nullable|string',
            ]);
            $year = date('Y');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;
            $month = date('m');
            if ($request->hasFile('smrfile1')) {
                //dd($request);
                $file = $request->file('smrfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('smrfile1')->storeas('samarinda/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('smrfile2')){
                $file = $request->file('smrfile2');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/cargo_manifest';
                $path = $request->file('smrfile2')->storeas('samarinda/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                       
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }
            }      
            if ($request->hasFile('smrfile3')){
                $file = $request->file('smrfile3');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/voyage';
                $path = $request->file('smrfile3')->storeas('samarinda/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('smrfile4')){
                $file = $request->file('smrfile4');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/bill_lading';
                $path = $request->file('smrfile4')->storeas('samarinda/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('smrfile5')){
                $file = $request->file('smrfile5');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/gerak_kapal';
                $path = $request->file('smrfile5')->storeas('samarinda/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('smrfile6')){
                $file = $request->file('smrfile6');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/docking';
                $path = $request->file('smrfile6')->storeas('samarinda/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([     
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('smrfile7')){
                $file = $request->file('smrfile7');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_kapal';
                $path = $request->file('smrfile7')->storeas('samarinda/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                 
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }else {
                    documentrpk::create([     
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }
            }
            return redirect('picsite/rpk')->with('success', 'Upload Success! Silahkan di cek  ke bagian "DASHBOARD !');
        }
        
        if(Auth::user()->cabang == 'Jakarta'){
            $request->validate([
                'jktfile1' => 'mimes:pdf|max:1024' ,
                'jktfile2' => 'mimes:pdf|max:1024' ,
                'jktfile3' => 'mimes:pdf|max:1024' ,
                'jktfile4' => 'mimes:pdf|max:1024' ,
                'jktfile5' => 'mimes:pdf|max:1024' ,
                'jktfile6' => 'mimes:pdf|max:1024' ,
                'jktfile7' => 'mimes:pdf|max:1024' ,
                'nama_kapal' => 'nullable|string',
                'Nama_Barge' => 'nullable|string',
            ]);

            // dd($request);
            $year = date('Y');
            $mergenama_kapal = $request->nama_kapal . '-' . $request->Nama_Barge;
            $month = date('m');
            if ($request->hasFile('jktfile1')) {
                //dd($request);
                $file = $request->file('jktfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('jktfile1')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('jktfile2')){
                $file = $request->file('jktfile2');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/cargo_manifest';
                $path = $request->file('jktfile2')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                       
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }
            }      
            if ($request->hasFile('jktfile3')){
                $file = $request->file('jktfile3');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/voyage';
                $path = $request->file('jktfile3')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('jktfile4')){
                $file = $request->file('jktfile4');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/bill_lading';
                $path = $request->file('jktfile4')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('jktfile5')){
                $file = $request->file('jktfile5');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/gerak_kapal';
                $path = $request->file('jktfile5')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('jktfile6')){
                $file = $request->file('jktfile6');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/docking';
                $path = $request->file('jktfile6')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([     
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }
            }
            if ($request->hasFile('jktfile7')){
                $file = $request->file('jktfile7');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_kapal';
                $path = $request->file('jktfile7')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                 
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }elseif(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->exists()){
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->where('periode_akhir' , $request->tgl_akhir)->whereDate('periode_akhir' , '<' ,date('Y-m-d'))->update([
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }else {
                    documentrpk::create([     
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $mergenama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }
            }

            return redirect('picsite/rpk')->with('success', 'Upload Success! Silahkan di cek  ke bagian "DASHBOARD !');
        }

        //email to user
    // $details = [
    //         'title' => 'Thank you for receiving this email', 
    //         'body' => 'you are a test subject for the project hehe'
    //     ];
        
    //     Mail::to('stanlytong@gmail.com')->send(new Gmail($details));

    //     return redirect('picsite/rpk');
    // }

    }
}