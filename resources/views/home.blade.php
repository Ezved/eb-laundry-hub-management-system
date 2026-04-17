<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E&B Laundry Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/E&B_Logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="navbar" id="navbar">
    <div class="wrap">
        <a href="{{ route('home') }}" class="nav-logo">
            <div class="logo-bubble">
                <img src="{{ asset('images/E&B_Logo.png') }}" alt="E&B Logo">
            </div>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>

        <div class="nav-menu" id="navMenu">
            <ul>
                <li><a href="#top">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <a href="{{ route('login') }}" class="btn-login">Login</a>
        </div>
    </div>
</nav>

{{-- ===== HERO ===== --}}
<section class="hero" id="top">
    <div class="hero-glow hero-glow-1"></div>
    <div class="hero-glow hero-glow-z2"></div>
    <div class="hero-glow hero-glow-3"></div>

    <div class="wrap">
        <div class="hero-content">
            <div class="hero-eyebrow">
                <span class="pulse-dot"></span>
                Davao City's Trusted Laundry
            </div>
            <h1 class="hero-title">
                We Care For<br>
                The Clothes<br>
                <em>You Wear</em>
            </h1>
            <p class="hero-desc">
                Laundry care made easy with quick turnaround, eco-friendly detergents, and convenient pickup & delivery — proudly serving Maa, Davao City.
            </p>
            <div class="hero-actions">
                <a href="{{ route('login') }}" class="btn-primary">Book a Pickup</a>
                <a href="#services" class="btn-ghost">See Services</a>
            </div>
        </div>

        <div class="hero-photo">
            <img src="{{ asset('images/E&B_Pic2.jpg') }}" alt="E&B Laundry Hub storefront in Maa, Davao City">
        </div>
    </div>
</section>

{{-- ===== STATS STRIP ===== --}}
<div class="stats-strip">
    <div class="wrap">
        <div class="sstat"><span class="sstat-n">100+</span><span class="sstat-l">Happy Customers</span></div>
        <div class="sstat"><span class="sstat-n">10k+</span><span class="sstat-l">Loads Washed</span></div>
        <div class="sstat"><span class="sstat-n">5★</span><span class="sstat-l">Average Rating</span></div>
        <div class="sstat"><span class="sstat-n">2h</span><span class="sstat-l">Avg Turnaround</span></div>
    </div>
</div>

{{-- ===== WHY CHOOSE US ===== --}}
<section class="why-section">
    <div class="wrap">
        <div class="why-left">
            <div class="section-tag">Why Choose Us</div>
            <h2 class="section-h">Laundry Done Right,<br>Every Time</h2>
            <p class="section-sub">From basic loads to premium hand-care treatments — we handle everything so you don't have to.</p>
            <div class="why-cards">
                <div class="why-card">
                    <div class="why-icon pink">🚚</div>
                    <h3>Pickup & Delivery</h3>
                    <p>Schedule your pickup online and we'll come right to your door.</p>
                </div>
                <div class="why-card">
                    <div class="why-icon navy">🌿</div>
                    <h3>Eco-Friendly</h3>
                    <p>Gentle detergents that keep colors vivid and fabrics soft.</p>
                </div>
                <div class="why-card">
                    <div class="why-icon pink">⚡</div>
                    <h3>Fast Turnaround</h3>
                    <p>Most orders ready within the same day or next morning.</p>
                </div>
                <div class="why-card">
                    <div class="why-icon navy">🎖️</div>
                    <h3>Loyalty Rewards</h3>
                    <p>Earn stamps and redeem for a free wash after every 10.</p>
                </div>
            </div>
        </div>
        <div class="why-panel">
            <div class="wp-top">
                <div class="wp-eyebrow">Loyalty Program</div>
                <div class="wp-big">10<span class="wp-arrow">→</span>1</div>
                <div class="wp-free">Free Wash</div>
                <p class="wp-desc">Collect 10 stamps — one per order — and redeem for a free full-service load. Our way of saying thank you.</p>
            </div>
            <div class="wp-bottom">
                <div class="wp-stamp-label">Your stamps</div>
                <div class="wp-stamps">
                    <div class="wp-stamp filled">✓</div>
                    <div class="wp-stamp filled">✓</div>
                    <div class="wp-stamp filled">✓</div>
                    <div class="wp-stamp filled">✓</div>
                    <div class="wp-stamp filled">✓</div>
                    <div class="wp-stamp filled">✓</div>
                    <div class="wp-stamp"></div>
                    <div class="wp-stamp"></div>
                    <div class="wp-stamp"></div>
                    <div class="wp-stamp"></div>
                </div>
                <div class="wp-progress-label">6 of 10 — 4 more to go!</div>
            </div>
        </div>
    </div>
