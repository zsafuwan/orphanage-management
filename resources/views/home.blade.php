<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/malaysia-orphanages.js') }}?v={{ substr(hash_file('sha256', public_path('js/malaysia-orphanages.js')), 0, 12) }}" defer></script>
    @env('local')
        <meta name="development-revision" content="{{ route('development.revision') }}">
    @endenv
    <title>HopeHaven | Orphanage & Child Care</title>
    <meta name="description" content="HopeHaven Orphanage Management System - Providing shelter, education, healthcare, and loving guidance to vulnerable children.">
    
    <link rel="stylesheet" href="{{ asset('css/design-utilities.css') }}?v={{ substr(hash_file('sha256', public_path('css/design-utilities.css')), 0, 12) }}">
    <!-- Animate.css for signature template 2 entrance animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    
    <!-- Template 2 Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/template2.css') }}?v={{ substr(hash_file('sha256', public_path('css/template2.css')), 0, 12) }}">
    
    <!-- jQuery for template 2 smooth scroll and dynamic interactions -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/care-home.css') }}?v={{ substr(hash_file('sha256', public_path('css/care-home.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/hopehaven-studio.css') }}?v={{ substr(hash_file('sha256', public_path('css/hopehaven-studio.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/floating-cards.css') }}?v={{ substr(hash_file('sha256', public_path('css/floating-cards.css')), 0, 12) }}">
