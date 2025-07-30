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
use App\Models\ContactMessage;

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
        $userBalance = 0;
        $userComissionTotal = 0;
        $userComissionTotalPending = 0;
        $userNoId = 999;

        if (!is_null($user)) {
            // get env from the 
            $userReferral = $user->generateReferralUrl();
            $userCount = User::where('parent_referral_code', $user->referral_code)->count();

            $userAccount = Account::getAccountUserByType($user->id, AccountType::E_WALLET->value);        
            $userBalance = $userAccount->balance;
            
            $userComissionTotal = AccountTransaction::getCachedTransactionSum(
                $user,
                $userAccount,
                AccountTransactionPurpose::COMMISSION_CREDIT,
                AccountTransactionStatus::COMPLETED,
            );

            $userComissionTotalPending = AccountTransaction::getCachedTransactionSum(
                $user,
                $userAccount,
                AccountTransactionPurpose::COMMISSION_CREDIT,
                AccountTransactionStatus::PENDING,
            );
            
            // AccountTransaction::where('account_id', $userAccount->id)
            //     ->where('status', AccountTransactionStatus::PENDING->value)
            //     ->where('purpose', AccountTransactionPurpose::COMMISSION_CREDIT->value)
            //     ->sum('amount');
            
            $userNoId = $user->id;
        }

        // Get Pengundang
        $pengundang = User::where('referral_code',$user->parent_referral_code)->first();
        
        // Using ->with() to pass variables to the view
        return view('page.memberarea')->with([
            'userReferral' => $userReferral,
            'userCount' => $userCount,
            'userComissionTotal' => $userComissionTotal,
            'userComissionTotalPending' => $userComissionTotalPending,
            'userNoId' => $userNoId,
            'userPremiumMembership' => $user ? $user->activeMembershipPremium() : null,
            'pengundang' => $pengundang,
            'userBalance' => $userBalance,
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

    // PAGE CONTACT (USERS)
    public function contact()
    {
        return view('page.contact');
    }

    // KIRIM PESAN (USERS)
    public function submitContact(Request $request)
    {
        // Cegah spam: cek apakah sudah submit dalam 5 menit terakhir
        if (session()->has('last_contact_submission')) {
            $lastSubmission = session('last_contact_submission');
            if (now()->diffInMinutes($lastSubmission) < 5) {
                return back()->withErrors(['pesan' => 'Anda baru saja mengirim pesan. Silakan tunggu 5 menit.']);
            }
        }
        // Validasi
        $rules = [
            'judul' => 'required|max:255',
            'nama' => 'required|max:255',
            'whatsapp' => 'required|numeric',
            'pesan' => 'required',
        ];
        // Hanya validasi email jika user belum login
        if (!Auth::check()) {
            $rules['email'] = 'required|email';
        }
        $validated = $request->validate($rules);
        // Jika login, isi email dari Auth user
        if (Auth::check()) {
            $validated['email'] = Auth::user()->email;
        }
        // Simpan ke database
        ContactMessage::create($validated);
        // Simpan waktu terakhir pengiriman ke session
        session(['last_contact_submission' => now()]);
        return back()->with('success', 'Pesan Anda berhasil dikirim.');
    }
}
