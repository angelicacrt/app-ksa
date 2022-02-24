<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\JobHead;
use App\Models\OrderDo;
use App\Exports\DOExport;
use App\Exports\PRExport;
use App\Models\OrderHead;
use App\Models\JobDetails;
use App\Models\OrderDetail;
use App\Models\Tug;
use App\Models\Barge;
use App\Models\Cart;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\OrderInExport;
use App\Models\ItemBelowStock;
use App\Exports\JR_full_Export;
use App\Exports\OrderOutExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Exports\PurchasingReportExport;
use App\Mail\DailyItemBelowStockReport;
use App\Jobs\SendItemBelowStockReportJob;

class SupervisorController extends Controller
{
    public function checkStock(){
        $items_below_stock = ItemBelowStock::join('items', 'items.id', '=', 'item_below_stocks.item_id')->where('cabang', Auth::user()->cabang)->get();

        return $items_below_stock;
    }

    public function completedOrder(){
        // Find order, then they can approve and send it to the purchasing role
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
        })->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');

        // Then find all the order details from the orderHeads
        $order_id = OrderHead::whereIn('user_id', $users)->whereYear('created_at', date('Y'))->pluck('id');
        $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

        $in_progress = OrderHead::where(function($query){
            $query->where('status', 'like', '%' . 'In Progress By Supervisor' . '%')
            ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
            ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
            ->orWhere('status', 'like', '%' . 'Revised' . '%')
            ->orWhere('status', 'like', '%' . 'Finalized' . '%')
            ->orWhere('status', 'like', '%' . 'Delivered By Supplier' . '%');
        })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->count();

        if(request('search')){
            $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query){
                $query->where('status', 'like', '%'. request('search') .'%')
                ->orWhere('order_id', 'like', '%'. request('search') .'%');
            })->whereYear('created_at', date('Y'))->latest()->paginate(7);

            // Count the completed & in progress order
            $completed = OrderHead::where(function($query){
                $query->where('status', 'like', 'Order Completed (Logistic)')
                ->orWhere('status', 'like', 'Order Rejected By Supervisor')
                ->orWhere('status', 'like', 'Order Rejected By Purchasing');
            })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->count();

            $items_below_stock = $this -> checkStock();

            return view('supervisor.supervisorDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'items_below_stock'));
        }else{
            $orderHeads = OrderHead::where(function($query){
                $query->where('status', 'like', 'Order Completed (Logistic)')
                ->orWhere('status', 'like', 'Order Rejected By Supervisor')
                ->orWhere('status', 'like', 'Order Rejected By Purchasing');
            })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);
    
            $completed = $orderHeads->count();
            
            $items_below_stock = $this -> checkStock();

            return view('supervisor.supervisorDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'items_below_stock'));
        }
    }

    public function inProgressOrder(){
        // Find order from logistic role, then they can approve and send it to the purchasing role
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
        })->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');

        // Then find all the order details from the orderHeads
        $order_id = OrderHead::whereIn('user_id', $users)->whereYear('created_at', date('Y'))->pluck('id');
        $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

        // Count the completed & in progress order
        $completed = OrderHead::where(function($query){
            $query->where('status', 'like', 'Order Completed (Logistic)')
            ->orWhere('status', 'like', 'Order Rejected By Supervisor')
            ->orWhere('status', 'like', 'Order Rejected By Purchasing');
        })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->count();

        if(request('search')){
            $orderHeads = OrderHead::with('user')->whereIn('user_id', $users)->where(function($query){
                $query->where('status', 'like', '%'. request('search') .'%')
                ->orWhere('order_id', 'like', '%'. request('search') .'%');
            })->whereYear('created_at', date('Y'))->latest()->paginate(7);

            $in_progress = OrderHead::where(function($query){
                $query->where('status', 'like', '%' . 'In Progress By Supervisor' . '%')
                ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'like', '%' . 'Delivered By Supplier' . '%');
            })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->count();

            $items_below_stock = $this -> checkStock();

            return view('supervisor.supervisorDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'items_below_stock'));
        }else{
            $orderHeads =  OrderHead::where(function($query){
                $query->where('status', 'like', '%' . 'In Progress By Supervisor' . '%')
                ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'like', '%' . 'Delivered By Supplier' . '%');
            })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7);
    
            $in_progress = $orderHeads->count();
            
            $items_below_stock = $this -> checkStock();

            return view('supervisor.supervisorDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress', 'items_below_stock'));
        }
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

    public function approveOrder(OrderHead $orderHeads){
        // Check if already been processed or not
        if($orderHeads -> order_tracker == 3){
            // return redirect('/dashboard')->with('error', 'Order Already Been Processed');
            return redirect()->back()->with('error', 'Order Already Been Processed');
        }

        // If they approve the order, then change the status of the order into purchasing
        OrderHead::find($orderHeads->id)->update([
            'order_tracker' => 3,
            'status' => 'Order In Progress By Purchasing'
        ]);

        // return redirect('/dashboard')->with('status', 'Order Approved');
        return redirect()->back()->with('status', 'Order Approved');
    }

    public function rejectOrder(Request $request, OrderHead $orderHeads){
        // Reject the order, required valid reason
        $request->validate([
            'reason' => 'required'
        ]);

        if($orderHeads -> order_tracker == 3){
            return redirect('/dashboard')->with('error', 'Order Already Been Processed');
        }

        // Then update the status as well as the reason
        OrderHead::find($orderHeads->id)->update([
            'status' => 'Order Rejected By Supervisor',
            'order_tracker' => 3,
            'reason' => $request -> reason
        ]);

        // return redirect('/dashboard')->with('status', 'Order Rejected');
        return redirect()->back()->with('status', 'Order Rejected');
    }

    public function downloadPr(OrderHead $orderHeads){
        // Find the order id then, return download        
        return (new PRExport($orderHeads -> order_id))->download('PR-' . $orderHeads -> order_id . '_' .  date("d-m-Y") . '.pdf', Excel::DOMPDF);
    }

    public function reportsPage(){
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

        // Find order from user/goods in
        $users = User::whereHas('roles', function($query){
                $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
            })->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');
        
        // Find all the items that has been approved from the logistic | last 6 month
        $orders = OrderDetail::with(['item'])->join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->whereIn('user_id', $users)->where(function($query){
            $query->where('status', 'like', 'Order Completed (Logistic)')
                ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                ->orWhere('status', 'like', '%' . 'Revised' . '%')
                ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                ->orWhere('status', 'like', 'Item Delivered By Supplier');
        })->whereBetween('order_heads.created_at', [$start_date, $end_date])->where('cabang', 'like', Auth::user()->cabang)->orderBy('order_heads.updated_at', 'desc')->get();

        $items_below_stock = $this -> checkStock();

        return view('supervisor.supervisorReport', compact('orders', 'str_month', 'items_below_stock'));
    }

    public function downloadReport(Excel $excel){
        return $excel -> download(new PurchasingReportExport(Auth::user()->cabang), 'Reports_'. date("d-m-Y") . '.xlsx');
    }

    public function historyOut(){
        // Find order from crew role/goods out
        $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where('role_user.role_id' , '=', '2')->pluck('users.id');
        
        // Find all the items that has been approved/completed from the user feedback | last 6 month
        $orderHeads = OrderDetail::with('item')->join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->whereIn('user_id', $users)->where('cabang', 'like', Auth::user()->cabang,)->where('status', 'like', '%' . 'Completed' . '%')->whereMonth('order_heads.created_at', date('m'))->whereYear('order_heads.created_at', date('Y'))->orderBy('order_details.created_at', 'desc')->get();

        $items_below_stock = $this -> checkStock();

        return view('supervisor.supervisorHistoryOut', compact('orderHeads', 'items_below_stock'));
    }

    public function historyIn(){
        // Find order from logistic role/goods in
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
        })->pluck('users.id');
        
        // Find all the items that has been approved from the user | last 6 month
        $orderHeads = OrderDetail::with('item')->join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->whereIn('user_id', $users)->where('cabang', 'like', Auth::user()->cabang,)->where('status', 'like', '%' . 'Completed'. '%')->whereMonth('order_heads.created_at', date('m'))->whereYear('order_heads.created_at', date('Y'))->orderBy('order_heads.updated_at', 'desc')->get();

        $items_below_stock = $this -> checkStock();
        
        return view('supervisor.supervisorHistoryIn', compact('orderHeads', 'items_below_stock'));
    }

    public function downloadOut(Excel $excel){
        // Export the data of history goods out
        return $excel -> download(new OrderOutExport, 'OrderGoodsOut_'. date("d-m-Y") . '.xlsx');
    }

    public function downloadIn(Excel $excel){
        // Export the data of history goods in
        return $excel -> download(new OrderInExport, 'OrderGoodsIn_'. date("d-m-Y") . '.xlsx');
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

            $items_below_stock = $this -> checkStock();

            return view('supervisor.supervisorItemStock', compact('items', 'items_below_stock', 'default_branch'));
        }else{
            $items = Item::orderBy('cabang')->Paginate(10)->withQueryString();
            // $items = Item::latest()->Paginate(10)->withQueryString();

            $items_below_stock = $this -> checkStock();

            return view('supervisor.supervisorItemStock', compact('items', 'items_below_stock', 'default_branch'));
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

            $items_below_stock = $this -> checkStock();

            return view('supervisor.supervisorItemStock', compact('items', 'items_below_stock', 'default_branch'));
        }else{
            if($default_branch == 'All'){
                $items = Item::orderBy('cabang')->Paginate(10)->withQueryString();
            }else{
                $items = Item::where('cabang', $default_branch)->Paginate(10)->withQueryString();
            }

            $items_below_stock = $this -> checkStock();

            return view('supervisor.supervisorItemStock', compact('items', 'items_below_stock', 'default_branch'));
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
            'serialNo' => 'required',
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
            // 'serialNo' => 'required|regex:/^[0-9]{2}-[0-9]{4}-[0-9]/',
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

        return redirect('/supervisor/item-stocks')->with('status', 'Added Successfully');
    }

    public function deleteItemStock(Item $item){
        Item::destroy($item->id);

        return redirect('/supervisor/item-stocks')->with('status', 'Deleted Successfully');
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
            // 'serialNo' => 'required|regex:/^[0-9]{2}-[0-9]{4}-[0-9]/',
            'serialNo' => 'required',
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

        return redirect('/supervisor/item-stocks')->with('status', 'Edit Successfully');
    }

    public function makeOrderPage(){
        // Supervisor role can only select the items that are only available to their branches & carts according to the login user
        $items = Item::where('cabang', Auth::user()->cabang)->where('itemState', 'like', 'Available')->get();

        // Get all the tugs, barges, and cart of the following user
        $barges = Barge::all();
        $tugs = Tug::all();
        $carts = Cart::with('item')->where('user_id', Auth::user()->id)->get();

        $items_below_stock = $this -> checkStock();

        return view('supervisor.supervisorMakeOrder', compact('items', 'carts', 'tugs', 'barges', 'items_below_stock'));
    }

    public function addItemToCart(Request $request){
        // Validate Cart Request
        $validated = $request->validate([
            'item_id' => 'required',
            'quantity' => 'required|numeric|min:1',
            'department' => 'nullable',
            'note' => 'nullable'
        ]);

        // Check if the item state is on hold, then return error
        $check_item_state = Item::where('id', $request -> item_id)->pluck('itemState')[0];
        if($check_item_state == 'Hold'){
            return redirect('/supervisor/make-order')->with('error', 'Item is Unavailable');
        }

        // Check if the cart within the user is already > 12 items, then cart is full & return with message
        $counts = Cart::where('user_id', Auth::user()->id)->count();
        if($counts ==  12){
            return redirect('/supervisor/make-order')->with('error', 'Cart is Full');
        }

        // Find if the same configuration of item is already exist in cart or no
        $itemExistInCart = Cart::where('user_id', Auth::user()->id)->where('item_id', $request->item_id)->where('department', $request->department)->where('golongan', $request->golongan)->first();

        if($itemExistInCart){
            Cart::find($itemExistInCart->id)->increment('quantity', $request->quantity);
            Cart::find($itemExistInCart->id)->update([
                'note' => $request -> note
            ]);
        }else{
            // Add cabang to the cart
            $validated['cabang'] = Auth::user()->cabang;
            // Then add item to the cart
            $validated['user_id'] = Auth::user()->id;
            Cart::create($validated);
        }

        return redirect('/supervisor/make-order')->with('status', 'Add Item Success');
    }

    public function deleteItemFromCart(Cart $cart){
        // Delete item from cart of the following user
        Cart::destroy($cart->id);

        return redirect('/supervisor/make-order')->with('status', 'Delete Item Success');
    }

    public function submitOrder(Request $request){
        $request -> validate([
            'tugName' => 'required',
            'bargeName' => 'nullable',
            'company' => 'required',
            'orderType' => 'required|in:Susulan,Real Time',
            'descriptions' => 'nullable'
        ]);

        // Find the cart of the following user
        $carts = Cart::where('user_id', Auth::user()->id)->get();

        // Double check the item state, if there are items that is on 'Hold' status, then return error
        foreach($carts as $c){
            if($c -> item -> itemState == 'Hold'){
                return redirect('/supervisor/make-order')->with('errorCart', $c -> item -> itemName . ' is Currently Unavailable, Kindly Remove it From the Cart');
            }
        }

        // Validate cart size
        if(count($carts) == 0){
            return redirect('/supervisor/make-order')->with('errorCart', 'Cart is Empty');
        }

        // String formatting for boatName with tugName + bargeName
        $boatName = $request->tugName . '/' . $request->bargeName;
        
        // Create Order Head
        $orderHead = OrderHead::create([
            'created_by' => Auth::user()->name,
            'user_id' => Auth::user()->id,
            'cabang' => Auth::user()->cabang,
            'boatName' => $boatName,
            'orderType' => $request -> orderType,
            'order_tracker' => 3,
            'status' => 'Order In Progress By Purchasing',
            'descriptions' => $request -> descriptions
        ]);
        
        // Formatting the PR format requirements
        $month_arr_in_roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        $cabang_arr = [
            'Jakarta' => 'JKT',
            'Banjarmasin' => 'BNJ',
            'Samarinda' => 'SMD',
            'Bunati' => 'BNT',
            'Babelan' => 'BBL',
            'Berau' => 'BER'
        ];

        $pr_id = $orderHead -> id;
        $first_char_name = strtoupper(Auth::user()->name[0]);
        $location = $cabang_arr[Auth::user()->cabang];
        $month = date('n');
        $month_to_roman = $month_arr_in_roman[$month - 1];
        $year = date('Y');

        // Create the PR Number => 001.A/PR-ISA-SMD/IX/2021
        $pr_number = $pr_id . '.' . $first_char_name . '/' . 'PR-' . $request->company . '-' . $location . '/' . $month_to_roman . '/' . $year;

        $company_arr = [
            'KSA' => 'Kartika Samudera Adijaya',
            'ISA' => 'Iriana Samudera Adijaya',
            'SKB' => 'Semoga Kartika Bahagia',
            'KSAO' => 'Kartika Samudera Adijaya Offshore',
            'KSAM' => 'Kartika Samudera Adijaya Maritime',
        ];
        $company_full = $company_arr[$request -> company];

        OrderHead::find($orderHead->id)->update([
            'order_id' => 'SOID#' . $orderHead->id,
            'noPr' => $pr_number,
            'company' => $request->company,
            'companyFull' => $company_full,
            'prDate' => date("d/m/Y")
        ]);

        // Then fill the Order Detail with the cart items
        foreach($carts as $c){
            OrderDetail::create([
                'orders_id' => $orderHead -> id,
                'item_id' => $c -> item_id,
                'quantity' => $c -> quantity,
                'acceptedQuantity' => $c -> quantity,
                'unit' => $c -> item -> unit,
                'serialNo' => $c -> item->serialNo,
                'department' => $c -> department,
                'note' => $c -> note
            ]);
        }

        // Emptying the cart items
        Cart::where('user_id', Auth::user()->id)->delete();

        return redirect('/dashboard')->with('status', 'Submit Order Success');
    }

    public function approvalDoPage(){
        // Find all of the ongoing DO from the requested branch OR the destination branch
        $ongoingOrders = OrderDo::with(['item_requested', 'user'])->where(function($query){
            $query->where('fromCabang', Auth::user()->cabang)
            ->orWhere('toCabang', Auth::user()->cabang);
        })->whereYear('created_at', date('Y'))->latest()->paginate(7);

        $items_below_stock = $this -> checkStock();

        return view('supervisor.supervisorApprovalDO', compact('ongoingOrders', 'items_below_stock'));
    }

    public function forwardDo(OrderDo $orderDos){
        // Validate the stock first
        if($orderDos -> quantity > $orderDos -> item_requested_from -> itemStock){
            // return redirect('/supervisor/approval-do')->with('error', 'Stocks Insufficient, Kindly Re-Check the Stocks');
            return redirect()->back()->with('error', 'Stocks Insufficient, Kindly Re-Check the Stocks');
        }else{
            // Then validate order_tracker
            if($orderDos -> order_tracker == 3){
                // return redirect('/supervisor/approval-do')->with('error', 'Order Already Been Processed');
                return redirect()->back()->with('error', 'Order Already Been Processed');
            }

            // Forward the DO to the requested branches, then update the order_tracker to validate
            OrderDo::where('id', $orderDos -> id)->update([
                'order_tracker' => 3,
                'status' => 'Waiting Approval By Supervisor Cabang ' . $orderDos->toCabang
            ]);
    
            // return redirect('/supervisor/approval-do')->with('status', 'Approved Successfully');
            return redirect()->back()->with('status', 'Approved Successfully');
        }
    }
    
    public function denyDo(OrderDo $orderDos){
        // Validate if the order already been processed
        if($orderDos -> order_tracker == 3){
            // return redirect('/supervisor/approval-do')->with('error', 'Order Already Been Processed');
            return redirect()->back()->with('error', 'Order Already Been Processed');
        }

        // Else, update the status
        OrderDo::where('id', $orderDos -> id)->update([
            'order_tracker' => 3,
            'status' => 'Rejected By Supervisor Cabang ' . $orderDos->fromCabang
        ]);

        // return redirect('/supervisor/approval-do')->with('status', 'Request Rejected');
        return redirect()->back()->with('status', 'Request Rejected');
    }

    public function approveDo(OrderDo $orderDos){
        // Re-validate quantity, even we already put the validation from previous supervisor just to be sure the data is valid
        if($orderDos -> quantity > $orderDos -> item_requested_from -> itemStock){
            // return redirect('/supervisor/approval-do')->with('error', 'Stocks Insufficient, Kindly Re-Check the Stocks');
            return redirect()->back()->with('error', 'Stocks Insufficient, Kindly Re-Check the Stocks');
        }else{
            // Validate if the order already been processed
            if($orderDos -> order_tracker == 4){
                // return redirect('/supervisor/approval-do')->with('error', 'Order Already Been Processed');
                return redirect()->back()->with('error', 'Order Already Been Processed');
            }
            
            OrderDo::where('id', $orderDos -> id)->update([
                'order_tracker' => 4,
                'status' => 'On Delivery'
            ]);
            
            // Decrement the stock for the requested branch
            $item = Item::where('id', $orderDos -> item_requested_from_id);
            $item ->decrement('itemStock', $orderDos -> quantity);

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

            // return redirect('/supervisor/approval-do')->with('status', 'Approved Successfully');
            return redirect()->back()->with('status', 'Approved Successfully');
        }
    }

    public function rejectDo(OrderDo $orderDos){
        // Validate if the order already been processed
        if($orderDos -> order_tracker == 4){
            // return redirect('/supervisor/approval-do')->with('error', 'Order Already Been Processed');
            return redirect()->back()->with('error', 'Order Already Been Processed');
        }

        OrderDo::where('id', $orderDos -> id)->update([
            'order_tracker' => 4,
            'status' => 'Rejected By Supervisor Cabang ' . $orderDos->toCabang
        ]);

        // return redirect('/supervisor/approval-do')->with('status', 'Request Rejected');
        return redirect()->back()->with('status', 'Request Rejected');
    }

    public function downloadDo(OrderDo $orderDos){
        // Find the specific DO, then download it
        return (new DOExport($orderDos -> id))->download('DO-' . $orderDos -> id . '_' .  date("d-m-Y") . '.xlsx');
    }

    public function JR_list_page() {
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic');
        })->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');

        if(request('search')){
            $JobRequestHeads = JobHead::with('user')->where(function($query){
                $query->where('status', 'like', '%'. request('search') .'%')
                ->orWhere( 'Headjasa_id', 'like', '%'. request('search') .'%');
            })->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->latest()->paginate(7)->withQueryString();
        }else{
            $JobRequestHeads = JobHead::with('user')->where('cabang', 'like', Auth::user()->cabang)->whereYear('created_at', date('Y'))->paginate(7)->withQueryString();
        }

        $items_below_stock = $this -> checkStock();

        // show job request
        $job_id = $JobRequestHeads->pluck('id');
        $jobDetails = JobDetails::whereIn('jasa_id', $job_id)->get();

        
         // Count the completed & in progress job Requests
        $job_completed = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request Completed')
            ->orWhere('status', 'like', 'Job Request Rejected By Logistics')
            ->orWhere('status', 'like', 'Order Rejected By Purchasing');
        })->whereYear('created_at', date('Y'))->count();
        
        $job_in_progress = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request In Progress By' . '%')
            ->orWhere('status', 'like', 'Job Request Approved By' . '%');
        })->whereYear('created_at', date('Y'))->count();

        return view('supervisor.supervisorJobRequestList', compact('items_below_stock','job_in_progress' , 'job_completed' ,'JobRequestHeads','jobDetails'));
    }
    public function Jr_Reports_Page(){
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

        // Find order from user/goods in => order created from logistic
        // $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where('role_user.role_id' , '=', '3')->where('cabang', 'like', Auth::user()->cabang)->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic');
        })->where('cabang', 'like', Auth::user()->cabang)->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');
        
        // Find all the items that has been approved from the logistic | last 6 month
        // $orderHeads = OrderHead::whereIn('user_id', $users)->where('status', 'like', 'Order Completed (Logistic)')->whereBetween('order_heads.created_at', [$start_date, $end_date])->where('cabang', 'like', Auth::user()->cabang)->orderBy('order_heads.updated_at', 'desc')->get();

        $jobs = JobDetails::join('job_heads', 'job_heads.id', '=', 'job_details.jasa_id')
        ->where('job_heads.status', 'like', 'Job Request Approved By Logistics')
        ->whereBetween('job_heads.created_at', [$start_date, $end_date])
        ->where('job_details.cabang', Auth::user()->cabang)->orderBy('job_heads.updated_at', 'desc')->get();

        $items_below_stock = $this -> checkStock();

        return view('supervisor.supervisorReportJR', compact('jobs', 'str_month', 'items_below_stock'));
    }
    // export excel jr
    public function Download_JR_report(Excel $excel) {
        // Get all job on the cabang
        // dd("hello");
        return $excel -> download(new JR_full_Export, 'Job_Request_'. date("d-m-Y") . '.xlsx');
    }
    public function Download_JR_report_PDF(Excel $excel) {
        // Get all job on the cabang
        // dd("hello");
        return $excel -> download(new JR_full_Export, 'Job_Request_'. date("d-m-Y") . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
        
    }
}
