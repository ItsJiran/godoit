<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\User;
use App\Models\Account;
use App\Models\AccountTransaction;

use App\Enums\Account\AccountType;
use App\Enums\Account\AccountTransactionStatus;
use App\Enums\Account\AccountTransactionPurpose;

class DashboardController extends Controller
{
    public function home(Request $request): View
    {
        $user = $request->user();

        // Initialize variables with default values to ensure they are always defined
        $userReferral = "http://godoitnew.test/register?reg=";
        $userCount = 0;
        $userComissionTotal = 0;
        $userComissionTotalPending = 0;
        $userNoId = 999;

        if (!is_null($user)) {
            $userReferral = "http://godoitnew.test/register?reg=" . $user->username;
            $userCount = User::where('parent_referral_code', $user->referral_code)->count();

            // Corrected: Pass $user->id and the enum instance to getAccountUserByType
            $userAccount = Account::getAccountUserByType($user->id, AccountType::E_WALLET->value);
            
            // Access the balance directly from the retrieved account model
            $userComissionTotal = $userAccount->balance;
            
            // Corrected: Query for pending commissions using the correct status and purpose
            $userComissionTotalPending = AccountTransaction::where('account_id', $userAccount->id)
                ->where('status', AccountTransactionStatus::PENDING->value)
                ->where('purpose', AccountTransactionPurpose::COMMISSION_CREDIT->value)
                ->sum('amount');
            
            $userNoId = $user->id;
        }
        
        // Using ->with() to pass variables to the view
        return view('welcome')->with([
            'userReferral' => $userReferral,
            'userCount' => $userCount,
            'userComissionTotal' => $userComissionTotal,
            'userComissionTotalPending' => $userComissionTotalPending,
            'userNoId' => $userNoId,
        ]);
    }

    public function index(Request $request): View
    {
        return view('dashboard.index');
    }
}