</head>
<body class="care-home" data-page="/">
    <!-- Preloader Spinner matching template 2 -->
    <div id="loading">
        <div id="spinner"></div>
        <div id="loading-text">HOPEHAVEN ORPHANAGE</div>
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
                        <span class="logo-sub">Orphanage & Child Care</span>
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
                <i class="fas fa-hand-holding-heart"></i> Good humans. Real impact.
            </span>
            <h1>Small acts.<br><span>Brighter futures.</span></h1>
            <p class="hero-description">
                A safe home. A chance to learn. Someone who cares. Together, we help children build the future they deserve.
            </p>
            <div class="hero-btn-group">
                <a href="#programs" class="btn_one">Explore our impact <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                <a href="#contact" class="btn_two">Be part of the good <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
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
            <span class="studio-photo-sticker" aria-hidden="true">A little care.<br>A lot of possibility. <i class="fas fa-asterisk"></i></span>
            <div class="hero-overlay-card">
                <h3>148 Children in Active Care</h3>
                <p>100% of public donations directly fund child healthcare, nutrition, and schooling programs with clear records of how funds are used.</p>
            </div>
        </div>
    </div>
    <section id="rightMap" aria-labelledby="care-locations-heading">
            <div class="care-locations-intro">
                <span class="hero-tag"><i class="fas fa-location-dot" aria-hidden="true"></i> A COMMUNITY OF CARE</span>
                <h2 id="care-locations-heading">Find a place.<br>Make a difference.</h2>
                <p>Care starts close to home. Explore sanctuaries around Malaysia and discover where your time and kindness can help.</p>
            </div>
            <div class="malaysia-map-card">
                <div class="map-card-header">
                    <div class="map-title-wrap">
                        <span class="map-live-tag"><span class="pulse-glow"></span> EXPLORE GOOGLE MAPS RESULTS</span>
                        <h4 class="map-card-heading"><i class="fas fa-map-marked-alt"></i> Orphanages Around Malaysia</h4>
                    </div>
                    <button type="button" id="btn_locate_user" class="btn-locate-gps" title="Find nearest orphanage to your location">
                        <i class="fas fa-crosshairs"></i> <span id="locate_btn_text">Near Me</span>
                    </button>
                </div>

                <!-- Nationwide map reset -->
                <div class="malaysia-state-tabs" id="state_filter_tabs">
                    <button type="button" class="state-tab active" data-region="Malaysia" aria-pressed="true">All Malaysia</button>
                </div>

                <!-- Google Places map; search embed remains a clearly labelled fallback. -->
                @if(config('services.google_maps.browser_key'))
                    <div id="malaysia_places_map" class="live-map-canvas malaysia-map-height" data-api-key="{{ config('services.google_maps.browser_key') }}" style="width:100%;" aria-label="Orphanage locations across Malaysia"></div>
                @endif
                <iframe id="live_orphanage_map" @if(config('services.google_maps.browser_key')) hidden @endif class="live-map-canvas malaysia-map-height" title="Google Maps orphanage search in Malaysia" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen style="width:100%;border:0;" src="https://www.google.com/maps?q=orphanages%20in%20Malaysia&amp;output=embed&amp;ll=4.2,109.5&amp;z=5"></iframe>
                <p id="orphanage_map_status" role="status" style="font-size:12px;color:#65765d;margin:12px 0;">Google search preview only. Nationwide location loading is not connected yet.</p>

                <!-- Nearest / Selected Orphanage Info Bar -->
                <div class="map-nearest-bar" id="nearest_orphanage_bar">
                    <div class="nearest-bar-icon">
                        <i class="fas fa-map-pin"></i>
                    </div>
                    <div class="nearest-bar-content">
                        <div class="nearest-bar-name" id="nearest_hub_name">Orphanages in Malaysia</div>
                        <div class="nearest-bar-meta">
                            <span id="nearest_hub_dist"><i class="fas fa-map-marker-alt"></i> Search all Malaysia</span>
                            <span class="nearest-status-dot">•</span>
                            <span id="nearest_hub_status" style="color: #10b981; font-weight: 700;">Check details with each home</span>
                        </div>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&amp;query=orphanages+in+Malaysia" id="nearest_hub_dir" target="_blank" class="nearest-dir-btn" title="Open in Google Maps">
                        <i class="fas fa-directions"></i> Google Maps
                    </a>
                </div>
            </div>
    </section>

    <div class="studio-values" aria-label="Our care priorities"><span>Care that shows up.</span><span><i class="fas fa-asterisk" aria-hidden="true"></i> A place to belong</span><span><i class="fas fa-asterisk" aria-hidden="true"></i> Room to grow</span><span><i class="fas fa-asterisk" aria-hidden="true"></i> People who care</span></div>
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
                    All operations are monitored by an independent advisory board, ensuring clear records of how funds are used, rigorous child protection protocols, and compassionate excellence.
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
                                        HopeHaven Orphanage Campus,<br>
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
    <script src="{{ asset('js/template2.js') }}?v={{ substr(hash_file('sha256', public_path('js/template2.js')), 0, 12) }}" type="text/javascript"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('malaysia_places_map')) return;
        const frame = document.getElementById('live_orphanage_map');
        const status = document.getElementById('orphanage_map_status');
        const embedKey = @json(config('services.google_maps.embed_key'));
        function search(query, label, center) {
            const url = embedKey
                ? new URL('https://www.google.com/maps/embed/v1/search')
                : new URL('https://www.google.com/maps');
            url.searchParams.set('q', query);
            if (embedKey) {
                url.searchParams.set('key', embedKey);
                url.searchParams.set('region', 'MY');
                url.searchParams.set('center', center || '4.2,109.5');
                url.searchParams.set('zoom', center ? '12' : '5');
            } else {
                url.searchParams.set('output', 'embed');
                url.searchParams.set('ll', center || '4.2,109.5');
                url.searchParams.set('z', center ? '12' : '5');
            }
            frame.src = url.toString();
            frame.title = 'Google Maps: ' + label;
            document.getElementById('nearest_hub_name').textContent = label;
            document.getElementById('nearest_hub_dist').textContent = 'Explore listings, contact details and directions';
            document.getElementById('nearest_hub_dir').href = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(query);
        }
        document.querySelectorAll('#state_filter_tabs .state-tab').forEach(function (button) {
            button.addEventListener('click', function () {
                document.querySelectorAll('#state_filter_tabs .state-tab').forEach(function (tab) {
                    tab.classList.toggle('active', tab === button);
                    tab.setAttribute('aria-pressed', String(tab === button));
                });
                const region = button.dataset.region;
                search('orphanages in ' + region + (region === 'Malaysia' ? '' : ', Malaysia'), 'Orphanages in ' + region);
                status.textContent = 'Google Maps results for ' + region + '. Zoom and move the map to explore; listings are not an exhaustive registry.';
            });
        });
        document.getElementById('btn_locate_user').addEventListener('click', function () {
            const button = this;
            if (!navigator.geolocation) { status.textContent = 'Location is unavailable. Use All Malaysia instead.'; return; }
            button.disabled = true;
            document.getElementById('locate_btn_text').textContent = 'Locating…';
            navigator.geolocation.getCurrentPosition(function (position) {
                const coordinates = position.coords.latitude + ',' + position.coords.longitude;
                search('orphanages near ' + coordinates + ' Malaysia', 'Orphanages near your location', coordinates);
                document.querySelectorAll('#state_filter_tabs .state-tab').forEach(function (tab) { tab.classList.remove('active'); tab.setAttribute('aria-pressed', 'false'); });
                status.textContent = 'Showing Google Maps results near your location. Confirm visiting arrangements directly with the home.';
                finish();
            }, function () { status.textContent = 'Could not access your location. Use All Malaysia instead.'; finish(); }, {timeout: 10000});
            function finish() { button.disabled = false; document.getElementById('locate_btn_text').textContent = 'Near Me'; }
        });
        search('orphanages in Malaysia', 'Orphanages in Malaysia');
    });
    </script>
    @env('local')
        <script src="{{ asset('js/development-refresh.js') }}?v={{ substr(hash_file('sha256', public_path('js/development-refresh.js')), 0, 12) }}"></script>
    @endenv
    <script src="{{ asset('js/hopehaven-studio.js') }}?v={{ substr(hash_file('sha256', public_path('js/hopehaven-studio.js')), 0, 12) }}" defer></script>
</body>
</html>
