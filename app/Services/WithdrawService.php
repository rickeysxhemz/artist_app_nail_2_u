<?php

namespace App\Services;

use Exception;
use App\Helper\Helper;
use App\Models\Withdraw;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Libs\Response\GlobalApiResponseCodeBook;
use Illuminate\Support\Facades\DB;

class WithdrawService extends BaseService
{
   public function withdrawPayment()
    {
        try {
            $payments = Payment::where('artist_id', Auth::id())->where('status', 'withdraw')->get();
            if($payments){

                $withdraw_exist = Withdraw::where('artist_id', Auth::id())->where('status', 'pending')->first();
                if($withdraw_exist){
                    $withdraw_exist->amount = $payments->sum('amount');
                    $withdraw_exist->save();
                    return $withdraw_exist;
                } else {
                    $withdraw = new Withdraw();
                    $withdraw->artist_id = Auth::id();
                    $withdraw->amount = $payments->sum('amount');
                    $withdraw->status = 'pending';
                    $withdraw->save();

                    foreach ($payments as $payment) {
                        DB::table('payment_withdraws')
                        ->insert([
                           'payment_id'=> $payment->id,
                           'withdraw_id'=> $withdraw->id
                        ]);
                    }
                    return $withdraw;
                }
                
            }

            return GlobalApiResponseCodeBook::RECORD_NOT_EXISTS['outcomeCode'];
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:AccountService: accountLink", $error);
            return false;
        }
    }
}