</section>

{{-- ===== SERVICES ===== --}}
<section class="services-section" id="services">
    <div class="wrap">
        <div class="services-header">
            <div>
                <div class="section-tag">Our Services</div>
                <h2 class="section-h">Your Time Matters,<br>Let Us Handle The Load</h2>
            </div>
            <p class="section-sub services-sub">Affordable packages for every budget, from daily basics to delicate premium care.</p>
        </div>

        <div class="services-grid">
            @foreach($services as $i => $svc)
                @php
                    $variants = ['light', 'dark', 'pink'];
                    $variant = $variants[$i % 3];
                    $badges = ['Basic', 'Popular', 'Premium', 'Add-on', 'Bulk', 'P&D'];
                    $badge = $badges[$i] ?? 'Service';
                @endphp
                <div class="svc-card svc-{{ $variant }}">
                    <div class="svc-badge badge-{{ $variant }}">{{ $badge }}</div>
                    <h3>{{ $svc->title }}</h3>
                    <p style="white-space:pre-wrap">{{ $svc->description }}</p>
                    <div class="svc-price">
                        <sup>₱</sup>{{ number_format($svc->price, 0) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== PICKUP & DELIVERY ===== --}}
<section class="pickup-section">
    <div class="wrap">
        <div class="pickup-left">
            <div class="section-tag light-tag">Pickup & Delivery</div>
            <h2 class="section-h white-h">We Come To You</h2>
            <p class="section-sub muted-sub">Schedule a pickup from your doorstep and get fresh, folded laundry delivered back — zero hassle.</p>
            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-text">
                        <h4>Schedule Online</h4>
                        <p>Book via our Facebook page or call/message us to set your preferred pickup time.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-text">
                        <h4>We Collect</h4>
                        <p>Our rider arrives at your doorstep to collect your laundry bag — no sorting needed.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-text">
                        <h4>We Wash & Pack</h4>
                        <p>Your clothes are washed, dried, neatly folded, and sealed in a clean bag.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <div class="step-text">
                        <h4>Delivered Fresh</h4>
                        <p>We drop your clean laundry right back at your door — ready to wear.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-card">
            <div class="oc-label">📦 Current Order</div>
            <div class="oc-rows">
                <div class="oc-row"><span class="oc-key">Order #</span><span class="oc-val">EB-2025-0412</span></div>
                <div class="oc-row"><span class="oc-key">Service</span><span class="oc-val">Full-Service Wash</span></div>
                <div class="oc-row"><span class="oc-key">Pickup</span><span class="oc-val">Today, 2:00 PM</span></div>
                <div class="oc-row"><span class="oc-key">Delivery</span><span class="oc-val">Tomorrow, 9:00 AM</span></div>
                <div class="oc-row"><span class="oc-key">Status</span><span class="oc-status">In Progress</span></div>
            </div>
            <div class="oc-progress-label">Progress</div>
            <div class="oc-bar"><div class="oc-fill"></div></div>
            <div class="oc-steps-row">
                <span>Picked Up</span><span>Washing</span><span>Ready</span><span>Delivered</span>
            </div>
        </div>
    </div>
</section>

{{-- ===== TESTIMONIALS ===== --}}
<section class="testimonials-section" id="feedback">
    <div class="wrap">
        <div class="section-tag center-tag">What Customers Say</div>
        <h2 class="section-h center-h">Real Words From Real<br>Happy Customers</h2>

        <div class="reviews-carousel" id="reviewsCarousel">
            @php $has = isset($feedbacks) && count($feedbacks) > 0; @endphp

            @if($has)
                @foreach($feedbacks->take(3) as $i => $fb)
                    <div class="review-card {{ $i === 1 ? 'review-featured' : '' }}">
                        @if($i === 1)
                            <div class="featured-badge">Top Review</div>
                        @endif
                        <div class="review-stars">
                            @for($s=1;$s<=5;$s++){{ $s <= (int)$fb->rating ? '★' : '☆' }}@endfor
                        </div>
                        <p class="review-text">"{{ $fb->message }}"</p>
                        <div class="reviewer">
                            <div class="rev-avatar">{{ strtoupper(substr($fb->name ?? 'C', 0, 1)) }}</div>
                            <div>
                                <div class="rev-name">{{ $fb->name ?? ($fb->user->name ?? 'Customer') }}</div>
                                <div class="rev-date">{{ $fb->created_at->timezone(config('app.timezone'))->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="review-card">
                    <div class="review-stars">★★★★★</div>
                    <p class="review-text">"Super bilis ng delivery and ang bango ng damit ko! Best laundry sa Maa!"</p>
                    <div class="reviewer">
                        <div class="rev-avatar">M</div>
                        <div><div class="rev-name">Maria Santos</div><div class="rev-date">Apr 2025</div></div>
                    </div>
                </div>
                <div class="review-card review-featured">
                    <div class="featured-badge">Top Review</div>
                    <div class="review-stars">★★★★★</div>
                    <p class="review-text">"Sobrang ganda ng serbisyo! Yung loyalty card nila ay napaka-sulit. 10 stamps = 1 free wash!"</p>
                    <div class="reviewer">
                        <div class="rev-avatar">J</div>
                        <div><div class="rev-name">Juan dela Cruz</div><div class="rev-date">Mar 2025</div></div>
                    </div>
                </div>
                <div class="review-card">
                    <div class="review-stars">★★★★★</div>
                    <p class="review-text">"Inentrust ko yung delicate na damit ko at hindi ako nabigo. Very careful and presentable ang pagkakatupi."</p>
                    <div class="reviewer">
                        <div class="rev-avatar">A</div>
                        <div><div class="rev-name">Ana Reyes</div><div class="rev-date">Feb 2025</div></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ===== CONTACT / FOOTER ===== --}}
