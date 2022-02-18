<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\ApList;
use App\Models\ApListDetail;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;
use App\Exports\ReportAPExport;
use Storage;

class AdminPurchasingController extends Controller
{
    public function formApPage(){
        // Show the form AP page
        $apList = ApList::with('orderHead')->where('cabang', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);
        
        // Get all the supplier
        $suppliers = Supplier::latest()->get();

        // Default branch is Jakarta
        $default_branch = 'Jakarta';

        return view('adminPurchasing.adminPurchasingFormAp', compact('apList', 'default_branch', 'suppliers'));
    }

    public function formApPageBranch($branch){
        // Show the form AP page
        $apList = ApList::with('orderHead')->where('cabang', $branch)->whereYear('created_at', date('Y'))->latest()->paginate(7);
        
        // Get all the supplier
        $suppliers = Supplier::latest()->get();

        // Get the branch from the parameter
        $default_branch = $branch;

        return view('adminPurchasing.adminPurchasingFormAp', compact('apList', 'default_branch', 'suppliers'));
    }

    public function uploadFile(Request $request){
        // Validate the file extension
        $request->validate([
            'doc_partial1' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial2' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial3' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial4' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial5' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',

            'doc_partial6' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial7' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial8' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial9' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial10' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            
            'doc_partial11' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial12' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial13' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial14' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial15' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',

            'doc_partial16' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial17' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial18' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial19' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
            'doc_partial20' => 'nullable|mimes:pdf,word,jpg,jpeg|max:5120',
        ]);

        // Helper var
        $filename = 'doc_partial';

        // Loop through all the inputs and check if they submit the file or not, if they submit a file then input into the database
        for($i = 1 ; $i <=20 ; $i++){
            if($request -> hasFile($filename . $i)){

                // Helper var
                $dynamic_file = $filename . $i;
                $dynamic_status = 'status_partial' . $i;
                $dynamic_uploadTime = 'uploadTime_partial' . $i;
                $dynamic_description = 'description_partial' . $i;
                $dynamic_path_to_file = 'path_to_file' . $i;
                $s3_url_to_file = 's3_url_to_file' . $i;

                // So in this case we want to store it in the folder according to the current month and year => /2021/12/"filename", so we decided to store the path also to the db
                $file_path_format = date('Y/m/');

                // ==================== Still in discussion if there will be 2 AP with the same file ===========================
                // Check if file already exists
                // $curr_file = ApList::find($request -> apListId)->pluck($dynamic_file)[0];

                // Then delete the file first, so it does not takes memory
                // if($curr_file && Storage::exists('APList/' . $curr_file)){
                //     unlink(storage_path('app/APList/' . $curr_file));
                // }
                // ==============================================================================================================

                // Get the file
                // $path = $year . '/' . $month . '/' . $request -> $dynamic_file -> getClientOriginalName();
                $file = $request -> $dynamic_file -> getClientOriginalName();

                // Store the file into storage folder, so it does not publicly accessible || the alternative way is store the files on public folder, but it is easier to access
                $url = $request -> file($dynamic_file) -> storeAs($file_path_format, $file, 's3');

                // Save all additional information to the database
                ApList::find($request -> apListId)->update([
                    $dynamic_file => $file,
                    $dynamic_status => 'On Review',
                    $dynamic_description => NULL,
                    $dynamic_uploadTime => date('d/m/Y'),
                    $dynamic_path_to_file => $file_path_format,
                    $s3_url_to_file => Storage::disk('s3')->url($url)
                ]);
            }
        };
        
        return redirect()->back()->with('status', 'Upload Successfully');
        // return redirect()->back()->with('status', 'Saved Successfully');
    }

    public function saveApDetail(Request $request){
        // Validate the request
        $request -> validate([
            'noInvoice' => 'required',
            'nominalInvoice' => 'required|numeric|between:0,99999999999.99',
            'noFaktur' => 'required',
            'noDo' => 'required',
            'dueDate' => 'required|date',
            'additionalInformation' => 'nullable'
        ]);

        $new_paid = ApList::find($request -> apListId)->paidPrice;
        $total_paid = $new_paid + $request -> nominalInvoice;
        
        if($total_paid > $request -> totalPrice){
            return redirect()->back()->with('fail', 'Nominal Invoice Invalid, Kindly Re-Check The Remaining Price');
        }

        ApList::where('id', $request -> apListId)->update([
            'paidPrice' => $total_paid
        ]);

        $apListDetail = ApListDetail::create([
            'aplist_id' => $request -> apListId,
            'supplierName' => $request -> supplierName,
            'noInvoice' => $request -> noInvoice,
            'noFaktur' => $request -> noFaktur,
            'noDo' => $request -> noDo,
            'nominalInvoice' => $request -> nominalInvoice,
            'additionalInformation' => $request -> additionalInformation,
            'dueDate' => date($request -> dueDate),
            'userWhoSubmitted' => Auth::user()->name,
        ]);

        $apListDetail->update([
            'helper_cursor' => $apListDetail -> id
        ]);

        return redirect()->back()->with('openApListModalWithId', $request -> apListId);
        // return redirect()->back()->with('status', 'Saved Successfully');
    }

