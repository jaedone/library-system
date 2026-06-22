@extends('common.main')

@section('title', 'Services | PUP Library')

@section('content')

<section class="services-page services-roulette-only-page">

    <section class="services-roulette-section services-roulette-full" data-service-roulette>
        <div class="services-roulette-header">
            <span class="services-eyebrow">PUP Library Services</span>

            <h1>Choose a library service</h1>

            <p>
                Browse available services using the arrow buttons or your keyboard arrow keys.
                The selected service appears larger at the center.
            </p>
        </div>

        <div class="services-roulette-stage">
            <button
                type="button"
                class="roulette-nav-btn roulette-prev"
                data-service-prev
                aria-label="Previous service"
            >
                <i class="bi bi-chevron-left"></i>
            </button>

            <div class="services-roulette-track">

                <a href="{{ route('services.show', 'book-borrowing') }}" class="service-roulette-card" data-service-card>
                    <div class="service-roulette-icon">
                        <i class="bi bi-book"></i>
                    </div>

                    <div class="service-roulette-content">
                        <span>Library Service</span>
                        <h3>Book Borrowing</h3>
                        <p>
                            Borrow eligible library materials for academic, research, and personal learning purposes.
                        </p>
                    </div>

                    <div class="service-roulette-action">
                        Browse Catalog
                    </div>
                </a>

                <a href="{{ route('services.show', 'book-reservation') }}" class="service-roulette-card" data-service-card>
                    <div class="service-roulette-icon">
                        <i class="bi bi-bookmark-check"></i>
                    </div>

                    <div class="service-roulette-content">
                        <span>Library Service</span>
                        <h3>Book Reservation</h3>
                        <p>
                            Reserve available books online before visiting the library.
                        </p>
                    </div>

                    <div class="service-roulette-action">
                        Reserve Book
                    </div>
                </a>

                <a href="{{ route('services.show', 'book-renewal') }}" class="service-roulette-card" data-service-card>
                    <div class="service-roulette-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>

                    <div class="service-roulette-content">
                        <span>Library Service</span>
                        <h3>Book Renewal</h3>
                        <p>
                            Extend the borrowing period of eligible borrowed materials.
                        </p>
                    </div>

                    <div class="service-roulette-action">
                        Renew Book
                    </div>
                </a>

                <a href="{{ route('services.show', 'book-return') }}" class="service-roulette-card" data-service-card>
                    <div class="service-roulette-icon">
                        <i class="bi bi-box-arrow-in-left"></i>
                    </div>

                    <div class="service-roulette-content">
                        <span>Library Service</span>
                        <h3>Book Return</h3>
                        <p>
                            View books due for return, return materials, and check penalties.
                        </p>
                    </div>

                    <div class="service-roulette-action">
                        View Due Books
                    </div>
                </a>

                <a href="{{ route('services.show', 'referral-letter') }}" class="service-roulette-card" data-service-card>
                    <div class="service-roulette-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                    <div class="service-roulette-content">
                        <span>Library Service</span>
                        <h3>Referral Letter</h3>
                        <p>
                            Request a referral letter when materials are unavailable at PUP Library.
                        </p>
                    </div>

                    <div class="service-roulette-action">
                        Request Letter
                    </div>
                </a>

                <a href="{{ route('services.show', 'facility-reservation') }}" class="service-roulette-card" data-service-card>
                    <div class="service-roulette-icon">
                        <i class="bi bi-building-check"></i>
                    </div>

                    <div class="service-roulette-content">
                        <span>Library Service</span>
                        <h3>Facility Reservation</h3>
                        <p>
                            Reserve discussion rooms, tables, desks, and other library facilities.
                        </p>
                    </div>

                    <div class="service-roulette-action">
                        Reserve Facility
                    </div>
                </a>

            </div>

            <button
                type="button"
                class="roulette-nav-btn roulette-next"
                data-service-next
                aria-label="Next service"
            >
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>

        <div class="services-roulette-dots" data-service-dots></div>

        <p class="services-roulette-hint">
            Click a side card to focus it. Click the center card to open the service.
        </p>
    </section>

</section>

@endsection