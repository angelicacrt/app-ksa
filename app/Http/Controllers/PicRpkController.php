<?php

namespace App\Http\Controllers;

use Storage;
use Response;
use validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Mail\Gmail;
use App\Models\documentrpk;
use App\Models\User;

class PicRpkController extends Controller
{
    public function rpk(){
        // $date = date('m');
        // $docrpk = documentrpk::with('user')->where('cabang',Auth::user()->cabang)->whereMonth('created_at', date('m'))->latest()->get();
        // return view('picsite.rpk' , compact('docrpk'));
        return view('picsite.rpk');
    }

    public function uploadrpk(Request $request){

        $docrpk = documentrpk::with('user')->where('cabang',Auth::user()->cabang)->latest()->get();

        if(Auth::user()->cabang == 'Babelan'){ 
            $year = date('Y');
            $month = date('m');
            
            $request->validate([
                'rfile1' => 'mimes:pdf|max:1024' ,
                'rfile2' => 'mimes:pdf|max:1024' ,
                'rfile3' => 'mimes:pdf|max:1024' ,
                'rfile4' => 'mimes:pdf|max:1024' ,
                'rfile5' => 'mimes:pdf|max:1024' ,
                'rfile6' => 'mimes:pdf|max:1024' ,
                'rfile7' => 'mimes:pdf|max:1024' 
            ]);
            if ($request->hasFile('rfile1')) {
                //dd($request);
                $file = $request->file('rfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('rfile1')->storeas('babelan/'. $year . "/". $month . '/RPK', $name1, 's3');
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([      
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([                   
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                  
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([      
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if (documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Babelan')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([              
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);  
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                         'nama_kapal' => $request->nama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }
            }
            return redirect('picsite/rpk')->with('message', 'Upload success!');
        }

        if(Auth::user()->cabang == 'Berau'){
            $year = date('Y');
            $month = date('m');
            $request->validate([
                'brfile1' => 'mimes:pdf|max:1024' ,
                'brfile2' => 'mimes:pdf|max:1024' ,
                'brfile3' => 'mimes:pdf|max:1024' ,
                'brfile4' => 'mimes:pdf|max:1024' ,
                'brfile5' => 'mimes:pdf|max:1024' ,
                'brfile6' => 'mimes:pdf|max:1024' ,
                'brfile7' => 'mimes:pdf|max:1024' 
            ]);
            if ($request->hasFile('brfile1')) {
                //dd($request);
                $file = $request->file('brfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('brfile1')->storeas('berau/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Berau')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }  
            }
            return redirect('picsite/rpk')->with('message', 'Upload success!');
        }

        if(Auth::user()->cabang == 'Banjarmasin' or Auth::user()->cabang == 'Bunati'){
            $year = date('Y');
            $month = date('m');
            $request->validate([
                'bjrfile1' => 'mimes:pdf|max:1024' ,
                'bjrfile2' => 'mimes:pdf|max:1024' ,
                'bjrfile3' => 'mimes:pdf|max:1024' ,
                'bjrfile4' => 'mimes:pdf|max:1024' ,
                'bjrfile5' => 'mimes:pdf|max:1024' ,
                'bjrfile6' => 'mimes:pdf|max:1024' ,
                'bjrfile7' => 'mimes:pdf|max:1024' 
            ]);
            if ($request->hasFile('bjrfile1')) {
                //dd($request);
                $file = $request->file('bjrfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('bjrfile1')->storeas('banjarmasin/'. $year . "/". $month. '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([       
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([         
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }else {
                    documentrpk::create([       
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }
            }
            return redirect('picsite/rpk')->with('message', 'Upload success!');
        }

        if(Auth::user()->cabang == 'Samarinda' or Auth::user()->cabang == 'Kendari'  or Auth::user()->cabang == 'Morosi'){
            $year = date('Y');
            $month = date('m');
            $request->validate([
                'smrfile1' => 'mimes:pdf|max:1024' ,
                'smrfile2' => 'mimes:pdf|max:1024' ,
                'smrfile3' => 'mimes:pdf|max:1024' ,
                'smrfile4' => 'mimes:pdf|max:1024' ,
                'smrfile5' => 'mimes:pdf|max:1024' ,
                'smrfile6' => 'mimes:pdf|max:1024' ,
                'smrfile7' => 'mimes:pdf|max:1024' ,
                'nama_kapal' => 'required'
            ]);
            if ($request->hasFile('smrfile1')) {
                //dd($request);
                $file = $request->file('smrfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('smrfile1')->storeas('samarinda/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                       
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([     
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , Auth::user()->cabang)->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                 
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }else {
                    documentrpk::create([     
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,

                        'nama_kapal' => $request->nama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }
            }
            return redirect('picsite/rpk')->with('message', 'Upload success!');
        }
        
        if(Auth::user()->cabang == 'Jakarta'){
            // dd($request);
            $year = date('Y');
            $month = date('m');
            $request->validate([
                'jktfile1' => 'mimes:pdf|max:1024' ,
                'jktfile2' => 'mimes:pdf|max:1024' ,
                'jktfile3' => 'mimes:pdf|max:1024' ,
                'jktfile4' => 'mimes:pdf|max:1024' ,
                'jktfile5' => 'mimes:pdf|max:1024' ,
                'jktfile6' => 'mimes:pdf|max:1024' ,
                'jktfile7' => 'mimes:pdf|max:1024' 
            ]);
            if ($request->hasFile('jktfile1')) {
                //dd($request);
                $file = $request->file('jktfile1');
                $name1 = 'Picsite-'. Auth::user()->cabang . $file->getClientOriginalName();
                $tujuan_upload = 'RPK/surat_barang';
                $path = $request->file('jktfile1')->storeas('jakarta/'. $year . "/". $month . '/RPK' , $name1, 's3');
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status1' => 'on review',
                        'surat_barang' => basename($path),
                        'time_upload1' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                       
                        'status2' => 'on review',
                        'cargo_manifest'=> basename($path) ,
                        'time_upload2' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status3' => 'on review',
                        'voyage' => basename($path),
                        'time_upload3' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([
                        'status4' => 'on review',
                        'bill_lading' => basename($path),
                        'time_upload4' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                   
                        'status5' => 'on review',
                        'gerak_kapal'=> basename($path) ,
                        'time_upload5' => date("Y-m-d h:i:s"),
                    ]);
                }else{
                    documentrpk::create([    
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                
                        'status6' => 'on review',
                        'docking' => basename($path),
                        'time_upload6' => date("Y-m-d h:i:s"),
                    ]);
                }else {
                    documentrpk::create([     
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $request->nama_kapal,
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
                if(documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->exists()){
                    Storage::disk('s3')->delete($path."/".$name1);
                    documentrpk::where('nama_kapal', 'Like', '%' . $request->nama_kapal . '%')->where('cabang' , 'Jakarta')->whereMonth('created_at' , $month)->whereDate('periode_akhir' , '>=' ,date('Y-m-d'))->update([                 
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }else {
                    documentrpk::create([     
                        'user_id' => Auth::user()->id,
                        'cabang' => Auth::user()->cabang ,
            
                        'nama_kapal' => $request->nama_kapal,
                        'periode_awal' => $request->tgl_awal,
                        'periode_akhir' => $request->tgl_akhir,
                        
                        'status7' => 'on review',
                        'time_upload7' => date("Y-m-d h:i:s"),
                        'surat_kapal' => basename($path),
                    ]);
                }
            }

            return redirect('picsite/rpk')->with('message', 'Upload success!');
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
