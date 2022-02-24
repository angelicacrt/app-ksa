<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderHead;
use App\Models\OrderDetail;
use App\Models\OrderDo;
use App\Models\User;
use App\Models\ApList;
use App\Models\ApListDetail;
use App\Models\Supplier;
// use App\Models\Tug;
use App\Models\Item;
use Illuminate\Http\Request;

class DashboardAjaxController extends Controller
{
    public function crewRefreshDashboard(Request $request){
        if($request->ajax()){
            // Get all the order within the logged in user within 6 month
            $orderHeads = OrderHead::with('user')->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->latest()->paginate(7);

            // Get the orderDetail from orders_id within the orderHead table 
            // $order_id = OrderHead::where('user_id', Auth::user()->id)->pluck('order_id');
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            return view('crew.crewDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
        }
    }

    public function crewRefreshDashboardCompleted(Request $request){
        if($request -> ajax()){
            // Get all the order within the logged in user within 6 month
            $orderHeads = OrderHead::with('user')->where(function($query){
                $query->where('status', 'like', 'Request Completed (Crew)')
                ->orWhere('status', 'like', 'Request Rejected By Logistic');
            })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->latest()->paginate(10);

            // Get the orderDetail from orders_id within the orderHead table 
            $order_id = OrderHead::where('user_id', Auth::user()->id)->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
            
            return view('crew.crewDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
        }
    }

    public function crewRefreshDashboardInProgress(Request $request){
        if($request -> ajax()){
            // Get all the order within the logged in user within 6 month
            $orderHeads = OrderHead::with('user')->where(function($query){
                $query->where('status', 'like', 'Request In Progress By Logistic')
                ->orWhere('status', 'like', 'Items Ready')
                ->orWhere('status', 'like', 'On Delivery');
            })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->paginate(10);

            // Get the orderDetail from orders_id within the orderHead table 
            $order_id = OrderHead::where('user_id', Auth::user()->id)->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            return view('crew.crewDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
        }
    }

    public function logisticRefreshDashboard(Request $request){
        if($request -> ajax()){
            try {
                // Search functonality
                if($request -> searchData != ''){
                    $search = $request -> searchData;

                    $orderHeads = OrderHead::with('user')->where(function($query) use ($search) {
                        $query->where('status', 'like', '%'. $search .'%')
                        ->orWhere( 'order_id', 'like', '%'. $search .'%');
                    })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7)->withQueryString();
                    //->whereBetween('created_at', [$start_date, $end_date])
                }else{
                    $orderHeads = OrderHead::with('user')->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7)->withQueryString();
                }            

                // Get all the order detail
                $order_id = $orderHeads->pluck('id');
                $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

                return view('logistic.logisticDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            } catch (\Throwable $th) {
                $orderHeads = null;
                $orderDetails = null;

                return view('logistic.logisticDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }
        }
    }

    public function logisticRefreshDashboardCompleted(Request $request){
        if($request -> ajax()){
            try {
                if($request -> searchData != ''){
                    $search = $request -> searchData;

                    // Search functonality
                    $orderHeads = OrderHead::with('user')->where(function($query) use($search) {
                        $query->where('status', 'like', '%'.  $search .'%')
                        ->orWhere( 'order_id', 'like', '%'.  $search .'%');
                    })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7)->withQueryString();
                    
                    // Get all the order detail
                    $order_id = OrderHead::whereYear('created_at', date('Y'))->pluck('id');
                    $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
        
                    return view('logistic.logisticDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
                }else{
                    $orderHeads = OrderHead::with('user')->where(function($query){
                        $query->where('status', 'like', '%' . 'Completed' . '%')
                        ->orWhere('status', 'like', '%' . 'Rejected' . '%');
                    })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);
            
                    // Get all the order detail
                    $order_id = OrderHead::whereYear('created_at', date('Y'))->pluck('id');
                    $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
            
        
                    return view('logistic.logisticDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
                }
            } catch (\Throwable $th) {
                $orderHeads = null;
                $orderDetails = null;

                return view('logistic.logisticDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }
        }
    }

    public function logisticRefreshDashboardInProgress(Request $request){
        if($request -> ajax()){
            try {
                if($request -> searchData != ''){
                    $search = $request -> searchData;

                    // Search functonality
                    $orderHeads = OrderHead::with('user')->where(function($query) use($search) {
                       $query->where('status', 'like', '%'. $search .'%')
                       ->orWhere( 'order_id', 'like', '%'. $search .'%');
                   })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7)->withQueryString();
                   
                   // Get all the order detail
                   $order_id = OrderHead::whereYear('created_at', date('Y'))->pluck('id');
                   $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
       
                   return view('logistic.logisticDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
               }else{
                   // Find all of the order that is "in progress" state
                   $orderHeads = OrderHead::with('user')->where(function($query){
                       $query->where('status', 'like', '%' . 'In Progress' . '%')
                       ->orWhere('status', 'like', 'Items Ready')
                       ->orWhere('status', 'like', 'On Delivery')
                       ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                       ->orWhere('status', 'like', '%' . 'Revised' . '%')
                       ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                       ->orWhere('status', 'like', '%' . 'Delivered' . '%');
                   })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);
       
                   // Then get all the order detail
                   $order_id = OrderHead::whereYear('created_at', date('Y'))->pluck('id');
                   $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
       
                   return view('logistic.logisticDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
               }
            } catch (\Throwable $th) {
                $orderHeads = null;
                $orderDetails = null;

                return view('logistic.logisticDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }
        }
    }

    public function logisticRefreshOngoingDOPage(Request $request){
        if($request -> ajax()){
            // Get all the DO from the last 6 month
            $ongoingOrders = OrderDo::with(['item_requested', 'user'])->where('fromCabang', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);

            return view('logistic.logisticOngoingDOComponent', compact('ongoingOrders'))->render();
        }
    }

    public function supervisorRefreshDashboard(Request $request){
        if($request -> ajax()){
            // Find order from logistic role, then they can approve and send it to the purchasing role
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');

            if($request -> searchData != ''){
                $search = $request -> searchData;

                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query) use($search) {
                    $query->where('status', 'like', '%'. $search .'%')
                    ->orWhere('order_id', 'like', '%'. $search .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(7);
            }else{
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->whereYear('created_at', date('Y'))->latest()->paginate(7)->withQueryString();
            }
            
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
            
            return view('supervisor.supervisorDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
        }
    }

    public function supervisorRefreshDashboardCompleted(Request $request){
        if($request -> ajax()){
            // Find order from logistic role, then they can approve and send it to the purchasing role
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');

            // Then find all the order details from the orderHeads
            $order_id = OrderHead::whereIn('user_id', $users)->whereYear('created_at', date('Y'))->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            if($request -> searchData != ''){
                $search = $request -> searchData;

                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query) use($search) {
                    $query->where('status', 'like', '%'. $search .'%')
                    ->orWhere('order_id', 'like', '%'. $search .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(7);

                return view('supervisor.supervisorDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }else{
                $orderHeads = OrderHead::where(function($query){
                    $query->where('status', 'like', 'Order Completed (Logistic)')
                    ->orWhere('status', 'like', 'Order Rejected By Supervisor')
                    ->orWhere('status', 'like', 'Order Rejected By Purchasing');
                })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);
        
                return view('supervisor.supervisorDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }
        }
    }
    
    public function supervisorRefreshDashboardInProgress(Request $request){
        if($request -> ajax()){
            // Find order from logistic role, then they can approve and send it to the purchasing role
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');

            // Then find all the order details from the orderHeads
            $order_id = OrderHead::whereIn('user_id', $users)->whereYear('created_at', date('Y'))->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            if($request -> searchData != ''){
                $search = $request -> searchData;

                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query) use($search) {
                    $query->where('status', 'like', '%'. $search .'%')
                    ->orWhere('order_id', 'like', '%'. $search .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(7);

                return view('supervisor.supervisorDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }else{
                $orderHeads =  OrderHead::where(function($query){
                    $query->where('status', 'like', '%' . 'In Progress By Supervisor' . '%')
                    ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                    ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                    ->orWhere('status', 'like', '%' . 'Revised' . '%')
                    ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                    ->orWhere('status', 'like', '%' . 'Delivered By Supplier' . '%');
                })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);

                return view('supervisor.supervisorDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
        }
        }
    }

    public function supervisorRefreshItemStockPage(Request $request){
        if($request -> ajax()){
            // Check the stocks of all branches
            if($request -> searchData != '' && $request -> default_branch !== 'All'){
                $search = $request -> searchData;
                
                $items = Item::where(function($query) use ($search) {
                    $query->where('itemName', 'like', '%' . $search . '%')
                    ->orWhere('codeMasterItem', 'like', '%' . $search . '%');
                })->where('cabang', $request -> default_branch)->Paginate(10);

                return view('supervisor.supervisorItemStockComponent', compact('items'))->render();
            }else{
                $items = Item::orderBy('cabang')->Paginate(10);

                return view('supervisor.supervisorItemStockComponent', compact('items'))->render();
            }
        }
    }

    public function supervisorRefreshApprovalDO(Request $request){
        if($request -> ajax()){
            // Find all of the ongoing DO from the requested branch OR the destination branch
            $ongoingOrders = OrderDo::with(['item_requested', 'user'])->where(function($query){
                $query->where('fromCabang', Auth::user()->cabang)
                ->orWhere('toCabang', Auth::user()->cabang);
            })->whereYear('created_at', date('Y'))->latest()->paginate(7);
    
            return view('supervisor.supervisorApprovalDOContent', compact('ongoingOrders'))->render();
        }
    }
    
    public function purchasingRefreshDashboard(Request $request){
        if($request -> ajax()){
            $default_branch = $request -> default_branch;

            // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', $default_branch)->pluck('users.id');
            
            if($request -> searchData != ''){
                $search = $request -> searchData;

                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query) use($search) {
                    $query->where('status', 'like', '%'. $search .'%')
                    ->orWhere('order_id', 'like', '%'. $search .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(6);
            }else{
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6)->withQueryString();
            }

            // Then find all the order details from the orderHeads
            // $order_id = OrderHead::whereIn('user_id', $users)->where('created_at', '>=', Carbon::now()->subDays(30))->pluck('order_id');
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            return view('purchasing.purchasingDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
        }
    }

    public function purchasingRefreshDashboardCompleted(Request $request){
        if($request -> ajax()){
            $default_branch = $request -> default_branch;

            // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', $default_branch)->pluck('users.id');


            if($request -> searchData != ''){
                $search = $request -> searchData;

                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query) use($search) {
                    $query->where('status', 'like', '%'. $search .'%')
                    ->orWhere('order_id', 'like', '%'. $search .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(6);
                
                // Then find all the order details from the orderHeads
                $order_id = $orderHeads->pluck('id');
                $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

                return view('purchasing.purchasingDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }else{
                $orderHeads = OrderHead::where(function($query){
                    $query->where('status', 'like', 'Order Completed (Logistic)')
                    ->orWhere('status', 'like', 'Order Rejected By Supervisor')
                    ->orWhere('status', 'like', 'Order Rejected By Purchasing');
                })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6);
        
                // Then find all the order details from the orderHeads
                $order_id = $orderHeads->pluck('id');
                $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
        
                return view('purchasing.purchasingDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }
        }
    }

    public function purchasingRefreshDashboardInProgress(Request $request){
        if($request -> ajax()){
            $default_branch = $request -> default_branch;

            // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', $default_branch)->pluck('users.id');

            if(request('search')){
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query){
                    $query->where('status', 'like', '%'. request('search') .'%')
                    ->orWhere('order_id', 'like', '%'. request('search') .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(6);

                // Then find all the order details from the orderHeads
                $order_id = $orderHeads->pluck('id');
                $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

                return view('purchasing.purchasingDashboardComponent', compact('orderHeads', 'orderDetails'));
            }else{
                $orderHeads =  OrderHead::where(function($query){
                    $query->where('status', 'like', 'Order In Progress By Supervisor')
                    ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                    ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                    ->orWhere('status', 'like', '%' . 'Revised' . '%')
                    ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                    ->orWhere('status', 'like', 'Item Delivered By Supplier');
                })->whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6);
        
                // Then find all the order details from the orderHeads
                $order_id = $orderHeads->pluck('id');
                $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

                return view('purchasing.purchasingDashboardComponent', compact('orderHeads', 'orderDetails'));
            }
        }
    }

    public function purchasingManagerRefreshDashboard(Request $request){
        if($request -> ajax()){
            $default_branch = $request -> default_branch;

            // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', $default_branch)->pluck('users.id');

            if($request -> searchData != ''){
                $search = $request -> searchData;

                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query) use($search) {
                    $query->where('status', 'like', '%'. $search .'%')
                    ->orWhere('order_id', 'like', '%'. $search .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(6);
            }else{
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6)->withQueryString();
            }

            // Then find all the order details from the orderHeads
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            return view('purchasingManager.purchasingManagerDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
        }
    }

    public function purchasingManagerRefreshDashboardCompleted(Request $request){
        if($request -> ajax()){
            $default_branch = $request -> default_branch;

            // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', $default_branch)->pluck('users.id');

            if($request -> searchData != ''){
                $search = $request -> searchData;

                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query) use($search) {
                    $query->where('status', 'like', '%'. $search .'%')
                    ->orWhere('order_id', 'like', '%'. $search .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(6);
                
                // Then find all the order details from the orderHeads
                $order_id = $orderHeads->pluck('id');
                $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

                return view('purchasingManager.purchasingManagerDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }else{
                $orderHeads = OrderHead::where(function($query){
                    $query->where('status', 'like', 'Order Completed (Logistic)')
                    ->orWhere('status', 'like', 'Order Rejected By Supervisor')
                    ->orWhere('status', 'like', 'Order Rejected By Purchasing');
                })->whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6);
        
                // Then find all the order details from the orderHeads
                $order_id = $orderHeads->pluck('id');
                $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
        
                return view('purchasingManager.purchasingManagerDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }
        }
    }
    
    public function purchasingManagerRefreshDashboardInProgress(Request $request){
        if($request -> ajax()){
            $default_branch = $request -> default_branch;

            // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', $default_branch)->pluck('users.id');

            if($request -> searchData != ''){
                $search = $request -> searchData;
                
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query) use($search) {
                    $query->where('status', 'like', '%'. $search .'%')
                    ->orWhere('order_id', 'like', '%'. $search .'%');
                })->whereIn('user_id', $users)->whereYear('created_at', date('Y'))->latest()->paginate(6);

                // Then find all the order details from the orderHeads
                $order_id = $orderHeads->pluck('id');
                $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

                return view('purchasingManager.purchasingManagerDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }else{
                $orderHeads =  OrderHead::where(function($query){
                    $query->where('status', 'like', 'Order In Progress By Supervisor')
                    ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                    ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                    ->orWhere('status', 'like', '%' . 'Revised' . '%')
                    ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                    ->orWhere('status', 'like', 'Item Delivered By Supplier');
                })->whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6);
        
                // Then find all the order details from the orderHeads
                $order_id = $orderHeads->pluck('id');
                $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
        
                return view('purchasingManager.purchasingManagerDashboardComponent', compact('orderHeads', 'orderDetails'))->render();
            }
        }
    }

    public function purchasingManagerRefreshItemStockPage(Request $request){
        if($request -> ajax()){
            // Check the stocks of all branches
            if($request -> searchData != '' && $request -> default_branch !== 'All'){
                $search = $request -> searchData;
                
                $items = Item::where(function($query) use ($search) {
                    $query->where('itemName', 'like', '%' . $search . '%')
                    ->orWhere('codeMasterItem', 'like', '%' . $search . '%');
                })->where('cabang', $request -> default_branch)->Paginate(10);

                return view('purchasingManager.purchasingManagerItemStockComponent', compact('items'))->render();
            }else{
                $items = Item::orderBy('cabang')->Paginate(10);

                return view('purchasingManager.purchasingManagerItemStockComponent', compact('items'))->render();
            }
        }
    }

    public function purchasingManagerRefreshFormAp(Request $request){
        if($request -> ajax()){
            $default_branch = $request -> default_branch;

            // Show the form AP page
            $apList = ApList::with('orderHead')->where('cabang', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(7);
            
            // Get the AP List Detail from the selected AP List
            $apListId = $apList -> pluck('id');
            $apListDetail = ApListDetail::with('apList')->whereIn('aplist_id', $apListId)->latest()->get()->unique('aplist_id');

            $check_ap_in_array = $apListDetail -> pluck('aplist_id') -> toArray();
            
            // Get all the supplier
            $suppliers = Supplier::latest()->get();

            return view('purchasingManager.purchasingManagerFormApComponent', compact('apList', 'suppliers', 'apListDetail', 'check_ap_in_array'))->render();
        }
    }

    public function adminPurchasingRefreshFormAp(Request $request){
        if($request -> ajax()){
            $default_branch = $request -> default_branch;

            // Show the form AP page
            $apList = ApList::with('orderHead')->where('cabang', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(7);
            
            // Get all the supplier
            $suppliers = Supplier::latest()->get();

            return view('adminPurchasing.adminPurchasingFormApComponent', compact('apList', 'suppliers'))->render();
        }
    }

    public function adminPurchasingRefreshReportAp(Request $request){
        if($request -> ajax()){
            // Basically the report is created per 3 months, so we divide it into 4 reports
            // Base on current month, then we classified what period is the report
            $month_now = (int)(date('m'));

            if($month_now <= 3){
                $start_date = date('Y-01-01');
                $end_date = date('Y-03-31');
            }elseif($month_now > 3 && $month_now <= 6){
                $start_date = date('Y-04-01');
                $end_date = date('Y-06-30');
            }elseif($month_now > 6 && $month_now <= 9){
                $start_date = date('Y-07-01');
                $end_date = date('Y-09-30');
            }else{
                $start_date = date('Y-10-01');
                $end_date = date('Y-12-31');
            }

            // Helper var
            $default_branch = $request -> default_branch;

            // Find all the AP within the 3 months period
            $apList = ApList::with('orderHead')->where('cabang', 'like', $default_branch)->join('ap_list_details', 'ap_list_details.aplist_id', '=', 'ap_lists.id')->whereBetween('ap_lists.created_at', [$start_date, $end_date])->orderBy('ap_lists.created_at', 'desc')->get();

            return view('adminPurchasing.adminPurchasingReportApPageComponent', compact('apList'))->render();
        }
    }
}
