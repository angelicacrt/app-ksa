<?php

namespace App\Http\Controllers;

use App\Models\Tug;
use App\Models\Barge;
use Illuminate\Http\Request;
use App\Exports\DailyReportsExport;
use App\Models\OperationalBoatData;
use Illuminate\Support\Facades\Auth;

class AdminOperationalController extends Controller
{   
    public function lostTimeDetailsPage(){
        $operationalData = OperationalBoatData::where('status', 'On Going')->where('taskType', 'Non Operational')->paginate(6);

        return view('adminOperational.adminOperationalLostTimeDetails', compact('operationalData'));
    }

    public function reportTranshipmentPage(){
        // Only Get The Tug|Barge That Is Inactive
        $tugs = Tug::where('tugAvailability', true)->get();
        $barges = Barge::where('bargeAvailability', true)->get();
        $operationalData = NULL;

        return view('adminOperational.adminOperationalReportTranshipment', compact('operationalData', 'tugs', 'barges'));
    }

    public function searchDailyReports(Request $request){
        if($request->ajax()){
            try{
                // Helper var
                $tugName = $request -> tugName;
                $bargeName = $request -> bargeName;
                $month = $request -> month;
                $year = $request -> year;
                $taskType = $request -> taskType;

                if($request -> taskType == 'Operational Transhipment'){
                    $operationalData = OperationalBoatData::where('status', 'Finalized')->where('tugName', $tugName)->where('bargeName', $bargeName)->where(function($query){
                        $query -> where('taskType', 'Operational Transhipment')
                        ->orWhere('taskType', 'Return Cargo');
                    })->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
                }else{
                    // $operationalData = OperationalBoatData::where('status', 'Finalized')->where('tugName', $tugName)->where('bargeName', $bargeName)->where('taskType', $taskType)->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
                    $operationalData = OperationalBoatData::where('status', 'Finalized')->where('tugName', $tugName)->where('bargeName', $bargeName)->where('taskType', $taskType)->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
                }

                return view('adminOperational.adminOperationalReportTranshipmentTable', compact('operationalData', 'taskType', 'tugName', 'bargeName', 'month', 'year'))->render();
            }catch(\Throwable $e){
                $operationalData = null;
                return view('adminOperational.adminOperationalReportTranshipmentTable', compact('operationalData'))->render();
            }
        }
    }

    public function downloadDailyReports(Request $request){
        $nameFormat = '';
        if($request -> taskType == 'Operational Shipment'){
            $nameFormat = 'Daily-Report-Shipment';
        }elseif($request -> taskType == 'Non Operational'){
            $nameFormat = 'Daily-Report-Non-Operational';
        }else{
            $nameFormat = 'Daily-Report-Transhipment';
        }

        return (new DailyReportsExport($request -> taskType, $request -> tugName, $request -> bargeName, $request -> month, $request -> year))->download($nameFormat . '_' .  date("d-m-Y") . '.xlsx');
    }

    public function monitoringPage(){
        // Only Get The Tug|Barge That Is Active
        $tugs = Tug::where('tugAvailability', false)->get();
        $barges = Barge::where('bargeAvailability', false)->get();

        // Get All The Destination|Origin
        $from = OperationalBoatData::where('status', 'On Going')->groupBy('from')->select('from')->get();
        $to = OperationalBoatData::where('status', 'On Going')->groupBy('to')->select('to')->get();

        $operationalData = null;

        // $operationalData = OperationalBoatData::where('status', 'Finalized')->where('taskType', 'Operational Transhipment')->select('tug_id', DB::raw('CONCAT(MONTHNAME(created_at),"/",YEAR(created_at)) as Shipment'))->get();

        // dd($operationalData);
        // $d1 = new DateTime($operationalData -> arrivalPOL);
        // $d2 = new DateTime($operationalData -> faJetty);
        // dd(strtotime($operationalData -> arrivalPOL) - strtotime($operationalData -> faJetty) / 3600);
        // dd((double)date_diff($d1, $d2)->format('%h.%i') + (double)date_diff($d1, $d2)->format('%h.%i'));

        return view('adminOperational.adminOperationalMonitoringShipment', compact('tugs', 'barges', 'from', 'to', 'operationalData'));
    }

