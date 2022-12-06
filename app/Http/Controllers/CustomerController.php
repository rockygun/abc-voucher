<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseTransaction;
use App\Models\Voucher;
use DB;
use Exception;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function checkEligible(Request $request, $customerId)
    {
        $transactions = PurchaseTransaction::select(
            "customer_id",
            DB::raw("COUNT(id) as ctr"),
            DB::raw("SUM(total_spent) as total_transactions"),
        )->where("customer_id", $customerId)
        ->whereDate("transaction_at", ">", now()->subDays(30)->endOfDay())
        ->groupBy("customer_id")
        ->first();
        
        if(isset($transactions) && $transactions->ctr >= 3 && $transactions->total_transactions >= 100){
            Voucher::where("status", "ACTIVE")->limit(1)
            ->update([
                "customer_id" => $customerId,
                "status" => "LOCKED",
            ]);

            return "VOUCHER LOCKED";
        }

        return "NOT ELIGIBLE";
    }

    public function validatePhoto(Request $request, $customerId)
    {
        // FOR TESTING PURPOSE, CUSTOMER WITH ODD ID WILL RETURN TRUE
        // FOR TESTING PURPOSE, CUSTOMER WITH EVEN ID WILL RETURN FALSE

        $voucher = Voucher::where("customer_id", $customerId)->first();
        if(!isset($voucher)){
            throw new Exception("PLEASE CHECK IF CUSTOMER ELIGIBLE FOR PHOTO SUBMISSION FIRST", 403);
        }

        if($customerId%2 == 0){
            Voucher::where("customer_id", $customerId)->update([
                "customer_id" => null,
                "status" => "ACTIVE",
            ]);

            return "Photo Not Valid";
        }else{
            $voucher = Voucher::where("customer_id", $customerId)->first();
            $lock = $voucher->updated_at;
            $interval = $lock->diff(now())->format("%i");

            if($interval > 10){
                Voucher::where("customer_id", $customerId)->update([
                    "customer_id" => null,
                    "status" => "ACTIVE",
                ]);
    
                return "Photo Not Valid";
            }else{
                $voucher->status = "USED";
                $voucher->save();
                return "CONGRATULATION, THIS IS YOUR VOUCHER REDEEM CODE :  " . $voucher->code;
            }
        }   

        $transactions = PurchaseTransaction::select(
            "customer_id",
            DB::raw("COUNT(id) as ctr"),
            DB::raw("SUM(total_spent) as total_transactions"),
        )->where("customer_id", $customerId)
        ->whereDate("transaction_at", ">", now()->subDays(30)->endOfDay())
        ->groupBy("customer_id")
        ->first();
        
        if(isset($transactions) && $transactions->ctr >= 3 && $transactions->total_transactions >= 100){
            Voucher::where("status", "ACTIVE")->limit(1)
            ->update([
                "customer_id" => $customerId,
                "status" => "LOCKED",
            ]);

            return "VOUCHER LOCKED";
        }

        return "NOT ELIGIBLE";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
