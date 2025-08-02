<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\View\View;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\MarketingKit;
use App\Models\Payment;
use App\Models\ContactMessage;
use App\Models\LandingSection;

use App\Enums\Account\AccountType;
use App\Enums\Account\AccountTransactionStatus;
use App\Enums\Account\AccountTransactionPurpose;

use App\Models\Image;
use App\Enums\Image\ImagePurposeType;
use App\Services\Media\ImageUploadService; // Ensure this is correct

class PageController extends Controller
{
    // MARKETING KIT (USERS)
    public function marketing_kit(Request $request): View
    {
        $user = $request->user();
        $userReferral = url('page/napak_tilas/?reg=');                    
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
        $userReferral = url('page/napak_tilas');                    
        $userCount = 0;
        $userBalance = 0;
        $userComissionTotal = 0;
        $userComissionTotalPending = 0;
        $userNoId = 999;

        if (!is_null($user)) {
            // get env from the 
            $userReferral = $userReferral . $user->generateReferralParam();
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

    public function indexPageSectionNapak(Request $request){
        $sections = LandingSection::where('landing_type','napak_tilas')->paginate(5);
        $landing_type = 'napak_tilas';

        return view('dashboard.pages.index', compact('sections','landing_type'));
    }

    public function indexPageSectionHome(Request $request){
        $sections = LandingSection::where('landing_type','homepage')->paginate(5);
        $landing_type = 'homepage';

        return view('dashboard.pages.index', compact('sections','landing_type'));
    }

    public function createSection(Request $request, $landing_type, $type){
        return view('dashboard.pages.create', compact('landing_type', 'type'));
    }

    public function storeSection(Request $request){

        $request->validate([
            'landing_type' => 'required',
            'index' => 'required',
            'type' => 'required',
        ]);

        if($request->type == 'homepage_description'){
            $request->validate([
                'hero_image' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);

            $landing_section = LandingSection::create([
                'index' => $request->index,
                'landing_type' => $request->landing_type,
                'type' => $request->type,
                'meta_content' => [],
            ]);

            $heroImage = Image::createImageRecord(
                $request->file('hero_image'),
                $landing_section,
                ImagePurposeType::PRODUCT_THUMBNAIL->value,
                'section' . $landing_section->id,
                'public',
                null,
                ImagePurposeType::PRODUCT_THUMBNAIL->value
            );

            $landing_section->meta_content = [
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'hero_image' => $heroImage->path,
                'image_model_id' => $heroImage->id,
            ];

            $landing_section->save();
        };

        if($request->type == 'homepage_product'){
            $request->validate([
                'title' => 'required',
                'subtitle' => 'required',
                'description' => 'required',
                'href' => 'required',
            ]);

            $landing_section = LandingSection::create([
                'index' => $request->index,
                'landing_type' => $request->landing_type,
                'type' => $request->type,
                'meta_content' => [
                    'title' => $request->title,
                    'subtitle' => $request->subtitle,
                    'description' => $request->description,
                    'button_more' => [
                        'href' => $request->href,
                    ],
                ],
            ]);
        }


        if($request->type == 'homepage_clients'){

            $validatedData = $request->validate([
                'title' => 'required',
                'clients' => 'required|array|min:1', // Ensure there's at least one client array
                'clients.*.name' => 'required|string|max:255', // Validate name for each client
                'clients.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image for each client (optional)    
            ]);

            // Using a database transaction for atomicity
            DB::beginTransaction();

            try {
                $landing_section = LandingSection::create([
                    'index' => $request->index,
                    'landing_type' => $request->landing_type,
                    'type' => $request->type,
                    'meta_content' => [],
                ]);

                $title = $request->title;
                $content = [];

                // Loop through each client data
                foreach ($validatedData['clients'] as $index => $clientData) {

                    $image = Image::createImageRecord(
                        $clientData['image'],
                        $landing_section,
                        ImagePurposeType::PRODUCT_THUMBNAIL->value,
                        'section' . $landing_section->id,
                        'public',
                        null,
                        ImagePurposeType::PRODUCT_THUMBNAIL->value
                    );

                    array_push($content, [
                        'name' => $clientData['name'],
                        // 'quote' => $clientData['quote'],
                        // 'role' => $clientData['role'],
                        'src' => $image->path,
                        'image_model_id' => $image->id,
                    ]);
                }

                $landing_section->meta_content = [
                    'content' => $content,
                    'title' => $title,
                ];
                $landing_section->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // Log the error
                \Log::error('Error storing clients: ' . $e->getMessage());
                return back()->with('error', 'Failed to add clients. Please try again.');
            }
        };

        if($request->type == 'homepage_testimonials'){

            $validatedData = $request->validate([
                'title' => 'required',
                'testimonials' => 'required|array|min:1', // Ensure there's at least one client array
                'testimonials.*.name' => 'required|string|max:255', // Validate name for each client
                'testimonials.*.role' => 'required|string|min:0|max:150', // Validate age for each client
                'testimonials.*.quote' => 'required|string|min:0|max:150', // Validate age for each client
                'testimonials.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image for each client (optional)    
            ]);

            // Using a database transaction for atomicity
            DB::beginTransaction();

            try {
                $landing_section = LandingSection::create([
                    'index' => $request->index,
                    'landing_type' => $request->landing_type,
                    'type' => $request->type,
                    'meta_content' => [],
                ]);

                $title = $request->title;
                $content = [];

                // Loop through each client data
                foreach ($validatedData['testimonials'] as $index => $clientData) {

                    $image = Image::createImageRecord(
                        $clientData['image'],
                        $landing_section,
                        ImagePurposeType::PRODUCT_THUMBNAIL->value,
                        'section' . $landing_section->id,
                        'public',
                        null,
                        ImagePurposeType::PRODUCT_THUMBNAIL->value
                    );

                    array_push($content, [
                        'name' => $clientData['name'],
                        'quote' => $clientData['quote'],
                        'role' => $clientData['role'],
                        'src' => $image->path,
                        'image_model_id' => $image->id,
                    ]);
                }

                $landing_section->meta_content = [
                    'content' => $content,
                    'title' => $title,
                ];
                $landing_section->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // Log the error
                \Log::error('Error storing clients: ' . $e->getMessage());
                return back()->with('error', 'Failed to add clients. Please try again.');
            }
        };
        
        // if($request->type == 'homepage_product'){
        //     $request->validate([
        //         'image' => 'required',
        //         'title' => 'required',
        //         'subtitle' => 'required',
        //         'description' => 'required',
        //     ]);

        //     LandingSection::create([
        //         'index' => $request->index,
        //         'landing_type' => $request->landing_type,
        //         'type' => $request->type,
        //         'meta_content' => [
        //             'title' => $request->title,
        //             'subtitle' => $request->subtitle,
        //             'description' => $request->description,
        //         ],
        //     ]);
        // };



        if($request->landing_type == 'homepage')    
            return redirect()->route('page.homepage')->with('success', 'Section berhasil ditambah.');
        else 
            return redirect()->route('page.napak')->with('success', 'Section berhasil ditambah.');
    }

    public function updateSection(){


    }

    public function deleteSection(Request $request, $id){
        LandingSection::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Section berhasil dihapus.');
    }




}