<section class="contact-section" id="contact">
    <div class="wrap">
        <div class="contact-left">
            <div class="section-tag light-tag">Contact Us</div>
            <h2 class="section-h white-h">Got Something<br>To Say?</h2>
            <div class="hours-pill">
                <span class="hours-dot"></span>
                Open Daily · 8 AM – 8 PM
            </div>
            <div class="contact-list">
                <div class="cinfo-row">
                    <div class="cinfo-icon">f</div>
                    <div>
                        <div class="cinfo-label">Facebook</div>
                        <div class="cinfo-val">
                            <a href="https://facebook.com/eblaundryhub" target="_blank">E&amp;B Laundry Hub</a>
                        </div>
                    </div>
                </div>
                <div class="cinfo-row">
                    <div class="cinfo-icon">☎</div>
                    <div>
                        <div class="cinfo-label">Phone</div>
                        <div class="cinfo-val">
                            <a href="tel:+639301902025">+63 930 190 2025</a>
                        </div>
                    </div>
                </div>
                <div class="cinfo-row">
                    <div class="cinfo-icon">✉</div>
                    <div>
                        <div class="cinfo-label">Email</div>
                        <div class="cinfo-val">
                            <a href="mailto:eblaundry@gmail.com">eblaundry@gmail.com</a>
                        </div>
                    </div>
                </div>
                <div class="cinfo-row">
                    <div class="cinfo-icon">📍</div>
                    <div>
                        <div class="cinfo-label">Address</div>
                        <div class="cinfo-val">Unit 2 Grace Bldg, Eden St., Maa, Davao City</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-right">
            <div class="map-wrap">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.366312735012!2d125.58520687406723!3d7.083463316404884!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f96d4265616cd7%3A0x9286c09070af166d!2sE%20%26%20B%20Laundry%20Hub!5e0!3m2!1sen!2sph!4v1763411573454!5m2!1sen!2sph"
                    allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="contact-btns">
                <a href="tel:+639301902025" class="btn-primary">📞 Call Us Now</a>
                <a href="https://facebook.com/eblaundryhub" target="_blank" class="btn-ghost-dark">💬 Message Us</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p class="footer-copy">© 2025 Senajon &amp; Crabajales. All rights reserved.</p>
        <p class="footer-brand">E&amp;B Laundry Hub</p>
    </div>
</section>

<script>
(function () {
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 60);
    });

    const toggle = document.getElementById('navToggle');
    const menu   = document.getElementById('navMenu');
    toggle.addEventListener('click', () => {
        toggle.classList.toggle('open');
        menu.classList.toggle('menu-open');
    });

    menu.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => {
            toggle.classList.remove('open');
            menu.classList.remove('menu-open');
        });
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll(
        '.why-card, .svc-card, .review-card, .step, .sstat'
    ).forEach(el => {
        el.classList.add('fade-in');
        observer.observe(el);
    });

    /* Drag-to-scroll for reviews carousel */
    const carousel = document.getElementById('reviewsCarousel');
    if (carousel) {
        let isDown = false, startX, scrollLeft;
        carousel.addEventListener('mousedown', e => {
            isDown = true;
            startX = e.pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
        });
        carousel.addEventListener('mouseleave', () => isDown = false);
        carousel.addEventListener('mouseup',    () => isDown = false);
        carousel.addEventListener('mousemove', e => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - carousel.offsetLeft;
            carousel.scrollLeft = scrollLeft - (x - startX) * 1.2;
        });
    }
})();
</script>

</body>
</html>