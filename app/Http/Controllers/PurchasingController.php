<?php

namespace App\Http\Controllers;

use Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ApList;
use App\Models\JobHead;
use App\Models\Supplier;
use App\Exports\JOExport;
use App\Exports\POExport;
use App\Models\OrderHead;
use App\Exports\JO_Report;
use App\Models\JobDetails;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ReportAPExport;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Exports\PurchasingReportExport;

class PurchasingController extends Controller
{

    public function branchDashboard($branch){
        $default_branch = $branch;

        // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
        // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where('role_user.role_id' , '=', '3')->where('cabang', 'like', $default_branch)->pluck('users.id');
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic');
        })->where('cabang', 'like', $default_branch)->pluck('users.id');
        
        
        if(request('search')){
            $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query){
                $query->where('status', 'like', '%'. request('search') .'%')
                ->orWhere('order_id', 'like', '%'. request('search') .'%');
            })->whereYear('created_at', date('Y'))->latest()->paginate(6);
        }else{
            $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->whereYear('created_at', date('Y'))->latest()->paginate(6)->withQueryString();
        }

        // Then find all the order details from the orderHeads
        // $order_id = OrderHead::whereIn('user_id', $users)->where('created_at', '>=', Carbon::now()->subDays(30))->pluck('order_id');
        $order_id = $orderHeads->pluck('id');
        $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

        // Count the completed & in progress order
        $completed = OrderHead::where(function($query){
            $query->where('status', 'like', 'Order Completed (Logistic)')
            ->orWhere('status', 'like', 'Order Rejected By Supervisor')
            ->orWhere('status', 'like', 'Order Rejected By Purchasing');
        })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->count();

        $in_progress = OrderHead::where(function($query){
            $query->where('status', 'like', 'Order In Progress By Supervisor')
            ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
            ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
            ->orWhere('status', 'like', '%' . 'Revised' . '%')
            ->orWhere('status', 'like', '%' . 'Finalized' . '%')
            ->orWhere('status', 'like', 'Item Delivered By Supplier');
        })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->count();

        // Get all the suppliers
        $suppliers = Supplier::latest()->get();

        return view('purchasing.purchasingDashboard', compact('orderHeads', 'orderDetails', 'suppliers', 'completed', 'in_progress', 'default_branch'));
    }

    public function completedOrder($branch){
        $default_branch = $branch;

        // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic');
        })->where('cabang', 'like', $default_branch)->pluck('users.id');

        $in_progress = OrderHead::where(function($query){
            $query->where('status', 'like', 'Order In Progress By Supervisor')
            ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
            ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
            ->orWhere('status', 'like', '%' . 'Revised' . '%')
            ->orWhere('status', 'like', '%' . 'Finalized' . '%')
            ->orWhere('status', 'like', 'Item Delivered By Supplier');
        })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->count();

        if(request('search')){
            $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query){
                $query->where('status', 'like', '%'. request('search') .'%')
                ->orWhere('order_id', 'like', '%'. request('search') .'%');
            })->whereYear('created_at', date('Y'))->latest()->paginate(6);
            
            // Count the completed & in progress order
            $completed = OrderHead::where(function($query){
                $query->where('status', 'like', 'Order Completed (Logistic)')
                ->orWhere('status', 'like', 'Order Rejected By Supervisor')
                ->orWhere('status', 'like', 'Order Rejected By Purchasing');
            })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->count();
            
            // Then find all the order details from the orderHeads
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            // Get all the suppliers
            $suppliers = Supplier::latest()->get();

            return view('purchasing.purchasingDashboard', compact('orderHeads', 'orderDetails', 'suppliers', 'completed', 'in_progress', 'default_branch'));
        }else{
            $orderHeads = OrderHead::where(function($query){
                $query->where('status', 'like', 'Order Completed (Logistic)')
                ->orWhere('status', 'like', 'Order Rejected By Supervisor')
                ->orWhere('status', 'like', 'Order Rejected By Purchasing');
            })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6);
    
            $completed = $orderHeads->count();
            
            // Then find all the order details from the orderHeads
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
            
            // Get all the suppliers
            $suppliers = Supplier::latest()->get();
    
            return view('purchasing.purchasingDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'suppliers', 'default_branch'));
        }
    }

    public function inProgressOrder($branch){
        $default_branch = $branch;

        // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic');
        })->where('cabang', 'like', $default_branch)->pluck('users.id');

        // Count the completed & in progress order
        $completed = OrderHead::where(function($query){
            $query->where('status', 'like', 'Order Completed (Logistic)')
            ->orWhere('status', 'like', 'Order Rejected By Supervisor')
            ->orWhere('status', 'like', 'Order Rejected By Purchasing Manager')
            ->orWhere('status', 'like', 'Order Rejected By Purchasing');
        })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->count();

        if(request('search')){
            $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query){
                $query->where('status', 'like', '%'. request('search') .'%')
                ->orWhere('order_id', 'like', '%'. request('search') .'%');
            })->whereYear('created_at', date('Y'))->latest()->paginate(6);

            $in_progress = OrderHead::where(function($query){
                $query->where('status', 'like', 'Order In Progress By Supervisor')
                ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'like', 'Item Delivered By Supplier');
            })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->count();

            // Then find all the order details from the orderHeads
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            // Get all the suppliers
            $suppliers = Supplier::latest()->get();

            return view('purchasing.purchasingDashboard', compact('orderHeads', 'orderDetails', 'suppliers', 'completed', 'in_progress', 'default_branch'));
        }else{
            $orderHeads =  OrderHead::where(function($query){
                $query->where('status', 'like', 'Order In Progress By Supervisor')
                ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'like', 'Item Delivered By Supplier');
            })->whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6);
    
            $in_progress = $orderHeads->count();
    
            // Then find all the order details from the orderHeads
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            // Get all the suppliers
            $suppliers = Supplier::latest()->get();
    
            return view('purchasing.purchasingDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'suppliers', 'default_branch'));
        }
    }

    public function approveOrderPage(OrderHead $orderHeads){
        // Formatting the PO code
        $month_arr_in_roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        // Prepare all of the required resources
        $cabang_arr = [
            'Jakarta' => 'JKT',
            'Banjarmasin' => 'BNJ',
            'Samarinda' => 'SMD',
            'Bunati' => 'BNT',
            'Babelan' => 'BBL',
            'Berau' => 'BER'
        ];

        $po_id = $orderHeads -> id;
        $first_char_name = strtoupper(Auth::user()->name[0]);
        $location = $cabang_arr[Auth::user()->cabang];
        $month = date('n');
        $month_to_roman = $month_arr_in_roman[$month - 1];
        $year = date('Y');

        // Create the PO Number => 1251.P/PO-KSA-JKT/IX/2021
        $poNumber = $po_id . '.' . $first_char_name . '/' . 'PO-' . $orderHeads->company . '-' . $location . '/' . $month_to_roman . '/' . $year;

        // Get the order details join with the item
        $orderDetails = OrderDetail::with('item')->where('orders_id', $orderHeads->id)->where('orderItemState', 'like', 'Accepted')->get();

        $suppliers = Supplier::latest()->get();

        return view('purchasing.purchasingApprovedPage', compact('orderHeads', 'orderDetails', 'poNumber', 'suppliers'));
    }

    public function editPriceOrderDetail(Request $request, OrderHead $orderHeads, OrderDetail $orderDetails){
        // Validate incoming request
        $request->validate([
            'itemPrice' => 'required|numeric|between:0,99999999999.99',
            'supplier' => 'required',
        ]);
        
        // Calculate the subtotal (qty * price)
        $subTotal = $orderDetails -> acceptedQuantity * $request -> itemPrice;

        // Then update the individual total item price for order details
        OrderDetail::where('id', $orderDetails->id)->update([
            'itemPrice' => $request->itemPrice,
            'totalItemPrice' => $subTotal,
            'supplier' => $request->supplier,
        ]);
        
        // Then re-calculate the total price
        $totalPriceBeforeCalculation = OrderDetail::where('orders_id', $orderHeads -> id)->sum('totalItemPrice');

        // Then update the total price on the order head
        OrderHead::find($orderHeads -> id)->update([
            // 'totalPrice' => $totalPriceBeforeCalculation,
            'totalPriceBeforeCalculation' => $totalPriceBeforeCalculation
        ]);

        // Redirect
        return redirect('/purchasing/order/' . $orderHeads->id . '/approve')->with('status', 'Price Updated Successfully');
    }
    
    public function dropOrderDetail(Request $request, OrderDetail $orderDetails){
        // Drop the order detail by changing the status
        OrderDetail::where('id', $orderDetails -> id)->update([
            'orderItemState' => 'Rejected',
        ]);

        // Also update the total price on the order head
        OrderHead::where('id', $request -> orderHeadsId)->decrement('totalPriceBeforeCalculation', $orderDetails -> totalItemPrice);

        // Redirect
        return redirect()->back()->with('dropStatus', $orderDetails -> id);
    }

    public function undoDropOrderDetail(OrderHead $orderHeads, OrderDetail $orderDetails){
        // Undo the drop item
        OrderDetail::where('id', $orderDetails -> id)->update([
            'orderItemState' => 'Accepted',
        ]);

        // Increment back the total price from the deleted item
        OrderHead::where('id', $orderHeads -> id)->increment('totalPriceBeforeCalculation', $orderDetails -> totalItemPrice);

        // Redirect
        return redirect()->back()->with('status', 'Updated Succesfully');
    }

    // ============================================ Fix this section ===========================================================
    public function approveOrder(Request $request, OrderHead $orderHeads){
        // Set default branch
        $default_branch = $orderHeads -> cabang;
        // Validate the request form
        $request -> validate([
            'boatName' => 'required',
            'noPr' => 'required',
            'noPo' => 'required',
            'invoiceAddress' => 'required',
            'itemAddress' => 'required',
            'ppn' => 'required|numeric|in:0,10,11',
            'discount' => 'nullable|numeric|min:0|max:100',
            'totalPrice' => 'required',
            'itemType' => 'required|in:Barang'
        ]);

        // calculate discount first, then PPN
        $updatedPriceAfterDiscount = $orderHeads -> totalPriceBeforeCalculation - ($orderHeads -> totalPriceBeforeCalculation * $request -> discount / 100);

        // calculate PPN
        $updatedPriceAfterPPN = $updatedPriceAfterDiscount + ($updatedPriceAfterDiscount * $request -> ppn / 100);

        // Check if already been processed or not
        if($orderHeads -> order_tracker == 4){
            // return redirect('/purchasing/dashboard/' . $default_branch)->with('errorB', 'Order Already Been Processed');
            return redirect('/purchasing/dashboard/' . $default_branch)->with('errorB', 'Order Already Been Processed');
        }

        $checkIsSupplierEmpty = OrderDetail::where('orders_id', $orderHeads -> id)->get();
        
        foreach($checkIsSupplierEmpty as $c){
            if($c -> supplier == null){
                return redirect()->back()->with('error', 'Supplier Is Empty, Please Save The Item Before Submit');
            }
        }

        // =============================== Still in Discussion ====================================
        // Check if there is one item that price is 0 also check if they somehow manage to bypass supplier inputs

        // $orderDetails = OrderDetail::where('orders_id', $orderHeads -> id)->get();
        
        // foreach($orderDetails as $od){
        //     if($od -> totalItemPrice == 0){
        //         return redirect('/purchasing/order/' . $orderHeads -> id . '/approve')->with('error', 'Harga ' . $od -> item -> itemName . ' Invalid');
        //     }
        //     if(!$od -> supplier){
        //         return redirect('/purchasing/order/' . $orderHeads -> id . '/approve')->with('error', 'Supplier Invalid');
        //     }
        // }
        // =============================== Still in Discussion ====================================
        

        // Check if the discount value is null then set to zero
        if(!$request -> discount){
            $updatedDiscount = 0;
        }else{
            $updatedDiscount = $request -> discount;
        }

        // Then update the following order
        OrderHead::find($orderHeads -> id)->update([
            'approvedBy' => Auth::user()->name,
            'status' => 'Order In Progress By Purchasing Manager',
            'poDate' => date('d/m/Y'),
            'noPo' => $request -> noPo,
            'invoiceAddress' => $request -> invoiceAddress,
            'itemAddress' => $request -> itemAddress,
            'ppn' => $request -> ppn,
            'discount' => $updatedDiscount,
            'totalPrice' => $updatedPriceAfterPPN,
            'itemType' => $request -> itemType,
            'order_tracker' => 4,
        ]);
        return redirect('/purchasing/dashboard/' . $default_branch)->with('statusB', 'Order Approved By Purchasing');
        // return redirect()->back()->with('statusB', 'Order Approved By Purchasing');
    }
    
    public function reviseOrder(Request $request, OrderHead $orderHeads){
        // Set default branch
        $default_branch = $orderHeads -> cabang;

        // Validate the request form
        $request -> validate([
            'invoiceAddress' => 'required',
            'itemAddress' => 'required',
            'ppn' => 'required|numeric|in:0,10',
            'discount' => 'nullable|numeric|min:0|max:100',
            'itemType' => 'required|in:Barang'
        ]);

        // calculate discount first, then PPN
        $updatedPriceAfterDiscount = $orderHeads -> totalPriceBeforeCalculation - ($orderHeads -> totalPriceBeforeCalculation * $request -> discount / 100);

        // calculate PPN
        $updatedPriceAfterPPN = $updatedPriceAfterDiscount + ($updatedPriceAfterDiscount * $request -> ppn / 100);

        // Check if already been processed or not
        if($orderHeads -> order_tracker == 7){
            return redirect('/purchasing/dashboard/' . $default_branch)->with('errorB', 'Order Already Been Processed');
            // return redirect()->back()->with('errorB', 'Order Already Been Processed');
        }

        $orderDetails = OrderDetail::where('orders_id', $orderHeads -> id)->get();

        // Check if there is one item that price is 0 also check if they somehow manage to bypass supplier inputs
        foreach($orderDetails as $od){
            if($od -> totalItemPrice == 0){
                return redirect('/purchasing/order/' . $orderHeads -> id . '/revise')->with('error', 'Harga ' . $od -> item -> itemName . ' Invalid');
            }
            if(!$od -> supplier){
                return redirect('/purchasing/order/' . $orderHeads -> id . '/revise')->with('error', 'Supplier Invalid');
            }
        }
        
        // Check if the discount value is null then set to zero
        if(!$request -> discount){
            $updatedDiscount = 0;
        }else{
            $updatedDiscount = $request -> discount;
        }

        // Then update the following order
        OrderHead::find($orderHeads -> id)->update([
            'status' => 'Order Being Finalized By Purchasing Manager',
            'invoiceAddress' => $request->invoiceAddress,
            'itemAddress' => $request->itemAddress,
            'ppn' => $request->ppn,
            'discount' => $updatedDiscount,
            'totalPrice' => $updatedPriceAfterPPN,
            'itemType' => $request -> itemType,
            'order_tracker' => 7,
        ]);
        return redirect('/purchasing/dashboard/' . $default_branch)->with('statusB', 'Order Approved By Purchasing');
    }

    public function rejectOrder(Request $request, OrderHead $orderHeads){
        // Reject the order made from logistic
        $request->validate([
            'reason' => 'required'
        ]);

        // Check if already been processed or not
        if($orderHeads -> order_tracker == 4){
            return redirect('/dashboard')->with('errorB', 'Order Already Been Processed');
        }
        
        // Then update the status + reason
        OrderHead::where('id', $orderHeads->id)->update([
            'order_tracker' => 4,
            'approvedBy' => Auth::user()->name,
            'status' => 'Order Rejected By Purchasing',
            'reason' => $request->reason
        ]);
        // return redirect('/dashboard')->with('statusB', 'Order Rejected');
        return redirect()->back()->with('statusB', 'Order Rejected');
    }

    public function downloadPo(OrderHead $orderHeads){
        return (new POExport($orderHeads -> order_id))->download('PO-' . $orderHeads -> order_id . '_' .  date("d-m-Y") . '.xlsx');
    }

    public function editSupplier(Request $request, Supplier $suppliers){
        // Find the supplier id, then edit the ratings
        Supplier::find($suppliers->id)->update([
            'quality' => $request -> quality,
            'top' => $request -> top,
            'price' => $request -> price,
            'deliveryTime' => $request -> deliveryTime,
            'availability' => $request -> availability,
        ]);

        return redirect('/dashboard')->with('statusA', 'Edited Successfully');
    }

    public function reportPage(){
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

        // Default branch is Jakarta
        $default_branch = 'Jakarta';

        // Find order from user/goods in
        // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where(function($query){
        //     $query->where('role_user.role_id' , '=', '3')
        //     ->orWhere('role_user.role_id' , '=', '4');
        // })->where('cabang', 'like', $default_branch)->pluck('users.id');
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic');
        })->where('cabang', 'like', $default_branch)->pluck('users.id');
                
        // Find all the items that has been approved from the logistic | Per 3 months

        // $orderHeads = OrderHead::whereIn('user_id', $users)->where(function($query){
        //     $query->where('status', 'like', 'Order Completed (Logistic)')
        //         ->orWhere('status', 'like', 'Item Delivered By Supplier');
        // })->whereBetween('order_heads.created_at', [$start_date, $end_date])->where('cabang', 'like', $default_branch)->orderBy('order_heads.updated_at', 'desc')->get();

        $orders = OrderDetail::with(['item'])->join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->whereIn('user_id', $users)->where(function($query){
            $query->where('status', 'like', 'Order Completed (Logistic)')
                ->orWhere('status', 'like', 'Item Delivered By Supplier');
        })->whereBetween('order_heads.created_at', [$start_date, $end_date])->where('cabang', 'like', $default_branch)->orderBy('order_heads.updated_at', 'desc')->get();

        return view('purchasing.purchasingReport', compact('orders', 'default_branch', 'str_month'));
    }

    public function reportPageBranch($cabang){
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

        $default_branch = $cabang;

        // Find order from user that created the order
        // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where(function($query){
        //     $query->where('role_user.role_id' , '=', '3')
        //     ->orWhere('role_user.role_id' , '=', '4');
        // })->where('cabang', 'like', $default_branch)->pluck('users.id');
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic');
        })->where('cabang', 'like', $default_branch)->pluck('users.id');
                
        // Find all the items that has been approved from the logistic | Per 3 months

        // $orderHeads = OrderHead::whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->where(function($query){
        //     $query->where('status', 'like', 'Order Completed (Logistic)')
        //         ->orWhere('status', 'like', 'Item Delivered By Supplier');
        // })->whereBetween('order_heads.created_at', [$start_date, $end_date])->orderBy('order_heads.updated_at', 'desc')->get();

        $orders = OrderDetail::with(['item'])->join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->whereIn('user_id', $users)->where(function($query){
            $query->where('status', 'like', 'Order Completed (Logistic)')
                ->orWhere('status', 'like', 'Item Delivered By Supplier');
        })->whereBetween('order_heads.created_at', [$start_date, $end_date])->where('cabang', 'like', $default_branch)->orderBy('order_heads.updated_at', 'desc')->get();

        return view('purchasing.purchasingReport', compact('orders', 'default_branch', 'str_month'));
    }

    public function downloadReport(Excel $excel, $cabang){
        $default_branch = $cabang;
        return $excel -> download(new PurchasingReportExport($default_branch), 'Reports_'. date("d-m-Y") . '.xlsx');
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

        return view('purchasing.purchasingReportApPage', compact('default_branch', 'str_month', 'apList'));
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

        return view('purchasing.purchasingReportApPage', compact('default_branch', 'str_month', 'apList'));
    }

    public function exportReportAp(Excel $excel, $branch){

        // Export into excel
        return $excel -> download(new ReportAPExport($branch), 'Reports_AP('. $branch . ')_'. date("d-m-Y") . '.xlsx');
    }

    public function supplierPage(){
        // Get all supplier
        $suppliers = Supplier::latest()->get();

        return view('purchasing.purchasingSupplierPage', compact('suppliers'));
    }

    public function addSupplier(Request $request){
        // Validate request
        $validated = $request -> validate([
            'supplierName' => ['required', 'string', 'starts_with:CV,PT', 'unique:suppliers'],
            'supplierPic' => ['required', 'string'],
            'supplierEmail' => ['required', 'string', 'email:rfc,dns', 'unique:suppliers'],
            'supplierAddress' => ['required', 'string'],
            'supplierNoRek' => ['required', 'string'],
            'supplierNPWP' => ['required', 'string'],
            'supplierCode' => ['required', 'string'],
            'supplierNote' => ['nullable', 'string']
        ]);
        
        // Then create the supplier
        Supplier::create($validated);

        // Redirect
        return redirect('/purchasing/supplier')->with('status', 'Added Successfully');
    }

    public function editSupplierDetail(Request $request){
        // Validate Request
        $validated = $request -> validate([
            'supplierEmail' => ['required', 'string', 'email:rfc,dns', Rule::unique('suppliers')->ignore($request->supplier_id)],
            'supplierAddress' => ['required', 'string'],
            'supplierNoRek' => ['required', 'string'],
            'supplierNPWP' => ['required', 'string'],
            'supplierNote' => ['nullable', 'string']
        ]);

        // Then update the supplier
        Supplier::where('id', $request -> supplier_id)->update($validated);

        // Redirect
        return redirect('/purchasing/supplier')->with('status', 'Edit Successfully');
    }

    public function deleteSupplier(Request $request){
        // Find the supplier, then delete it
        Supplier::where('id', $request -> supplier_id)->delete();

        // Redirect
        return redirect('/purchasing/supplier')->with('status', 'Delete Successfully');
    }

    // ============================================ Dev section ===========================================================
    public function ApproveJobPage(Request $request,JobHead $JobHeads){
        $Jobfind = JobHead::find($JobHeads->id);
        $jobDetails = JobDetails::whereIn('jasa_id', $Jobfind)->get();
        $lokasi =JobDetails::whereIn('jasa_id', $Jobfind)->pluck('lokasi')[0];
        $no_jr = JobHead::whereIn('id', $Jobfind)->pluck('noJr')[0];
        $tug = JobDetails::whereIn('jasa_id', $Jobfind)->pluck('tugName')[0];
        $Barge = JobDetails::whereIn('jasa_id', $Jobfind)->pluck('bargeName')[0];
        $tugboat = $tug . '/' .$Barge;

         // Formatting the PO code
         $month_arr_in_roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

         // Prepare all of the required resources
         $cabang_arr = [
             'Jakarta' => 'JKT',
             'Banjarmasin' => 'BNJ',
             'Samarinda' => 'SMD',
             'Bunati' => 'BNT',
             'Babelan' => 'BBL',
             'Berau' => 'BER'
         ];
 
         $Jo_id = $Jobfind-> id;
         $first_char_name = strtoupper(Auth::user()->name[0]);
         $location = $cabang_arr[Auth::user()->cabang];
         $month = date('n');
         $month_to_roman = $month_arr_in_roman[$month - 1];
         $year = date('Y');
 
        // Create the JO Number => 1251.P/PO-KSA-JKT/IX/2021
        $JoNumber = $Jo_id . '.' . $first_char_name . '/' . 'JO-' . $Jobfind->company . '-' . $location . '/' . $month_to_roman . '/' . $year;

        $suppliers = Supplier::latest()->get();

        return view('purchasing.purchasingApprovedJobsPage', compact('Jobfind', 'no_jr' , 'lokasi' , 'tugboat' , 'jobDetails' , 'suppliers' , 'JoNumber'));
    }

    public function JobRequestListPage(){
        $default_branch = 'Jakarta';
        
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic');
        })->where('cabang', 'like', $default_branch)->pluck('users.id');

        if(request('search')){
            $JobRequestHeads = JobHead::with('user')->where(function($query){
                    $query->where('status', 'like', '%'. request('search') .'%')
                    ->orWhere( 'Headjasa_id', 'like', '%'. request('search') .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(6);
        }else{
            $JobRequestHeads = JobHead::where('cabang', 'like',  $default_branch)
            ->where('status', 'like', 'Job Request Approved By'. '%')
            ->orwhere('status', 'like', 'Job Request Rejected By Purchasing')
            ->orwhere('status', 'like', 'Job Request Completed')
            ->whereYear('created_at', date('Y'))->latest()->paginate(6)->withQueryString();
        }
        
        $job_id = $JobRequestHeads->pluck('id');
        $jobDetails = JobDetails::whereIn('jasa_id', $job_id)->get();
          // Get all the suppliers
        $suppliers = Supplier::latest()->get();

         // Count the completed & in progress job Requests
         $completed = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request Completed')
            ->orWhere('status', 'like', 'Job Request Rejected By Purchasing');
        })->whereYear('created_at', date('Y'))->count();
        
        $in_progress = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request In Progress By'. '%')
            ->orWhere('status', 'like', 'Job Request Approved By' . '%');
        })->whereYear('created_at', date('Y'))->count();

        return view('purchasing.purchasingReviewJobRequestList', compact('JobRequestHeads','jobDetails' , 'suppliers', 'completed', 'in_progress', 'default_branch'));
    }

    public function reportJOPage(){
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
        $jobs = JobDetails::join('job_heads', 'job_heads.id', '=', 'job_details.jasa_id')
        ->orwhere('job_heads.status', 'like', 'Job Request Approved By' . '%')
        ->orwhere('job_heads.status', 'like', 'Job Request Completed')
        ->whereBetween('job_heads.created_at', [$start_date, $end_date])
        ->where('job_details.cabang', $default_branch)->orderBy('job_heads.updated_at', 'desc')->get();
        return view('purchasing.purchasingReportJOPage', compact('default_branch', 'str_month', 'jobs'));
    }

    public function reportJOPageBranch($branch){
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
        $jobs = JobDetails::join('job_heads', 'job_heads.id', '=', 'job_details.jasa_id')
        ->orwhere('job_heads.status', 'like', 'Job Request Approved By' .'%')
        ->orwhere('job_heads.status', 'like', 'Job Request Completed')
        ->whereBetween('job_heads.created_at', [$start_date, $end_date])
        ->where('job_details.cabang', $branch)->orderBy('job_heads.updated_at', 'desc')->get();

        // dd($jobs);
        return view('purchasing.purchasingReportJOPage', compact('default_branch', 'str_month', 'jobs'));
    }

    public function dropjobDetail(Request $request, JobDetails $jobDetail){
        // Drop the order detail by changing the status
        JobDetails::where('id', $jobDetail -> id)->update([
            'job_State' => 'Rejected',
        ]);

        // Also update the total price on the order head
        JobHead::where('id', $jobDetail -> jasa_id)->decrement('totalPriceBeforeCalculation', $jobDetail -> totalHargaJob);

        // Redirect
        return redirect()->back()->with('dropStatus', $jobDetail -> id);
    }

    public function undoDropjobDetail(JobHead $JobHeads, JobDetails $jobDetail ){
        // Undo the drop item
        JobDetails::where('id', $jobDetail -> id)->update([
            'job_State' => 'Accepted',
        ]);

        // Increment back the total price from the deleted item
        JobHead::where('id', $JobHeads -> id)->increment('totalPriceBeforeCalculation', $jobDetail -> totalHargaJob);

        // Redirect
        return redirect()->back()->with('status', 'Updated Succesfully');
    }

    public function JobRequestList_branch($branch){
        $default_branch = $branch;
        // $default_branch = $request -> input_default_branch;

        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic');
        })->where('cabang', 'like', $default_branch)->pluck('users.id');

        if(request('search')){
            $JobRequestHeads = JobHead::with('user')->where(function($query){
                    $query->where('status', 'like', '%'. request('search') .'%')
                    ->orWhere( 'Headjasa_id', 'like', '%'. request('search') .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(6);
        }else{
            $JobRequestHeads = JobHead::where('cabang', 'like',  $default_branch)
            ->where('status', 'like', 'Job Request Approved By'.'%')
            ->orwhere('status', 'like', 'Job Request Rejected By Purchasing')
            ->orwhere('status', 'like', 'Job Request Completed')
            ->whereYear('created_at', date('Y'))->latest()->paginate(6)->withQueryString();
        }
        
        $job_id = $JobRequestHeads->pluck('id');
        $jobDetails = JobDetails::whereIn('jasa_id', $job_id)->get();
          // Get all the suppliers
        $suppliers = Supplier::latest()->get();

         // Count the completed & in progress job Requests
         $completed = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request Completed')
            ->orWhere('status', 'like', 'Job Request Rejected By Purchasing');
        })->whereYear('created_at', date('Y'))->count();
        
        $in_progress = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request In Progress By'. '%')   
            ->orWhere('status', 'like', 'Job Request Approved By' . '%');
        })->whereYear('created_at', date('Y'))->count();

        return view('purchasing.purchasingReviewJobRequestList', compact('JobRequestHeads','jobDetails' , 'suppliers', 'completed', 'in_progress', 'default_branch'));
    }

    public function editPriceJobDetail(Request $request, JobHead $JobHeads, JobDetails $jobDetail){
        // Validate incoming request
        $request->validate([
            'HargaJob' => 'required|numeric|between:0,99999999999.99',
            'supplier' => 'required',
        ]);
        
        // Calculate the subtotal (qty * price)
        $subTotal = $jobDetail -> quantity * $request -> HargaJob;
        
        // dd($subTotal);
        // Then update the individual total item price for order details
       JobDetails::where('id', $jobDetail->id)->update([
            'HargaJob' => $request->HargaJob,
            'totalHargaJob' => $subTotal,
            'supplier' => $request->supplier,
        ]);
        
        // Then re-calculate the total price
        $totalPriceBeforeCalculation = JobDetails::where('jasa_id', $JobHeads -> id)->sum('totalHargaJob');

        // Then update the total price on the order head
        JobHead::find($JobHeads -> id)->update([
            // 'totalPrice' => $totalPriceBeforeCalculation,
            'totalPriceBeforeCalculation' => $totalPriceBeforeCalculation
        ]);

        // Redirect
        return redirect()->back()->with('status', 'Price Updated Successfully');
    }

    public function ApproveJobOrder(Request $request , JobHead $checkJobStatus) {
        $JobHeads = JobHead::find($checkJobStatus->id);
        $job_id = $checkJobStatus->pluck('id');
        $jobhead_id = $checkJobStatus->Headjasa_id;

        // dd($jobhead_id);

        //JoB order status
        if($checkJobStatus->Headjasa_tracker_id == 4){
            // return redirect('/purchasing/dashboard/' . $default_branch)->with('errorB', 'Order Already Been Processed');
            return redirect()->back()->with('failed', 'Order Already Been Processed');
        }

        // Set default branch
        $default_branch = $checkJobStatus -> cabang;
        // Validate the request form
        $request -> validate([
            'boatName' => 'required',
            'noJr' => 'required',
            'noJo' => 'required',
            'invoiceAddress' => 'required',
            'ppn' => 'required|numeric|in:0,10,11',
            'discount' => 'nullable|numeric|min:0|max:100',
            'totalPrice' => 'required',
            'itemType' => 'required|in:Barang,Jasa'
        ]);

       if(!$request -> discount){
           $updatedDiscount = 0;
       }else{
           $updatedDiscount = $request -> discount;
       }

        // calculate discount first, then PPN
        $updatedPriceAfterDiscount = $checkJobStatus -> totalPriceBeforeCalculation - ($checkJobStatus -> totalPriceBeforeCalculation * $request -> discount / 100);

        // calculate PPN
        $updatedPriceAfterPPN = $updatedPriceAfterDiscount + ($updatedPriceAfterDiscount * $request -> ppn / 100);
        
        JobHead::where('Headjasa_id', '=' ,$jobhead_id)
        ->whereYear('created_at', date('Y'))
        ->update([
            'check_by' => Auth::user()->name ,
            'Headjasa_tracker_id' => 4 ,
            'status' => 'Job Request Approved By Purchasing',
            'boatName' => $request->boatName,
            'Approved_by' => Auth::user()->name,
            'JODate' => date('Y/m/d'),
            'JO_id' => $request -> noJo,
            'invoiceAddress' => $request -> invoiceAddress,
            'ppn' => $request -> ppn,
            'discount' => $updatedDiscount,
            'totalPrice' => $updatedPriceAfterPPN,
        ]);
        // dd($request);
        
        return redirect('/purchasing/Job_Request_List')->with('success', 'Job Request Approved.');
        
    }

    public function RejectJobOrder(Request $request , JobHead $checkJobStatus) {
        $job_id = $checkJobStatus->id;
        $jobheadorder_id = $checkJobStatus->pluck('Headjasa_id');
        $jr_id = $request->jobhead_id;
        $date = date('Y');
       
        $request->validate([
            'reasonbox' => 'required',
        ]);

        if($checkJobStatus -> Headjasa_tracker_id == 4){
            return redirect('/purchasing/Job_Request_List')->with('failed', 'Order Already Been Processed');
        }

        JobHead::where('id',$job_id)
        ->where('Headjasa_id',$jr_id)
        ->whereYear('created_at', $date)
        ->update([
            'status' => 'Job Request Rejected By Purchasing',
            'Headjasa_tracker_id' => 4 ,
            'reason' => $request-> reasonbox
        ]);
        
        // dd($job);
        
        return redirect('/purchasing/Job_Request_List')->with('failed', 'Job Request Rejected.');  
    }

    public function FinalizeJobOrder(Request $request , JobHead $checkJobStatus) {
        $job_id = $checkJobStatus->id;
        $jobheadorder_id = $checkJobStatus->pluck('Headjasa_id');
        $jr_id = $request->jobhead_id;
        $date = date('Y');
       

        if($checkJobStatus -> Headjasa_tracker_id == 10){
            return redirect('/purchasing/Job_Request_List')->with('failed', 'Order Already Been Processed');
        }

        JobHead::where('id',$job_id)
        ->where('Headjasa_id',$jr_id)
        ->whereYear('created_at', $date)
        ->update([
            'status' => 'Job Request Completed',
            'Headjasa_tracker_id' => 10 ,
        ]);
        
        // dd($job);
        
        return redirect('/purchasing/Job_Request_List')->with('failed', 'Job Request Finalized.');  
    }

    public function downloadJOreport(Excel $excel,$branch) {
        $date = Carbon::now();
        $monthName = $date->format('F');
        // $id = $JobRequestHeads -> id;
        return $excel -> download(new JO_Report($branch), 'Report-JO-' . $monthName . '.xlsx');
    }

    public function downloadJOPDF(JobHead $JobRequestHeads) {
        $date = Carbon::now();
        $monthName = $date->format('F');
        $id = $JobRequestHeads -> id;
        
        return (new JOExport($id))->download('JO-' .$JobRequestHeads -> Headjasa_id . '-'. $monthName .'.pdf' , \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function downloadJOEXCEL(Excel $excel , JobHead $JobRequestHeads) {
        $date = Carbon::now();
        $monthName = $date->format('F');
        $id = $JobRequestHeads -> id;
        return $excel -> download(new JOExport($id), 'JO-' . $JobRequestHeads -> Headjasa_id . '-' . $monthName . '.xlsx');
    }

}