    public function searchMonitoring(Request $request){
        if($request->ajax()){  
            try{
                $operationalData = OperationalBoatData::where('status', 'On Going')->where('tugName', $request -> tugName)->where('bargeName', $request -> bargeName)->where('from', $request -> from)->where('to', $request -> to)->where('taskType', $request -> taskType)->latest()->first();
                
                return view('adminOperational.adminOperationalJumbotronMonitoring', compact('operationalData'))->render();

                // =================================== Delete Later =====================================
                // if($request -> taskType == 'Operational Shipment'){
                //     // // Total Time = Arrival Time - Departure time
                //     // $total_time = !empty($operationalData -> arrivalTime) && !empty($operationalData -> departureTime) ? date_diff(new DateTime($operationalData -> arrivalTime), new DateTime($operationalData -> departureTime))->format('%D Days %H Minutes') : '';

                //     return view('adminOperational.adminOperationalJumbotronMonitoring', compact('operationalData'))->render();
                // }elseif($request -> taskType == 'Operational Transhipment'){
                //     // Sailing To Jetty = (Arrival POL - F/A Vessel)
                //     $sailingToJetty = !empty($operationalData -> arrivalPOL) && !empty($operationalData -> faVessel) ? date_diff(new DateTime($operationalData -> arrivalPOL), new DateTime($operationalData -> faVessel))->format('%h.%i') : '';

                //     // Prepare Ldg = (Commence Load (L) -Aside (L))
                //     $prepareLdg = !empty($operationalData -> commenceLoadL) && !empty($operationalData -> asideL) ? date_diff(new DateTime($operationalData -> commenceLoadL), new DateTime($operationalData -> asideL))->format('%h.%i') : '';

                //     // Ldg Time = (C/Off (L) - Commence Load (L))
                //     $ldgTime = !empty($operationalData -> cOffL) && !empty($operationalData -> commenceLoadL) ? date_diff(new DateTime($operationalData -> cOffL), new DateTime($operationalData -> commenceLoadL))->format('%h.%i') : '';

                //     // Ldg Rate = Quantity : Actual Ldg Time
                //     $ldgRate = $operationalData -> cargoAmountEnd != 0 && !empty($ldgTime) ? (double) $operationalData -> cargoAmountEnd / (double) $ldgTime : '';

                //     // Berthing = (Aside (L) - Start Aside (L))
                //     $berthing = !empty($operationalData -> asideL) && !empty($operationalData -> startAsideL) ? date_diff(new DateTime($operationalData -> asideL), new DateTime($operationalData -> startAsideL))->format('%h.%i') : '';

                //     // Unberthing = DOH - C/OFF (L)
                //     $unberthing = !empty($operationalData -> DOH) && !empty($operationalData -> cOffL) ? date_diff(new DateTime($operationalData -> DOH), new DateTime($operationalData -> cOffL))->format('%h.%i') : '';

                //     // Sailing to MV = (Arrival POD - Departure POD)
                //     $sailingToMV = !empty($operationalData -> arrivalPODGeneral) && !empty($operationalData -> departurePOD) ? date_diff(new DateTime($operationalData -> arrivalPODGeneral), new DateTime($operationalData -> departurePOD))->format('%h.%i') : '';

                //     // Disch Time = (Comp (MV) - Comm (MV))
                //     $dischTime = !empty($operationalData -> compMVTranshipment) && !empty($operationalData -> commMVTranshipment) ? date_diff(new DateTime($operationalData -> compMVTranshipment), new DateTime($operationalData -> commMVTranshipment))->format('%h.%i') : '';

                //     // Disch Rate / day = (Quantity - Actual Disch Time)
                //     $dischRate = $operationalData -> cargoAmountEnd != 0 && !empty($dischTime) ? (double) $operationalData -> cargoAmountEnd - (double) $dischTime : '';

                //     // Manuever = (Aside (MV) - Start Aside (MV))
                //     $maneuver = !empty($operationalData -> asideMVTranshipment) && !empty($operationalData -> startAsideMVTranshipment) ? date_diff(new DateTime($operationalData -> asideMVTranshipment), new DateTime($operationalData -> startAsideMVTranshipment))->format('%h.%i') : '';

                //     // Cycle Time = Disch Time + Manuever + Sailing to MV + Unberthing + Ldg Time + Prepare Ldg + Berthing + Sailing to Jetty
                //     $cycleTime = !empty($dischTime) && !empty($maneuver) && !empty($sailingToMV) && !empty($unberthing) && !empty($ldgTime) && !empty($prepareLdg) && !empty($berthing) && !empty($sailingToJetty) ? 
                //     (double) $dischTime + (double) $maneuver + (double) $sailingToMV + (double) $unberthing + (double) $ldgTime + (double) $prepareLdg + (double) $berthing + (double) $sailingToJetty : 
                //     '';

                    // return view('adminOperational.adminOperationalJumbotronMonitoring', compact('operationalData', 'sailingToJetty', 'ldgTime', 'ldgRate', 'unberthing', 'sailingToMV', 'maneuver', 'dischTime', 'dischRate', 'cycleTime', 'total_time'))->render();
                    // return view('adminOperational.adminOperationalJumbotronMonitoring', compact('operationalData'))->render();
                // }elseif($request -> taskType == 'Return Cargo'){
                    
                //     // Sailing To MV = (Arrival POD - Departure POD)
                //     $sailingToMV = !empty($operationalData -> arrivalPODCargo) && !empty($operationalData -> departurePOD) ? date_diff(new DateTime($operationalData -> arrivalPODCargo), new DateTime($operationalData -> departurePOD))->format('%h.%i') : '';

                //     // Maneuver = (Aside (MV) - Start Aside (MV))
                //     $maneuver = !empty($operationalData -> asideMVCargo) && !empty($operationalData -> startAsideMVCargo) ? date_diff(new DateTime($operationalData -> asideMVCargo), new DateTime($operationalData -> startAsideMVCargo))->format('%h.%i') : '';

                //     // Disch Time = (Comp (MV) - Comm (MV))
                //     $dischTime = !empty($operationalData -> compMVCargo) && !empty($operationalData -> commMVCargo) ? date_diff(new DateTime($operationalData -> compMVCargo), new DateTime($operationalData -> commMVCargo))->format('%h.%i') : '';

                //     // Disch Rate / day = (Quantity - Actual Disch Time)
                //     $dischRate = $operationalData -> cargoAmountEnd != 0 && !empty($dischTime) ? (double) $operationalData -> cargoAmountEnd - (double) $dischTime : '';

                //     // Unberthing = DOH - C/OFF (L)
                //     $unberthing = !empty($operationalData -> DOH) && !empty($operationalData -> cOffL) ? date_diff(new DateTime($operationalData -> DOH), new DateTime($operationalData -> cOffL))->format('%h.%i') : '';

                //     // Berthing = (Aside (L) - Start Aside (L))
                //     $berthing = !empty($operationalData -> asideL) && !empty($operationalData -> startAsideL) ? date_diff(new DateTime($operationalData -> asideL), new DateTime($operationalData -> startAsideL))->format('%h.%i') : '';

                //     // Ldg Time = (C/Off (L) - Commence Load (L))
                //     $ldgTime = !empty($operationalData -> cOffL) && !empty($operationalData -> commenceLoadL) ? date_diff(new DateTime($operationalData -> cOffL), new DateTime($operationalData -> commenceLoadL))->format('%h.%i') : '';

                //     // Prepare Ldg = (Commence Load (L) - Aside (L))
                //     $prepareLdg = !empty($operationalData -> commenceLoadL) && !empty($operationalData -> asideL) ? date_diff(new DateTime($operationalData -> commenceLoadL), new DateTime($operationalData -> asideL))->format('%h.%i') : '';

                //     // Sailing To Jetty = (Arrival POL - F/A Vessel)
                //     $sailingToJetty = !empty($operationalData -> arrivalPOL) && !empty($operationalData -> faVessel) ? date_diff(new DateTime($operationalData -> arrivalPOL), new DateTime($operationalData -> faVessel))->format('%h.%i') : '';

                //     // Cycle Time = Disch Time + Manuever + Sailing to MV + Unberthing + Ldg Time + Prepare Ldg + Berthing + Sailing to Jetty
                //     $cycleTime = !empty($dischTime) && !empty($maneuver) && !empty($sailingToMV) && !empty($unberthing) && !empty($ldgTime) && !empty($prepareLdg) && !empty($berthing) && !empty($sailingToJetty) ? 
                //     (double) $dischTime + (double) $maneuver + (double) $sailingToMV + (double) $unberthing + (double) $ldgTime + (double) $prepareLdg + (double) $berthing + (double) $sailingToJetty : 
                //     '';

                //     // return view('adminOperational.adminOperationalJumbotronMonitoring', compact('operationalData', 'sailingToMV', 'maneuver', 'dischTime', 'dischRate', 'cycleTime', 'total_time'))->render();
                //     return view('adminOperational.adminOperationalJumbotronMonitoring', compact('operationalData'))->render();
                // }


            }catch(\Throwable $e){
                $operationalData = null;
                return view('adminOperational.adminOperationalJumbotronMonitoring', compact('operationalData'))->render();
            }
        }
    }

