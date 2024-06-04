<x-filament-panels::page>
    


@php
    $user = filament()->auth()->user();
    $user_balance = number_format($user->balance,2);
    $cashback_amount = number_format($user->cashback_balance,2);

    function getFirstWord($string)
    {
        // Split the string by spaces
        $words = explode(' ', $string);
        // Return the first element
        return $words[0];
    }
@endphp

    @assets
    <style>

        @media (max-width: 767px) {
           .transaction-history, .balanceToggle{
            font-size: 0.7em !important;
           }
        }





        main.custom {
            text-align: center;
        }

        .card {
            background: linear-gradient(135deg, #FE5007, #FFA500);
            color: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            max-width: auto;
            margin: 10px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        .left-column {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            z-index: 1;
        }

        .wallet-balance {
            font-size: 1.5em;
            font-weight: bold;
            position: relative;
            margin-bottom: 10px;
        }

        .toggle-container {
            display: flex;
            align-items: center;
        }

        .toggle-container label {
            margin-left: 10px;
            font-size: 0.9em;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #FFA500;
            transition: .4s;
            border-radius: 28px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #ccc;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(22px);
        }

        .right-column {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
            z-index: 1;
            margin-left: 20px;
        }

        .transaction-history {
            color: white;
            text-decoration: none;
            font-size: 0.6em;
            margin-bottom: 10px;
        }

        .fund-wallet-btn {
            background-color: white;
            color: #FE5007;
            border: none;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .fund-wallet-btn:hover {
            background-color: #e64a06;
            color: white;
        }

        .services {
            background-color: white;
            padding: 20px 10px;
            margin-top: 40px;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: auto;
            margin: 40px auto;
            text-align: left;
        }

        .services h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 1.2em;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .service-item {
            background-color: #f0f0f0;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 10px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .service-item:hover {
            background-color: #e0e0e0;
        }

        .service-item img {
            width: 30px;
            height: 30px;
        }

        .service-item p {
            margin: 10px 0 0;
            font-size: 0.9em;
        }

        .welcome-text {
            text-align: left;
        }

        span.package {
            font-size: 12px;
            color: #333;
            display: block;
        }

        span.package b {
            font-weight: 500;
        }

        .welcome-text .first {
            font-weight: 500;
        }

        .services-heading {
            font-weight: 500;
            color: gray;
            font-size: 16px !important;
        }

        .cashback-commission {
            margin-top: 1px;
            margin-bottom: 7px;
            font-size: 0.9em;
            text-align: center;
            color: white;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    <main class="custom">
        <div class="welcome-text">
            <span class="first">Hello, {{ filament()->getUserName($user) }} 👋</span>
            <span class="package">You're on the <b>{{ strtoupper(filament()->auth()->user()->package) }}</b> package</span>
        </div>

        <div class="card" wire:poll.5000ms>
            <div class="left-column">
                <div class="wallet-balance" id="walletBalance">
                    @if($balanceToggle)
                        *****
                    @else
                        ₦{{ $user_balance }}
                    @endif
                </div>
                <div class="cashback-commission">
                    <span>Cashback: 
                        <span id="cashbackBalance">
                            @if($balanceToggle)
                              *****  
                            @else
                                ₦{{ $cashback_amount }}
                            @endif
                        </span>
                    </span>
                </div>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox" id="balanceToggle" wire:change="toggleBalance" @if($balanceToggle) checked @endif>
                        <span class="toggle-slider"></span>
                    </label>
                    <label for="balanceToggle" class="balanceToggle">Toggle Balance</label>
                </div>
            </div>
            <div class="right-column">
                <a href="app/customer-transactions" wire:navigate class="transaction-history">Transaction History &rarr;</a>
                <a href="app/fund-wallet" wire:navigate class="fund-wallet-btn">Fund Wallet</a>
            </div>
        </div>
        

        <div class="announcement-pane">
            @if ($announcement_status === true)
                @if ($announcement_style == 'scroll')
                    <div style="border: 1px solid #FFA500; border-radius: 18px; padding: 5px 10px; display:flex;">
                        <x-heroicon-m-speaker-wave style="color:#FFA500; height:21px; width:21px;" />
                        <marquee behavior="scroll" direction="left" onmouseover="this.stop()" onmouseout="this.start()">
                            {!! $announcement_content !!}
                        </marquee>
                    </div>
                @endif

                @if ($announcement_style == 'banner')
                    <div style="background-color: rgba(255, 255, 255, 0.904); color: #fe5006; padding: 5px; border-radius: 4px; border-left: 3px solid #fe5006;">
                        {!! $announcement_content !!}
                    </div>
                @endif

                @if ($announcement_style == 'pop')
                    <div id="popElem" rel="on" style="display: none;">
                        {!! $announcement_content !!}
                    </div>
                @endif
            @endif

            <div id="popElem" rel="off"></div>
        </div>

        <div class="services">
            <h2 class="services-heading">Services</h2>
            <div class="services-grid">
                <a class="service-item" href="app/buy-data" wire:navigate>
                    <center><x-heroicon-s-chart-bar-square class="h-6 w-6" style="color:#FFA500;" /></center>
                    <p>Data</p>
                </a>
                <a class="service-item" href="app/buy-air-time" wire:navigate>
                    <center><x-heroicon-s-phone-arrow-up-right class="h-6 w-6" style="color:#0f9a70;" /></center>
                    <p>Airtime</p>
                </a>
                <a href="app/cable-subscriptions" wire:navigate class="service-item">
                    <center><x-heroicon-s-tv class="h-6 w-6" style="color:#0f9a70;" /></center>
                    <p>Cable</p>
                </a>
                <a href="app/electricity" wire:navigate class="service-item">
                    <center><x-heroicon-s-light-bulb class="h-6 w-6" style="color:#FFA500;" /></center>
                    <p>Electricity</p>
                </a>
                <a href="app/fund-wallet" wire:navigate class="service-item">
                    <center><x-heroicon-s-wallet class="h-6 w-6" style="color:#FE5007;" /></center>
                    <p>Fund Wallet</p>
                </a>
                <a href="app/share-wallet" wire:navigate class="service-item">
                    <center><x-heroicon-s-cursor-arrow-rays class="h-6 w-6" style="color:#FE5007;" /></center>
                    <p>Share</p>
                </a>
                <a href="app/cashback-withdrawal-page" wire:navigate class="service-item">
                    <center><x-heroicon-s-banknotes class="h-6 w-6" style="color:#FE5007;" /></center>
                    <p>Withdraw</p>
                </a>
                <a href="app/my-beneficiaries" wire:navigate class="service-item">
                    <center><x-heroicon-s-cog-6-tooth class="h-6 w-6" style="color:#0f9a70;" /></center>
                    <p>Beneficiaries</p>
                </a>
                <a href="https://wa.link/sbdozm" class="service-item">
                    <center><x-heroicon-s-chat-bubble-left-ellipsis class="h-6 w-6" style="color:#FE5007;" /></center>
                    <p>Chat Us</p>
                </a>
            </div>
        </div>



        {{$this->table}}
    </main>
    
<div>
    @script
    <script>
    

            const balanceToggle = document.getElementById('balanceToggle');
            const walletBalance = document.getElementById('walletBalance');
            const cashbackBalance = document.getElementById('cashbackBalance');
            const userBalance = @json($user_balance);
            const userCashback = @json($cashback_amount);


            if (balanceToggle && walletBalance) {
                balanceToggle.addEventListener('change', () => {
                    if (balanceToggle.checked) {
                        walletBalance.textContent = "*****";
                        cashbackBalance.textContent = "*****";
                        
                    } else {
                        walletBalance.textContent = `₦${userBalance}`;
                        cashbackBalance.textContent = `₦${userCashback}`;
                    }
                });
            }

            let popElem = document.getElementById('popElem');

            if (popElem && popElem.getAttribute('rel') === "on") {
                Swal.fire({
                    title: 'Announcement',
                    html: popElem.innerHTML,
                    icon: 'info',
                    confirmButtonText: 'Got it!'
                });
            }
        
        
    </script>
    @endscript
</div>
</x-filament-panels::page>
