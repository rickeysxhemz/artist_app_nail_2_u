<?php

namespace App\Services;

use Exception;
use App\Helper\Helper;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use App\Libs\Response\GlobalApiResponseCodeBook;

class AccountService extends BaseService
{
   public function accountLink($request)
    {
        try {
            
            $account_exist = Account::where('artist_id', Auth::id())->where('status', 'active')->first();
            if($account_exist){
                $account_exist->status = 'deactive';
                $account_exist->save();
            }

            $account = new Account();
            $account->artist_id = Auth::id();
            $account->account_type = $request->account_type;
            $account->routing_number = $request->routing_number;
            $account->account_number = $request->account_number;
            $account->status = 'active';
            $account->save();

            return $account;
        } catch (Exception $e) {
            $error = "Error: Message: " . $e->getMessage() . " File: " . $e->getFile() . " Line #: " . $e->getLine();
            Helper::errorLogs("Artist:AccountService: accountLink", $error);
            return false;
        }
    }
}
