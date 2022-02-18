<?php

namespace App\Http\Controllers;

use Storage;
use Response;
use validator;
use Carbon\Carbon;
use App\Mail\Gmail;
use App\Models\User;
use App\Models\NoteSpgr;
use App\Models\spgrfile;
use App\Models\tempcart;
use App\Exports\FCIexport;
use App\Models\formclaims;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ExportNoteSPGR;
use App\Models\headerformclaim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;


class picincidentController extends Controller
{
    //view Form Claim page
    public function formclaim(){
        $tempcarts = tempcart::where('user_id', Auth::user()->id)->get();
        $latestcarts = tempcart::where('user_id', Auth::user()->id)->first();
        return view('picincident.formclaim' , compact('tempcarts' , 'latestcarts'));
    }
   
    // addtoCart 
    public function submitformclaim(Request $request){
        // dd($request);
        // $request->validate([
        //     'name'  => 'nullable|alpha_num',
        //     'dateincident' => 'nullable|date',
        //     'dateclaim' => 'nullable|date', 
        //     'FormClaim' => 'nullable|alpha_num', 
        //     'barge' => 'nullable|alpha_num',
        //     'TugBoat' => 'nullable|alpha_num',
        //     'TSI_barge' => 'numeric|nullable',
        //     'TSI_TugBoat' => 'numeric|nullable',
        //     'surveyor' => 'nullable|alpha_num',

        //     'Item_name'=> 'required|alpha_num',
        //     'incident'=> 'required|alpha_num',
        //     'description'=> 'required|alpha_num',
        //     'deductible' => 'nullable|numeric',
        //     'amount'=> 'required|numeric',
        // ]);
        $mergeamount=  $request->mata_uang_amount .' - '. $request->Amount;
        $mergeTSI_Barge= $request->TSI_barge .' - '. $request-> mata_uang_TSI_barge;
        $mergeTSI_Tugboat= $request->TSI_TugBoat .' - '. $request-> mata_uang_TSI;
        tempcart::create([
            'user_id' => Auth::user()->id,
            'tgl_insiden' => $request->dateincident ,
            'tgl_formclaim' => $request->dateclaim , 
            'name'=> $request->name ,
            'jenis_incident'=> $request->jenisincident ,
            'item' => $request->Item_name ,
            'no_FormClaim'=> $request->FormClaim , 
            'barge'=> $request->barge ,
            'tugBoat'=> $request->TugBoat,
            'TSI_barge'=> $mergeTSI_Barge,
            'TSI_TugBoat'=> $mergeTSI_Tugboat,
            'deductible'=>$request->Deductible ,
            'amount'=> $mergeamount,
            'surveyor'=> $request->Surveyor,
            'incident'=> $request->Incident ,
            'description'=> $request->reasonbox ,
        ]);

        // return redirect('/picincident/formclaim')->with('success', 'Form Telah Berhasil Di Tambahkan.');
        return redirect()->back()->withInput()->with('success', 'Form Telah Berhasil Di Tambahkan.');
    }

    //delete post on Form Claim page
    public function destroy(tempcart $temp){
        tempcart::destroy($temp->id); 
        formclaims::destroy($temp->id); 
        return redirect('/picincident/formclaim')->with('success', 'post telah dihapus.'); 
    }

    // create finallize/historyFCI
    public function createformclaim(Request $request){
        // tempcart::truncate();
        $temp = tempcart::where('user_id', Auth::user()->id)->get();
        $temphead = tempcart::where('user_id', Auth::user()->id)->pluck('no_FormClaim')[0];

        if (count($temp) == 0){
            return redirect()->back()->with('ERR' , "Cart is empty , Please ADD to list" );
        }

        
        $headerid = headerformclaim::create([
            'user_id' => Auth::user()->id,
            'nama_file'=> $temphead ,
        ]);

        foreach( $temp as $temp){
            formclaims::create([
                'user_id' => Auth::user()->id,
                'header_id' => $headerid -> id,
                'tgl_insiden' => $temp->tgl_insiden ,
                'tgl_formclaim' => $temp->tgl_formclaim , 
                'name'=> $temp->name ,
                'jenis_incident'=> $temp->jenis_incident ,
                'item' => $temp->item ,
                'no_FormClaim'=> $temp->no_FormClaim , 
                'barge'=> $temp->barge ,
                'tugBoat'=> $temp->tugBoat,
                'TSI_barge'=> $temp->TSI_barge,
                'TSI_TugBoat'=> $temp->TSI_TugBoat,
                'deductible'=>$temp->deductible ,
                'amount'=> $temp->amount,
                'surveyor'=> $temp->surveyor,
                'incident'=> $temp->incident ,
                'description'=> $temp->description ,
            ]);
        }
        
        tempcart::where('user_id', Auth::user()->id)->delete();
        
        return redirect('/picincident/history');
    }
    //view FCI for export page
    public function formclaimhistory() {
        $Headclaim = headerformclaim::all();
        return view('picincident.historyFCI' , compact('Headclaim'));
    }
    //delete Export post
    public function DestroyExcel(headerformclaim $claims){
        headerformclaim::destroy($claims->id); 
        return redirect('/picincident/history')->with('success', 'File telah dihapus.'); 
    }

