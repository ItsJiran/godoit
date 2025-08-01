<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('img/favicon.png')}}" rel='icon' type='image/x-icon'/>

    <title>@yield('title', 'Dashboard')</title>
    <meta http-equiv="Cache-Control" content="public, max-age=604800">
    <meta http-equiv="Expires" content="Mon, 15 Jan 2027 12:00:00 GMT">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @yield('header')
</head>
<body>
    <div class="dashboard-container">
        <div class="overlay" id="overlay"></div>
        
        <nav class="sidebar" id="sidebar">
            <button class="toggle-btn" id="toggleBtn">
                <div class="toggle-icon">☰</div>
            </button>
            
            <div class="sidebar-header">
                <div class="profile-section">
                    <div class="profile-section">
                        <div class="profile-avatar" style="overflow: hidden">
                            <img style="width:100%" src="{{ Auth::user()->avatar ?  asset('storage/' . Auth::user()->avatar->conversions['medium']['path']) : '' }}">                                            
                        </div>                     
                    </div>
                    <div class="profile-info">
                        <div class="profile-name">
                            <a href="{{ route('profile.edit') }}" title="Profile">{{Auth::user()->name}}</a>
                        </div>
                        <div class="profile-email">{{Auth::user()->email}}</div>
                    </div>
                </div>
            </div>
            
            <div class="menu">
                <a href="/dashboard" class="menu-item">
                    <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M341.8 72.6C329.5 61.2 310.5 61.2 298.3 72.6L74.3 280.6C64.7 289.6 61.5 303.5 66.3 315.7C71.1 327.9 82.8 336 96 336L112 336L112 512C112 547.3 140.7 576 176 576L464 576C499.3 576 528 547.3 528 512L528 336L544 336C557.2 336 569 327.9 573.8 315.7C578.6 303.5 575.4 289.5 565.8 280.6L341.8 72.6zM304 384L336 384C362.5 384 384 405.5 384 432L384 528L256 528L256 432C256 405.5 277.5 384 304 384z"/></svg></div>
                    <span class="menu-text">Dashboard</span>
                </a>            

                <a href="/admin/transaction" class="menu-item">
                    <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M192 160L192 144C192 99.8 278 64 384 64C490 64 576 99.8 576 144L576 160C576 190.6 534.7 217.2 474 230.7C471.6 227.9 469.1 225.2 466.6 222.7C451.1 207.4 431.1 195.8 410.2 187.2C368.3 169.7 313.7 160.1 256 160.1C234.1 160.1 212.7 161.5 192.2 164.2C192 162.9 192 161.5 192 160.1zM496 417L496 370.8C511.1 366.9 525.3 362.3 538.2 356.9C551.4 351.4 564.3 344.7 576 336.6L576 352C576 378.8 544.5 402.5 496 417zM496 321L496 288C496 283.5 495.6 279.2 495 275C510.5 271.1 525 266.4 538.2 260.8C551.4 255.2 564.3 248.6 576 240.5L576 255.9C576 282.7 544.5 306.4 496 320.9zM64 304L64 288C64 243.8 150 208 256 208C362 208 448 243.8 448 288L448 304C448 348.2 362 384 256 384C150 384 64 348.2 64 304zM448 400C448 444.2 362 480 256 480C150 480 64 444.2 64 400L64 384.6C75.6 392.7 88.5 399.3 101.8 404.9C143.7 422.4 198.3 432 256 432C313.7 432 368.3 422.3 410.2 404.9C423.4 399.4 436.3 392.7 448 384.6L448 400zM448 480.6L448 496C448 540.2 362 576 256 576C150 576 64 540.2 64 496L64 480.6C75.6 488.7 88.5 495.3 101.8 500.9C143.7 518.4 198.3 528 256 528C313.7 528 368.3 518.3 410.2 500.9C423.4 495.4 436.3 488.7 448 480.6z"/></svg></div>
                    <span class="menu-text">Transaction</span>
                </a>

                <a href="/admin/marketing-kit" class="menu-item">
                    <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M525.2 82.9C536.7 88 544 99.4 544 112L544 528C544 540.6 536.7 552 525.2 557.1C513.7 562.2 500.4 560.3 490.9 552L444.3 511.3C400.7 473.2 345.6 451 287.9 448.3L287.9 544C287.9 561.7 273.6 576 255.9 576L223.9 576C206.2 576 191.9 561.7 191.9 544L191.9 448C121.3 448 64 390.7 64 320C64 249.3 121.3 192 192 192L276.5 192C338.3 191.8 397.9 169.3 444.4 128.7L491 88C500.4 79.7 513.9 77.8 525.3 82.9zM288 384L288 384.2C358.3 386.9 425.8 412.7 480 457.6L480 182.3C425.8 227.2 358.3 253 288 255.7L288 384z"/></svg></div>
                    <span class="menu-text">Marketing Kit</span>
                </a>

                @if(Auth::user()->role == "admin")
                    {{-- <a href="/admin/page/homepage" class="menu-item">
                        <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M288 64L288 128C288 136.8 295.2 144 304 144L336 144C344.8 144 352 136.8 352 128L352 64L384 64C419.3 64 448 92.7 448 128L448 256C448 261.5 447.3 266.9 446 272L194 272C192.7 266.9 192 261.5 192 256L192 128C192 92.7 220.7 64 256 64L288 64zM384 576C372.8 576 362.2 573.1 353 568C362.5 551.5 368 532.4 368 512L368 384C368 363.6 362.5 344.5 353 328C362.2 322.9 372.7 320 384 320L416 320L416 384C416 392.8 423.2 400 432 400L464 400C472.8 400 480 392.8 480 384L480 320L512 320C547.3 320 576 348.7 576 384L576 512C576 547.3 547.3 576 512 576L384 576zM64 384C64 348.7 92.7 320 128 320L160 320L160 384C160 392.8 167.2 400 176 400L208 400C216.8 400 224 392.8 224 384L224 320L256 320C291.3 320 320 348.7 320 384L320 512C320 547.3 291.3 576 256 576L128 576C92.7 576 64 547.3 64 512L64 384z"/></svg></div>
                        <span class="menu-text">Halaman Homepage</span>
                    </a>

                    <a href="/admin/page/napak" class="menu-item">
                        <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M288 64L288 128C288 136.8 295.2 144 304 144L336 144C344.8 144 352 136.8 352 128L352 64L384 64C419.3 64 448 92.7 448 128L448 256C448 261.5 447.3 266.9 446 272L194 272C192.7 266.9 192 261.5 192 256L192 128C192 92.7 220.7 64 256 64L288 64zM384 576C372.8 576 362.2 573.1 353 568C362.5 551.5 368 532.4 368 512L368 384C368 363.6 362.5 344.5 353 328C362.2 322.9 372.7 320 384 320L416 320L416 384C416 392.8 423.2 400 432 400L464 400C472.8 400 480 392.8 480 384L480 320L512 320C547.3 320 576 348.7 576 384L576 512C576 547.3 547.3 576 512 576L384 576zM64 384C64 348.7 92.7 320 128 320L160 320L160 384C160 392.8 167.2 400 176 400L208 400C216.8 400 224 392.8 224 384L224 320L256 320C291.3 320 320 348.7 320 384L320 512C320 547.3 291.3 576 256 576L128 576C92.7 576 64 547.3 64 512L64 384z"/></svg></div>
                        <span class="menu-text">Halaman Napak</span>
                    </a> --}}

                    <a href="/admin/setting" class="menu-item">
                        <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M259.1 73.5C262.1 58.7 275.2 48 290.4 48L350.2 48C365.4 48 378.5 58.7 381.5 73.5L396 143.5C410.1 149.5 423.3 157.2 435.3 166.3L503.1 143.8C517.5 139 533.3 145 540.9 158.2L570.8 210C578.4 223.2 575.7 239.8 564.3 249.9L511 297.3C511.9 304.7 512.3 312.3 512.3 320C512.3 327.7 511.8 335.3 511 342.7L564.4 390.2C575.8 400.3 578.4 417 570.9 430.1L541 481.9C533.4 495 517.6 501.1 503.2 496.3L435.4 473.8C423.3 482.9 410.1 490.5 396.1 496.6L381.7 566.5C378.6 581.4 365.5 592 350.4 592L290.6 592C275.4 592 262.3 581.3 259.3 566.5L244.9 496.6C230.8 490.6 217.7 482.9 205.6 473.8L137.5 496.3C123.1 501.1 107.3 495.1 99.7 481.9L69.8 430.1C62.2 416.9 64.9 400.3 76.3 390.2L129.7 342.7C128.8 335.3 128.4 327.7 128.4 320C128.4 312.3 128.9 304.7 129.7 297.3L76.3 249.8C64.9 239.7 62.3 223 69.8 209.9L99.7 158.1C107.3 144.9 123.1 138.9 137.5 143.7L205.3 166.2C217.4 157.1 230.6 149.5 244.6 143.4L259.1 73.5zM320.3 400C364.5 399.8 400.2 363.9 400 319.7C399.8 275.5 363.9 239.8 319.7 240C275.5 240.2 239.8 276.1 240 320.3C240.2 364.5 276.1 400.2 320.3 400z"/></svg></div>
                        <span class="menu-text">Settings</span>
                    </a>
                    

                    <a href="/admin/product" class="menu-item">
                        <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M288 64L288 128C288 136.8 295.2 144 304 144L336 144C344.8 144 352 136.8 352 128L352 64L384 64C419.3 64 448 92.7 448 128L448 256C448 261.5 447.3 266.9 446 272L194 272C192.7 266.9 192 261.5 192 256L192 128C192 92.7 220.7 64 256 64L288 64zM384 576C372.8 576 362.2 573.1 353 568C362.5 551.5 368 532.4 368 512L368 384C368 363.6 362.5 344.5 353 328C362.2 322.9 372.7 320 384 320L416 320L416 384C416 392.8 423.2 400 432 400L464 400C472.8 400 480 392.8 480 384L480 320L512 320C547.3 320 576 348.7 576 384L576 512C576 547.3 547.3 576 512 576L384 576zM64 384C64 348.7 92.7 320 128 320L160 320L160 384C160 392.8 167.2 400 176 400L208 400C216.8 400 224 392.8 224 384L224 320L256 320C291.3 320 320 348.7 320 384L320 512C320 547.3 291.3 576 256 576L128 576C92.7 576 64 547.3 64 512L64 384z"/></svg></div>
                        <span class="menu-text">Products</span>
                    </a>
                    
                    <a href="/admin/all-users" class="menu-item">
                        <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M320 64C355.3 64 384 92.7 384 128C384 163.3 355.3 192 320 192C284.7 192 256 163.3 256 128C256 92.7 284.7 64 320 64zM416 376C416 401 403.3 423 384 435.9L384 528C384 554.5 362.5 576 336 576L304 576C277.5 576 256 554.5 256 528L256 435.9C236.7 423 224 401 224 376L224 336C224 283 267 240 320 240C373 240 416 283 416 336L416 376zM160 96C190.9 96 216 121.1 216 152C216 182.9 190.9 208 160 208C129.1 208 104 182.9 104 152C104 121.1 129.1 96 160 96zM176 336L176 368C176 400.5 188.1 430.1 208 452.7L208 528C208 529.2 208 530.5 208.1 531.7C199.6 539.3 188.4 544 176 544L144 544C117.5 544 96 522.5 96 496L96 439.4C76.9 428.4 64 407.7 64 384L64 352C64 299 107 256 160 256C172.7 256 184.8 258.5 195.9 262.9C183.3 284.3 176 309.3 176 336zM432 528L432 452.7C451.9 430.2 464 400.5 464 368L464 336C464 309.3 456.7 284.4 444.1 262.9C455.2 258.4 467.3 256 480 256C533 256 576 299 576 352L576 384C576 407.7 563.1 428.4 544 439.4L544 496C544 522.5 522.5 544 496 544L464 544C451.7 544 440.4 539.4 431.9 531.7C431.9 530.5 432 529.2 432 528zM480 96C510.9 96 536 121.1 536 152C536 182.9 510.9 208 480 208C449.1 208 424 182.9 424 152C424 121.1 449.1 96 480 96z"/></svg></div>
                        <span class="menu-text">All Users</span>
                    </a>

                    <a href="/admin/inbox" class="menu-item">
                        <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M128.4 239.8L320 97.9L511.6 239.8L353.5 357C343.8 364.2 332.1 368 320 368C307.9 368 296.2 364.1 286.5 357L128.4 239.8zM320 32C307.9 32 296.2 35.9 286.5 43L89.9 188.7C73.6 200.8 64 219.8 64 240.1L64 480C64 515.3 92.7 544 128 544L512 544C547.3 544 576 515.3 576 480L576 240.1C576 219.8 566.4 200.7 550.1 188.7L353.5 43C343.8 35.8 332.1 32 320 32z"/></svg></div>
                        <span class="menu-text">Inbox</span>
                    </a>

                    <a href="/admin/withdraw" class="menu-item">
                        <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M64 192L64 224L576 224L576 192C576 156.7 547.3 128 512 128L128 128C92.7 128 64 156.7 64 192zM64 272L64 448C64 483.3 92.7 512 128 512L512 512C547.3 512 576 483.3 576 448L576 272L64 272zM128 424C128 410.7 138.7 400 152 400L200 400C213.3 400 224 410.7 224 424C224 437.3 213.3 448 200 448L152 448C138.7 448 128 437.3 128 424zM272 424C272 410.7 282.7 400 296 400L360 400C373.3 400 384 410.7 384 424C384 437.3 373.3 448 360 448L296 448C282.7 448 272 437.3 272 424z"/></svg></div>
                        <span class="menu-text">Withdraw</span>
                    </a>
                @endif
            </div>
            
            <div class="logout-button">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button class="menu-item" type="submit">
                    <div class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M569 337C578.4 327.6 578.4 312.4 569 303.1L425 159C418.1 152.1 407.8 150.1 398.8 153.8C389.8 157.5 384 166.3 384 176L384 256L272 256C245.5 256 224 277.5 224 304L224 336C224 362.5 245.5 384 272 384L384 384L384 464C384 473.7 389.8 482.5 398.8 486.2C407.8 489.9 418.1 487.9 425 481L569 337zM224 160C241.7 160 256 145.7 256 128C256 110.3 241.7 96 224 96L160 96C107 96 64 139 64 192L64 448C64 501 107 544 160 544L224 544C241.7 544 256 529.7 256 512C256 494.3 241.7 480 224 480L160 480C142.3 480 128 465.7 128 448L128 192C128 174.3 142.3 160 160 160L224 160z"/></svg></div>
                    <span class="menu-text">Logout</span>
                </button>
            </form>
            </div>
        </nav>

        <main class="main-content">
            @yield('content')
        </main>

    </div>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>