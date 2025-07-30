<?php

namespace App\Http\Controllers;

use App\Services\Account\TransactionService;

use App\Models\UserBank;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\WithdrawManual;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Enums\Account\AccountType;
use App\Enums\Account\AccountTransactionType;
use App\Enums\Account\AccountTransactionStatus;
use App\Enums\Account\AccountTransactionPurpose;

class WalletController extends Controller
{
    // WITHDRAW BALANCE (USER)
    public function withdraw()
    {
        $account = Account::getAccountUserByType(
            Auth::id(),
            AccountType::E_WALLET->value 
        ); 

        $saldoPenarikanPending = AccountTransaction::getCachedTransactionSum(
            Auth::user(),
            $account,
            AccountTransactionPurpose::WITHDRAWAL,
            AccountTransactionStatus::PENDING,
        );
        $saldo = $account->balance - $saldoPenarikanPending;


        $bank = UserBank::where('user_id', Auth::id())->first();

        if (!$bank) {
            return redirect()->route('wallet.mybank')
                ->with('error', 'Silakan isi data bank Anda terlebih dahulu sebelum melakukan withdraw.');
        }
        // Ambil riwayat withdraw user terbaru (urutkan dari terbaru ke lama)
        $riwayat = WithdrawManual::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('page.wallet.withdraw', compact('saldo', 'saldoPenarikanPending', 'bank', 'riwayat'));
    }

    // SIMPAN PENARIKAN (USER)
    public function withdrawStore(Request $request)
    {
        $account = Account::getAccountUserByType(
            Auth::id(),
            AccountType::E_WALLET->value 
        ); 

        $saldo = $account->balance;
        $saldoPenarikanPending = AccountTransaction::getCachedTransactionSum(
            Auth::user(),
            $account,
            AccountTransactionPurpose::WITHDRAWAL,
            AccountTransactionStatus::PENDING,
        );

        $bank = UserBank::where('user_id', Auth::id())->first();

        if (!$bank) {
            return redirect()->route('wallet.mybank')->with('error', 'Silakan isi data bank Anda terlebih dahulu.');
        }
        $request->validate([
            'jumlah'   => 'required|numeric|min:1',
            'password' => 'required|string'
        ]);
        // Validasi jumlah tidak melebihi saldo
        if ($request->jumlah + $saldoPenarikanPending > $saldo) {
            return back()->withErrors(['jumlah' => 'Jumlah penarikan tidak boleh melebihi saldo tersedia'])->withInput();
        }

        // Cek password user
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Password yang Anda masukkan salah'])->withInput();
        }
        // Simpan data withdraw
        $withdrawal = WithdrawManual::create([
            'user_id'   => Auth::id(),
            'jumlah'    => $request->jumlah,
            'atas_nama' => $bank->atas_nama,
            'nama_bank' => $bank->nama_bank,
            'no_rek'    => $bank->no_rek,
            'status'    => 'pending'
        ]);

        AccountTransaction::createTransaction(
            userId: Auth::user()->id,
            accountId: $account->id,
            amount: $request->jumlah,
            direction: AccountTransactionType::OUT, // Commission is a credit to the referrer
            purpose: AccountTransactionPurpose::WITHDRAWAL, // Or COMMISSION_CREDIT
            status: AccountTransactionStatus::PENDING, // Start as pending
            description: 'Penarikan Komisi',
            sourceable: $withdrawal,
        );

        return redirect()->route('wallet.withdraw')->with('success', 'Permintaan withdraw berhasil diajukan.');
    }

    // MY BANK (USER)
    public function mybank()
    {
        $bank = UserBank::where('user_id', Auth::id())->first();
        return view('page.wallet.mybank', compact('bank'));
    }

    // SAVE MY BANK DATA (USER)
    public function saveBank(Request $request)
    {
        $request->validate([
            'atas_nama' => 'required|string|max:255',
            'nama_bank' => 'required|string|max:255',
            'no_rek'    => 'required|string|max:50'
        ]);
        UserBank::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'atas_nama' => $request->atas_nama,
                'nama_bank' => $request->nama_bank,
                'no_rek'    => $request->no_rek
            ]
        );
        return redirect()->route('wallet.mybank')->with('success', 'Data bank berhasil disimpan.');
    }
    
}
