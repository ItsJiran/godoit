@section('title', 'Member Area')
<x-app-layout>
    <section class="hero-title">
        <h1>Member Area</h1>
    </section>
    <!-- SECTION MEMBER AREA -->
    <section class="hero orange-bg">
        <div class="container">
            <!-- WALLET -->
            <div class="box-custom">
                <div class="data-wallet">
                    <h2>Total Balance</h2>
                    <div class="wallet-balance">Rp {{$userBalance}}</div>
                    <div class="wallet-button">
                        <a class="btn label-button" href="/withdraw" title="Withdraw">Withdraw</a>
                        <a class="btn label-button classic-button" href="/my-bank" title="Your Bank">Your Bank</a>
                    </div>
                </div>
            </div>

            <!-- AFFILIATE -->
            <div class="affiliate-link-section">
                <div class="link-label">Link Affiliasi Anda:</div>
                <div class="link-input-wrapper">
                    <input type="text" class="link-input" value="{{ $userReferral }}" readonly>
                    <button class="copy-button" onclick="copyLink()">
                        <svg class="copy-icon" viewBox="0 0 24 24">
                            <path d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z"/>
                        </svg>
                        Copy
                    </button>
                </div>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M234.38,210a123.36,123.36,0,0,0-60.78-53.23,76,76,0,1,0-91.2,0A123.36,123.36,0,0,0,21.62,210a12,12,0,1,0,20.77,12c18.12-31.32,50.12-50,85.61-50s67.49,18.69,85.61,50a12,12,0,0,0,20.77-12ZM76,96a52,52,0,1,1,52,52A52.06,52.06,0,0,1,76,96Z"></path></svg>
                    </div>
                    <div class="stat-title">Pendaftar</div>
                    <div class="stat-value">{{ $userCount }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M196,136a16,16,0,1,1-16-16A16,16,0,0,1,196,136Zm40-36v80a32,32,0,0,1-32,32H60a32,32,0,0,1-32-32V60.92A32,32,0,0,1,60,28H192a12,12,0,0,1,0,24H60a8,8,0,0,0-8,8.26v.08A8.32,8.32,0,0,0,60.48,68H204A32,32,0,0,1,236,100Zm-24,0a8,8,0,0,0-8-8H60.48A33.72,33.72,0,0,1,52,90.92V180a8,8,0,0,0,8,8H204a8,8,0,0,0,8-8Z"></path></svg>
                    </div>
                    <div class="stat-title">Total Komisi</div>
                    <div class="stat-value">Rp {{ $userComissionTotal }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M232.76,137.88A28.39,28.39,0,0,0,208.13,133L172,141.26c0-.42,0-.84,0-1.26a32,32,0,0,0-32-32H89.94a35.76,35.76,0,0,0-25.45,10.54L43,140H20A20,20,0,0,0,0,160v40a20,20,0,0,0,20,20H120a11.89,11.89,0,0,0,2.91-.36l64-16a11.4,11.4,0,0,0,1.79-.6l38.82-16.54c.23-.09.45-.19.67-.3a28.61,28.61,0,0,0,4.57-48.32ZM36,196H24V164H36Zm181.68-31.39-37.51,16L118.52,196H60V157l21.46-21.46A11.93,11.93,0,0,1,89.94,132H140a8,8,0,0,1,0,16H112a12,12,0,0,0,0,24h32a12.19,12.19,0,0,0,2.69-.3l67-15.41.47-.12a4.61,4.61,0,0,1,5.82,4.44A4.58,4.58,0,0,1,217.68,164.61ZM164,100a40.36,40.36,0,0,0,5.18-.34,40,40,0,1,0,29.67-59.32A40,40,0,1,0,164,100Zm40-36a16,16,0,1,1-16,16A16,16,0,0,1,204,64ZM164,44a16,16,0,0,1,12.94,6.58A39.9,39.9,0,0,0,164.2,76H164a16,16,0,0,1,0-32Z"></path></svg>
                    </div>
                    <div class="stat-title">Komisi Pending</div>
                    <div class="stat-value">Rp {{ $userComissionTotalPending }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M148,108a12,12,0,0,1,12-12h28a12,12,0,0,1,0,24H160A12,12,0,0,1,148,108Zm40,28H168a12,12,0,0,0,0,24h20a12,12,0,0,0,0-24Zm48-80V200a20,20,0,0,1-20,20H40a20,20,0,0,1-20-20V56A20,20,0,0,1,40,36H216A20,20,0,0,1,236,56Zm-24,4H44V196H212ZM58.28,159.37A43.82,43.82,0,0,1,71.53,142a36,36,0,1,1,56.94,0,43.84,43.84,0,0,1,13.26,17.37,12,12,0,0,1-22.15,9.26C116.48,161.19,108.42,156,100,156s-16.47,5.2-19.59,12.63a12,12,0,1,1-22.13-9.26ZM88,120a12,12,0,1,0,12-12A12,12,0,0,0,88,120Z"></path></svg>
                    </div>
                    <div class="stat-title">No. ID</div>
                    <div class="stat-value">{{ $userNoId }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M164,56a12,12,0,0,1,12-12h12V32a12,12,0,0,1,24,0V44h12a12,12,0,0,1,0,24H212V80a12,12,0,0,1-24,0V68H176A12,12,0,0,1,164,56Zm70.51,54A107.88,107.88,0,1,1,146,21.49a12,12,0,0,1-4,23.67A85,85,0,0,0,128,44,83.94,83.94,0,0,0,62.05,179.94a83.48,83.48,0,0,1,29-23.42,52,52,0,1,1,74,0,83.36,83.36,0,0,1,29,23.42A83.52,83.52,0,0,0,212,128a85.2,85.2,0,0,0-1.16-14,12,12,0,0,1,23.67-4ZM128,148a28,28,0,1,0-28-28A28,28,0,0,0,128,148Zm0,64a83.53,83.53,0,0,0,48.43-15.43,60,60,0,0,0-96.86,0A83.53,83.53,0,0,0,128,212Z"></path></svg>
                    </div>
                    <div class="stat-title">Status</div>
                    <div class="stat-value">{{ $userPremiumMembership ? 'Premium' : 'Free' }}</div>                    
                </div>
            </div>
        </div>
    </section>
    <div class="notification" id="notification">Link berhasil disalin!</div>

    <!-- SECTION SELANJUTNYA -->
    <section class="min-hero">
        <div class="container">
            @if(Auth::user()->parent_referral_code != NULL)
            <div class="konten-pengundang">
                @php
                    $whatsappNumber = $pengundang->whatsapp;
                    if (str_starts_with($whatsappNumber, '0')) {
                        $whatsappNumber = '62' . substr($whatsappNumber, 1);
                    }
                @endphp
                Pengundang Anda: <b>{{$pengundang->name}}</b> <a class="btn btn-whatsapp label-btn" href="https://wa.me/{{$whatsappNumber}}" target="_blank" title="Whatsapp">Chat via Whatsapp</a>
            </div>
            @endif
            @if(!$userPremiumMembership)
            <div class="mysection-item reverse mysection-box">
                <div class="mysection-image">
                    <img src="https://t4.ftcdn.net/jpg/15/02/52/47/360_F_1502524799_DgQUHNZHSrbxB1OQr5nbMLuxQQAcjqf6.jpg" alt="Program">
                </div>
                <div class="mysection-content">
                    <h2 class="mysection-title">Mau Komisi Maksimal?</h2>
                    <p class="mysection-description">Upgrade status anda dan dapatkan manfaat lebih</p>
                    <a href="{{ route('membership.upgrade') }}" class="btn btn-primary">Upgrade Now</a>
                </div>
            </div>
            @endif
        </div>
    </section>
</x-app-layout>
