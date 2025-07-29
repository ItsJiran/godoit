<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\MarketingKit;
use App\Models\Payment;

use App\Enums\Account\AccountType;
use App\Enums\Account\AccountTransactionStatus;
use App\Enums\Account\AccountTransactionPurpose;

class PageController extends Controller
{
    // MARKETING KIT (USERS)
    public function marketing_kit(Request $request): View
    {
        $user = $request->user();
        $userReferral = url('/?reg=');                    
        if (!is_null($user)) {
            $userReferral = $user->generateReferralUrl();
        }
        $kits = MarketingKit::latest()->paginate(10);
        return view('page.marketing-kit')->with([
            'userReferral' => $userReferral,
            'kits' => $kits,
        ]);
    }

    // MEMBER AREA (USERS)
    public function memberarea(Request $request): View
    {
        $user = $request->user();

        // Initialize variables with default values to ensure they are always defined
        $userReferral = url('/?reg=');                    
        $userCount = 0;
        $userComissionTotal = 0;
        $userComissionTotalPending = 0;
        $userNoId = 999;

        if (!is_null($user)) {
            // get env from the 
            $userReferral = $user->generateReferralUrl();
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
        return view('page.memberarea')->with([
            'userReferral' => $userReferral,
            'userCount' => $userCount,
            'userComissionTotal' => $userComissionTotal,
            'userComissionTotalPending' => $userComissionTotalPending,
            'userNoId' => $userNoId,
            'userPremiumMembership' => $user ? $user->activeMembershipPremium() : null,
        ]);
    }

    // PAGE TRANSACTION (USERS)
    public function transaction(Request $request)
    {
        $query = $request->input('search');
        if ($query) {
            $payments = Payment::where('user_id',Auth::user()->id)
                                ->where('id_order', 'like', '%' . $query . '%')
                                ->paginate(10);
        } else {
            $payments = Payment::where('user_id',Auth::user()->id)->latest()->paginate(5);
        }
        return view('page.transaction', compact('payments', 'query'));
    }
}
