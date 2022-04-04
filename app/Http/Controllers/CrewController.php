<?php

namespace App\Http\Controllers;

use App\Models\OperationalBoatData;
use App\Models\Tug;
use App\Models\Cart;
use App\Models\Item;
use App\Models\User;
use App\Models\Barge;
use App\Models\JobHead;
use App\Models\cartJasa;
use App\Models\OrderHead;
use App\Models\JobDetails;
use App\Models\OrderDetail;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class CrewController extends Controller
{
    
    public function changeBranch(Request $request){
        User::find(Auth::user()->id)->update([
            'cabang' => $request->cabang
        ]);      

        return redirect('/dashboard')->with('status', 'Change Branch Successfully');
    }

    public function completedOrder(){
        // Get all the order within the logged in user within 6 month
        $orderHeads = OrderHead::with('user')->where(function($query){
            $query->where('status', 'like', 'Request Completed (Crew)')
            ->orWhere('status', 'like', 'Request Rejected By Logistic');
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->latest()->paginate(10);

        // Get the orderDetail from orders_id within the orderHead table 
        $order_id = OrderHead::where('user_id', Auth::user()->id)->pluck('id');
        $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();
        
        $in_progress = OrderHead::where(function($query){
            $query->where('status', 'like', 'Request In Progress By Logistic')
            ->orWhere('status', 'like', 'Items Ready')
            ->orWhere('status', 'like', 'On Delivery');
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->count();
        
        $completed = $orderHeads->count();

        return view('crew.crewDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress'));
    }

    public function inProgressOrder(){
        // Get all the order within the logged in user within 6 month
        $orderHeads = OrderHead::with('user')->where(function($query){
            $query->where('status', 'like', 'Request In Progress By Logistic')
            ->orWhere('status', 'like', 'Items Ready')
            ->orWhere('status', 'like', 'On Delivery');
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->paginate(10);

        // Get the orderDetail from orders_id within the orderHead table 
        $order_id = OrderHead::where('user_id', Auth::user()->id)->pluck('id');
        $orderDetails = OrderDetail::with('item')->whereIn('orders_id', $order_id)->get();

        $completed = OrderHead::where(function($query){
            $query->where('status', 'like', 'Request Completed (Crew)')
            ->orWhere('status', 'like', 'Request Rejected By Logistic');
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->count();
        
        $in_progress = $orderHeads->count();

        return view('crew.crewDashboard', compact('orderHeads', 'orderDetails', 'completed', 'in_progress'));
    }

    // order_tracker is a validation mechanism (somewhat) to validate if the order is already being processed or not,
    // scenario : 2 people open the page at the same time (A & B), then A processed the order, while B has not refreshed the same page (even tho we already create auto
    // refresh the pages every 60 seconds), then there will be a case where the order in guy B where it has not been processed still shown it can be processed, 
    // while it has already been processed by guy A, 
    // this mechanism will check if the order is already processed or not by checking the number => crew(1), logistic(2), supervisor A(3), supervisor B(4),
    // purchasing (5), if the tracker number is different then it will return the "error" message.

    public function orderPage(){
        // Select items to choose in the order page & carts according to the login user
        $items = Item::where('cabang', Auth::user()->cabang)->where('itemState', 'like', 'Available')->get();
        $barges = Barge::all();
        $tugs = Tug::all();
        $carts = Cart::with('item')->where('cabang', Auth::user()->cabang)->where('user_id', Auth::user()->id)->get();

        return view('crew.crewOrder', compact('items', 'carts', 'tugs', 'barges'));
    }

    public function addItemToCart(Request $request){
        // Validate Cart Request
        $request->validate([
            'item_id' => 'required',
            'department' => 'required',
            'quantity' => 'required|numeric|min:1',
        ]);

        // Check if the item state is on hold, then return error
        $check_item_state = Item::where('id', $request -> item_id)->pluck('itemState')[0];
        if($check_item_state == 'Hold'){
            return redirect('/crew/order')->with('error', 'Item is Unavailable');
        }

        // Check if the cart within the user is already > 12 items, then return with message
        $counts = Cart::where('user_id', Auth::user()->id)->count();
        if($counts ==  12){
            return redirect('/crew/order')->with('error', 'Cart is Full');
        }

         // Find if the same configuration of item is already exist in cart or no
         $itemExistInCart = Cart::where('user_id', Auth::user()->id)->where('item_id', $request->item_id)->where('department', $request->department)->first();

         if($itemExistInCart){
            Cart::find($itemExistInCart->id)->increment('quantity', $request->quantity);
         }else{
            // Else add item to the cart
            Cart::create([
                'user_id' => Auth::user()->id,
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
                'cabang' => Auth::user()->cabang,
                'department' => $request->department
            ]);  
         }
        
        return redirect('/crew/order')->with('status', 'Add Item Success');
    }

    public function deleteItemFromCart(Cart $cart){
        // Delete item from cart of the following user
        Cart::destroy($cart->id);

        return redirect('/crew/order')->with('status', 'Delete Item Success');
    }

    public function submitOrder(Request $request){
        $request -> validate([
            'tugName' => 'required|exists:tugs,tugName',
            'bargeName' => 'nullable'
        ]);

        // Find the cart of the following user
        $carts = Cart::where('user_id', Auth::user()->id)->where('cabang', Auth::user()->cabang)->get();

        // Validate cart size, if the cart size is zero then return error message
        if(count($carts) == 0){
            return redirect('/crew/order')->with('errorCart', 'Cart is Empty');
        }

        // Double check the item state, if there are items that is on 'Hold' status, then return error
        foreach($carts as $c){
            if($c -> item -> itemState == 'Hold'){
                return redirect('/crew/order')->with('errorCart', $c -> item -> itemName . ' is Currently Unavailable, Kindly Remove it From the Cart');
            }
        }

        // String formatting for boatName with tugName + bargeName
        $boatName = $request->tugName . '/' . $request->bargeName;

        // Create Order Head firstly
        $o_id = OrderHead::create([
            'user_id' => Auth::user()->id,
            'cabang' => Auth::user()->cabang,
            'boatName' => $boatName,
            'order_tracker' => 1,
            'status' => 'Request In Progress By Logistic'
        ]);

        // Formatted branch for SBK
        $cabang_arr = [
            'Jakarta' => 'JKT',
            'Banjarmasin' => 'BNJ',
            'Samarinda' => 'SMD',
            'Bunati' => 'BNT',
            'Babelan' => 'BBL',
            'Berau' => 'BER'
        ];

        // Update the order id and SBK
        OrderHead::find($o_id->id)->update([
            'order_id' => 'COID#' . $o_id->id,
            'noSbk' => $o_id->id . '/' . $cabang_arr[Auth::user()->cabang]
        ]);

        // Then fill the Order Detail with the cart items of the following Order Head
        foreach($carts as $c){
            $serialNo = Item::where('id', $c->item_id)->pluck('serialNo');
            $unit = Item::where('id', $c->item_id)->pluck('unit');
            OrderDetail::create([
                'orders_id' => $o_id -> id,
                'item_id' => $c -> item_id,
                'quantity' => $c -> quantity,
                'acceptedQuantity' => $c -> quantity,
                'unit' => $unit[0],
                'serialNo' => $serialNo[0],
                'department' => $c->department,
            ]);
        }

        // After all of that, emptying the cart items to reset the cart
        Cart::where('user_id', Auth::user()->id)->where('cabang', Auth::user()->cabang)->delete();

        return redirect('/dashboard')->with('status', 'Submit Request Order Success');
    }

    public function acceptOrder(OrderHead $orderHeads){
        // Get the order details of the following order
        // $orderDetails = OrderDetail::where('orders_id', $orderHeads->order_id)->get();

        if($orderHeads -> order_tracker == 1){
            return redirect('/dashboard')->with('error', 'Request Order Already Accepted');
        }

        OrderHead::find($orderHeads -> id)->update([
            'order_tracker' => 1
        ]);

        // Crew accept the order, then the status will be completed
        OrderHead::where('id', $orderHeads->id)->update([
            'status' => 'Request Completed (Crew)',
        ]);

        // foreach($orderDetails as $od){
        //     Item::where('id', $od -> item -> id)->update([
        //         'lastGiven' => date("d/m/Y")
        //     ]);
        //     Item::where('id', $od -> item -> id)->decrement('itemStock', $od -> quantity);
        // }

        return redirect('/dashboard')->with('status', 'Request Order Accepted');
    }

    // ============================ Task Section =========================

    public function taskPage()
    {   
        // Find There Is An Ongoing Task Running
        $ongoingTask = OperationalBoatData::where('user_id', Auth::user()->id)->where('status', 'On Going')->get();

        // Get all the tugs and barge
        $tugs = Tug::where('tugAvailability', true)->get();
        $barges = Barge::where('bargeAvailability', true)->get();

        // Check If There Is No Task Running, Redirect To Create Task Page
        if(count($ongoingTask) == 0){
            return view('crew.crewCreateTask', compact('tugs', 'barges'));

        // Check If There Is Task Running, Then User Can Access The Ongoing Task Page
        }elseif(count($ongoingTask) == 1){

            return redirect('/crew/ongoing-task')->with('error', 'There Is Still An Ongoing Task');
        }
    }

    public function createTaskPost(Request $request){
        if($request -> taskType == 'Non Operational'){
            // Validate Request For Non Operational Task (barge can be null)
            // kendari does not need cargoAmountStart
            if (Auth::user()->cabang == 'Kendari') {
                $validated = $request -> validate([
                    'tugName' => 'required|exists:tugs,tugName',
                    'bargeName' => 'nullable',
                    'portOfLoading' => 'required|string',
                    'portOfDischarge' => 'required|string',
                    'cargoAmountStart' => 'nullable|numeric',
                    'taskType' => 'required|in:Operational Shipment,Operational Transhipment,Non Operational'
                ]);
            }else{
                $validated = $request -> validate([
                    'tugName' => 'required|exists:tugs,tugName',
                    'bargeName' => 'nullable',
                    'portOfLoading' => 'required|string',
                    'portOfDischarge' => 'required|string',
                    'cargoAmountStart' => 'required|numeric|min:1',
                    'taskType' => 'required|in:Operational Shipment,Operational Transhipment,Non Operational'
                ]);
            }

            // Checking If The bargeName Null
            $checkTugIsAvailable = null;
            $checkBargeIsAvailable = null;

            // If The bargeName is Null Then We Only Need To Check tugAvailability
            if($request -> bargeName == ''){
                $checkTugIsAvailable = Tug::where('tugName', $request -> tugName)->first();

                if($checkTugIsAvailable -> tugAvailability == false){
                    return redirect()->back()->with('failed', 'Tug Already Used');
                }
            // Else We Check The Availability For Both Tugs & Barges
            }else{
                $checkTugIsAvailable = Tug::where('tugName', $request -> tugName)->first();
                $checkBargeIsAvailable = Barge::where('bargeName', $request -> bargeName)->first();

                if($checkTugIsAvailable -> tugAvailability == false){
                    return redirect()->back()->with('failed', 'Tug Already Used');
                }
                
                if($checkBargeIsAvailable -> bargeAvailability == false){
                    return redirect()->back()->with('failed', 'Barge Already Used');
                }
            }
        }else{
            // Validate Request For Other Task (barge cannot be null)
            if (Auth::user()->cabang == 'Kendari') {
                $validated = $request -> validate([
                    'tugName' => 'required|exists:tugs,tugName',
                    'bargeName' => 'nullable',
                    'portOfLoading' => 'required|string',
                    'portOfDischarge' => 'required|string',
                    'cargoAmountStart' => 'nullable|numeric',
                    'taskType' => 'required|in:Operational Shipment,Operational Transhipment,Non Operational'
                ]);
            }else{
                $validated = $request -> validate([
                    'tugName' => 'required|exists:tugs,tugName',
                    'bargeName' => 'required|exists:barges,bargeName',
                    'portOfLoading' => 'required|string',
                    'portOfDischarge' => 'required|string',
                    'cargoAmountStart' => 'required|numeric|min:1',
                    'taskType' => 'required|in:Operational Shipment,Operational Transhipment,Non Operational'
                ]);
            }

            // Check if Tug|Barge Already Taken or Not
            $checkTugIsAvailable = Tug::where('tugName', $request -> tugName)->first();
            $checkBargeIsAvailable = Barge::where('bargeName', $request -> bargeName)->first();

            if($checkTugIsAvailable -> tugAvailability == false){
                return redirect()->back()->with('failed', 'Tug Already Used');
            }
            
            if($checkBargeIsAvailable -> bargeAvailability == false){
                return redirect()->back()->with('failed', 'Barge Already Used');
            }
        }

        $validated['portOfLoading'] = strtoupper($request -> portOfLoading);
        $validated['portOfDischarge'] = strtoupper($request -> portOfDischarge);

        // Add User Id To The Collection
        $validated['user_id'] = Auth::user()->id;
        
        // Create The Data
        OperationalBoatData::create($validated);

        // Update Tug & Barge Availability
        Tug::where('tugName', $request -> tugName)->update([
            'tugAvailability' => false
        ]);

        if($request -> bargeName !== ''){
            Barge::where('bargeName', $request -> bargeName)->update([
                'bargeAvailability' => false
            ]);
        }

        // Then Redirect
        return redirect('/crew/ongoing-task')->with('status', 'Task Created Successfully');
    }

    public function ongoingTaskPage(){
        // Find There Is An Ongoing Task Running
        $ongoingTask = OperationalBoatData::with(['user'])->where('user_id', Auth::user()->id)->where('status', 'On Going')->get();

        // Check If There Is No Task Running, Redirect To Create Task Page
        if(count($ongoingTask) == 0){
            return redirect('/crew/create-task')->with('failed', 'No Ongoing Task Is Running');
        // Check If There Is Task Running, Then User Can Access The Ongoing Task Page
        }elseif(count($ongoingTask) == 1){
            // Get all the tugs and barge
            $tugs = Tug::all();
            $barges = Barge::all();

            return view('crew.crewCreateTaskDetail', compact('tugs', 'barges', 'ongoingTask'));
        }
    }

    public function updateOngoingTask(Request $request){
        $operationalData = OperationalBoatData::where('id', $request -> taskId)->first();
        if($operationalData -> taskType == 'Return Cargo'){
            // Validate All The Return Cargo Fields
            $validated = $request -> validate([
                'from' => 'required|string',
                'to' => 'required|string',
                'condition' => 'nullable|string',
                'cargoAmountEndCargo' => 'nullable|numeric|min:1',
                'description' => 'nullable',

                // Return Cargo
                'arrivalPODCargo' => 'nullable|date',
                'startAsideMVCargo' => 'nullable|date',
                'asideMVCargo' => 'nullable|date',
                'commMVCargo' => 'nullable|date',
                'compMVCargo' => 'nullable|date',
                'cOffMVCargo' => 'nullable|date',
                
                'departureTime' => 'nullable|date'
            ]);
        }elseif ($operationalData -> taskType == 'Towing Cargo') {
            $validated = $request -> validate([
                'from' => 'required|string',
                'to' => 'required|string',
                'condition' => 'nullable|string',
                'cargoAmountEndCargo' => 'nullable|numeric|min:1',
                'description' => 'nullable',

                // Return Cargo
                'depart_towing' => 'nullable|date',
                'Arrival_at_towing' => 'nullable|date',
                
            ]);
        }elseif($operationalData -> taskType == 'Operational Shipment' || $operationalData -> taskType == 'Operational Transhipment'){
            // Validate All The Fields
            $validated = $request -> validate([
                'from' => 'nullable|string',
                'to' => 'nullable|string',
                'MotherVessel' => 'nullable|string',
                'condition' => 'required|string',
                'customer' => 'nullable',
                'cargoAmountEnd' => 'nullable|numeric|min:1',
                'description' => 'nullable',
    
                // Operational Shipment
                'arrivalTime' => 'nullable|date',
                'departureTime' => 'nullable|date',
                'startAsideL' => 'nullable|date',
                'asideL' => 'nullable|date',
                'commenceLoadL' => 'nullable|date',
                'completedLoadingL' => 'nullable|date',
                'cOffL' => 'nullable|date',
                'StandByB' => 'nullable|date',
                'DOH' => 'nullable|date',
                'DOB' => 'nullable|date',
                'departurePOD' => 'nullable|date',
                'arrivalPODGeneral' => 'nullable|date',
                'startAsidePOD' => 'nullable|date',
                'asidePOD' => 'nullable|date',
                'commenceLoadPOD' => 'nullable|date',
                'completedLoadingPOD' => 'nullable|date',
                'cOffPOD' => 'nullable|date',
                'DOBPOD' => 'nullable|date',
    
                // Operational Transhipment
                'Arrival_at' => 'nullable|date',
                'faVessel' => 'nullable|date',
                'arrivalPOL' => 'nullable|date',
                'startAsideMVTranshipment' => 'nullable|date',
                'asideMVTranshipment' => 'nullable|date',
                'commMVTranshipment' => 'nullable|date',
                'compMVTranshipment' => 'nullable|date',
                'cOffMVTranshipment' => 'nullable|date',
                'departureTimeTranshipment' => 'nullable|date',

                // Samarinda Only
                'departureJetty' => 'nullable|date',
                'pengolonganNaik' => 'nullable|date',
                'pengolonganTurun' => 'nullable|date',
                'mooringArea' => 'nullable|date',

            ]);
        }else{
             // Validate All The Non Operational Fields
             $validated = $request -> validate([
                'from' => 'required|string',
                'to' => 'required|string',
                'condition' => 'nullable|string',
                'estimatedTime' => 'nullable|regex:/^[a-zA-Z0-9\s]+$/',
                'description' => 'nullable',

                // Non Operational
                'arrivalTime' => 'nullable|date',
                'startDocking' => 'nullable|date',
                'finishDocking' => 'nullable|date',
                'departurePOL' => 'nullable|date',
            ]);
        }

        // Validation Of CargoAmountEnd, Max 2 Updates => Crew can only update twice of cargo amount, Operational Transhipment and Return Cargo does not share the updates
        if($operationalData -> taskType == 'Return Cargo'){
            if($operationalData -> cargo2ChangeTracker < 3 && $operationalData -> cargoAmountEndCargo != $request -> cargoAmountEndCargo){
                $validated['cargo2ChangeTracker'] = $operationalData -> cargo2ChangeTracker + 1;
            }elseif($operationalData -> cargo2ChangeTracker == 3 && $operationalData -> cargoAmountEndCargo != $request -> cargoAmountEndCargo){
                return redirect()->back()->with('error', 'Has Reached The Maximum Amount Of Jumlah Kargo Update');
            }
        }else{
            if($operationalData -> cargoChangeTracker < 3 && $operationalData -> cargoAmountEnd != $request -> cargoAmountEnd){
                $validated['cargoChangeTracker'] = $operationalData -> cargoChangeTracker + 1;
            }elseif($operationalData -> cargoChangeTracker == 3 && $operationalData -> cargoAmountEnd != $request -> cargoAmountEnd){
                return redirect()->back()->with('error', 'Has Reached The Maximum Amount Of Jumlah Kargo Update');
            }
        }

        // $validated['from'] = strtolower($request -> from);
        // $validated['to'] = strtolower($request -> to);

        // Update The Following Task Id
        OperationalBoatData::where('id', $request -> taskId)->update($validated);

        $operationalData = OperationalBoatData::where('id', $request -> taskId)->first();

        // Data Calculation

        // Document = Departure POD - D.O.B
        $document = !empty($operationalData -> departurePOD) && !empty($operationalData -> DOB) ? date_diff(new DateTime($operationalData -> departurePOD), new DateTime($operationalData -> DOB))->format('%h.%i') : (double) 0;

        $calculation['document'] = (double) $document;

        if($operationalData -> taskType == 'Operational Shipment'){
            // khusus Babelan dan Kendari => Total Time = Arrival Time - Stand By Berlabuh (StandByB)
            if(Auth::user()->cabang == 'Kendari' or Auth::user()->cabang == 'Babelan'){
                $totalTime = !empty($operationalData -> arrivalTime) && !empty($operationalData -> StandByB) ? date_diff(new DateTime($operationalData -> arrivalTime), new DateTime($operationalData -> StandByB))->format('%D Days %H Hours') : 'n/a';
            }else{
                // Total Time = Arrival Time - Departure time
                $totalTime = !empty($operationalData -> arrivalTime) && !empty($operationalData -> departureTime) ? date_diff(new DateTime($operationalData -> arrivalTime), new DateTime($operationalData -> departureTime))->format('%D Days %H Hours') : 'n/a';
            }

            $calculation['totalTime'] = $totalTime;
        }elseif($operationalData -> taskType == 'Operational Transhipment'){

            // StandBy = (Arrival POL - prepare alongside) , note: prepare alongside is startAsideL
            $StandBy_POL = !empty($operationalData -> arrivalPOL) && !empty($operationalData -> startAsideL) ? date_diff(new DateTime($operationalData -> arrivalPOL), new DateTime($operationalData -> startAsideL))->format('%h.%i') : (double) 0;
            
            // StandBy_POD = (prepare alongside - Arrival POD) , note: prepare alongside is startAsideL
            $StandBy_POD = !empty($operationalData -> startAsideL) && !empty($operationalData -> arrivalPODGeneral) ? date_diff(new DateTime($operationalData -> startAsideL), new DateTime($operationalData -> arrivalPODGeneral))->format('%h.%i') : (double) 0;
            
            // Waiting_Doc_Boat = (DOB - cOffL)
            $Waiting_Doc_Boat = !empty($operationalData -> DOB) && !empty($operationalData -> cOffL) ? date_diff(new DateTime($operationalData -> DOB), new DateTime($operationalData -> cOffL))->format('%h.%i') : (double) 0;
           
            // Sailing time masuk = Departure POD - Arrival At
            $Sailing_time_MSK = !empty($operationalData -> departurePOD) && !empty($operationalData -> Arrival_at) ? date_diff(new DateTime($operationalData -> departurePOD), new DateTime($operationalData -> Arrival_at))->format('%h.%i') : (double) 0;
            
            // Sailing time keluar  = arrival POD -  Departure POD
            $Sailing_time_KLR = !empty($operationalData -> arrivalPODGeneral) && !empty($operationalData -> departurePOD) ? date_diff(new DateTime($operationalData -> arrivalPODGeneral), new DateTime($operationalData -> departurePOD))->format('%h.%i') : (double) 0;
           
            // Prepare Ldg = (Commence Load (L) -Aside (L))
            $prepareLdg = !empty($operationalData -> commenceLoadL) && !empty($operationalData -> asideL) ? date_diff(new DateTime($operationalData -> commenceLoadL), new DateTime($operationalData -> asideL))->format('%h.%i') : (double) 0;
           
            if(Auth::user()->cabang == 'Batu Licin'){
                // Ldg Time = (Complete loading - commance loading)
                $ldgTimeBL = !empty($operationalData -> completedLoadingL) && !empty($operationalData -> commenceLoadL) ? date_diff(new DateTime($operationalData -> completedLoadingL), new DateTime($operationalData -> commenceLoadL))->format('%h.%i') : (double) 0;

                // Ldg Rate = Quantity : Actual Ldg Time
                $ldgRate = $operationalData -> cargoAmountEnd != (double) 0 && (double) $ldgTimeBL > (double) 0 ? (double) $operationalData -> cargoAmountEnd / (double) $ldgTimeBL : (double) 0;
            }else{
                 // Sailing To Jetty = (Arrival POL - F/A Vessel)
                $sailingToJetty = !empty($operationalData -> arrivalPOL) && !empty($operationalData -> faVessel) ? date_diff(new DateTime($operationalData -> arrivalPOL), new DateTime($operationalData -> faVessel))->format('%h.%i') : (double) 0;

                // Ldg Time = (C/Off (L) - Commence Load (L))
                $ldgTime = !empty($operationalData -> cOffL) && !empty($operationalData -> commenceLoadL) ? date_diff(new DateTime($operationalData -> cOffL), new DateTime($operationalData -> commenceLoadL))->format('%h.%i') : (double) 0;
                
                // Ldg Rate = Quantity : Actual Ldg Time
                $ldgRate = $operationalData -> cargoAmountEnd != (double) 0 && (double) $ldgTime > (double) 0 ? (double) $operationalData -> cargoAmountEnd / (double) $ldgTime : (double) 0;
                
                
                // Unberthing = DOH - C/OFF (L)
                $unberthing = !empty($operationalData -> DOH) && !empty($operationalData -> cOffL) ? date_diff(new DateTime($operationalData -> DOH), new DateTime($operationalData -> cOffL))->format('%h.%i') : (double) 0;
            }
            // Berthing = (Aside (L) - Start Aside (L))
            $berthing = !empty($operationalData -> asideL) && !empty($operationalData -> startAsideL) ? date_diff(new DateTime($operationalData -> asideL), new DateTime($operationalData -> startAsideL))->format('%h.%i') : (double) 0;
            
            // Sailing to MV = (Arrival POD - Departure POD)
            $sailingToMV = !empty($operationalData -> arrivalPODGeneral) && !empty($operationalData -> departurePOD) ? date_diff(new DateTime($operationalData -> arrivalPODGeneral), new DateTime($operationalData -> departurePOD))->format('%h.%i') : (double) 0;

            // Disch Time = (Comp (MV) - Comm (MV))
            $dischTime = !empty($operationalData -> compMVTranshipment) && !empty($operationalData -> commMVTranshipment) ? date_diff(new DateTime($operationalData -> compMVTranshipment), new DateTime($operationalData -> commMVTranshipment))->format('%h.%i') : (double) 0;

            // Disch Rate / day = (Quantity - Actual Disch Time)
            $dischRate = $operationalData -> cargoAmountEnd != (double) 0 && !empty($dischTime) ? (double) $operationalData -> cargoAmountEnd - (double) $dischTime : (double) 0;

            // Manuever = (Aside (MV) - Start Aside (MV))
            $maneuver = !empty($operationalData -> asideMVTranshipment) && !empty($operationalData -> startAsideMVTranshipment) ? date_diff(new DateTime($operationalData -> asideMVTranshipment), new DateTime($operationalData -> startAsideMVTranshipment))->format('%h.%i') : (double) 0;
            
            
                // Cycle Time = Disch Time + Manuever + Sailing to MV + Unberthing + Ldg Time + Prepare Ldg + Berthing + Sailing to Jetty + time sailing masuk dan keluar
                $cycleTime = !empty($dischTime) && !empty($maneuver) && !empty($sailingToMV) && !empty($unberthing) && !empty($ldgTime) && !empty($prepareLdg) && !empty($berthing) && !empty($sailingToJetty) && !empty($StandBy) && !empty($Sailing_time_MSK)  && !empty($Sailing_time_KLR) && !empty($StandBy_POL) && !empty($StandBy_POD) && !empty($Waiting_Doc_Boat)? 
                (double) $dischTime + (double) $maneuver + (double) $sailingToMV + (double) $unberthing + (double) $ldgTime + (double) $prepareLdg + (double) $berthing + (double) $sailingToJetty + (double) ($Sailing_time_MSK)  + (double) ($Sailing_time_KLR) + (double) ($StandBy_POL) + (double) ($StandBy_POD) + (double) ($Waiting_Doc_Boat) : 
                (double) 0 ;
            
                // // Cycle Time = Disch Time + Manuever + Sailing to MV + Unberthing + Ldg Time + Prepare Ldg + Berthing + Sailing to Jetty
                // $cycleTime = !empty($dischTime) && !empty($maneuver) && !empty($sailingToMV) && !empty($unberthing) && !empty($ldgTime) && !empty($prepareLdg) && !empty($berthing) && !empty($sailingToJetty) ? 
                // (double) $dischTime + (double) $maneuver + (double) $sailingToMV + (double) $unberthing + (double) $ldgTime + (double) $prepareLdg + (double) $berthing + (double) $sailingToJetty : 
                // (double) 0;

                $calculation['Waiting_Doc_Boat'] = number_format((double) $Waiting_Doc_Boat, 2);
                $calculation['StandBy_POL'] = number_format((double) $StandBy_POL, 2);
                $calculation['StandBy_POD'] = number_format((double) $StandBy_POD, 2);
                $calculation['sailingTimeMsk'] = number_format((double) $Sailing_time_MSK, 2);
                $calculation['sailingTimeKlr'] = number_format((double) $Sailing_time_KLR, 2);
                $calculation['prepareLdg'] = number_format((double) $prepareLdg, 2);
                if(Auth::user()->cabang == 'Batu Licin'){
                    $calculation['ldgTime'] = number_format((double) $ldgTimeBL, 2);
                }else{
                    $calculation['sailingToJetty'] = number_format((double) $sailingToJetty, 2);
                    $calculation['ldgTime'] = number_format((double) $ldgTime, 2);
                    $calculation['unberthing'] = number_format((double) $unberthing, 2);
                }
            $calculation['ldgRate'] = $ldgRate;
            $calculation['berthing'] = number_format((double) $berthing, 2);
            $calculation['sailingToMV'] = number_format((double) $sailingToMV, 2);
            $calculation['dischTime'] = number_format((double) $dischTime, 2);
            $calculation['dischRate'] = $dischRate;
            $calculation['maneuver'] = number_format((double) $maneuver, 2);
            $calculation['cycleTime'] = number_format((double) $cycleTime, 2);
        }elseif($operationalData -> taskType == 'Return Cargo'){
            // Sailing to MV = (Arrival POD - Departure POD)
            $sailingToMVCargo = !empty($operationalData -> arrivalPODCargo) && !empty($operationalData -> departurePOD) ? date_diff(new DateTime($operationalData -> arrivalPODCargo), new DateTime($operationalData -> departurePOD))->format('%h.%i') : (double) 0;

            // Manuever = (Aside (MV) - Start Aside (MV))
            $maneuverCargo = !empty($operationalData -> asideMVCargo) && !empty($operationalData -> startAsideMVCargo) ? date_diff(new DateTime($operationalData -> asideMVCargo), new DateTime($operationalData -> startAsideMVCargo))->format('%h.%i') : (double) 0;

            // Disch Time = (Comp (MV) - Comm (MV))
            $dischTimeCargo = !empty($operationalData -> compMVCargo) && !empty($operationalData -> commMVCargo) ? date_diff(new DateTime($operationalData -> compMVCargo), new DateTime($operationalData -> commMVCargo))->format('%h.%i') : (double) 0;

            // Disch Rate / day = (Quantity - Actual Disch Time)
            $dischRateCargo = $operationalData -> cargoAmountEndCargo != (double) 0 && !empty($dischTime) ? (double) $operationalData -> cargoAmountEndCargo - (double) $dischTime : (double) 0;

            // Unberthing = DOH - C/OFF (L)
            $unberthing = !empty($operationalData -> DOH) && !empty($operationalData -> cOffL) ? date_diff(new DateTime($operationalData -> DOH), new DateTime($operationalData -> cOffL))->format('%h.%i') : (double) 0;

            // Berthing = (Aside (L) - Start Aside (L))
            $berthing = !empty($operationalData -> asideL) && !empty($operationalData -> startAsideL) ? date_diff(new DateTime($operationalData -> asideL), new DateTime($operationalData -> startAsideL))->format('%h.%i') : (double) 0;

            // Ldg Time = (C/Off (L) - Commence Load (L))
            $ldgTime = !empty($operationalData -> cOffL) && !empty($operationalData -> commenceLoadL) ? date_diff(new DateTime($operationalData -> cOffL), new DateTime($operationalData -> commenceLoadL))->format('%h.%i') : (double) 0;

            // Prepare Ldg = (Commence Load (L) -Aside (L))
            $prepareLdg = !empty($operationalData -> commenceLoadL) && !empty($operationalData -> asideL) ? date_diff(new DateTime($operationalData -> commenceLoadL), new DateTime($operationalData -> asideL))->format('%h.%i') : (double) 0;

            // Sailing To Jetty = (Arrival POL - F/A Vessel)
            $sailingToJetty = !empty($operationalData -> arrivalPOL) && !empty($operationalData -> faVessel) ? date_diff(new DateTime($operationalData -> arrivalPOL), new DateTime($operationalData -> faVessel))->format('%h.%i') : (double) 0;

            // Cycle Time = Disch Time + Manuever + Sailing to MV + Unberthing + Ldg Time + Prepare Ldg + Berthing + Sailing to Jetty
            $cycleTimeCargo = !empty($dischTimeCargo) && !empty($maneuverCargo) && !empty($sailingToMVCargo) && !empty($unberthing) && !empty($ldgTime) && !empty($prepareLdg) && !empty($berthing) && !empty($sailingToJetty) ? 
            (double) $dischTimeCargo + (double) $maneuverCargo + (double) $sailingToMVCargo + (double) $unberthing + (double) $ldgTime + (double) $prepareLdg + (double) $berthing + (double) $sailingToJetty : 
            (double) 0;

            $calculation['sailingToJetty'] = number_format((double) $sailingToJetty, 2);
            $calculation['prepareLdg'] = number_format((double) $prepareLdg, 2);
            $calculation['ldgTime'] = number_format((double) $ldgTime, 2);
            $calculation['berthing'] = number_format((double) $berthing, 2);
            $calculation['unberthing'] = number_format((double) $unberthing, 2);
            $calculation['sailingToMVCargo'] = number_format((double) $sailingToMVCargo, 2);
            $calculation['dischTimeCargo'] = number_format((double) $dischTimeCargo, 2);
            $calculation['dischRateCargo'] = $dischRateCargo;
            $calculation['maneuverCargo'] = number_format((double) $maneuverCargo, 2);
            $calculation['cycleTimeCargo'] = number_format((double) $cycleTimeCargo, 2);

        }elseif($operationalData -> taskType == 'Non Operational'){
            $totalLostDays = !empty($operationalData -> arrivalTime) && !empty($operationalData -> departurePOL) ? date_diff(new DateTime($operationalData -> arrivalTime), new DateTime($operationalData -> departurePOL))->format('%D Days %H Hours') : 'n/a';

            $calculation['totalLostDays'] = $totalLostDays;
        }

        // Update The Following Task Id
        OperationalBoatData::where('id', $request -> taskId)->update($calculation);

        // Then Redirect
        return redirect('/crew/ongoing-task')->with('status', 'Task Updated Successfully');
    }

    public function finalizeOngoingTask(Request $request){
        // Find The Task Using Task ID
        $data = OperationalBoatData::where('id', $request -> taskId)->first();
        
        // Helper Var
        if($data -> user -> cabang == 'Samarinda'){
            $operationShipment_loops = ['MotherVessel', 'condition', 'cargoAmountEnd', 'customer', 'arrivalTime', 'departureTime', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'departureJetty', 'pengolonganNaik', 'pengolonganTurun', 'mooringArea', 'DOB', 'departurePOD', 'arrivalPODGeneral', 'startAsidePOD', 'asidePOD', 'commenceLoadPOD', 'completedLoadingPOD', 'cOffPOD', 'DOBPOD'];
            $operationTranshipment_loops = ['MotherVessel', 'condition', 'cargoAmountEnd','customer', 'faVessel', 'departureJetty', 'pengolonganNaik', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'pengolonganTurun', 'mooringArea', 'DOB', 'departurePOD', 'arrivalPODGeneral', 'startAsideMVTranshipment', 'asideMVTranshipment', 'commMVTranshipment', 'compMVTranshipment', 'cOffMVTranshipment', 'departureTimeTranshipment'];
        
        }elseif($data -> user -> cabang == 'Kendari' || $data -> user -> cabang == 'Babelan'){
            $operationShipment_loops = ['MotherVessel', 'condition','StandByB', 'cargoAmountEnd', 'customer', 'arrivalTime', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL'];
            $operationTranshipment_loops = ['MotherVessel', 'condition','StandByB', 'cargoAmountEnd','customer', 'faVessel', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL'];
        
        }elseif($data -> user -> cabang == 'Morosi'){
            $operationShipment_loops = ['MotherVessel', 'condition', 'cargoAmountEnd', 'customer', 'arrivalTime', 'departureTime', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'departurePOD', 'arrivalPODGeneral', 'startAsidePOD', 'asidePOD', 'commenceLoadPOD', 'completedLoadingPOD', 'cOffPOD'];
            $operationTranshipment_loops = ['MotherVessel', 'condition', 'cargoAmountEnd','customer', 'faVessel', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'departurePOD', 'arrivalPODGeneral', 'startAsideMVTranshipment', 'asideMVTranshipment', 'commMVTranshipment', 'compMVTranshipment', 'cOffMVTranshipment', 'departureTimeTranshipment'];
        
        }elseif($data -> user -> cabang == 'Batu Licin'){
            $operationTranshipment_loops = [ 'MotherVessel','condition','customer', 'cargoAmountEnd', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL','DOB', 'departurePOD', 'arrivalPODGeneral', 'startAsideMVTranshipment', 'asideMVTranshipment', 'commMVTranshipment', 'compMVTranshipment', 'cOffMVTranshipment', 'departureTimeTranshipment'];
        }else{
            $operationShipment_loops = ['MotherVessel', 'condition', 'cargoAmountEnd', 'customer', 'arrivalTime', 'departureTime', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'DOH', 'DOB', 'departurePOD', 'arrivalPODGeneral', 'startAsidePOD', 'asidePOD', 'commenceLoadPOD', 'completedLoadingPOD', 'cOffPOD', 'DOBPOD'];
            $operationTranshipment_loops = ['MotherVessel', 'condition', 'cargoAmountEnd','customer', 'faVessel', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'DOH', 'DOB', 'departurePOD', 'arrivalPODGeneral', 'startAsideMVTranshipment', 'asideMVTranshipment', 'commMVTranshipment', 'compMVTranshipment', 'cOffMVTranshipment', 'departureTimeTranshipment'];
        
        }
       
        $returnCargo_loops = ['from', 'to', 'condition', 'cargoAmountEnd', 'cargoAmountEndCargo', 'arrivalPODCargo', 'startAsideMVCargo', 'asideMVCargo', 'commMVCargo', 'compMVCargo', 'cOffMVCargo', 'departureTime'];
        $nonOperational_loops = ['from', 'to', 'condition', 'estimatedTime', 'arrivalTime', 'startDocking', 'finishDocking', 'departurePOL'];


        // Looping Through Each Column To Check If There Is An Empty Data
        if($data -> taskType == 'Operational Shipment'){
            foreach($operationShipment_loops as $os){
                if($data -> $os == NULL){
                    return redirect()->back()->with('error', 'A Input Field Must Not Be Empty');
                }
            }
        }elseif($data -> taskType == 'Operational Transhipment'){
            foreach($operationTranshipment_loops as $ot){
                if($data -> $ot == NULL){
                    return redirect()->back()->with('error', 'B Input Field Must Not Be Empty');
                }
            }
        }elseif($data -> taskType == 'Return Cargo'){
            foreach($returnCargo_loops as $ot){
                if($data -> $ot == NULL){
                    return redirect()->back()->with('error', 'C Input Field Must Not Be Empty');
                }
            }
        }else{
            foreach($nonOperational_loops as $ot){
                if($data -> $ot == NULL){
                    return redirect()->back()->with('error', 'Input Field Must Not Be Empty');
                }
            }
        }

        // Find The Task, Then Update The Status
        if($data -> task_tracker == 1){
            return redirect('/crew/create-task')->with('failed', 'Task Already Been Processed');
        }else{
            OperationalBoatData::where('id', $request -> taskId)->update([
                'status' => 'Finalized',
                'task_tracker' => 1
            ]);
        }

        // Update Tug & Barge Availability
        Tug::where('tugName', $data -> tugName)->update([
            'tugAvailability' => true
        ]);

        if($data -> bargeName !== ''){
            Barge::where('bargeName', $data -> bargeName)->update([
                'bargeAvailability' => true
            ]);
        }

        // Then Redirect To Create Task Page
        return redirect('/crew/create-task')->with('status', 'Task Finalized Successfully');
    }

    public function continueReturnCargo(Request $request){
        // Validate The Task Type
        if($request -> taskType != 'Operational Transhipment') {
            return redirect()->back()->with('error', 'Wrong Task');
        }

        // Helper Var
        if($request -> cabang == 'Samarinda'){
            $operationTranshipment_loops = ['MotherVessel','condition', 'customer', 'cargoAmountEnd', 'faVessel', 'departureJetty', 'pengolonganNaik', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'pengolonganTurun', 'mooringArea', 'DOB', 'departurePOD', 'arrivalPODGeneral', 'startAsideMVTranshipment', 'asideMVTranshipment', 'commMVTranshipment', 'compMVTranshipment', 'cOffMVTranshipment', 'departureTimeTranshipment'];
        }elseif($request -> cabang == 'Kendari' || $request -> cabang == 'Babelan'){
            $operationTranshipment_loops = ['MotherVessel','condition', 'StandByB', 'cargoAmountEnd', 'faVessel', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL'];
        }elseif($request -> cabang == 'Morosi'){
            $operationTranshipment_loops = ['MotherVessel','condition', 'customer', 'cargoAmountEnd', 'faVessel', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'departurePOD', 'arrivalPODGeneral', 'startAsideMVTranshipment', 'asideMVTranshipment', 'commMVTranshipment', 'compMVTranshipment', 'cOffMVTranshipment', 'departureTimeTranshipment'];
        }elseif($request -> cabang == 'Batu Licin'){
            $operationTranshipment_loops = [ 'MotherVessel','condition','customer', 'cargoAmountEnd', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL','DOB', 'departurePOD', 'arrivalPODGeneral', 'startAsideMVTranshipment', 'asideMVTranshipment', 'commMVTranshipment', 'compMVTranshipment', 'cOffMVTranshipment', 'departureTimeTranshipment'];
        }else{
            $operationTranshipment_loops = ['MotherVessel','condition', 'customer', 'cargoAmountEnd', 'faVessel', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'DOH', 'DOB', 'departurePOD', 'arrivalPODGeneral', 'startAsideMVTranshipment', 'asideMVTranshipment', 'commMVTranshipment', 'compMVTranshipment', 'cOffMVTranshipment', 'departureTimeTranshipment'];
        }
        
        $data = OperationalBoatData::where('id', $request -> taskId)->first();

        // Then Validate Empty Fields
        foreach($operationTranshipment_loops as $ot){
            if($data -> $ot == NULL){
                return redirect()->back()->with('error', 'Input Field Must Not Be Empty');
            }
        };

        // Then Change The Task Type Into Return Cargo
        $data->update([
            'taskType' => 'Return Cargo'
        ]);

        return redirect()->back()->with('status', 'Task Updated Successfully');
    }

    public function continueTowingCargo(Request $request){
        // Validate The Task Type
        if($request -> taskType != 'Operational Transhipment') {
            return redirect()->back()->with('error', 'Wrong Task');
        }

        // Helper Var
        if($request -> cabang == 'Babelan'){
            $operationTranshipment_loops = ['MotherVessel','condition', 'StandByB', 'cargoAmountEnd', 'faVessel', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL'];
        }
        
        $data = OperationalBoatData::where('id', $request -> taskId)->first();

        // Then Validate Empty Fields
        foreach($operationTranshipment_loops as $ot){
            if($data -> $ot == NULL){
                return redirect()->back()->with('error', 'Input Field Must Not Be Empty');
            }
        };

        // Then Change The Task Type Into Return Cargo
        $data->update([
            'taskType' => 'Towing Cargo'
        ]);

        return redirect()->back()->with('status', 'Task Updated Successfully');
    }

    public function cancelOngoingTask(Request $request){
        // Update Tug & Barge Availability
        Tug::where('tugName', $request -> tugName)->update([
            'tugAvailability' => true
        ]);

        if($request -> bargeName !== ''){
            Barge::where('bargeName', $request -> bargeName)->update([
                'bargeAvailability' => true
            ]);
        }

        // Find The Task, Then Delete It
        OperationalBoatData::where('id', $request -> taskId)->delete();

        // Then Redirect To Create Task Page
        return redirect('/crew/create-task')->with('status', 'Task Deleted Successfully');
    }

    public function cancelTowingOngoingTask(Request $request){
        // Update Tug & Barge Availability
        Tug::where('tugName', $request -> tugName)->update([
            'tugAvailability' => true
        ]);

        if($request -> bargeName !== ''){
            Barge::where('bargeName', $request -> bargeName)->update([
                'bargeAvailability' => true
            ]);
        }

        // Find The Task, Then change It
        OperationalBoatData::where('id', $request -> taskId)->update([
            'taskType' => 'Operational Transhipment'
        ]);

        // Then Redirect To Create Task Page
        return redirect('/crew/create-task')->with('status', 'Towing Cancelled Successfully');
    }

    //job request
    public function completedJobRequest(){
        // Get all the job request within the logged in user within 6 month
        $JobRequestHeads = JobHead::with('user')->where(function($query){
            $query->where('status', 'like', 'Job Request Completed (Crew)')
            ->orWhere('status', 'like', 'Job Request Rejected By Logistic');
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->latest()->paginate(10);

         // Get the jobDetail from jasa_id within the orderHead table 
        $job_id = JobHead::where('user_id', Auth::user()->id)->pluck('id');
        $jobDetails = JobDetails::whereIn('jasa_id', $job_id)->get();
        // Count the completed & in progress job Requests
        
        $job_in_progress = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request In Progress By Logistic');           
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->count();
        
        $completedJR = $JobRequestHeads->count();
        return view('crew.crewDashboard', compact('job_in_progress','JobRequestHeads' , 'jobDetails', 'completedJR'));
    }

    public function inProgressJobRequest(){
        // Get all the order within the logged in user within 6 month
        $JobRequestHeads = JobHead::with('user')->where(function($query){
            $query->where('status', 'like', 'Job Request In Progress By Logistic');
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->paginate(10);

        // Get the orderDetail from orders_id within the orderHead table 
        $job_id = $JobRequestHeads->pluck('id');
        $jobDetails = JobDetails::whereIn('jasa_id', $job_id)->get();

        $job_completed = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request Completed (Crew)')
            ->orWhere('status', 'like', 'Job Request Rejected By Logistic');
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->count();
        
        $JR_in_progress = $JobRequestHeads->count();

        return view('crew.crewDashboard', compact('JR_in_progress' ,'jobDetails' ,'JobRequestHeads','job_completed'));
    }

    public function makeJobPage() {
        // Get all the tugs, barges, and cart of the following user
        $barges = Barge::all();
        $tugs = Tug::all();
        $carts = cartJasa::where('user_id', Auth::user()->id)->get();

        return view('crew.crewMakejob', compact('carts', 'tugs', 'barges'));
    }

    public function ViewJobPage() {
        // Get all the job request within the logged in user within 6 month
        $JobRequestHeads = JobHead::with('user')->where('cabang' , Auth::user()->cabang)->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->latest()->paginate(7); 
        $job_id = $JobRequestHeads->pluck('id');
        $jobDetails = JobDetails::whereIn('jasa_id', $job_id)->get();

        // Count the completed & in progress job Requests
        $job_completed = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request Completed')
            ->orWhere('status', 'like', 'Job Request Rejected By'. '%');
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->count();
        
        $job_in_progress = JobHead::where(function($query){
            $query->where('status', 'like', 'Job Request In Progress By'. '%')
            ->orWhere('status', 'like', 'Job Request Approved By' . '%');
        })->where('user_id', 'like', Auth::user()->id)->whereYear('created_at', date('Y'))->count();


        return view('crew.crewListJobOrder', compact('job_completed','job_in_progress','jobDetails','JobRequestHeads'));
    }

    public function addjasaToCart(Request $request){
        // Validate Cart Request
        $checkinput = $request->validate([
            'tugName' => ['nullable','regex:/^[A-Za-z -][A-Za-z0-9 -]*$/'] ,
            'bargeName' => ['nullable','regex:/^[A-Za-z -][A-Za-z0-9 -]*$/'] ,
            'quantity' => ['required' , 'numeric'],
            'note' => ['required' , 'string']
        ]);

        // Check if the cart within the user is already > 12 items, then cart is full & return with message
        $counts = cartJasa::where('user_id', Auth::user()->id)->count();
        if($counts ==  12){
            return redirect('/crew/make-Job')->with('error', 'Cart is Full');
        }

        

        // Find if the same configuration of item is already exist in cart or no
        $itemExistInCart = cartJasa::where('user_id', Auth::user()->id)->whereRaw('LOWER(`note`) LIKE ? ',strtolower($request->note))->where('lokasi', $request->lokasi)->first();
        if($itemExistInCart){
            cartJasa::find($itemExistInCart->id)->increment('quantity', $request->quantity);
        }else{
        // Else add item to the cart
            cartJasa::create([
                'tugName' => $request->tugName ,
                'bargeName' => $request->bargeName ,
                'lokasi' => $request->lokasi ,
                'quantity' => $request->quantity ,
                'note' => $request->note,
                // Add cabang & user id to the cart
                'cabang' => Auth::user()->cabang,
                'user_id'=> Auth::user()->id
            ]);
        }
 
        return Redirect::back()->withInput()->with('success', 'Add Item Success');
    }

    public function deleteJasaFromCart(cartJasa $cart){
        // Delete item from cart of the following user
        cartJasa::destroy($cart->id);

        return redirect('/crew/make-Job')->with('status', 'Delete Item Success');
    }

    public function submitJasa(){
        // Find the cart of the following user
        $carts = cartJasa::where('user_id', Auth::user()->id)->get();

        // Validate cart size
        if(count($carts) == 0){
            return redirect('/crew/make-Job')->with('errorCart', 'Cart is Empty');
        }
        
            // Formatting the PR format requirements
            $month_arr_in_roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

            $cabang_arr = [
                'Jakarta' => 'JKT',
                'Banjarmasin' => 'BNJ',
                'Samarinda' => 'SMD',
                'Bunati' => 'BNT',
                'Babelan' => 'BBL',
                'Berau' => 'BER',
                "Kendari" => 'KDR',
                "Morosi" => "MRS"
            ];
         
            
            // Create job request Head
            $JobHead = JobHead::create([
                'user_id' => Auth::user()->id,
                'created_by' => Auth::user()->name,
                'cabang' => Auth::user()->cabang,
                'status' => 'Job Request In Progress By Logistics',
                'jrDate' => date("Y/m/d"),
                'Headjasa_tracker_id' => 1 ,
            ]);

            $Jr_id = $JobHead -> id;
            $headID = 'JRID#' . $JobHead -> id;
            $first_char_name = strtoupper(Auth::user()->name[0]);
            $location = $cabang_arr[Auth::user()->cabang];
            $month = date('n');
            $month_to_roman = $month_arr_in_roman[$month - 1];
            $year = date('Y');

            // Create the JR Number => 001.A/JR-SMD/IX/2021
            $Jr_number = $Jr_id . '.' . $first_char_name . '/' . 'JR-'. $location . '/' . $month_to_roman . '/' . $year;

            JobHead::find($JobHead->id)->update([
                'noJr' => $Jr_number,
                'Headjasa_id' => $headID,
            ]);

            // Then fill the job Detail with the cart items
            foreach($carts as $c){
                JobDetails::create([
                    'user_id' => Auth::user()->id,
                    'jasa_id' => $JobHead -> id,
                    'cabang' => $c->cabang,
                    'tugName' => $c ->tugName,
                    'bargeName' => $c ->bargeName,
                    'quantity' => $c->quantity ,
                    'lokasi' => $c ->lokasi ,
                    'note' => $c ->note,
                ]);
            }
    
            // Emptying the cart items
            cartJasa::where('user_id', Auth::user()->id)->delete();
        return redirect('/crew/Job_Request_List')->with('status', 'Submit Order Success');
    }
}
