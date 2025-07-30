<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Account;
use App\Models\Product;
use App\Models\ProductRegular;
use App\Models\AccountTransaction;
use App\Models\MarketingKit;
use App\Models\Payment;
use App\Models\ContactMessage;
use App\Models\UserBank;
use App\Models\WithdrawManual;

use App\Enums\Account\AccountType;
use App\Enums\Account\AccountTransactionStatus;
use App\Enums\Account\AccountTransactionPurpose;

class DashboardController extends Controller
{
    // INDEX HOME LANDING PAGE
    public function home(Request $request): View
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
        $kits = MarketingKit::latest()->paginate(2);
        $products = Product::latest()->where('productable_type',ProductRegular::class)->with(['productable','thumbnail'])->paginate(2);
        // Refferal Save for This Page
        $parentReferralCandidate = $request->query('reg');
        $sessionReferralCode = $request->session()->get('parent_referral_code_session');
        $parentReferralCode = $sessionReferralCode;
        if (!$parentReferralCode && $parentReferralCandidate) {
            $foundUser = null;
            $foundUser = User::where('referral_code', $parentReferralCandidate)->first();
            if (!$foundUser) {
                $foundUser = User::where('username', $parentReferralCandidate)->first();
            }
            if ($foundUser) {
                $parentReferralCode = $foundUser->referral_code;
                $request->session()->put('parent_referral_code_session', $parentReferralCode);
            }
        }
        // Using ->with() to pass variables to the view
        return view('welcome')->with([
            'userReferral' => $userReferral,
            'userCount' => $userCount,
            'userComissionTotal' => $userComissionTotal,
            'userComissionTotalPending' => $userComissionTotalPending,
            'userNoId' => $userNoId,
            'kits' => $kits,
            'products' => $products,
            'userPremiumMembership' => $user ? $user->activeMembershipPremium() : null,
        ]);
    }

    public function product(Request $request, $product_id): View
    {
        $user = $request->user();
        $product = Product::with(['productable','thumbnail'])->findOrFail($product_id);

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

        $kits = MarketingKit::latest()->paginate(2);
        
        // Using ->with() to pass variables to the view
        return view('welcome_product')->with([
            'userReferral' => $userReferral,
            'userCount' => $userCount,
            'userComissionTotal' => $userComissionTotal,
            'userComissionTotalPending' => $userComissionTotalPending,
            'userNoId' => $userNoId,
            'kits' => $kits,
            'product' => $product,
            'userPremiumMembership' => $user ? $user->activeMembershipPremium() : null,
        ]);
    }

    // INDEX DASHBOARD
    public function index(Request $request): View
    {
        $user = $request->user();

        // Initialize variables with default values to ensure they are always defined
        $userReferral = url('/?reg=');            
        $userCount = 0;
        $userComissionTotal = 0;
        $userComissionTotalPending = 0;
        $userNoId = 999;

        if (!is_null($user)) {
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
        return view('dashboard.index')->with([
            'userReferral' => $userReferral,
            'userCount' => $userCount,
            'userComissionTotal' => $userComissionTotal,
            'userComissionTotalPending' => $userComissionTotalPending,
            'userNoId' => $userNoId,
            'userPremiumMembership' => $user ? $user->activeMembershipPremium() : null,            
        ]);
    }

    // MARKETING KIT
    public function marketing_kit(Request $request)
    {
        $query = $request->input('search');
        if ($query) {
            $kits = MarketingKit::where('judul', 'like', '%' . $query . '%')
                                ->paginate(10);
        } else {
            $kits = MarketingKit::latest()->paginate(2);
        }
        return view('dashboard.marketing-kit.index', compact('kits', 'query'));
    }

    // SIMPAN DATA MARKETING KIT
    public function simpankit(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'konten' => 'required|string',
        ]);
        // Simpan gambar
        $gambarPath = $request->file('gambar')->store('marketing-kit', 'public');
        MarketingKit::create([
            'judul' => $request->judul,
            'gambar' => $gambarPath,
            'konten' => $request->konten,
        ]);
        return redirect()->route('marketingkit')->with('success', 'Marketing Kit berhasil ditambahkan.');
    }

    // EDIT MARKETING KIT
    public function editkit($id)
    {
        $kit = MarketingKit::findOrFail($id);
        return view('dashboard.marketing-kit.edit', compact('kit'));
    }

    // UPDATE DATA MARKETING KIT
    public function updatekit(Request $request, $id)
    {
        $kit = MarketingKit::findOrFail($id);
        $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'konten' => 'required|string',
        ]);
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('marketing-kit', 'public');
            $kit->gambar = $gambarPath;
        }
        $kit->judul = $request->judul;
        $kit->konten = $request->konten;
        $kit->save();
        return redirect()->route('marketingkit')->with('success', 'Marketing Kit berhasil diperbarui.');
    }

    // HAPUS DATA MARKETING KIT
    public function hapuskit($id)
    {
        $kit = MarketingKit::findOrFail($id);
        $kit->delete();
        return redirect()->route('marketingkit')->with('success', 'Marketing Kit berhasil dihapus.');
    }

    // ALL USERS (ADMIN)
    public function allusers(Request $request)
    {
        $query = $request->input('search');
        if ($query) {
            $users = User::where('role','user')->where('name', 'like', '%' . $query . '%')
                                ->paginate(4);
        } else {
            $users = User::where('role','user')->latest()->paginate(4);
        }
        return view('dashboard.users', compact('users', 'query'));
    }

    // BLOKIR USERS (ADMIN)
    public function blokiruser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $today = Carbon::now('Asia/Jakarta');
        $user->deleted_at = $today;
        $user->save();
        return redirect()->route('allusers')->with('success', 'Akses Pengguna: '.$user->name.' telah diblokir.');
    }

    // UN-BLOKIR USERS (ADMIN)
    public function unblokiruser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $today = Carbon::now('Asia/Jakarta');
        $user->deleted_at = NULL;
        $user->save();
        return redirect()->route('allusers')->with('success', 'Akses Pengguna: '.$user->name.' telah dibuka.');
    }

    // EDIT USER (ADMIN)
    public function edituser($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.user-edit', compact('user'));
    }

    // AKSI EDIT USER (ADMIN)
    public function adminedituser(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'whatsapp' => 'required|unique:users,whatsapp,' . $user->id,
            'kota' => 'required|string|max:255',
        ]);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp ?? null,
            'kota' => $request->kota ?? null,
        ]);
        return redirect()->route('allusers')->with('success', 'Profil '.$user->name.' berhasil diperbarui.');
    }

    // PAYMENT TRANSACTION (ADMIN)
    public function admin_transaction(Request $request)
    {
        $query = $request->input('search');
        $payments = Payment::when($query, function ($q) use ($query) {
                            return $q->where('id_order', 'like', '%' . $query . '%');
                        })
                        ->latest()
                        ->paginate(5);
        return view('dashboard.transaction', compact('payments', 'query'));
    }

    // PAGE INBOX (ADMIN)
    public function inbox(Request $request)
    {
        $query = $request->input('search');
        if ($query) {
            $contacts = ContactMessage::where('judul', 'like', '%' . $query . '%')
                                ->paginate(10);
        } else {
            $contacts = ContactMessage::latest()->paginate(5);
        }
        return view('dashboard.inbox', compact('contacts', 'query'));
    }

    // ADMIN WITHDRAW (ADMIN)
    public function admin_withdraw(Request $request)
    {
        $query = $request->get('search');
        $withdraws = WithdrawManual::with('user')
            ->when($query, function ($q) use ($query) {
                $q->whereHas('user', function ($sub) use ($query) {
                    $sub->where('name', 'like', "%$query%");
                })
                ->orWhere('atas_nama', 'like', "%$query%")
                ->orWhere('nama_bank', 'like', "%$query%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('dashboard.withdraw', compact('withdraws', 'query'));
    }

    // AKSI WITHDRAW (ADMIN)
    public function admin_withdraw_update($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:sukses,ditolak'
        ]);
        $withdraw = WithdrawManual::findOrFail($id);
        $withdraw->status = $request->status;
        $withdraw->save();
        return redirect()->route('admin.withdraw')->with('success', 'Status withdraw berhasil diperbarui.');
    }
}
