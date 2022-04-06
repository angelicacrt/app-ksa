<?php
namespace App\Http\Controllers;

use App\Models\ItemBelowStock;
use App\Models\User;
use App\Models\OrderHead;
use App\Models\OrderDetail;
use App\Models\Supplier;
use App\Models\ApList;
use App\Models\OperationalBoatData;
use App\Models\Barge;
use App\Models\Tug;

use App\Models\JobHead;
use App\Models\JobDetails;
use App\Models\documents;
use App\Models\documentrpk;
use App\Models\documentberau;
use App\Models\documentsamarinda;
use App\Models\documentbanjarmasin;
use App\Models\documentJakarta;
use App\Models\spgrfile;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Storage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request){
        if(Auth::user()->hasRole('crew')){
            // Get all the order within the logged in user within 6 month
            $orderHeads = OrderHead::with('user')->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->latest()->paginate(7);
            
            // Get the orderDetail from orders_id within the orderHead table 
            // $order_id = OrderHead::where('user_id', Auth::user()->id)->pluck('order_id');
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
            
            // Count the completed & in progress order
            $completed = OrderHead::where(function($query){
                $query->where('status', 'like', 'Request Completed (Crew)')
                ->orWhere('status', 'like', 'Request Rejected By Logistic');
            })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->count();
            
            $in_progress = OrderHead::where(function($query){
                $query->where('status', 'like', 'Request In Progress By Logistic')
                ->orWhere('status', 'like', 'Items Ready')
                ->orWhere('status', 'like', 'On Delivery');
            })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->count();

            return view('crew.crewDashboard', compact('orderHeads','orderDetails', 'completed', 'in_progress'));

        }elseif(Auth::user()->hasRole('logistic')){
            // Search functonality
            if(request('search')){
                $orderHeads = OrderHead::with('user')->where(function($query){
                    $query->where('status', 'like', '%'. request('search') .'%')
                    ->orWhere( 'order_id', 'like', '%'. request('search') .'%');
                })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7)->withQueryString(); 
            }else{
                $orderHeads = OrderHead::with('user')->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7)->withQueryString();
            }

            // Get all the order detail
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            // Count the completed & in progress order
            $completed = OrderHead::where(function($query){
                $query->where('status', 'like', '%' . 'Completed' . '%')
                ->orWhere('status', 'like', '%' . 'Rejected' . '%');
            })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->count();
            
            $in_progress = OrderHead::where(function($query){
                $query->where('status', 'like', '%' . 'In Progress' . '%')
                ->orWhere('status', 'like', 'Items Ready')
                ->orWhere('status', 'like', 'On Delivery')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'like', '%' . 'Delivered By Supplier' . '%');
            })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->count();

            $items_below_stock = $this -> checkStock();

            return view('logistic.logisticDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'items_below_stock'));
            
        }elseif(Auth::user()->hasRole('supervisorLogistic') or Auth::user()->hasRole('supervisorLogisticMaster')){
            // Find order from logistic role, then they can approve and send it to the purchasing role
            // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where('role_user.role_id' , '=', '3')->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');

            if(request('search')){
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query){
                    $query->where('status', 'like', '%'. request('search') .'%')
                    ->orWhere('order_id', 'like', '%'. request('search') .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(7);
            }else{
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->whereYear('created_at', date('Y'))->latest()->paginate(7)->withQueryString();
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
            })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->count();

            $in_progress = OrderHead::where(function($query){
                $query->where('status', 'like', 'In Progress By Supervisor')
                ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Delivered By Supplier' . '%');
            })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->count();

            $items_below_stock = $this -> checkStock();

            return view('supervisor.supervisorDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'items_below_stock'));

        }elseif(Auth::user()->hasRole('purchasing')){
            $default_branch = 'Jakarta';

            // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
            // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where('role_user.role_id' , '=', '3')->where('cabang', 'like', $default_branch)->pluck('users.id');
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', $default_branch)->pluck('users.id');
            
            if(request('search')){
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query){
                    $query->where('status', 'like', '%'. request('search') .'%')
                    ->orWhere('order_id', 'like', '%'. request('search') .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(6);
            }else{
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6)->withQueryString();
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
                // ->orWhere('status', 'like', 'Order In Progress By Purchasing')
                ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'like', 'Item Delivered By Supplier');
            })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->count();

            // Get all the suppliers
            $suppliers = Supplier::latest()->get();

            return view('purchasing.purchasingDashboard', compact('orderHeads', 'orderDetails', 'suppliers', 'completed', 'in_progress', 'default_branch'));

        }elseif(Auth::user()->hasRole('adminPurchasing')){
            // Show the form AP page
            $apList = ApList::with('orderHead')->where('cabang', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);
            
            // Get all the supplier
            $suppliers = Supplier::latest()->get();

            // Default branch is Jakarta
            $default_branch = 'Jakarta';

            return view('adminPurchasing.adminPurchasingFormAp', compact('apList', 'default_branch', 'suppliers'));
        }elseif(Auth::user()->hasRole('purchasingManager')){
            // Default branch is Jakarta, first time login
            $default_branch = 'Jakarta';

            // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
            // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where('role_user.role_id' , '=', '3')->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');
            $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', $default_branch)->pluck('users.id');

            if(request('search')){
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query){
                    $query->where('status', 'like', '%'. request('search') .'%')
                    ->orWhere('order_id', 'like', '%'. request('search') .'%');
                })->whereYear('created_at', date('Y'))->latest()->paginate(6);
            }else{
                $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6)->withQueryString();
            }

            // Then find all the order details from the orderHeads
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

            // Get all suppliers
            $suppliers = Supplier::latest()->get();

            return view('purchasingManager.purchasingManagerDashboard', compact('orderHeads', 'orderDetails', 'suppliers', 'default_branch', 'completed', 'in_progress'));
        }elseif(Auth::user()->hasRole('adminOperational')){

            // Sum The DAYS Of Each Condition, Not The Count Of The Ship
            $docking_days = OperationalBoatData::where('status', 'On Going')->sum('dockingDays');
            $standby_docking_days = OperationalBoatData::where('status', 'On Going')->sum('standbyDockingDays');
            $standby_days = OperationalBoatData::where('status', 'On Going')->sum('standbyDays');
            $grounded_barge_days = OperationalBoatData::where('status', 'On Going')->sum('groundedBargeDays');
            $repair_days = OperationalBoatData::where('status', 'On Going')->sum('repairDays');
            $waiting_schedule_days = OperationalBoatData::where('status', 'On Going')->sum('waitingScheduleDays');

            // Ship Count
            $on_sailing_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'On Sailing')->count();
            $loading_activity_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Loading Activity')->count();
            $discharge_activity_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Discharge Activity')->count();
            $standby_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Standby')->count();
            $repair_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Repair')->count();
            $grounded_barge_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Grounded Barge')->count();
            $waiting_schedule_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Waiting Schedule')->count();

            // Get Each Amount Of Tug & Barges (Non Operational Only)
            $tug_docking_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Docking')->count();
            $barge_docking_count = OperationalBoatData::where('status', 'On Going')->whereNotNull('bargeName')->where('condition', 'Docking')->count();

            $tug_standby_docking_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Standby Docking')->count();
            $barge_standby_docking_count = OperationalBoatData::where('status', 'On Going')->whereNotNull('bargeName')->where('condition', 'Standby Docking')->count();

            // formula => Total lost time : docking + standby docking + standby + grounded barge + repair + waiting schedule
            $total_lost_time = $docking_days + $standby_docking_days + $standby_days + $grounded_barge_days + $repair_days + $waiting_schedule_days;

            // formula => AKTIF : 
            //  (31*total barge)-Total lost time:
            $total_barges = Barge::count();
            $aktif = (31 * $total_barges) - $total_lost_time;
            
            // formula => percentage ship's activity :
            // Aktif / (31*total barge) * 100
            $percentage_ship_activity = 0;
            if($total_barges > 0){
                $percentage_ship_activity = $aktif / (31 * $total_barges) * 100;
            }

            // Total fleets => all tugs + barges
            $total_tugs = Tug::count();

            $total_fleets = $total_tugs + $total_barges;

            return view('adminOperational.adminOperationalDashboard', compact('total_barges', 'total_tugs', 'on_sailing_count', 'loading_activity_count', 'discharge_activity_count', 'standby_count', 'repair_count', 'tug_docking_count', 'barge_docking_count', 'tug_standby_docking_count', 'barge_standby_docking_count', 'grounded_barge_count', 'waiting_schedule_count', 'percentage_ship_activity', 'total_lost_time'));
        }elseif(Auth::user()->hasRole('StaffOperasional')){
             // Sum The DAYS Of Each Condition, Not The Count Of The Ship
             $docking_days = OperationalBoatData::where('status', 'On Going')->sum('dockingDays');
             $standby_docking_days = OperationalBoatData::where('status', 'On Going')->sum('standbyDockingDays');
             $standby_days = OperationalBoatData::where('status', 'On Going')->sum('standbyDays');
             $grounded_barge_days = OperationalBoatData::where('status', 'On Going')->sum('groundedBargeDays');
             $repair_days = OperationalBoatData::where('status', 'On Going')->sum('repairDays');
             $waiting_schedule_days = OperationalBoatData::where('status', 'On Going')->sum('waitingScheduleDays');
 
             // Ship Count
             $on_sailing_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'On Sailing')->count();
             $loading_activity_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Loading Activity')->count();
             $discharge_activity_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Discharge Activity')->count();
             $standby_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Standby')->count();
             $repair_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Repair')->count();
             $grounded_barge_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Grounded Barge')->count();
             $waiting_schedule_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Waiting Schedule')->count();
 
             // Get Each Amount Of Tug & Barges (Non Operational Only)
             $tug_docking_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Docking')->count();
             $barge_docking_count = OperationalBoatData::where('status', 'On Going')->whereNotNull('bargeName')->where('condition', 'Docking')->count();
 
             $tug_standby_docking_count = OperationalBoatData::where('status', 'On Going')->where('condition', 'Standby Docking')->count();
             $barge_standby_docking_count = OperationalBoatData::where('status', 'On Going')->whereNotNull('bargeName')->where('condition', 'Standby Docking')->count();
 
             // formula => Total lost time : docking + standby docking + standby + grounded barge + repair + waiting schedule
             $total_lost_time = $docking_days + $standby_docking_days + $standby_days + $grounded_barge_days + $repair_days + $waiting_schedule_days;
 
             // formula => AKTIF : 
             //  (31*total barge)-Total lost time:
             $total_barges = Barge::count();
             $aktif = (31 * $total_barges) - $total_lost_time;
             
             // formula => percentage ship's activity :
             // Aktif / (31*total barge) * 100
             $percentage_ship_activity = 0;
             if($total_barges > 0){
                 $percentage_ship_activity = $aktif / (31 * $total_barges) * 100;
             }
 
             // Total fleets => all tugs + barges
             $total_tugs = Tug::count();
 
             $total_fleets = $total_tugs + $total_barges;
 
             return view('adminOperational.adminOperationalDashboard', compact('total_barges', 'total_tugs', 'on_sailing_count', 'loading_activity_count', 'discharge_activity_count', 'standby_count', 'repair_count', 'tug_docking_count', 'barge_docking_count', 'tug_standby_docking_count', 'barge_standby_docking_count', 'grounded_barge_count', 'waiting_schedule_count', 'percentage_ship_activity', 'total_lost_time'));
        }elseif(Auth::user()->hasRole('picSite')){
            $datetime = date('Y-m-d');
            $year = $request->created_at_Year;
            $month = $request->created_at_month;
            // Fund Request view ----------------------------------------------------------
                if($request->tipefile == 'DANA' && $request->type_upload == 'Fund_Req'){
                    if ($request->cabang == 'Babelan'){
                        // dd($month);
                        $filename = $request->viewdoc;
                        $kapal_id = $request->kapal_nama;
                        $result = $request->result;
                        $viewer = documents::whereDate('periode_akhir', '>=', $datetime)
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
                        $viewer = documentberau::whereDate('periode_akhir', '>=', $datetime)
                        ->whereNotNull ($filename)
                        ->where('upload_type','Fund_Req')
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
                        $viewer = documentbanjarmasin::whereDate('periode_akhir', '>=', $datetime)
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
                        $viewer = documentsamarinda::whereDate('periode_akhir', '>=', $datetime)
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
                        // dd($request);
                        $filename = $request->viewdoc;
                        $kapal_id = $request->kapal_nama;
                        $result = $request->result;
                        $viewer = documentJakarta::whereDate('periode_akhir', '>=', $datetime)
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
            // SearchBar ---------------------------------------------------------
                if(Auth::user()->cabang == "Bunati" or Auth::user()->cabang == 'Batu Licin'){
                    $docrpk = DB::table('rpkdocuments')->where('cabang', Auth::user()->cabang)->whereDate('periode_akhir', '>=', $datetime)->latest()->paginate(1);
                    return view('picsite.picDashboardRPK', compact('docrpk'));
                }
                // babelan search feature
                if(Auth::user()->cabang == "Babelan"){
                    //STILL IN PROGRESS 

                    // if(Auth::user()->cabang == "Babelan" and $request->filled('search_status') and $request->filled('search_kapal')){
                    //     for($i = 1; $i <= 24 ; $i++){
                    //         $angka = 'status'. $i;
                    //         $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    //         ->where($angka , 'Like', '%' . $request->search_status . '%')
                    //         ->whereDate('periode_akhir', '>=', $datetime)
                    //         ->orderBy('id', 'DESC')
                    //         ->latest()->get();
                    //         dd($angka);
                    //     }
                    //     for($j = 1; $j <= 7 ; $j++){
                    //         //get DocRPK Data as long as the periode_akhir and search based (column database)
                    //         $rpkangka = 'status'. $j;
                    //         $docrpk = DB::table('rpkdocuments')->where('cabang', Auth::user()->cabang)
                    //         ->where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    //         ->where($rpkangka ,'Like', '%' . $request->search_status . '%')
                    //         ->whereDate('periode_akhir', '>=', $datetime)
                    //         ->orderBy('id', 'DESC')
                    //         ->latest()->get();
                    //     }
                        
                    //     return view('picsite.picDashboard', compact('document','docrpk'));
                    // }
                    // elseif(Auth::user()->cabang == "Babelan" and $request->filled('search_status')){
                    //     for($i = 1; $i <= 24 ; $i++){
                    //         $angka = 'status'. $i;
                    //         $document = documents::where($angka,'Like', '%' . $request->search_status . '%')
                    //         ->whereDate('periode_akhir', '>=', $datetime)
                    //         ->orderBy('id', 'DESC')
                    //         ->latest()->get();
                    //     }
                    //     for($j = 1; $j <= 7 ; $j++){
                    //         //get DocRPK Data as long as the periode_akhir and search based (column database)
                    //         $rpkangka = 'status'. $j;
                    //         $docrpk = DB::table('rpkdocuments')->where('cabang', Auth::user()->cabang)
                    //         ->where($rpkangka ,'Like', '%' . $request->search_status . '%')
                    //         ->whereDate('periode_akhir', '>=', $datetime)
                    //         ->orderBy('id', 'DESC')
                    //         ->latest()->get();
                    //     }
                    //     return view('picsite.picDashboard', compact('document','docrpk'));
                    // }
                    //check if search-bar is filled or not
                    if(Auth::user()->cabang == "Babelan" and $request->filled('search_kapal')) {
                        //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
                        $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                        ->whereDate('periode_akhir', '>=', $datetime)
                        ->where('upload_type','Fund_Req')
                        ->orderBy('id', 'DESC')
                        ->latest()->paginate(10)->withQueryString();
        
                        return view('picsite.picDashboard', compact('document'));
                     }else{
                        $document = documents::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                        return view('picsite.picDashboard', compact('document'));
                    }
                }
                if(Auth::user()->cabang == "Berau"){
                    if (Auth::user()->cabang == "Berau" and $request->filled('search_kapal')) {
                        //berau search bar
                        $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                        ->whereDate('periode_akhir', '>=', $datetime)
                        ->where('upload_type','Fund_Req')
                        ->orderBy('id', 'DESC')
                        ->latest()->paginate(10)->withQueryString();
        
                        return view('picsite.picDashboard', compact('documentberau'));
                    }else{
                        $documentberau = documentberau::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                        return view('picsite.picDashboard', compact('documentberau'));
                    }
                }
                if(Auth::user()->cabang == "Banjarmasin"){
                    if ($request->filled('search_kapal')) {
                        //banjarmasin search bar
                        $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                        ->whereDate('periode_akhir', '>=', $datetime)
                        ->where('upload_type','Fund_Req')->where('cabang', Auth::user()->cabang)
                        ->orderBy('id', 'DESC')
                        ->latest()->paginate(10)->withQueryString();
        
                        return view('picsite.picDashboard', compact('documentbanjarmasin'));
                    }else{
                        $documentbanjarmasin = documentbanjarmasin::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->where('cabang', Auth::user()->cabang)->latest()->paginate(10)->withQueryString();
                        return view('picsite.picDashboard', compact('documentbanjarmasin'));
                    }
                }
                if(Auth::user()->cabang == "Samarinda" or Auth::user()->cabang == "Kendari" or Auth::user()->cabang == "Morosi"){
                    if ($request->filled('search_kapal')) {
                        //samarinda search bar
                        $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                        ->whereDate('periode_akhir', '>=', $datetime)
                        ->where('upload_type','Fund_Req')->where('cabang', Auth::user()->cabang)
                        ->orderBy('id', 'DESC')
                        ->latest()->paginate(10)->withQueryString();
        
                        return view('picsite.picDashboard', compact('documentsamarinda'));
                    }else{
                        $documentsamarinda = documentsamarinda::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->where('cabang', Auth::user()->cabang)->latest()->paginate(10)->withQueryString();
                        return view('picsite.picDashboard', compact('documentsamarinda'));
                    }
                }
                if(Auth::user()->cabang == "Jakarta"){
                    if (Auth::user()->cabang == "Jakarta" and $request->filled('search_kapal')) {
                        $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                        ->whereDate('periode_akhir', '>=', $datetime)
                        ->where('upload_type','Fund_Req')
                        ->orderBy('id', 'DESC')
                        ->latest()->paginate(10)->withQueryString();
    
                        return view('picsite.picDashboard', compact('documentjakarta'));
                    }else{
                        $documentjakarta = documentJakarta::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                        return view('picsite.picDashboard', compact('documentjakarta'));
                    }
                }
        }elseif(Auth::user()->hasRole('picAdmin')){
            $datetime = date('Y-m-d');
            $year = $request->created_at_Year;
            $month = $request->created_at_month;
            // Dana view ----------------------------------------------------------
            if($request->tipefile == 'DANA'){
                if ($request->cabang == 'Babelan'){
                    $filename = $request->viewdoc;
                    $kapal_id = $request->kapal_nama;
                    $result = $request->result;
                    $viewer = documents::whereDate('periode_akhir', '>=', $datetime)
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
                    $viewer = documentberau::whereDate('periode_akhir', '>=', $datetime)
                    ->whereNotNull ($filename)
                    ->where('upload_type','Fund_Req')
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
                    $viewer = documentbanjarmasin::whereDate('periode_akhir', '>=', $datetime)
                    ->whereNotNull ($filename)
                    ->where('upload_type','Fund_Req')
                    ->where('cabang', $request->cabang)
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
                    $viewer = documentsamarinda::whereDate('periode_akhir', '>=', $datetime)
                    ->whereNotNull ($filename)
                    ->where('upload_type','Fund_Req')
                    ->where('cabang', $request->cabang)
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
                    $viewer = documentJakarta::whereDate('periode_akhir', '>=', $datetime)
                    ->whereNotNull ($filename)
                    ->where('upload_type','Fund_Req')
                    ->where($filename, 'Like', '%' . $result . '%')
                    ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
                    ->pluck($filename)[0];
                    // dd($viewer);
                    // dd($request);
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
            //Search bar
            //check if search-bar is filled or not
            //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
            //pakai whereColumn untuk membandingkan antar 2 value column agar munculkan data dari pembuatan sampai bulan akhir periode
            //search filter based on cabang on picadmin dashboard page
                $searchresult = $request->search;
                if ($searchresult == 'All') {
                    $document = documents::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentberau = documentberau::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentbanjarmasin = documentbanjarmasin::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentsamarinda = documentsamarinda::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentjakarta = documentJakarta::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    return view('picadmin.picAdminDashboard', compact('searchresult','document', 'documentberau' , 'documentbanjarmasin' , 'documentsamarinda' , 'documentjakarta'));
                }
                elseif ($request->filled('search')) {
                    $document = DB::table('documents')->where('cabang', request('search'))->whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentberau = DB::table('beraudb')->where('cabang', request('search'))->whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentbanjarmasin = DB::table('banjarmasindb')->where('cabang', request('search'))->whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentsamarinda = DB::table('samarindadb')->where('cabang', request('search'))->whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentjakarta = documentJakarta::where('cabang', request('search'))->whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    return view('picadmin.picAdminDashboard', compact('searchresult','document', 'documentberau' , 'documentbanjarmasin' , 'documentsamarinda','documentjakarta'));
                }else if($request->filled('search_kapal')){
                    //search for nama kapal in picsite dashboard page dan show sesuai yang mendekati
                    //pakai whereColumn untuk membandingkan antar 2 value column agar munculkan data dari pembuatan sampai bulan akhir periode
                    $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->orderBy('id', 'DESC')
                    ->latest()->paginate(10)->withQueryString();

                    //berau search bar
                    $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->orderBy('id', 'DESC')
                    ->latest()->paginate(10)->withQueryString();

                    $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->orderBy('id', 'DESC')
                    ->latest()->paginate(10)->withQueryString();

                    $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->orderBy('id', 'DESC')
                    ->latest()->paginate(10)->withQueryString();

                    $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->orderBy('id', 'DESC')
                    ->latest()->paginate(10)->withQueryString();
                    return view('picadmin.picAdminDashboard', compact('searchresult','document', 'documentberau' , 'documentbanjarmasin' , 'documentsamarinda', 'documentjakarta'));
                }elseif($request->filled('search_kapal') && $request->filled('search')){
                    $document = documents::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->where('cabang', request('search'))
                    ->latest()->paginate(10)->withQueryString();

                    //berau search bar
                    $documentberau = documentberau::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->where('cabang', request('search'))
                    ->latest()->paginate(10)->withQueryString();

                    $documentbanjarmasin = documentbanjarmasin::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->where('cabang', request('search'))
                    ->latest()->paginate(10)->withQueryString();

                    $documentsamarinda = documentsamarinda::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->where('cabang', request('search'))
                    ->latest()->paginate(10)->withQueryString();

                    $documentjakarta = documentJakarta::where('nama_kapal', 'Like', '%' . $request->search_kapal . '%')
                    ->whereDate('periode_akhir', '>=', $datetime)
                    ->where('upload_type','Fund_Req')
                    ->where('cabang', request('search'))
                    ->latest()->paginate(10)->withQueryString();
                    return view('picadmin.picAdminDashboard', compact('searchresult','document', 'documentberau' , 'documentbanjarmasin' , 'documentsamarinda' , 'documentjakarta'));
                }else{
                    $document = documents::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentberau = documentberau::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentbanjarmasin = documentbanjarmasin::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentsamarinda = documentsamarinda::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    $documentjakarta = documentJakarta::whereDate('periode_akhir', '>=', $datetime)->where('upload_type','Fund_Req')->latest()->paginate(10)->withQueryString();
                    return view('picadmin.picAdminDashboard', compact('searchresult','document', 'documentberau' , 'documentbanjarmasin' , 'documentsamarinda' , 'documentjakarta'));
                };
        }elseif(Auth::user()->hasRole('AsuransiIncident')){
            $datetime = date('Y-m-d');
            $year = date('Y');
            $uploadspgr = spgrfile::where('cabang', 'Jakarta')->whereYear('created_at', date('Y'))->latest()->get();
            
            //Search bar
            //check if search-bar is filled or not
            if ($request->filled('search_no_formclaim')) {
                $uploadspgr = spgrfile::where('no_formclaim', 'Like', '%' . $request->search_no_formclaim . '%')
                ->whereYear('created_at', '=', $year)
                ->latest()->get();
            }

            // view spgr
            if($request->tipefile == 'SPGR'){
                $year1 = $request->created_at_Year;
                $month = $request->created_at_month;

                $cabang = $request->cabang;
                $result = $request->result;
                $filename = $request->viewspgrfile;
                $claim = $request->no_claim;

                $viewer = spgrfile::where('cabang', 'Jakarta')
                ->whereNotNull ($filename)
                ->where('no_formclaim', 'Like', '%' . $claim . '%')
                ->where($filename, 'Like', '%' . $result . '%')
                ->pluck($filename)[0];
                // dd($request);
                // dd($viewer);
                return Storage::disk('s3')->response('spgr/' . $year1 . "/". $month . "/" . $viewer);
            }
            
            return view('picincident.dashboardincident', compact('uploadspgr'));
        }elseif(Auth::user()->hasRole('InsuranceManager')){
            $datetime = date('Y-m-d');
            $year = $request->created_at_Year;
            $month = $request->created_at_month;
            $uploadspgr = spgrfile::where('cabang', 'Jakarta')->latest()->paginate(7);
            
            //Search bar
            //check if search-bar is filled or not
                if ($request->filled('search_no_formclaim')) {
                    $uploadspgr = spgrfile::where('no_formclaim', 'Like', '%' . $request->search_no_formclaim . '%')
                    ->latest()->get();
                }

            // view spgr
                if($request->tipefile == 'SPGR'){
                   
                    $cabang = $request->cabang;
                    $result = $request->result;
                    $filename = $request->viewspgrfile;
                    $claim = $request->no_claim;

                    $viewer = spgrfile::where('cabang', 'Jakarta')
                    ->whereNotNull ($filename)
                    ->where('no_formclaim', 'Like', '%' . $claim . '%')
                    ->where($filename, 'Like', '%' . $result . '%')
                    ->pluck($filename)[0];

                    return Storage::disk('s3')->response('spgr/' . $year . "/". $month . "/" . $viewer);
                }
            return view('insurance.Dashboardinsurance', ['uploadspgr' => $uploadspgr]);
        }
    }

    public function checkStock(){
        $items_below_stock = ItemBelowStock::join('items', 'items.id', '=', 'item_below_stocks.item_id')->where('cabang', Auth::user()->cabang)->get();

        return $items_below_stock;
    }

    public function completedJobRequest(){
        // Get all the job request within the logged in user within 6 month
        $JobRequestHeads = JobHead::with('user')->where(function($query){
            $query->where('status', 'like', 'Job Request Completed (Crew)')
            ->orWhere('status', 'like', 'Job Request Rejected By Logistic');
        })->whereYear('created_at', date('Y'))->latest()->paginate(10);

         // Get the jobDetail from jasa_id within the orderHead table 
        $job_id = JobHead::where('user_id', Auth::user()->id)->pluck('id');
        $jobDetails = JobDetails::whereIn('jasa_id', $job_id)->get();
        // Count the completed & in progress job Requests
        
        $job_in_progress = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request In Progress By Logistic');           
        })->whereYear('created_at', date('Y'))->count();
        
        $completedJR = $JobRequestHeads->count();
        return view('supervisor.supervisorDashboard', compact('job_in_progress','JobRequestHeads' , 'jobDetails', 'completedJR'));
    }

    public function inProgressJobRequest(){
        // Get all the order within the logged in user within 6 month
        $JobRequestHeads = JobHead::with('user')->where(function($query){
            $query->where('status', 'like', 'Job Request In Progress By Logistic');
        })->whereYear('created_at', date('Y'))->paginate(10);

        // Get the orderDetail from orders_id within the orderHead table 
        $job_id = $JobRequestHeads->pluck('id');
        $jobDetails = JobDetails::whereIn('jasa_id', $job_id)->get();

        $job_completed = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request Completed (Crew)')
            ->orWhere('status', 'like', 'Job Request Rejected By Logistic');
        })->whereYear('created_at', date('Y'))->count();
        
        $JR_in_progress = $JobRequestHeads->count();

        return view('supervisor.supervisorDashboard', compact('JR_in_progress' ,'jobDetails' ,'JobRequestHeads','job_completed'));
    }
}