    public function addTugboatPage(){
        $tugs = Tug::latest()->paginate(10);

        return view('adminOperational.adminOperationalAddTugboat', compact('tugs'));
    }

    public function addNewTugboat(Request $request){
        $validated = $request -> validate([
            'tugName' => 'required|string|unique:tugs' ,
            'gt' => 'required|string',
            'nt' => 'required|string',
            'master' => 'required|string',
            'flag' => 'required|string',
            'IMONumber' => 'required|string',
            'callSign' => 'required|string',
        ]);

        Tug::create([
            "tugName" => $request->tugName,
            "gt" => $request->gt,
            "nt" => $request->nt,
            "master" => $request->master,
            "flag" => $request->flag,
            "IMONumber" => $request->IMONumber,
            "callSign" => $request->callSign,
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->back()->with('status', 'Added Successfully');
    }

    public function deleteTugboat(Request $request){
        $request -> validate([
            'tugId' => 'required'
        ]);

        Tug::where('id', $request -> tugId)->delete();

        return redirect()->back()->with('status', 'Deleted Successfully');
    }

    public function searchTugboat(Request $request){
        if($request->ajax()){
            $tugs = Tug::where('tugName', 'like', '%' . $request -> searchData . '%')->paginate(10);

            return view('adminOperational.adminOperationalAddTugboatTable', compact('tugs'))->render();
        }
    }

    public function paginationTugBoat(Request $request){
        if($request->ajax()){
            $tugs = Tug::latest()->paginate(10);

            return view('adminOperational.adminOperationalAddTugboatTable', compact('tugs'))->render();
        }
    }

    public function addBargePage(){
        $barges = Barge::latest()->paginate(10);

        return view('adminOperational.adminOperationalAddBarge', compact('barges'));
    }

    public function addNewBarge(Request $request){
        $validated = $request -> validate([
            'bargeName' => 'required|string|unique:barges',
            'gt' => 'required|string',
            'nt' => 'required|string',
            'flag' => 'required|string',
        ]);

        Barge::create([
            'user_id' => Auth::user()->id,
            "bargeName" => $request->bargeName,
            "gt" => $request->gt,
            "nt" => $request->nt,
            "flag" => $request->flag,
        ]);

        return redirect()->back()->with('status', 'Added Successfully');
    }

    public function searchBarge(Request $request){
        if($request->ajax()){
            $barges = Barge::where('bargeName', 'like', '%' . $request -> searchData . '%')->paginate(10);

            return view('adminOperational.adminOperationalAddBargeTable', compact('barges'))->render();
        }
    }

    public function paginationBarge(Request $request){
        if($request->ajax()){
            $barges = Barge::latest()->paginate(10);

            return view('adminOperational.adminOperationalAddTugboatTable', compact('barges'))->render();
        }
    }

    public function deleteBarge(Request $request){
        $request -> validate([
            'bargeId' => 'required'
        ]);

        Barge::where('id', $request -> bargeId)->delete();

        return redirect()->back()->with('status', 'Deleted Successfully');
    }
}
