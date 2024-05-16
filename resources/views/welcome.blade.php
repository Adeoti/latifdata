<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>SweetBill - Your ultimate app to settle your bills with sweetness.</title>
    <meta name="description" content="If you want the best app to buy affordable MTN, Glo, Airtel and 9Mobile Data and Airtime, SweetBill is your best friend." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset("assets/images/sweetbilllogob.svg") }}" />

    <!-- ========================= CSS here ========================= -->
    <link rel="stylesheet" href="{{ asset("assets/css/bootstrap.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/css/LineIcons.3.0.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/css/animate.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/css/tiny-slider.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/css/glightbox.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/css/main.css") }}" />

</head>

<body>
    <!--[if lte IE 9]>
      <p class="browserupgrade">
        You are using an <strong>outdated</strong> browser. Please
        <a href="https://browsehappy.com/">upgrade your browser</a> to improve
        your experience and security.
      </p>
    <![endif]-->

    <!-- Preloader -->
    <div class="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- /End Preloader -->

    <!-- Start Header Area -->
    <header class="header navbar-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="nav-inner">
                        <!-- Start Navbar -->
                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand" href="{{ url("/") }}">
                                <img src="{{ asset("/assets/images/sweetbilllogo.svg") }}" style="border-radius: 10px; transform:scale(.6);" alt="#">
                                <!-- <span style="color:#ffffff; font-weight:700; font-size:29px;">SweetBill</span> -->

                            </a>
                            <button class="navbar-toggler mobile-menu-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                <ul id="nav" class="navbar-nav ms-auto">
                                    <li class="nav-item">
                                        <a href="{{ url("/") }}" class="active" aria-label="Toggle navigation">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url("app/register") }}" class="active" aria-label="Toggle navigation">Sign Up</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url("app") }}" class="active" aria-label="Toggle navigation">Sign In</a>
                                    </li>
                                   
                                   
                                    
                                </ul>
                            </div> <!-- navbar collapse -->
                            <div class="button home-btn">
                                <a href="{{url('app/register')}}" class="btn">Sign up for free</a>
                            </div>
                        </nav>
                        <!-- End Navbar -->
                    </div>
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
    </header>
    <!-- End Header Area -->

    <!-- Start Hero Area -->
    <section class="hero-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-12 col-12">
                    <div class="hero-content">
                        <h4>The sweetest App to  Settle Your</h4>
                        <h1>Daily Bills and <br>
                            Enjoy the Process</h1>
                        <p>SweetBill gives you the sweetest experience while topping up your DATA Bundles, Airtime, Electricity Bills, and so on.
                        </p>
                        <div class="button">
                            <a href="{{url('app/register')}}" class="btn ">Get started now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-12">
                    <div class="hero-image wow fadeInRight" data-wow-delay=".4s">
                        <img class="main-image" src="{{ asset("assets/images/after-hero-left.png") }}"  alt="#">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Hero Area -->

    <!-- Start Features Area -->
    <section class="freatures section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-12">
                    <div class="image wow fadeInLeft" data-wow-delay=".3s">
                        <img src="{{ asset("/assets/images/herophoneb.png") }}" style="transform: scale(.7);" alt="#">
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="content">
                        <h3 class="heading wow fadeInUp" data-wow-delay=".5s"><span>Core Features</span>SweetBill is Designed & Built
                            
                            For<br> Your Daily Bill Settling</h3>
                        <!-- Start Single Feature -->
                        <div class="single-feature wow fadeInUp" data-wow-delay=".6s">
                            <div class="f-icon">
                                <i class="lni lni-dashboard"></i>
                            </div>
                            <h4>Fast performance</h4>
                            <p>Quickly buy Data, Airtime, Exam Pins, DSTV Subs, GoTv Subs, StarTime Subs, Electricity Bills, with ease</p>
                        </div>
                        <!-- End Single Feature -->
                        <!-- Start Single Feature -->
                        <div class="single-feature wow fadeInUp" data-wow-delay=".7s">
                            <div class="f-icon">
                                <i class="lni lni-pencil-alt"></i>
                            </div>
                            <h4>Affordable Products</h4>
                            <p>With SweetBill, you won't need to break your bank to settle your day-to-day bills. Infact, you can enjoy our products with as low as <b>N100</b></p>
                        </div>
                        <!-- End Single Feature -->
                        <!-- Start Single Feature -->
                        <div class="single-feature wow fadeInUp" data-wow-delay="0.8s">
                            <div class="f-icon">
                                <i class="lni lni-vector"></i>
                            </div>
                            <h4>Earn Cashback</h4>
                            <p>SweetBill gives you cashback on every successful transaction you make; what a sweet experience!</p>
                        </div>
                        <!-- End Single Feature -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Features Area -->

    <!-- Start Services Area -->
    <div class="services section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h3 class="wow zoomIn" data-wow-delay=".2s">What we offer</h3>
                        <h2 class="wow fadeInUp" data-wow-delay=".4s">Our Services</h2>
                        <p class="wow fadeInUp" data-wow-delay=".6s">SweetBill is packed with sweet services and features that you need to boost your every-day life.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".2s">
                    <div class="single-service">
             
                        <h4 class="text-title" style="border-left:9px solid #fe5006; border-radius:9px; padding-left:10px;">MTN, Glo, Airtel, and 9Mobile Data</h4>
                            <img src="{{ asset("/assets/images/networks-imageb.png") }}" style="height:180px;" alt="Networks">
                        <p>With a few clicks, you can buy affordable data bundles on SweetBill - with no stress at all.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".4s">
                    <div class="single-service">
                        
                        <h4 class="text-title">MTN, Glo, Airtel, and 9Mobile Airtime</h4>
                        <img src="{{ asset("/assets/images/networks-imageb.png") }}" style="height:180px;" alt="Networks">
                        <p>SweetBill takes away the stress of typing long numbers when trying to get airtime on your phone.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".6s">
                    <div class="single-service">
                        
                        <h4 class="text-title">DSTV, GoTV, and StartTime Cable Subscriptions</h4>
                        <img src="{{ asset("/assets/images/cable-image.png") }}" style="height:180px;" alt="Networks">
                        <p>Now, you don't have to run out of your home to subscribe to your favorite decoder and catch up with decent programs.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".2s">
                    <div class="single-service">
                        
                        <h4 class="text-title">Electricity Bill Payment</h4>
                        <img src="{{ asset("/assets/images/electricity-image.png") }}" style="height:180px;" alt="Networks">
                        <p>On the SweetBill App, just tap 2 to 3 buttons and get your electricity meter loaded ASAP!</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".4s">
                    <div class="single-service">
                        
                        <h4 class="text-title">WAEC, JAMB, and NABTEB Pins</h4>
                        <img src="{{ asset("/assets/images/exams-image.png") }}" style="height:180px;" alt="Networks">
                        <p>Just login to your SweetBill App and purchase your exam pins; without sweating a bit.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".6s">
                    <div class="single-service">
                        
                        <h4 class="text-title">Trade Gift Cards Seamlessly</h4>

                        <img src="{{ asset("/assets/images/giftcard-image.png") }}" style="height:180px;" alt="Networks">
                        <p>Trade your decent Gift Cards and enjoy the seamless and transparent experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Services Area -->

    <!-- Start Pricing Table Area -->
    <section id="pricing" class="pricing-table section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h3 class="wow zoomIn" data-wow-delay=".2s">pricing</h3>
                        <h2 class="wow fadeInUp" data-wow-delay=".4s">Pricing & Plans</h2>
                        <p class="wow fadeInUp" data-wow-delay=".6s">
                            Unlock value, not just prices. Discover transparent pricing tailored to your needs. See how affordability meets quality with our flexible pricing options.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".4s">
                    <!-- Single Table -->
                    <div class="single-table">
                        <!-- Table Head -->
                        <div class="table-head">
                            <h4 class="title">Primary</h4>
                            <p class="sub-title">Enjoy affordable Data Bundles and other subs</p>
                            <div class="price">
                                <h2 class="amount"><span class="duration">FREE</span>
                                </h2>
                            </div>
                        </div>
                        <!-- End Table Head -->
                        <!-- Start Table Content -->
                        <div class="table-content">
                            <!-- Table List -->
                            <ul class="table-list">
                                <li>Airtime top-up</li>
                                <li>Data bundles</li>
                                <li>Cable subscriptions</li>
                                <li>Examination pins</li>
                                <li class="disable">Agent private group</li>
                                <li class="disable">Own a VTU Portal</li>
                                <li class="disable">Resell @ your rates</li>
                            </ul>
                            <!-- End Table List -->
                        </div>
                        <!-- End Table Content -->
                        <div class="button">
                            <a href="{{ url("app/register") }}" class="btn">Create a Free Account <i
                                    class="lni lni-arrow-right"></i></a>
                        </div>
                        <p class="no-card">No credit card required</p>
                    </div>
                    <!-- End Single Table-->
                </div>
                <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".6s">
                    <!-- Single Table -->
                    <div class="single-table middle">
                        <span class="popular">Most Popular</span>
                        <!-- Table Head -->
                        <div class="table-head">
                            <h4 class="title">Agent</h4>
                            <p class="sub-title">Sell Data and other subs to make money</p>
                            <div class="price">
                                <h2 class="amount"><span class="currency">₦</span>2k<span class="duration">/Lifetime</span>
                                </h2>
                            </div>
                        </div>
                        <!-- End Table Head -->
                        <!-- Start Table Content -->
                        <div class="table-content">
                            <!-- Table List -->
                            <ul class="table-list">
                                <li>Airtime top-up</li>
                                <li>Data bundles</li>
                                <li>Cable subscriptions</li>
                                <li>Examination pins</li>
                                <li>Agent private group</li>
                                <li class="disable">Own a VTU Portal</li>
                                <li class="disable">Resell @ your rates</li>
                            </ul>
                            <!-- End Table List -->
                        </div>
                        <!-- End Table Content -->
                        <div class="button">
                            <a href="{{ url("app/register") }}" class="btn btn-alt">Create a Free Account <i
                                    class="lni lni-arrow-right"></i></a>
                        </div>
                        <p class="no-card">Upgrade to the Agent Package after your registration</p>
                    </div>
                    <!-- End Single Table-->
                </div>
                <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".8s">
                    <!-- Single Table -->
                    <div class="single-table">
                        <!-- Table Head -->
                        <div class="table-head">
                            <h4 class="title">Affiliate User</h4>
                            <p class="sub-title">Own your VTU Website and App and make money everyday</p>
                            <div class="price">
                                <h2 class="amount"><span class="currency">₦</span>45k<span class="duration">/year</span>
                                </h2>
                            </div>
                        </div>
                        <!-- End Table Head -->
                        <!-- Start Table Content -->
                        <div class="table-content">
                            <!-- Table List -->
                            <ul class="table-list">
                                <li>Airtime top-up</li>
                                <li>Data bundles</li>
                                <li>Cable subscriptions</li>
                                <li>Examination pins</li>
                                <li>Agent private group</li>
                                <li>Own a VTU Portal</li>
                                <li>Resell @ your rates</li>
                            </ul>
                            <!-- End Table List -->
                        </div>
                        <!-- End Table Content -->
                        <div class="button">
                            <a href="{{ url("javascript:void(0)") }}" class="btn">Chat our rep <i
                                    class="lni lni-arrow-right"></i></a>
                        </div>
                        <p class="no-card">You will have a website and app like SweetBill</p>
                    </div>
                    <!-- End Single Table-->
                </div>
            </div>
        </div>
    </section>
    <!--/ End Pricing Table Area -->

    <!-- Start Intro Video Area -->
    <section class="intro-video-area section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="inner-content-head">
                        <div class="inner-content">
                            <img class="shape1" src="{{ asset("/assets/images/video/shape1.svg") }}" alt="#">
                            <img class="shape2" src="{{ asset("/assets/images/video/shape2.svg") }}" alt="#">
                            <div class="section-title">
                                <span class="wow zoomIn" data-wow-delay=".2s">SweetBill is your best friend that you will always love!</span>
                                <h2 class="wow fadeInUp" data-wow-delay=".4s">Watch Our intro video</h2>
                                <p class="wow fadeInUp" data-wow-delay=".6s">
                                    Top up your airtime, purchase internet data bundles, renew your cable subscriptions, and even buy examination pins like WAEC and JAMB with just a few taps. Our secure and reliable platform ensures that your transactions are processed swiftly, giving you peace of mind every time.
                                </p>
                            </div>
                            <div class="intro-video-play">
                                <div class="play-thumb wow zoomIn" data-wow-delay=".2s">
                                    <a href="https://www.youtube.com/watch?v=r44RKWyfcFw&fbclid=IwAR21beSJORalzmzokxDRcGfkZA1AtRTE__l5N4r09HcGS5Y6vOluyouM9EM"
                                        class="glightbox video"><i class="lni lni-play"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Intro Video Area -->

    <!-- Start Team Area -->

    {{--
    <section class="team section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h3 class="wow zoomIn" data-wow-delay=".2s">Expert Team</h3>
                        <h2 class="wow fadeInUp" data-wow-delay=".4s">Meet Our Team</h2>
                        <p class="wow fadeInUp" data-wow-delay=".6s">There are many variations of passages of Lorem
                            Ipsum available, but the majority have suffered alteration in some form.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 wow fadeInUp" data-wow-delay=".3s">
                    <!-- Start Single Team -->
                    <div class="single-team">
                        <div class="team-image">
                            <img src="https://via.placeholder.com/400x400" alt="#">
                        </div>
                        <div class="content">
                            <h4>Deco Milan
                                <span>Founder</span>
                            </h4>
                            <ul class="social">
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-facebook-filled"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-instagram"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-twitter-original"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-linkedin-original"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Single Team -->
                </div>
                <div class="col-lg-3 col-md-6 col-12 wow fadeInUp" data-wow-delay=".5s">
                    <!-- Start Single Team -->
                    <div class="single-team">
                        <div class="team-image">
                            <img src="https://via.placeholder.com/400x400" alt="#">
                        </div>
                        <div class="content">
                            <h4>Liza Marko
                                <span>Developer</span>
                            </h4>
                            <ul class="social">
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-facebook-filled"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-instagram"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-twitter-original"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-linkedin-original"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Single Team -->
                </div>
                <div class="col-lg-3 col-md-6 col-12 wow fadeInUp" data-wow-delay=".7s">
                    <!-- Start Single Team -->
                    <div class="single-team">
                        <div class="team-image">
                            <img src="https://via.placeholder.com/400x400" alt="#">
                        </div>
                        <div class="content">
                            <h4>John Smith
                                <span>Designer</span>
                            </h4>
                            <ul class="social">
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-facebook-filled"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-instagram"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-twitter-original"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-linkedin-original"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Single Team -->
                </div>
                <div class="col-lg-3 col-md-6 col-12 wow fadeInUp" data-wow-delay=".9s">
                    <!-- Start Single Team -->
                    <div class="single-team">
                        <div class="team-image">
                            <img src="https://via.placeholder.com/400x400" alt="#">
                        </div>
                        <div class="content">
                            <h4>Amion Doe
                                <span>Co-Founder</span>
                            </h4>
                            <ul class="social">
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-facebook-filled"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-instagram"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-twitter-original"></i></a></li>
                                <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-linkedin-original"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Single Team -->
                </div>
            </div>
        </div>
    </section>
    --}}

    <!--/ End Team Area -->

    <!-- Start Testimonials Area -->
    <section class="testimonials style2 section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h3 class="wow zoomIn" data-wow-delay=".2s">Customer Reviews</h3>
                        <h2 class="wow fadeInUp" data-wow-delay=".4s">Our Testimonials</h2>
                        <p class="wow fadeInUp" data-wow-delay=".6s">Our customers are vouching for SweetBill everywhere. See what they have say!</p>
                    </div>
                </div>
            </div>
            <div class="row testimonial-slider">
                <div class="col-lg-6 col-12 ">
                    <!-- Start Single Testimonial -->
                    <div class="single-testimonial">
                        <div class="inner-content">
                            <div class="quote-icon">
                                <i class="lni lni-quotation"></i>
                            </div>
                            <div class="text">
                                <p>“I used to dread the hassle of buying airtime and data bundles, especially during busy days. Since I started using the SweetBill app, it's been a game-changer! Quick, convenient, and reliable. I can't imagine going back to the old way.”</p>
                            </div>
                            <div class="author">
                                <img src="https://via.placeholder.com/100x100" alt="#">
                                <h4 class="name">Sarah, Lagos, Nigeria
                                    <span class="deg">Business Manager</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Testimonial -->
                </div>
                <div class="col-lg-6 col-12 ">
                    <!-- Start Single Testimonial -->
                    <div class="single-testimonial">
                        <div class="inner-content">
                            <div class="quote-icon">
                                <i class="lni lni-quotation"></i>
                            </div>
                            <div class="text">
                                <p>
                                    “I'm not very tech-savvy, but this app is so easy to use! I can now pay my bills without needing assistance from anyone. The customer support team is also very helpful whenever I have questions. Thank you for making my life easier!”
                                </p>
                            </div>
                            <div class="author">
                                <img src="https://via.placeholder.com/100x100" alt="#">
                                <h4 class="name">Fatima, Abuja, Nigeria
                                    <span class="deg">Trader</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Testimonial -->
                </div>
                <div class="col-lg-6 col-12 ">
                    <!-- Start Single Testimonial -->
                    <div class="single-testimonial">
                        <div class="inner-content">
                            <div class="quote-icon">
                                <i class="lni lni-quotation"></i>
                            </div>
                            <div class="text">
                                <p>
                                    “I was skeptical at first, but after trying out the SweetBill app, I was pleasantly surprised by how smooth and hassle-free it is. Now, I can pay my bills from the comfort of my home, without having to stand in long queues or deal with paper receipts. Thank you for simplifying my life!”
                                </p>
                            </div>
                            <div class="author">
                                <img src="https://via.placeholder.com/100x100" alt="#">
                                <h4 class="name">Daniel, Ogun, Nigeria
                                    <span class="deg">Graphics Designer</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Testimonial -->
                </div>
                <div class="col-lg-6 col-12 ">
                    <!-- Start Single Testimonial -->
                    <div class="single-testimonial">
                        <div class="inner-content">
                            <div class="quote-icon">
                                <i class="lni lni-quotation"></i>
                            </div>
                            <div class="text">
                                <p>“
                                    I travel frequently for work, and this app has been a lifesaver! No matter where I am, I can easily top up my airtime and data bundles within seconds. It's reliable, efficient, and saves me a lot of time. Definitely worth it!
                                ”</p>
                            </div>
                            <div class="author">
                                <img src="https://via.placeholder.com/100x100" alt="#">
                                <h4 class="name">Ibrahim, Ibadan, Nigeria
                                    <span class="deg">Real Estate Engineer</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Testimonial -->
                </div>
                <div class="col-lg-6 col-12 ">
                    <!-- Start Single Testimonial -->
                    <div class="single-testimonial">
                        <div class="inner-content">
                            <div class="quote-icon">
                                <i class="lni lni-quotation"></i>
                            </div>
                            <div class="text">
                                <p>“
                                    I've tried several bill payment apps in the past, but none have impressed me as much as this SweetBill ooo. It's user-friendly, fast, and offers a wide range of services. Whether it's topping up my airtime or renewing my DSTV subscription, this app has become my go-to solution.
                                ”</p>
                            </div>
                            <div class="author">
                                <img src="https://via.placeholder.com/100x100" alt="#">
                                <h4 class="name">Yusuf, Shagamu, Nigeria
                                    <span class="deg">Naval Officer</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Testimonial -->
                </div>
            </div>
        </div>
    </section>
    <!-- End Testimonial Area -->

    <!-- Start Blog Section Area -->
    {{-- <section class="blog-section section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h3 class="wow zoomIn" data-wow-delay=".2s">Blogs</h3>
                        <h2 class="wow fadeInUp" data-wow-delay=".4s">Our Latest News</h2>
                        <p class="wow fadeInUp" data-wow-delay=".6s">
                            Stay informed, stay inspired. Explore our latest updates and insights in our blog section. Your go-to destination for fresh perspectives and timely news about billing and techs in Africa.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                @if (!empty($posts))
                @foreach ($posts as $post)
                    
                    <div class="col-lg-4 col-md-6 col-12 wow fadeInUp" data-wow-delay=".4s">
                        <!-- Start Single Blog Grid -->
                        <div class="single-blog-grid">
                            <div class="blog-img">
                                <a href="{{ url("blog-single.html") }}">
                                    @if (!empty($post['featured_image']))
                                        <img src="{{ $post['featured_image'] }}" alt="Featured Image">
                                    @endif
                                </a>
                            </div>
                            <div class="blog-content">
                                <div class="meta-info">
                                    <a class="date" href="{{ url("javascript:void(0)") }}"><i class="lni lni-timer"></i> {{ $post['date'] }}
                                    </a>
                                    <a class="author" href="{{ url("javascript:void(0)") }}"><i class="lni lni-user"></i> {{ $post['author'] }}
                                    </a>
                                </div>
                                <h4>
                                    <a href="{{ url("blog-single.html") }}">
                                        {!! $post['title']['rendered'] !!}
                                    </a>
                                </h4>
                                <p> 
                                    {!! $post['excerpt']['rendered'] !!}
                                </p>
                                <div class="button">
                                    <a href="{{ url("") }}" class="btn">Read More</a>
                                </div>
                            </div>
                        </div>
                        <!-- End Single Blog Grid -->
                    </div>
                @endforeach
            @else
                <p>No posts available.</p>
            @endif
               
                

            </div>
        </div>
    </section> --}}
    <!-- End Blog Section Area -->

    <!-- Start Faq Area -->
    <section class="faq section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h3 class="wow zoomIn" data-wow-delay=".2s">SweetBill Faq</h3>
                        <h2 class="wow fadeInUp" data-wow-delay=".4s"> frequently asked questions</h2>
                        <p class="wow fadeInUp" data-wow-delay=".6s">Explore Our Knowledge Base: Answers to Frequently Asked Questions About Our Services and Products.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                    <span class="title">Are there any additional fees or charges when using the app?</span><i
                                        class="lni lni-plus"></i>
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="heading1"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <p>We strive to keep our services transparent and affordable. While there may be minor transaction fees depending on the service and payment method used, we always display these fees upfront before you confirm your transaction.</p>
                                    <p>There are no hidden charges.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                    <span class="title">How quickly are bill payments processed on SweetBill?</span><i
                                        class="lni lni-plus"></i>
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <p>Bill payments on our app are typically processed instantly. Once you initiate a transaction, you should see the corresponding service activated or credited to your account within seconds.
                                    </p>
                                    <p>
                                        In rare cases where there are delays, we provide timely updates and support to resolve any issues.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    <span class="title">What should I do if I encounter an issue with a transaction?</span><i
                                        class="lni lni-plus"></i>
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <p>
                                        If you encounter any issues or have questions about a transaction, our customer support team is here to assist you. You can reach out to us through the app's support portal, and our dedicated team will work to resolve your concerns promptly.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-12 xs-margin">
                    <div class="accordion" id="accordionExample2">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading11">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse11" aria-expanded="false" aria-controls="collapse11">
                                    <span class="title">
                                        Is there a limit to the number or amount of bills I can pay through the SweetBill app?
                                    </span><i class="lni lni-plus"></i>
                                </button>
                            </h2>
                            <div id="collapse11" class="accordion-collapse collapse" aria-labelledby="heading11"
                                data-bs-parent="#accordionExample2">
                                <div class="accordion-body">
                                    <p>
                                        We do not impose limits on the number or amount of bills you can pay through our app. You can conveniently settle all your day-to-day bills without worrying about restrictions.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading22">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse22" aria-expanded="false" aria-controls="collapse22">
                                    <span class="title">
                                        Can I use the app to pay bills for others, such as family members or friends?
                                    </span><i class="lni lni-plus"></i>
                                </button>
                            </h2>
                            <div id="collapse22" class="accordion-collapse collapse" aria-labelledby="heading22"
                                data-bs-parent="#accordionExample2">
                                <div class="accordion-body">
                                    <p>
                                        Absolutely! SweetBill allows you to pay bills not only for yourself but also for others. Whether you're helping out a family member or covering a friend's expenses, you can easily initiate transactions on their behalf.
                                    </p>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading33">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse33" aria-expanded="false" aria-controls="collapse33">
                                    <span class="title">Can I view my transaction history on the app?</span><i class="lni lni-plus"></i>
                                </button>
                            </h2>
                            <div id="collapse33" class="accordion-collapse collapse" aria-labelledby="heading33"
                                data-bs-parent="#accordionExample2">
                                <div class="accordion-body">
                                    <p>
                                        Yes, you can easily view your transaction history within the app. Simply navigate to the "Transaction History" section, where you'll find a comprehensive record of all your past payments and purchases.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Faq Area -->

    <!-- Start Call Action Area -->
    <section class="call-action">
        <div class="container">
            <div class="inner-content">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-7 col-12">
                        <div class="text">
                            <h2>Download Our App &
                                <br> Start enjoying your bill payment today.
                            </h2>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-5 col-12">
                        <div class="button">
                            <a href="{{ url("/") }}" class="btn"><i class="lni lni-apple"></i> App Store
                            </a>
                            <a href="{{ url("/") }}" class="btn btn-alt"><i class="lni lni-play-store"></i> Google
                                Play</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Call Action Area -->

    <!-- Start Footer Area -->
    <footer class="footer section">
        <!-- Start Footer Top -->
        <div class="footer-top">
            <div class="container">
                <div class="inner-content">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer f-about">
                                <div class="logo">
                                    <a href="{{ url("/") }}">
                                        <img src="{{ asset("/assets/images/sweetbilllogo.svg") }}" style="border-radius: 10px; transform:scale(.6);" alt="#">
                                    </a>
                                </div>
                                <p>Making the world a better place through seamless bill payment.</p>
                                <h4 class="social-title">Follow Us On:</h4>
                                <ul class="social">
                                    <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-facebook-filled"></i></a></li>
                                    <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-instagram"></i></a></li>
                                    <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-twitter-original"></i></a></li>
                                    <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-linkedin-original"></i></a></li>
                                    <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-pinterest"></i></a></li>
                                    <li><a href="{{ url("javascript:void(0)") }}"><i class="lni lni-youtube"></i></a></li>
                                </ul>
                                <p>
                                    <b>Address: </b> 5, IFELODUN STREET AJEGUNLE IKORODU, Lagos 
                                </p>
                                <p>
                                    <b>Phone No.: </b> 09114071688 
                                </p>

                                <p>
                                    <b>Email: </b> Adeoti4tech@gmail.com
                                </p>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer f-link">
                                <h3>Solutions</h3>
                                <ul>
                                    <li><a href="{{ url("app/buy-data") }}">Buy Data</a></li>
                                    <li><a href="{{ url("app/buy-air-time") }}">Buy Airtime</a></li>
                                    <li><a href="{{ url("app/cable-subscriptions") }}">Pay for DSTV</a></li>
                                    <li><a href="{{ url("app/cable-subscriptions") }}">Pay for GoTV</a></li>
                                    <li><a href="{{ url("app/cable-subscriptions") }}">Pay for StarTime</a></li>
                                    <li><a href="{{ url("app/electricity") }}">Pay for Electricity</a></li>
                                    <li><a href="{{ url("app/exam-pins") }}">Buy Exam Pins</a></li>
                                    
                                </ul>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer f-link">
                                <h3>Support</h3>
                                <ul>
                                    <li><a href="{{ url("javascript:void(0)") }}">Pricing</a></li>
                                    <li><a href="{{ url("app/a-p-i-documentation") }}">API Documentation</a></li>
                                    <li><a href="{{ url("about") }}">About Us</a></li>
                                    <li><a href="{{ url("app/register") }}">Create Account</a></li>
                                    <li><a href="{{ url("javascript:void(0)") }}">Chat Us on WhatsApp</a></li>
                                </ul>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <!-- Single Widget -->
                            <div class="single-footer newsletter">
                                <h3>Subscribe</h3>
                                <p>Subscribe to our newsletter for the latest updates</p>
                                <form action="#" method="get" target="_blank" class="newsletter-form">
                                    <input name="EMAIL" placeholder="Email address" required="required" type="email">
                                    <div class="button">
                                        <button class="sub-btn"><i class="lni lni-envelope"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <!-- End Single Widget -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ End Footer Top -->
        <!-- Start Copyright Area -->
        <div class="copyright-area">
            <div class="container">
                <div class="inner-content">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12">
                            <p class="copyright-text">© 2024  SweetBill - All Rights Reserved</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <p class="copyright-owner">Designed and Developed by <a href="https://adeotidigital.com/"
                                    rel="nofollow" target="_blank">Adeoti Digital</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Copyright Area -->
    </footer>
    <!--/ End Footer Area -->

    <!-- ========================= scroll-top ========================= -->
    <a href="{{ url("#") }}" class="scroll-top">
        <i class="lni lni-chevron-up"></i>
    </a>

    <!-- ========================= JS here ========================= -->
    <script src="{{ asset("/assets/js/bootstrap.min.js") }}"></script>
    <script src="{{ asset("/assets/js/wow.min.js") }}"></script>
    <script src="{{ asset("/assets/js/tiny-slider.js") }}"></script>
    <script src="{{ asset("/assets/js/glightbox.min.js") }}"></script>
    <script src="{{ asset("/assets/js/count-up.min.js") }}"></script>
    <script src="{{ asset("/assets/js/main.js") }}"></script>
    <script>

        //========= testimonial 
        tns({
            container: '.testimonial-slider',
            items: 3,
            slideBy: 'page',
            autoplay: false,
            mouseDrag: true,
            gutter: 0,
            nav: true,
            controls: false,
            responsive: {
                0: {
                    items: 1,
                },
                540: {
                    items: 1,
                },
                768: {
                    items: 2,
                },
                992: {
                    items: 2,
                },
                1170: {
                    items: 3,
                }
            }
        });

        //====== counter up 
        var cu = new counterUp({
            start: 0,
            duration: 2000,
            intvalues: true,
            interval: 100,
            append: " ",
        });
        cu.start();

        //========= glightbox
        GLightbox({
            'href': 'https://www.youtube.com/watch?v=r44RKWyfcFw&fbclid=IwAR21beSJORalzmzokxDRcGfkZA1AtRTE__l5N4r09HcGS5Y6vOluyouM9EM',
            'type': 'video',
            'source': 'youtube', //vimeo, youtube or local
            'width': 900,
            'autoplayVideos': true,
        });

    </script>
</body>

</html>