    // export excel FCI
    private $excel;
    public function __construct(Excel $excel){
        $this->excel = $excel;
    }

    public function export(Request $request) {
        // dd($request);
        $name = $request->file_name;
        $replaced = Str::replace('/', '_', $name);
        $identify = $request->file_id;
        return $this->excel::download(new FCIexport($identify), date("d-m-Y"). ' - ' .'FCI'. ' - ' . $replaced . '.xlsx');
    
    }

    //export Note SPGR
    public function exportNotes(Request $request) {
        // dd($request);
        $date = Carbon::now();
        $monthName = $date->format('F');
        return Excel::download(new ExportNoteSPGR, date("d-m-Y") . 'Note-SPGR'. '-' . $monthName . '-' . '.xlsx');
    }

   //Notes spgr page  
    public function notespgr(){
        $datetime = date('Y-m-d');
        $UploadNotes =  DB::table('note_spgrs')->get();
        return view('picincident.NoteSpgr', compact('UploadNotes'));
    }

    //create notes
    public function uploadnotespgr(Request $request){
        // dd($request);
        $request->validate([
            'No_SPGR'=> 'required',
            'NamaKapal'=> 'required',
            'No_FormClaim'=> 'required',
            'status_pembayaran'=> 'required',
            'NilaiClaim'=> 'required',
            // 'DateNote'=> 'required',
        ]);
        
        $noteNilai = $request->mata_uang_nilai . '-' .  $request->Nilai;
        $noteNilai_Claim =  $request->mata_uang_claim. '-' . $request->NilaiClaim;
                  
        NoteSpgr::create([
            'user_id' => Auth::user()->id,
            'DateNote' => $request->Datebox ,
            'No_SPGR' => $request->No_SPGR ,
            'No_FormClaim' => $request->No_FormClaim ,
            'Nama_Kapal' => $request->NamaKapal ,
            'status_pembayaran' => $request->status_pembayaran ,
            'Nilai' => $noteNilai,
            'Nilai_Claim' => $noteNilai_Claim ,
        ]);
        return redirect('/picincident/NoteSpgr')->with('success', 'Note telah ditambahkan.');
    }

    // Notes spgr delete
    public function destroynote(NoteSpgr $UpNotes){
        NoteSpgr::destroy($UpNotes->id); 
        return redirect('/picincident/NoteSpgr')->with('success', 'post telah dihapus.'); 
    }

    public function destroyallnote(){
        // Emptying the whole note spgr
        NoteSpgr::where('user_id', Auth::user()->id)->delete();
        return redirect('/picincident/NoteSpgr')->with('success', 'seluruh post telah dihapus.'); 
    }

    //edit page
    public function editnotespgr(NoteSpgr $UpNotes){
        // dd($request);
        return view('picincident.editNoteSpgr',compact('UpNotes'));
    }

    //update notes
    public function updatenote(Request $request, NoteSpgr $UpNotes){
        $noteNilai = $request->mata_uang_nilai . '-' .  $request->Nilai;
        $noteNilai_Claim =  $request->mata_uang_claim. '-' . $request->NilaiClaim;

        $UpNotes = NoteSpgr::find($UpNotes->id);
        $UpNotes->DateNote = $request->Datebox;
        $UpNotes->No_SPGR = $request->No_SPGR;
        $UpNotes->No_FormClaim = $request->No_FormClaim;
        $UpNotes->Nama_Kapal = $request->NamaKapal;
        $UpNotes->status_pembayaran = $request->status_pembayaran;
        $UpNotes->Nilai = $noteNilai;
        $UpNotes->Nilai_Claim = $noteNilai_Claim;
        $UpNotes->update();
        // dd($request);
        return redirect('/picincident/NoteSpgr')->with('success', 'post telah terupdate.'); 
    }
    
   