    public function closeAp(Request $request){
        // Find the AP
        $apList = ApList::find($request -> apListId);

        // Check if the AP is already processed Then redirect
        if($apList -> tracker == 6){
            return redirect()->back()->with('errorClosePo', $request -> apListId);
        }

        ApList::where('id', $request -> apListId)->update([
            'tracker' => 6,
            'status' => 'CLOSED'
        ]);

        return redirect()->back()->with('openApListModalWithId', $request -> apListId);
    }

    public function reportApPage(){
        // Basically the report is created per 3 months, so we divide it into 4 reports
        // Base on current month, then we classified what period is the report
        $month_now = (int)(date('m'));

        if($month_now <= 3){
            $start_date = date('Y-01-01');
            $end_date = date('Y-03-31');
            $str_month = 'Jan - Mar';
        }elseif($month_now > 3 && $month_now <= 6){
            $start_date = date('Y-04-01');
            $end_date = date('Y-06-30');
            $str_month = 'Apr - Jun';
        }elseif($month_now > 6 && $month_now <= 9){
            $start_date = date('Y-07-01');
            $end_date = date('Y-09-30');
            $str_month = 'Jul - Sep';
        }else{
            $start_date = date('Y-10-01');
            $end_date = date('Y-12-31');
            $str_month = 'Okt - Des';
        }

        // Helper var
        $default_branch = 'Jakarta';

        // Find all the AP within the 3 months period
        $apList = ApList::with('orderHead')->where('cabang', 'like', $default_branch)->join('ap_list_details', 'ap_list_details.aplist_id', '=', 'ap_lists.id')->whereBetween('ap_lists.created_at', [$start_date, $end_date])->orderBy('ap_lists.created_at', 'desc')->get();

        return view('adminPurchasing.adminPurchasingReportApPage', compact('default_branch', 'str_month', 'apList'));
    }

    public function reportApPageBranch($branch){
        // Basically the report is created per 3 months, so we divide it into 4 reports
        // Base on current month, then we classified what period is the report
        $month_now = (int)(date('m'));

        if($month_now <= 3){
            $start_date = date('Y-01-01');
            $end_date = date('Y-03-31');
            $str_month = 'Jan - Mar';
        }elseif($month_now > 3 && $month_now <= 6){
            $start_date = date('Y-04-01');
            $end_date = date('Y-06-30');
            $str_month = 'Apr - Jun';
        }elseif($month_now > 6 && $month_now <= 9){
            $start_date = date('Y-07-01');
            $end_date = date('Y-09-30');
            $str_month = 'Jul - Sep';
        }else{
            $start_date = date('Y-10-01');
            $end_date = date('Y-12-31');
            $str_month = 'Okt - Des';
        }

        // Helper Var
        $default_branch = $branch;

        // Find all the AP within the 3 months period
        $apList = ApList::with('orderHead')->where('cabang', 'like', $default_branch)->join('ap_list_details', 'ap_list_details.aplist_id', '=', 'ap_lists.id')->whereBetween('ap_lists.created_at', [$start_date, $end_date])->orderBy('ap_lists.created_at', 'desc')->get();

        return view('adminPurchasing.adminPurchasingReportApPage', compact('default_branch', 'str_month', 'apList'));
    }

    public function deleteApDetail(Request $request, $helper_cursor){
        // Get the paid nominal of single invoice, so in AP there are multiple payment, we need to get the nominal invoice of specific payment to add it back before delete it
        $invoice_paid = ApListDetail::find($helper_cursor)->nominalInvoice;

        // Then decrement the value
        ApList::find($request -> apListId)->decrement('paidPrice', $invoice_paid);
        
        // Find the Ap List Detail, then delete
        ApListDetail::where('id', $helper_cursor)->delete();

        // Refresh page
        return redirect()->back()->with('status', 'Delete Successfully');
    }

    public function exportReportAp(Excel $excel, $branch){

        // Export into excel
        return $excel -> download(new ReportAPExport($branch), 'Reports_AP('. $branch . ')_'. date("d-m-Y") . '.xlsx');
    }
}
