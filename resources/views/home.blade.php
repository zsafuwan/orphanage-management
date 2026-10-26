<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HopeHaven | Orphanage & Child Care Sanctuary</title>
    <meta name="description" content="HopeHaven Orphanage Management System - Providing shelter, education, healthcare, and loving guidance to vulnerable children.">
    
    <!-- Animate.css for signature template 2 entrance animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Leaflet.js Interactive Live Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    
    <!-- Template 2 Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/template2.css') }}">
    
    <!-- jQuery for template 2 smooth scroll and dynamic interactions -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <!-- Preloader Spinner matching template 2 -->
    <div id="loading">
        <div id="spinner"></div>
        <div id="loading-text">HOPEHAVEN SANCTUARY</div>
    </div>

    <!-- Image Magnify Modal Lightbox matching template 2 -->
    <div id="magnify">
        <h1 onclick="closemagnify()"><i class="fas fa-times"></i></h1>
        <div id="img_here"></div>
        <div id="magnify_caption">HopeHaven Orphanage Moments</div>
    </div>

    <!-- Fixed Header matching template 2 with sticky scroll effect -->
    <header id="header" class="animated slideInDown" style="animation-delay: 1.2s;">
        <table id="header_table">
            <tr>
                <td id="logo">
                    <a href="#top_part" style="color: inherit; text-decoration: none;">
                        HopeHaven<span class="logo-dot">.</span>
                        <span class="logo-sub">Child Care & Sanctuary</span>
                    </a>
                </td>
                <td id="mobile_toggle_cell">
                    <button id="mobile_nav_toggle" class="mobile-nav-toggle" aria-label="Toggle navigation" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                </td>
                <td id="navigation">
                    <a href="#programs">programs</a>
                    <a href="#work">gallery</a>
                    <a href="#bio">our story</a>
                    <a href="#contact">contact</a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-portal-link">
                            <i class="fas fa-columns"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="nav-portal-link">
                            <i class="fas fa-shield-alt"></i> Staff & Admin Portal
                        </a>
                    @endauth

                    <a href="#contact" class="nav-cta-btn">
                        <i class="fas fa-heart"></i> Get Involved
                    </a>
                </td>
            </tr>
        </table>
    </header>

    <!-- Top Hero Section matching template 2 -->
    <div id="top_part">
        <div id="about" class="animated slideInLeft" style="animation-delay: 1.4s;">
            <span class="hero-tag">
                <i class="fas fa-hand-holding-heart"></i> Empowering Young Lives Since 2012
            </span>
            <h1>Every child deserves a <span>safe home</span>, education, and bright future</h1>
            <p class="hero-description">
                HopeHaven is a loving residential sanctuary that provides orphaned and vulnerable children with comprehensive nutrition, trauma-informed counseling, high-quality schooling, and lifelong mentorship.
            </p>
            <div class="hero-btn-group">
                <a href="#programs" class="btn_one">Our Programs</a>
                <a href="#contact" class="btn_two">Volunteer With Us</a>
            </div>

            <table>
                <tr>
                    <td class="animated zoomIn" style="animation-delay: 1.6s;">
                        <a class="social" href="#contact" title="Facebook"><i class="fab fa-facebook"></i></a>
                    </td>
                    <td class="animated zoomIn" style="animation-delay: 1.8s;">
                        <a class="social" href="#contact" title="Twitter / X"><i class="fab fa-twitter"></i></a>
                    </td>
                    <td class="animated zoomIn" style="animation-delay: 2.0s;">
                        <a class="social" href="#contact" title="Instagram"><i class="fab fa-instagram"></i></a>
                    </td>
                    <td class="animated zoomIn" style="animation-delay: 2.2s;">
                        <a class="social" href="#contact" title="YouTube"><i class="fab fa-youtube"></i></a>
                    </td>
                    <td class="animated zoomIn" style="animation-delay: 2.4s;">
                        <a class="social" href="#contact" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </td>
                </tr>
            </table>
        </div>
        <div id="rightImage" class="animated jackInTheBox" style="animation-delay: 1.6s;">
            <div class="hero-overlay-card">
                <h3>148 Children in Active Care</h3>
                <p>100% of public donations directly fund child healthcare, nutrition, and schooling programs with complete fiscal transparency.</p>
            </div>
        </div>
        <div id="rightMap" class="animated fadeInRight" style="animation-delay: 1.8s;">
            <div class="malaysia-map-card">
                <div class="map-card-header">
                    <div class="map-title-wrap">
                        <span class="map-live-tag"><span class="pulse-glow"></span> VERIFIED GOOGLE MAPS LOCATIONS</span>
                        <h4 class="map-card-heading"><i class="fas fa-map-marked-alt"></i> Real Orphanages Around Malaysia</h4>
                    </div>
                    <button type="button" id="btn_locate_user" class="btn-locate-gps" title="Find nearest sanctuary to your location">
                        <i class="fas fa-crosshairs"></i> <span id="locate_btn_text">Near Me</span>
                    </button>
                </div>

                <!-- State Quick Filter Tabs -->
                <div class="malaysia-state-tabs" id="state_filter_tabs">
                    <button type="button" class="state-tab active" data-state="all">All Malaysia</button>
                    <button type="button" class="state-tab" data-state="kl_selangor">KL & Selangor</button>
                    <button type="button" class="state-tab" data-state="penang">Penang</button>
                    <button type="button" class="state-tab" data-state="johor">Johor</button>
                    <button type="button" class="state-tab" data-state="perak">Perak</button>
                    <button type="button" class="state-tab" data-state="melaka">Melaka</button>
                    <button type="button" class="state-tab" data-state="borneo">Sabah & Sarawak</button>
                </div>

                <!-- Leaflet Map Canvas -->
                <div id="live_orphanage_map" class="live-map-canvas malaysia-map-height"></div>

                <!-- Nearest / Selected Orphanage Info Bar -->
                <div class="map-nearest-bar" id="nearest_orphanage_bar">
                    <div class="nearest-bar-icon">
                        <i class="fas fa-map-pin"></i>
                    </div>
                    <div class="nearest-bar-content">
                        <div class="nearest-bar-name" id="nearest_hub_name">Rumah Titian Kaseh</div>
                        <div class="nearest-bar-meta">
                            <span id="nearest_hub_dist"><i class="fas fa-map-marker-alt"></i> Persiaran Titiwangsa, Kuala Lumpur</span>
                            <span class="nearest-status-dot">•</span>
                            <span id="nearest_hub_status" style="color: #10b981; font-weight: 700;">Open for visits</span>
                        </div>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=Rumah+Titian+Kaseh+Titiwangsa+Kuala+Lumpur" id="nearest_hub_dir" target="_blank" class="nearest-dir-btn" title="Open in Google Maps">
                        <i class="fas fa-directions"></i> Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs & Care Pillars Section -->
    <div id="programs">
        <div class="section-title-wrap">
            <h1 class="section-header">programs</h1>
            <p class="section-subtitle">Holistic development pillars designed to nurture every child's physical, emotional, and academic potential.</p>
        </div>
        <div class="programs-grid">
            <div class="program-card">
                <div class="program-icon-box">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3>Nutritional & Medical Care</h3>
                <p>Three freshly cooked balanced meals daily, pediatric check-ups, regular dental visits, and clean, modern dormitories.</p>
            </div>
            <div class="program-card">
                <div class="program-icon-box">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Education & STEM Academy</h3>
                <p>Enrolling all children in accredited K-12 schools, providing evening tutoring, computer literacy labs, and higher education scholarships.</p>
            </div>
            <div class="program-card">
                <div class="program-icon-box">
                    <i class="fas fa-heart-pulse"></i>
                </div>
                <h3>Emotional & Mental Wellness</h3>
                <p>Licensed child psychologists and trauma counselors providing safe, empathetic support to help heal from early-life hardship.</p>
            </div>
            <div class="program-card">
                <div class="program-icon-box">
                    <i class="fas fa-palette"></i>
                </div>
                <h3>Arts, Sports & Recreation</h3>
                <p>Igniting creativity through painting, theater, choir singing, and competitive soccer to build self-esteem and lasting friendships.</p>
            </div>
            <div class="program-card">
                <div class="program-icon-box">
                    <i class="fas fa-tools"></i>
                </div>
                <h3>Life Skills & Independence</h3>
                <p>Practical workshops in personal finance, vocational trades, cooking, and career mentorship for teenagers transitioning into adulthood.</p>
            </div>
            <div class="program-card">
                <div class="program-icon-box">
                    <i class="fas fa-people-roof"></i>
                </div>
                <h3>Kinship & Foster Reintegration</h3>
                <p>Whenever safe and viable, our social workers work to reconnect children with loving extended relatives and screened foster families.</p>
            </div>
        </div>
    </div>

    <!-- Gallery / Daily Life Section matching template 2 #work -->
    <div id="work">
        <div class="section-title-wrap">
            <h1 class="section-header">gallery</h1>
            <p class="section-subtitle">Click any photograph to magnify and explore everyday life, studies, and smiles at our home.</p>
        </div>
        <div id="photos">
            <div class="gallery-item">
                <img onclick="magnify(this.src, 'Interactive STEM & Reading Classrooms')" src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80" alt="Education Classroom">
                <span class="gallery-badge">Education & STEM</span>
            </div>
            <div class="gallery-item">
                <img onclick="magnify(this.src, 'Creative Watercolor & Expression Workshop')" src="https://images.unsplash.com/photo-1596495578065-6e0763fa1178?auto=format&fit=crop&w=800&q=80" alt="Creative Arts">
                <span class="gallery-badge">Arts & Music</span>
            </div>
            <div class="gallery-item">
                <img onclick="magnify(this.src, 'Outdoor Athletics & Friendship')" src="https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=800&q=80" alt="Sports & Play">
                <span class="gallery-badge">Recreation & Sports</span>
            </div>
            <div class="gallery-item">
                <img onclick="magnify(this.src, 'Communal Study and Teamwork')" src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=800&q=80" alt="Group Study">
                <span class="gallery-badge">Collaborative Study</span>
            </div>
            <div class="gallery-item">
                <img onclick="magnify(this.src, 'Graduation Day & Milestone Achievements')" src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80" alt="Academic Graduation">
                <span class="gallery-badge">Graduation & Success</span>
            </div>
            <div class="gallery-item">
                <img onclick="magnify(this.src, 'Joyful Daily Life and Community Care')" src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=800&q=80" alt="Joyful Moments">
                <span class="gallery-badge">Family & Care</span>
            </div>
        </div>
    </div>

    <!-- About Us & Impact Story matching template 2 #bio -->
    <div id="bio">
        <div class="section-title-wrap">
            <h1 class="section-header">our story</h1>
            <p class="section-subtitle">Dedicated guardians creating a safe harbor of warmth, dignity, and endless possibilities.</p>
        </div>
        <div class="bio-content-grid">
            <div class="bio-text">
                <p>
                    HopeHaven was established over a decade ago with a simple yet profound premise: every child, regardless of birth circumstances or loss, deserves unconditional safety, nutritious meals, access to great education, and the joyful freedom of childhood.
                </p>
                <p>
                    Rather than a cold institutional setting, our campus is structured around small family-style cottages guided by trained house parents and certified child caregivers. We combine loving daily routines with specialized psychosocial therapy, medical care, and mentorship that carries our youths into university or rewarding careers.
                </p>
                <p>
                    All operations are monitored by an independent advisory board, ensuring complete financial transparency, rigorous child protection protocols, and compassionate excellence.
                </p>
            </div>
            <div class="bio-stats-grid">
                <div class="stat-box">
                    <div class="stat-number">148+</div>
                    <div class="stat-label">Children in Active Care</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">420+</div>
                    <div class="stat-label">Graduates & Alumni</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">98.4%</div>
                    <div class="stat-label">School Pass Rate</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Health & Dental Covered</div>
                </div>
            </div>
        </div>
    </div>



    <!-- Contact & Inquiry Section matching template 2 -->
    <div id="contact">
        <div class="section-title-wrap">
            <h1 class="section-header">contact</h1>
            <p class="section-subtitle">Send an inquiry, schedule a volunteer visit, or request child sponsorship details.</p>
        </div>
        <table>
            <tr>
                <td>
                    <div id="inner_div">
                        <table id="inner_table">
                            <tr>
                                <td>
                                    <i class="fas fa-phone"></i> &nbsp; +1 (800) 467-3428
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-at"></i> &nbsp; care@hopehaven-orphanage.org
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-clock"></i> &nbsp; Visiting: Mon - Sat (9:00 AM - 4:00 PM)
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div id="address">
                                        HopeHaven Sanctuary Campus,<br>
                                        742 Blossom Lane, Haven Valley,<br>
                                        California, USA
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a class="social" href="#contact"><i class="fab fa-facebook"></i></a>
                                    <a class="social" href="#contact"><i class="fab fa-twitter"></i></a>
                                    <a class="social" href="#contact"><i class="fab fa-instagram"></i></a>
                                    <a class="social" href="#contact"><i class="fab fa-youtube"></i></a>
                                    <a class="social" href="#contact"><i class="fab fa-linkedin-in"></i></a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td>
                    <form id="inquiry_form" action="#" method="POST">
                        @csrf
                        <input type="text" name="name" placeholder="your name" required>
                        <input type="email" name="email" placeholder="your email" required>
                        <input type="tel" name="phone" placeholder="phone number (optional)">
                        
                        <select name="inquiry_type" required>
                            <option value="" disabled selected>select inquiry type</option>
                            <option value="sponsor">Child Sponsorship</option>
                            <option value="volunteer">Volunteer Application</option>
                            <option value="donation">Material / Food Donation</option>
                            <option value="visit">Schedule a Campus Visit</option>
                            <option value="general">General Inquiry</option>
                        </select>

                        <textarea name="message" placeholder="your message or inquiry details" required rows="5"></textarea>
                        
                        <div class="form-submit-row">
                            <button class="btn_one" type="submit">send message</button>
                        </div>
                    </form>
                </td>
            </tr>
        </table>
    </div>



    <!-- Notification Toast Element -->
    <div id="toast_msg"></div>

    <!-- Footer matching template 2 -->
    <div id="footer">
        <div>
            HopeHaven Orphanage Management System &copy; {{ date('Y') }} • Dedicated with love to every child
        </div>
        <div class="footer-links">
            <a href="#top_part">Home</a>
            <a href="#programs">Programs</a>
            <a href="#bio">Our Story</a>
            <a href="#contact">Contact</a>
            @auth
                <a href="{{ route('dashboard') }}">Admin Dashboard</a>
            @else
                <a href="{{ route('dashboard') }}">Staff & Admin Portal</a>
            @endauth
        </div>
    </div>

    <!-- Template 2 Interaction JS -->
    <script src="{{ asset('js/template2.js') }}" type="text/javascript"></script>

    <!-- Leaflet.js Interactive Live Map Engine -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        $(document).ready(function() {
            // Real Malaysian Registered Orphanages & Welfare Homes Directory (Google Maps Data)
            var malaysiaSanctuaries = [
                {
                    id: 1,
                    name: "Rumah Titian Kaseh",
                    stateKey: "kl_selangor",
                    stateName: "Kuala Lumpur",
                    tag: "Registered Welfare Home (JKM)",
                    lat: 3.1788,
                    lng: 101.7022,
                    address: "No. 15, Jalan Persiaran Titiwangsa, Taman Tasik Titiwangsa, 53200 Kuala Lumpur",
                    phone: "+60 3-4024 6470",
                    children: 85,
                    visiting: "Tue - Sun: 9:00 AM - 5:00 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=Rumah+Titian+Kaseh+Titiwangsa+Kuala+Lumpur"
                },
                {
                    id: 2,
                    name: "Yayasan Sunbeams Home",
                    stateKey: "kl_selangor",
                    stateName: "Kuala Lumpur / Ampang",
                    tag: "Children Welfare Home",
                    lat: 3.1470,
                    lng: 101.7565,
                    address: "No. 54, Jalan Bunga Melur 16A, Taman Mawar, 56100 Kuala Lumpur",
                    phone: "+60 3-4295 4519",
                    children: 62,
                    visiting: "Mon - Sat: 9:00 AM - 5:00 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=Yayasan+Sunbeams+Home+Taman+Mawar+Kuala+Lumpur"
                },
                {
                    id: 3,
                    name: "Asrama Damai Kuang",
                    stateKey: "kl_selangor",
                    stateName: "Selangor",
                    tag: "Rumah Anak-Anak Yatim Damai Kuang",
                    lat: 3.2310,
                    lng: 101.5430,
                    address: "Lorong Teratai, Kampung Damai, 48050 Kuang, Rawang, Selangor",
                    phone: "+60 3-6037 4004",
                    children: 54,
                    visiting: "Daily: 9:00 AM - 5:30 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=Asrama+Damai+Kuang+Rawang+Selangor"
                },
                {
                    id: 4,
                    name: "Pusat Jagaan Kasih Murni",
                    stateKey: "kl_selangor",
                    stateName: "Kuala Lumpur / Keramat",
                    tag: "Pusat Kebajikan Anak Yatim",
                    lat: 3.2093,
                    lng: 101.6102,
                    address: "No. 1, Jalan AU 2A/13, Taman Keramat, 54200 Kuala Lumpur",
                    phone: "+60 3-4251 7712",
                    children: 45,
                    visiting: "Daily: 9:00 AM - 5:00 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=Pusat+Jagaan+Kasih+Murni+Taman+Keramat"
                },
                {
                    id: 5,
                    name: "The Salvation Army Penang Children's Home",
                    stateKey: "penang",
                    stateName: "Penang",
                    tag: "Registered Children's Home",
                    lat: 5.4168,
                    lng: 100.3168,
                    address: "8-A, Jalan Logan, 10400 George Town, Pulau Pinang",
                    phone: "+60 4-227 0920",
                    children: 38,
                    visiting: "Mon - Sat: 9:00 AM - 5:00 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=The+Salvation+Army+Penang+Children's+Home+Jalan+Logan"
                },
                {
                    id: 6,
                    name: "Pertubuhan Kebajikan Anak Yatim Johor (Rumah Barkat)",
                    stateKey: "johor",
                    stateName: "Johor",
                    tag: "State Registered Orphanage (PKAYIJ)",
                    lat: 1.4904,
                    lng: 103.7524,
                    address: "No. 118, Jalan Bukit Chagar, 80300 Johor Bahru, Johor",
                    phone: "+60 7-222 3445",
                    children: 76,
                    visiting: "Daily: 9:00 AM - 5:00 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=Rumah+Barkat+Bukit+Chagar+Johor+Bahru"
                },
                {
                    id: 7,
                    name: "The Salvation Army Ipoh Children's Home",
                    stateKey: "perak",
                    stateName: "Perak",
                    tag: "Rumah Kanak-Kanak Bala Keselamatan",
                    lat: 4.5820,
                    lng: 101.0985,
                    address: "255, Jalan Kampar, 30250 Ipoh, Perak",
                    phone: "+60 5-254 9767",
                    children: 42,
                    visiting: "Mon - Fri: 9:00 AM - 4:30 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=The+Salvation+Army+Ipoh+Children's+Home+Jalan+Kampar"
                },
                {
                    id: 8,
                    name: "Kompleks Anak Yatim Fatimah Al-Zahrah (KAYFAZ)",
                    stateKey: "melaka",
                    stateName: "Melaka",
                    tag: "Charity & Welfare Complex",
                    lat: 2.2272,
                    lng: 102.3025,
                    address: "Lot 1120, Jalan Tebing Tinggi, 75450 Bukit Katil, Melaka",
                    phone: "+60 6-232 5520",
                    children: 50,
                    visiting: "Daily: 9:00 AM - 5:00 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=Kompleks+Anak+Yatim+Fatimah+Al-Zahrah+Bukit+Katil+Melaka"
                },
                {
                    id: 9,
                    name: "Rumah Anak Yatim & Asnaf As-Sakinah",
                    stateKey: "borneo",
                    stateName: "Sabah",
                    tag: "Registered Orphanage Home",
                    lat: 5.8352,
                    lng: 116.0418,
                    address: "No. 503, Jalan Pandan, Taman Pantai Lok Kawi, 89600 Kinarut / Kota Kinabalu, Sabah",
                    phone: "+60 88-751 220",
                    children: 48,
                    visiting: "Daily: 9:00 AM - 5:00 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=Rumah+Anak+Yatim+As-Sakinah+Lok+Kawi+Sabah"
                },
                {
                    id: 10,
                    name: "Kompleks Kebajikan Laila Taib (PERYATIM)",
                    stateKey: "borneo",
                    stateName: "Sarawak",
                    tag: "Sarawak State Children's Home",
                    lat: 1.6110,
                    lng: 110.3520,
                    address: "Lot 459, Block 14, Jalan Siol Kanan, Petra Jaya, 93050 Kuching, Sarawak",
                    phone: "+60 82-440 052",
                    children: 68,
                    visiting: "Daily: 8:30 AM - 4:30 PM",
                    googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=Kompleks+Kebajikan+Laila+Taib+Kuching+Sarawak"
                }
            ];

            // Initialize Map Element
            var mapElement = document.getElementById('live_orphanage_map');
            if (!mapElement) return;

            // Center on Malaysia (Peninsular overview by default)
            var defaultCenter = [3.8, 102.0];
            var map = L.map('live_orphanage_map', {
                center: defaultCenter,
                zoom: 7,
                zoomControl: true,
                attributionControl: false
            });

            // OpenStreetMap tile layer (clean & fast)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Custom Malaysia Sanctuary Map Marker Pin
            var sanctuaryIcon = L.divIcon({
                className: 'custom-sanctuary-pin-wrapper',
                html: '<div class="custom-sanctuary-pin"><i class="fas fa-home"></i></div>',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -30]
            });

            var userMarkerIcon = L.divIcon({
                className: 'custom-user-pin-wrapper',
                html: '<div class="custom-user-pin"></div>',
                iconSize: [22, 22],
                iconAnchor: [11, 11]
            });

            var markersGroup = L.featureGroup().addTo(map);
            var markerMap = {};
            var userMarker = null;
            var routeLine = null;

            // Plot sanctuary markers (supports state filter)
            function renderSanctuaryMarkers(stateFilter) {
                markersGroup.clearLayers();
                markerMap = {};

                var filtered = (stateFilter && stateFilter !== 'all')
                    ? malaysiaSanctuaries.filter(function(s) { return s.stateKey === stateFilter; })
                    : malaysiaSanctuaries;

                filtered.forEach(function(s) {
                    var popupHtml = '<div class="popup-sanctuary-card">' +
                        '<span class="popup-tag">' + s.tag + ' • ' + s.stateName + '</span>' +
                        '<div class="popup-title">' + s.name + '</div>' +
                        '<div class="popup-desc"><i class="fas fa-map-marker-alt" style="color:var(--primary);"></i> ' + s.address + '<br>' +
                        '<i class="fas fa-phone-alt" style="color:#3b82f6;"></i> ' + s.phone + '<br>' +
                        '<i class="fas fa-users" style="color:#10b981;"></i> <strong>' + s.children + ' children</strong> supported<br>' +
                        '<i class="fas fa-clock" style="color:#8b5cf6;"></i> ' + s.visiting + '</div>' +
                        '<div style="display:flex; gap:6px; margin-top:8px;">' +
                        '<a href="' + s.googleMapsUrl + '" target="_blank" class="popup-btn" style="flex:1; text-align:center;">' +
                        '<i class="fas fa-external-link-alt"></i> Google Maps</a>' +
                        '<a href="https://www.google.com/maps/dir/?api=1&destination=' + s.lat + ',' + s.lng + '" target="_blank" class="popup-btn" style="flex:1; text-align:center; background:#10b981;">' +
                        '<i class="fas fa-directions"></i> Route</a>' +
                        '</div>' +
                        '</div>';

                    var marker = L.marker([s.lat, s.lng], { icon: sanctuaryIcon })
                        .bindPopup(popupHtml)
                        .on('click', function() {
                            selectOrphanage(s);
                        });

                    markersGroup.addLayer(marker);
                    markerMap[s.id] = marker;
                });
            }

            renderSanctuaryMarkers('all');

            // Set initial selected hub to Rumah Titian Kaseh (Kuala Lumpur)
            selectOrphanage(malaysiaSanctuaries[0], "Persiaran Titiwangsa, Kuala Lumpur");

            function selectOrphanage(s, customDist) {
                $('#nearest_hub_name').text(s.name);
                if (customDist) {
                    $('#nearest_hub_dist').html('<i class="fas fa-map-marker-alt"></i> ' + customDist);
                } else {
                    $('#nearest_hub_dist').html('<i class="fas fa-map-marker-alt"></i> ' + s.address);
                }
                $('#nearest_hub_dir').attr('href', s.googleMapsUrl || ('https://www.google.com/maps/dir/?api=1&destination=' + s.lat + ',' + s.lng));
            }

            // Quick State Filter Tabs
            $('.state-tab').on('click', function() {
                var btn = $(this);
                $('.state-tab').removeClass('active');
                btn.addClass('active');

                var state = btn.data('state');
                renderSanctuaryMarkers(state);

                if (state === 'all') {
                    map.setView([3.8, 102.0], 7);
                    selectOrphanage(malaysiaSanctuaries[0], "All Real Registered Orphanages in Malaysia");
                } else if (state === 'kl_selangor') {
                    map.setView([3.1788, 101.68], 11);
                    selectOrphanage(malaysiaSanctuaries[0]);
                    if (markerMap[1]) markerMap[1].openPopup();
                } else if (state === 'penang') {
                    map.setView([5.4168, 100.3168], 13);
                    selectOrphanage(malaysiaSanctuaries[4]);
                    if (markerMap[5]) markerMap[5].openPopup();
                } else if (state === 'johor') {
                    map.setView([1.4904, 103.7524], 13);
                    selectOrphanage(malaysiaSanctuaries[5]);
                    if (markerMap[6]) markerMap[6].openPopup();
                } else if (state === 'perak') {
                    map.setView([4.5820, 101.0985], 13);
                    selectOrphanage(malaysiaSanctuaries[6]);
                    if (markerMap[7]) markerMap[7].openPopup();
                } else if (state === 'melaka') {
                    map.setView([2.2272, 102.3025], 13);
                    selectOrphanage(malaysiaSanctuaries[7]);
                    if (markerMap[8]) markerMap[8].openPopup();
                } else if (state === 'borneo') {
                    map.setView([4.0, 114.0], 7);
                    selectOrphanage(malaysiaSanctuaries[8]);
                    if (markerMap[9]) markerMap[9].openPopup();
                }
            });

            // Haversine distance formula in kilometers
            function calculateDistanceKm(lat1, lon1, lat2, lon2) {
                var R = 6371; // Earth radius in km
                var dLat = (lat2 - lat1) * Math.PI / 180;
                var dLon = (lon2 - lon1) * Math.PI / 180;
                var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon/2) * Math.sin(dLon/2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }

            // Live GPS "Near Me" detection
            $('#btn_locate_user').on('click', function() {
                var btn = $(this);
                var origText = $('#locate_btn_text').text();
                btn.prop('disabled', true);
                $('#locate_btn_text').html('<i class="fas fa-spinner fa-spin"></i> Locating...');

                if (!navigator.geolocation) {
                    btn.prop('disabled', false);
                    $('#locate_btn_text').text(origText);
                    showToastMsg("Geolocation is not supported by your browser.");
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        var userLat = position.coords.latitude;
                        var userLng = position.coords.longitude;

                        // Check distance to closest sanctuary in Malaysia
                        var nearest = null;
                        var minDistance = Infinity;

                        malaysiaSanctuaries.forEach(function(s) {
                            var d = calculateDistanceKm(userLat, userLng, s.lat, s.lng);
                            if (d < minDistance) {
                                minDistance = d;
                                nearest = s;
                            }
                        });

                        // Add or move user marker
                        if (userMarker) {
                            userMarker.setLatLng([userLat, userLng]);
                        } else {
                            userMarker = L.marker([userLat, userLng], { icon: userMarkerIcon })
                                .bindPopup('<strong><i class="fas fa-user-circle"></i> You Are Here</strong><br><span style="font-size:11px; color:#64748b;">Live GPS Location</span>')
                                .addTo(map);
                        }

                        // Draw dashed route line to nearest Malaysian sanctuary
                        if (routeLine) {
                            map.removeLayer(routeLine);
                        }
                        routeLine = L.polyline([[userLat, userLng], [nearest.lat, nearest.lng]], {
                            color: '#ff2c5a',
                            weight: 3,
                            dashArray: '6, 8',
                            opacity: 0.85
                        }).addTo(map);

                        // Fit map bounds to show both user and nearest sanctuary
                        var bounds = L.latLngBounds([
                            [userLat, userLng],
                            [nearest.lat, nearest.lng]
                        ]);
                        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 14 });

                        // Update UI info bar
                        var distFormatted = minDistance < 1 
                            ? Math.round(minDistance * 1000) + ' meters away' 
                            : minDistance.toFixed(1) + ' km away';
                            
                        selectOrphanage(nearest, distFormatted);
                        if (markerMap[nearest.id]) {
                            markerMap[nearest.id].openPopup();
                        }

                        btn.prop('disabled', false);
                        $('#locate_btn_text').text('Near Me');
                        btn.css({ 'background': '#10b981', 'color': '#ffffff', 'border-color': '#10b981' });

                        showToastMsg("Nearest Orphanage: " + nearest.name + " (" + distFormatted + ")");
                    },
                    function(error) {
                        btn.prop('disabled', false);
                        $('#locate_btn_text').text(origText);
                        
                        // Fallback gracefully on permission denial
                        var fallback = malaysiaSanctuaries[0];
                        selectOrphanage(fallback, "Rumah Titian Kaseh (Titiwangsa, Kuala Lumpur)");
                        map.setView([fallback.lat, fallback.lng], 13);
                        if (markerMap[fallback.id]) markerMap[fallback.id].openPopup();
                        showToastMsg("Displaying Rumah Titian Kaseh (Kuala Lumpur).");
                    },
                    { timeout: 10000, enableHighAccuracy: true }
                );
            });

            // Re-invalidate map size when hero animation completes and on window resize
            setTimeout(function() {
                map.invalidateSize();
            }, 600);

            $(window).on('resize', function() {
                map.invalidateSize();
            });
        });
    </script>
</body>
</html>