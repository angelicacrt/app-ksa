<?php

namespace App\Http\Controllers;
use Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ApList;
use App\Models\JobHead;
use App\Models\Supplier;
use App\Exports\POExport;
use App\Models\OrderHead;
use App\Exports\JO_Report;
use App\Models\JobDetails;
use App\Models\OrderDetail;
use App\Models\ApListDetail;
use App\Models\ItemBelowStock;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ReportAPExport;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendItemBelowStockReportJob;
use App\Exports\PurchasingReportExport;

class PurchasingManagerController extends Controller
{
    public function branchDashboard($branch){
        // Find the current month, display the transaction per 6 month => Jan - Jun || Jul - Dec
        $month_now = (int)(date('m'));
        
        if($month_now <= 6){
            $start_date = date('Y-01-01');
            $end_date = date('Y-06-30');
        }else{
            $start_date = date('Y-07-01');
            $end_date = date('Y-12-31');
        }

        $default_branch = $branch;

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
            ->orWhere('status', 'like', 'Order Delivered By Supplier');
        })->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->count();

        // Get all the suppliers
        $suppliers = Supplier::latest()->get();

        return view('purchasingManager.purchasingManagerDashboard', compact('orderHeads', 'orderDetails', 'suppliers', 'completed', 'in_progress', 'default_branch'));
    }

    public function completedOrder($branch){
        $default_branch = $branch;

        // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
        // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where('role_user.role_id' , '=', '3')->where('cabang', 'like', $default_branch)->pluck('users.id');
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
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

            return view('purchasingManager.purchasingManagerDashboard', compact('orderHeads', 'orderDetails', 'suppliers', 'completed', 'in_progress', 'default_branch'));
        }else{
            $orderHeads = OrderHead::where(function($query){
                $query->where('status', 'like', 'Order Completed (Logistic)')
                ->orWhere('status', 'like', 'Order Rejected By Supervisor')
                ->orWhere('status', 'like', 'Order Rejected By Purchasing');
            })->whereIn('user_id', $users)->where('cabang', 'like', $default_branch)->whereYear('created_at', date('Y'))->latest()->paginate(6);
    
            $completed = $orderHeads->count();
            
            // Then find all the order details from the orderHeads
            $order_id = $orderHeads->pluck('id');
            $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

            // Get all the suppliers
            $suppliers = Supplier::latest()->get();
    
            return view('purchasingManager.purchasingManagerDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'suppliers', 'default_branch'));
        }
    }

    public function inProgressOrder($branch){
        $default_branch = $branch;

        // Find order from the logistic role, because purchasing role can only see the order from "logistic/admin logistic" role NOT from "crew" roles
        // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where('role_user.role_id' , '=', '3')->where('cabang', 'like', $default_branch)->pluck('users.id');
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
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
            })->whereIn('user_id', $users)->whereYear('created_at', date('Y'))->latest()->paginate(6);

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

            return view('purchasingManager.purchasingManagerDashboard', compact('orderHeads', 'orderDetails', 'suppliers', 'completed', 'in_progress', 'default_branch'));
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
    
            return view('purchasingManager.purchasingManagerDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'suppliers', 'default_branch'));
        }
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

    public function approveOrderPage(OrderHead $orderHeads){
        // Find the order detail of the following order
        $orderDetails = OrderDetail::with('item')->where('orders_id', $orderHeads->id)->get();

        return view('purchasingManager.purchasingManagerApprovedPage', compact('orderHeads', 'orderDetails'));
    }

    public function approveOrder(OrderHead $orderHeads){
        $default_branch = $orderHeads -> cabang;

        // We are not validating anything because we won't change/input anything to the database, Purchasing Manager only sees the order and decide if he/she approve it or not
        // Check if the order already been processed or not using order tracker
        if($orderHeads -> order_tracker == 5){
            return redirect('/purchasing-manager/order/' . $orderHeads -> id . '/approve')->with('error', 'Order Already Been Processed');
        }

        OrderHead::find($orderHeads->id)->update([
            'order_tracker' => 5,
            // 'status' => 'Item Delivered By Supplier'
            'status' => 'Order Being Finalized By Purchasing Manager'
        ]);

        // return redirect('/dashboard')->with('statusB', 'Order Approved By Purchasing Manager');
        return redirect('/purchasing-manager/dashboard/' . $default_branch)->with('statusB', 'Updated Successfully');
    }

    public function rejectOrder (Request $request, OrderHead $orderHeads){
        $default_branch = $orderHeads -> cabang;
        
        $request -> validate([
            'reason' => 'string|required'
        ]);

        // We are not validating anything because we won't change/input anything to the database, Purchasing Manager only sees the order and decide if he/she approve it or not
        // Check if the order already been processed or not using order tracker
        if($orderHeads -> order_tracker == 5){
            return redirect('/purchasing-manager/dashboard/' . $default_branch)->with('errorB', 'Order Already Been Processed');
            // return redirect('/purchasing-manager/order/' . $orderHeads -> id . '/approve')->with('error', 'Order Already Been Processed');
            // return redirect()->back()->with('error', 'Order Already Been Processed');
        };

        // Reset the price before ppn and discount
        // Then update the status, order tracker, also the reason as well
        OrderHead::find($orderHeads -> id)->update([
            'order_tracker' => 5,
            'status' => 'Order Being Rechecked By Purchasing',
            'totalPrice' => $orderHeads -> totalPriceBeforeCalculation,
            'reason' => $request -> reason
        ]);

        // return redirect('/dashboard')->with('statusB', 'Order Rejected By Purchasing Manager');
        return redirect('/purchasing-manager/dashboard/' . $default_branch)->with('statusB', 'Updated Successfully');
    }

    public function reviseOrder(Request $request, OrderHead $orderHeads){
        $default_branch = $orderHeads -> cabang;

        $request -> validate([
            'reason' => 'string|required'
        ]);
        
        // Check if the order already been processed or not
        if($orderHeads -> order_tracker == 6){
            return redirect('/purchasing-manager/dashboard/' . $default_branch)->with('errorB', 'Order Already Been Processed');

        };

        // Then update the status
        OrderHead::where('id', $orderHeads -> id)->update([
            'order_tracker' => 6,
            'status' => 'Order Being Revised By Purchasing',
            'totalPrice' => $orderHeads -> totalPriceBeforeCalculation,
            'retries' => $orderHeads -> retries += 1
        ]);
        
        return redirect('/purchasing-manager/dashboard/' . $default_branch)->with('statusB', 'Updated Successfully');
    }

    public function finalizeOrder(OrderHead $orderHeads){
        $default_branch = $orderHeads -> cabang;

        // Check if the order already been processed or not
        if($orderHeads -> order_tracker == 8){
            return redirect('/purchasing-manager/dashboard/' . $default_branch)->with('errorB', 'Order Already Been Processed');
        };

        // Then update the following order head
        OrderHead::find($orderHeads->id)->update([
            'order_tracker' => 8,
            'status' => 'Item Delivered By Supplier'
        ]);

        // After finalized, then create new AP List
        ApList::create([
            'order_id' => $orderHeads -> id,
            'cabang' => $orderHeads -> cabang,
            'creationTime' => date("d/m/Y"),
            'status' => 'OPEN',
            'tracker' => 5
        ]);

        return redirect('/purchasing-manager/dashboard/' . $default_branch)->with('statusB', 'Updated Successfully');
    }

    public function itemStock(){
        // Check the stocks of all branches
        $default_branch = 'All';

        if(request('search')){
            $items = Item::where(function($query){
                $query->where('itemName', 'like', '%' . request('search') . '%')
                ->orWhere('cabang', 'like', '%' . request('search') . '%')
                ->orWhere('codeMasterItem', 'like', '%' . request('search') . '%');
            })->Paginate(10)->withQueryString();

            return view('purchasingManager.purchasingManagerItemStock', compact('items', 'default_branch'));
        }else{
            $items = Item::orderBy('cabang')->Paginate(10)->withQueryString();
            // $items = Item::latest()->Paginate(10)->withQueryString();

            return view('purchasingManager.purchasingManagerItemStock', compact('items', 'default_branch'));
        }
    }

    public function itemStockBranch($branch){
        // Check the stocks of all branches
        $default_branch = $branch;

        if(request('search')){
            $items = Item::where(function($query){
                $query->where('itemName', 'like', '%' . request('search') . '%')
                ->orWhere('codeMasterItem', 'like', '%' . request('search') . '%');
            })->where('cabang', $default_branch)->Paginate(10)->withQueryString();

            return view('purchasingManager.purchasingManagerItemStock', compact('items', 'default_branch'));
        }else{
            if($default_branch == 'All'){
                $items = Item::orderBy('cabang')->Paginate(10)->withQueryString();
            }else{
                $items = Item::where('cabang', $default_branch)->Paginate(10)->withQueryString();
            }

            return view('purchasingManager.purchasingManagerItemStock', compact('items', 'default_branch'));
        }
    }

    public function addItemStock(Request $request){
        // Storing the item to the stock
        $request->validate([
           'itemName' => 'required',
           'itemAge' => 'required|integer|min:1',
           'umur' => 'required',
           'itemStock' => 'required|integer|min:1',
           'minStock' => 'required|integer|min:1',
           'unit' => 'required',
           'golongan' => 'required',
           // 'serialNo' => 'nullable|numeric',
           'serialNo' => 'required|regex:/^[0-9]{2}-[0-9]{4}-[0-9]/',
           'codeMasterItem' => 'nullable',
           'cabang' => 'required',
           'description' => 'nullable'
       ]);

       // Formatting the item age
       $new_itemAge = $request->itemAge . ' ' . $request->umur;
       
       // Create the item
       $item = Item::create([
           'itemName' => $request -> itemName,
           'itemAge' => $new_itemAge,
           'itemStock' => $request -> itemStock,
           'minStock' => $request -> minStock,
           'unit' => $request -> unit,
           'golongan' => $request -> golongan,
           'serialNo' => $request -> serialNo,
           'codeMasterItem' => $request -> codeMasterItem,
           'cabang' => $request->cabang,
           'description' => $request -> description
       ]);

       // Check if the item stock is below the minimum stock, if it is true then insert a new data to the ItemBelowStock table and dispatch a new email to user using job
       if($item -> itemStock < $item -> minStock){
           if(ItemBelowStock::where('item_id', $item -> id)->exists()){
               ItemBelowStock::where('item_id', $item -> id)->update([
                   'stock_defficiency' => ($item -> minStock) - ($item -> itemStock)
               ]);
           }else{
               ItemBelowStock::create([
                   'item_id' => $item -> id,
                   'stock_defficiency' => ($item -> minStock) - ($item -> itemStock)
               ]);
               SendItemBelowStockReportJob::dispatch($item->id, $item->cabang);
           }
       }elseif(ItemBelowStock::where('item_id', $item -> id)->exists()){
           ItemBelowStock::find($item -> id)->destroy();
       }

       return redirect('/purchasing-manager/item-stocks')->with('status', 'Added Successfully');
    }

    public function deleteItemStock(Item $item){
        Item::destroy($item->id);

        return redirect('/purchasing-manager/item-stocks')->with('status', 'Deleted Successfully');
    }

    public function editItemStock(Request $request, Item $item){
        // Edit the requested item
        $request->validate([
            'itemName' => 'required',
            'itemAge' => 'required|integer|min:1',
            'umur' => 'required',
            'itemStock' => 'required|integer|min:1',
            'minStock' => 'required|integer|min:1',
            'unit' => 'required',
            'golongan' => 'required',
            'serialNo' => 'required|regex:/^[0-9]{2}-[0-9]{4}-[0-9]/',
            'codeMasterItem' => 'nullable',
            'itemState' => 'required|in:Available,Hold',
            'description' => 'nullable'
        ]);

        // Formatting the item age
        $new_itemAge = $request->itemAge . ' ' . $request->umur;

        // Update the item
        Item::where('id', $item->id)->update([
            'itemName' => $request -> itemName,
            'itemAge' => $new_itemAge,
            'itemStock' => $request->itemStock,
            'minStock' => $request -> minStock,
            'unit' => $request -> unit,
            'golongan' => $request -> golongan,
            'serialNo' => $request -> serialNo,
            'codeMasterItem' => $request -> codeMasterItem,
            'itemState' => $request -> itemState,
            'description' => $request -> description
        ]);

        $item_to_find = Item::where('id', $item->id)->first();

        // Check if the item stock is below the minimum stock, if it is true then insert a new data to the ItemBelowStock table and dispatch a new email to user using job
        if($item_to_find -> itemStock < $item_to_find -> minStock){
            if(ItemBelowStock::where('item_id', $item_to_find -> id)->exists()){
                ItemBelowStock::where('item_id', $item_to_find -> id)->update([
                    'stock_defficiency' => ($item_to_find -> minStock) - ($item_to_find -> itemStock)
                ]);
            }else{
                ItemBelowStock::create([
                    'item_id' => $item_to_find -> id,
                    'stock_defficiency' => ($item_to_find -> minStock) - ($item_to_find -> itemStock)
                ]);
                SendItemBelowStockReportJob::dispatch($item_to_find->id, $item_to_find->cabang);
            }
        }elseif(ItemBelowStock::where('item_id', $item_to_find -> id)->exists()){
            ItemBelowStock::where('item_id', $item_to_find -> id)->delete();
        }

        return redirect('/purchasing-manager/item-stocks')->with('status', 'Edit Successfully');
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
            $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
        })->where('cabang', $default_branch)->pluck('users.id');
                
        // Find all the items that has been approved from the logistic | Per 3 months
        $orders = OrderDetail::with(['item'])->join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->whereIn('user_id', $users)->where(function($query){
            $query->where('status', 'Order Completed (Logistic)')
                // ->orWhere('status', 'Order In Progress By Purchasing')
                // ->orWhere('status', 'Order In Progress By Purchasing Manager')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'Item Delivered By Supplier');
        })->whereBetween('order_heads.created_at', [$start_date, $end_date])->where('cabang', 'like', $default_branch)->orderBy('order_heads.updated_at', 'desc')->get();

        return view('purchasingManager.purchasingManagerReport', compact('orders', 'default_branch', 'str_month'));
    }

    public function reportPageBranch($branch){
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

        $default_branch = $branch;

        // Find order from user that created the order
        // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where(function($query){
        //     $query->where('role_user.role_id' , '=', '3')
        //     ->orWhere('role_user.role_id' , '=', '4');
        // })->where('cabang', 'like', $default_branch)->pluck('users.id');
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
        })->where('cabang', 'like', $default_branch)->pluck('users.id');
                
        // Find all the items that has been approved from the logistic | Per 3 months
        $orders = OrderDetail::with(['item'])->join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->whereIn('user_id', $users)->where(function($query){
            $query->where('status', 'Order Completed (Logistic)')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'Item Delivered By Supplier');
        })->whereBetween('order_heads.created_at', [$start_date, $end_date])->where('cabang', 'like', $default_branch)->orderBy('order_heads.updated_at', 'desc')->get();

        return view('purchasingManager.purchasingManagerReport', compact('orders', 'default_branch', 'str_month'));
    }

    public function downloadPo(OrderHead $orderHeads){
        return (new POExport($orderHeads -> order_id))->download('PO-' . $orderHeads -> order_id . '_' .  date("d-m-Y") . '.xlsx');
    }

    public function downloadReport(Excel $excel, $branch){
        $default_branch = $branch;
        return $excel -> download(new PurchasingReportExport($default_branch), 'Reports_'. date("d-m-Y") . '.xlsx');
    }

    public function formApPage(){
        // Show the form AP page
        $apList = ApList::with('orderHead')->where('cabang', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);
        
        // Get the AP List Detail from the selected AP List
        $apListId = $apList -> pluck('id');
        $apListDetail = ApListDetail::with('apList')->whereIn('aplist_id', $apListId)->latest()->get()->unique('aplist_id');

        $check_ap_in_array = $apListDetail -> pluck('aplist_id') ->toArray();
        
        // Get all the supplier
        $suppliers = Supplier::latest()->get();

        // Default branch is Jakarta
        $default_branch = 'Jakarta';

        return view('purchasingManager.purchasingManagerFormAp', compact('apList', 'default_branch', 'suppliers', 'apListDetail', 'check_ap_in_array'));
    }

    public function formApPageBranch($branch){
        // Show the form AP page
        $apList = ApList::with('orderHead')->where('cabang', $branch)->whereYear('created_at', date('Y'))->latest()->paginate(7);
        
        // Get the AP List Detail from the selected AP List
        $apListId = $apList -> pluck('id');
        $apListDetail = ApListDetail::with('apList')->whereIn('aplist_id', $apListId)->latest()->get()->unique('aplist_id');


        // Get all the supplier
        $suppliers = Supplier::latest()->get();

        // Get the branch from the parameter
        $default_branch = $branch;

        return view('purchasingManager.purchasingManagerFormAp', compact('apList', 'default_branch', 'suppliers', 'apListDetail'));
    }

    public function downloadFile(Request $request){
        // ($request -> apListId) will be the AP ID => 1 || ($request -> filename) will be the column => ex : doc_partial1, doc_partial2, and goes on
        // Get the filename of the following AP ID
        $filename = ApList::where('id', $request -> apListId)->pluck($request -> filename)[0];
        $path = ApList::where('id', $request -> apListId)->pluck($request -> pathToFile)[0];

        // Then, find the file to download it
        // return Storage::download($path . $filename);
        return Storage::disk('s3')->response($path . $filename);
    }

    public function approveDocument(Request $request){
        
        // Find the document (ex : doc_partial1), then update the status
        ApList::find($request -> apListId)->update([
            $request -> statusColumn => 'Approved'
        ]);

        // Redirect
        return redirect('/purchasing-manager/form-ap/' . $request -> default_branch)->with('openApListModalWithId', $request -> apListId);
    }

    public function rejectDocument(Request $request){

        $request -> validate([
            'reason' => 'string|required'
        ]);

        // Find the document (ex : doc_partial1), then update the status
        ApList::find($request -> apListId)->update([
            $request -> statusColumn => 'Rejected',
            $request -> description => $request -> reason
        ]);

        return redirect('/purchasing-manager/form-ap/' . $request -> default_branch)->with('openApListModalWithId', $request -> apListId);
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

        return view('purchasingManager.purchasingManagerReportApPage', compact('default_branch', 'str_month', 'apList'));
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

        return view('purchasingManager.purchasingManagerReportApPage', compact('default_branch', 'str_month', 'apList'));
    }

    public function exportReportAp(Excel $excel, $branch){

        // Export into excel
        return $excel -> download(new ReportAPExport($branch), 'Reports_AP('. $branch . ')_'. date("d-m-Y") . '.xlsx');
    }

    public function checklistPrPage(){
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

        $orderDetails = OrderDetail::with(['item'])->join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->join('users', 'users.id', '=', 'order_heads.user_id')->whereBetween('order_heads.created_at', [$start_date, $end_date])->where('order_heads.cabang', $default_branch)->where(function($query){
            $query->where('status', 'Order Completed (Logistic)')
                ->orWhere('status', 'Order In Progress By Purchasing')
                ->orWhere('status', 'Order In Progress By Purchasing Manager')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'Item Delivered By Supplier');
        })->get();
        
        return view('purchasingManager.purchasingManagerChecklistPrPage', compact('default_branch', 'str_month', 'orderDetails'));
    }
    
    public function checklistPrPageBranch($branch){
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
        $default_branch = $branch;

        $orderDetails = OrderDetail::join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->join('users', 'users.id', '=', 'order_heads.user_id')->whereBetween('order_heads.created_at', [$start_date, $end_date])->where('order_heads.cabang', $default_branch)->where(function($query){
            $query->where('status', 'Order Completed (Logistic)')
                ->orWhere('status', 'Order In Progress By Purchasing')
                ->orWhere('status', 'Order In Progress By Purchasing Manager')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'Item Delivered By Supplier');
        })->get();

        return view('purchasingManager.purchasingManagerChecklistPrPage', compact('default_branch', 'str_month', 'orderDetails'));
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
        return view('purchasingManager.purchasingManagerJOReport', compact('default_branch', 'str_month', 'jobs'));
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
        return view('purchasingManager.purchasingManagerJOReport', compact('default_branch', 'str_month', 'jobs'));
    }

    public function downloadJOreport(Excel $excel,$branch) {
        $date = Carbon::now();
        $monthName = $date->format('F');
        // $id = $JobRequestHeads -> id;
        return $excel -> download(new JO_Report($branch), 'Report-JO-' . $monthName . '.xlsx');
    }
    
}