    // upload spgr file
    public function spgr(){
        // $uploadspgr = spgrfile::with('user')->where('id', '>=' , 1)->latest()->get();
        return view('picincident.spgr');
    }

    public function spgrupload(Request $request){
        // $uploadspgr = spgrfile::with('user')->where('id', '>=' , 1)->latest()->get();
        // dd($request);
        $year = date('Y');
        $month = date('m');
        $request->validate([
            'spgrfile1' => 'mimes:pdf|max:3072', 
            'spgrfile2' => 'mimes:pdf|max:3072', 
            'spgrfile3' => 'mimes:pdf|max:3072', 
            'spgrfile4' => 'mimes:pdf|max:3072', 
            'spgrfile5' => 'mimes:pdf|max:3072', 
            'spgrfile6' => 'mimes:pdf|max:3072', 
            'spgrfile7' => 'mimes:pdf|max:3072' ,
            'no_formclaim' => 'required'
        ]);
        
        if ($request->hasFile('spgrfile1')) {
            $file = $request->File('spgrfile1');
            $name1 = 'SPGR-'. Auth::user()->name . '-' . $file->getClientOriginalName();
            $path = $request->file('spgrfile1')->storeas('spgr/'. $year . "/". $month , $name1, 's3');

            if (spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->whereMonth('created_at', date('m'))->exists()){
                Storage::disk('s3')->delete($path."/".$name1);
                spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->update([
                    'status1' => 'on review',
                    'time_upload1' => date("Y-m-d"),
                    'spgr' => basename($path),]);
            }else{
                spgrfile::create([
                    'no_formclaim' => $request->no_formclaim,
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status1' => 'on review',
                    'time_upload1' => date("Y-m-d"),
                    'spgr' => basename($path),
                ]);
            }
        }
        if ($request->hasFile('spgrfile2')) {
            $file = $request->File('spgrfile2');
            $name1 = 'SPGR-'. Auth::user()->name . '-' . $file->getClientOriginalName();
            $path = $request->file('spgrfile2')->storeas('spgr/'. $year . "/". $month , $name1, 's3');

            if (spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->whereMonth('created_at', date('m'))->exists()){
                Storage::disk('s3')->delete($path."/".$name1);
                spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->update([
                    'status2' => 'on review',
                    'time_upload2' => date("Y-m-d"),
                    'Letter_of_Discharge' => basename($path),]);
                }else{
                spgrfile::create([
                    'no_formclaim' => $request->no_formclaim,
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status2' => 'on review',
                    'time_upload2' => date("Y-m-d"),
                    'Letter_of_Discharge' => basename($path),
                ]);
            }
        }
        if ($request->hasFile('spgrfile3')) {
            $file = $request->File('spgrfile3');
            $name1 = 'SPGR-'. Auth::user()->name . '-' . $file->getClientOriginalName();
            $path = $request->file('spgrfile3')->storeas('spgr/'. $year . "/". $month , $name1, 's3');
            if (spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->whereMonth('created_at', date('m'))->exists()){
                Storage::disk('s3')->delete($path."/".$name1);
                spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->update([
                    'status3' => 'on review',
                    'time_upload3' => date("Y-m-d"),
                    'CMC' => basename($path),]);
                }else{
                spgrfile::create([
                    'no_formclaim' => $request->no_formclaim,
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status3' => 'on review',
                    'time_upload3' => date("Y-m-d"),
                    'CMC' => basename($path),
                ]);
            }
        }
        if ($request->hasFile('spgrfile4')) {
            $file = $request->File('spgrfile4');
            $name1 = 'SPGR-'. Auth::user()->name . '-' . $file->getClientOriginalName();
            $path = $request->file('spgrfile4')->storeas('spgr/'. $year . "/". $month , $name1, 's3');
            if (spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->whereMonth('created_at', date('m'))->exists()){
                Storage::disk('s3')->delete($path."/".$name1);
                spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->update([
                    'status4' => 'on review',
                    'time_upload4' => date("Y-m-d"),
                    'surat_laut' => basename($path),]);
                }else{
                spgrfile::create([
                    'no_formclaim' => $request->no_formclaim,
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status4' => 'on review',
                    'time_upload4' => date("Y-m-d"),
                    'surat_laut' => basename($path),
                ]);
            }
        }
        if ($request->hasFile('spgrfile5')) {
            $file = $request->File('spgrfile5');
            $name1 = 'SPGR-'. Auth::user()->name . '-' . $file->getClientOriginalName();
            $path = $request->file('spgrfile5')->storeas('spgr/'. $year . "/". $month , $name1, 's3');
            if (spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->whereMonth('created_at', date('m'))->exists()){
                Storage::disk('s3')->delete($path."/".$name1);
                spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->update([
                    'status5' => 'on review',
                    'time_upload5' => date("Y-m-d"),
                    'spb' => basename($path),]);
                }else{
                spgrfile::create([
                    'no_formclaim' => $request->no_formclaim,
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status5' => 'on review',
                    'time_upload5' => date("Y-m-d"),
                    'spb' => basename($path),
                ]);
            }
        }
        if ($request->hasFile('spgrfile6')) {
            $file = $request->File('spgrfile6');
            $name1 = 'SPGR-'. Auth::user()->name . '-' . $file->getClientOriginalName();
            $path = $request->file('spgrfile6')->storeas('spgr/'. $year . "/". $month , $name1, 's3');
            if (spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->whereMonth('created_at', date('m'))->exists()){
                Storage::disk('s3')->delete($path."/".$name1);
                spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->update([
                    'status6' => 'on review',
                    'time_upload6' => date("Y-m-d"),
                    'load_line' => basename($path),]);
                }else{
                spgrfile::create([
                    'no_formclaim' => $request->no_formclaim,
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status6' => 'on review',
                    'time_upload6' => date("Y-m-d"),
                    'load_line' => basename($path),
                ]);
            }
        }
        if ($request->hasFile('spgrfile7')) {
            $file = $request->File('spgrfile7');
            $name1 = 'SPGR-'. Auth::user()->name . '-' . $file->getClientOriginalName();
            $path = $request->file('spgrfile7')->storeas('spgr/'. $year . "/". $month , $name1, 's3');
            if (spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->whereMonth('created_at', date('m'))->exists()){
                Storage::disk('s3')->delete($path."/".$name1);
                spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->update([
                    'status7' => 'on review',
                    'time_upload7' => date("Y-m-d"),
                    'surat_keterangan_bank' => basename($path),]);
                }else{
                spgrfile::create([
                    'no_formclaim' => $request->no_formclaim,
                    'cabang' => Auth::user()->cabang ,
                    'user_id' => Auth::user()->id,

                    'status7' => 'on review',
                    'time_upload7' => date("Y-m-d"),
                    'surat_keterangan_bank' => basename($path),
                ]);
            }
        }
        // if ($request->hasFile('spgrfile8')) {
        //     $file = $request->File('spgrfile8');
        //     $name1 = 'SPGR-'. Auth::user()->name . '-' . $file->getClientOriginalName();
        //     $path = $request->file('spgrfile8')->storeas('spgr/'. $year . "/". $month , $name1, 's3');
        //     if (spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->whereMonth('created_at', date('m'))->exists()){
        //         Storage::disk('s3')->delete($path."/".$name1);
        //         spgrfile::where('no_formclaim', 'Like', '%' . $request->no_formclaim . '%')->where('cabang', 'Jakarta')->update([
        //             'status8' => 'on review',
        //             'time_upload8' => date("Y-m-d"),
        //             'surat_keterangan_bank' => basename($path),]);
        //         }else{
        //         spgrfile::create([
        //             'no_formclaim' => $request->no_formclaim,
        //             'cabang' => Auth::user()->cabang ,
        //             'user_id' => Auth::user()->id,

        //             'status8' => 'on review',
        //             'time_upload8' => date("Y-m-d"),
        //             'surat_keterangan_bank' => basename($path),
        //         ]);
        //     }
        // }
        return redirect('picincident/spgr')->with('success', 'Upload success!');
    }

}
