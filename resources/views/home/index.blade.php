@extends('common.main')

@section('title', 'Home | PUP Library')

@section('content')

<section class="home-page">

    {{-- HERO / WELCOME BANNER --}}
    <section
        class="home-hero"
        style="--hero-bg: url('{{ asset($hero['background_image']) }}');">
        <div class="home-hero-content">
            <span class="home-eyebrow">PUP Library Management System</span>

            <h1>Welcome to the PUP Library!</h1>

            <p>
                Access thousands of books, journals, references, and digital resources
                for students, researchers, faculty, and visitors.
            </p>

            <div class="home-hero-actions">
                <a href="{{ url('/catalog') }}" class="home-btn home-btn-light">
                    <i class="bi bi-search"></i>
                    Search Catalog
                </a>

                <a href="{{ url('/services') }}" class="home-btn home-btn-outline">
                    <i class="bi bi-grid"></i>
                    Browse Services
                </a>
            </div>
        </div>

        <div class="home-hero-visual">
            <div class="home-video-card">
                <iframe
                    src="https://www.youtube.com/embed/{{ $hero['youtube_video_id'] }}?autoplay=1&mute=1&loop=1&playlist={{ $hero['youtube_video_id'] }}&controls=1&rel=0"
                    title="PUP Library Video"
                    frameborder="0"
                    allow="autoplay; encrypted-media; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>
            </div>
        </div>
    </section>

    {{-- NEWS AND ANNOUNCEMENTS --}}
    <section class="home-section">
        <div class="home-section-header">
            <div>
                <h2>News & Announcements</h2>
            </div>
        </div>

        <div class="announcement-grid">
    @foreach ($announcements as $announcement)
        <article class="announcement-card">
            <div class="announcement-icon">
                @if (!empty($announcement->image_path))
                    <img src="{{ asset($announcement->image_path) }}" alt="{{ $announcement->title }}">
                @else
                    <i class="bi {{ $announcement->icon ?? 'bi-megaphone' }}"></i>
                @endif
            </div>

            <h3>{{ $announcement->title }}</h3>

            <p>{{ $announcement->description }}</p>
        </article>
    @endforeach
</div>
    </section>

    {{-- SERVICES OFFERED --}}
    <section class="home-section">
        <div class="home-section-header">
            <div>
                <h2>Services Offered</h2>
            </div>
        </div>

        <div class="services-grid">
            @foreach ($services as $service)
            <a href="{{ $service['url'] }}" class="service-card">
                <i class="bi {{ $service['icon'] }}"></i>
                <h3>{{ $service['title'] }}</h3>
                <p>{{ $service['description'] }}</p>
            </a>
            @endforeach
        </div>
    </section>

    {{-- FEATURED BOOKS --}}
    <section class="home-section">
        <div class="home-section-header">
            <div>
                <h2>Featured Books</h2>
            </div>

            <a href="{{ url('/catalog') }}" class="section-link">Open catalog</a>
        </div>

        <ul class="nav home-book-tabs" id="bookTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link active"
                    id="new-arrivals-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#new-arrivals"
                    type="button"
                    role="tab">
                    New Arrivals
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="most-borrowed-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#most-borrowed"
                    type="button"
                    role="tab">
                    Most Borrowed
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="recommended-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#recommended"
                    type="button"
                    role="tab">
                    Recommended Books
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="new-arrivals" role="tabpanel">
                <div class="books-grid">
                    @forelse ($newArrivals as $book)
                    @include('common.resource-card', [
                        'resource' => $book,
                        'variant' => 'compact',
                        'borrowBlockReason' => $borrowBlockReason ?? null
                    ])
                    @empty
                    <p class="empty-message">No new books available yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="tab-pane fade" id="most-borrowed" role="tabpanel">
                <div class="books-grid">
                    @forelse ($mostBorrowed as $book)
                    @include('common.resource-card', [
                        'resource' => $book,
                        'variant' => 'compact',
                        'borrowBlockReason' => $borrowBlockReason ?? null
                    ])
                    @empty
                    <p class="empty-message">No borrowing records yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="tab-pane fade" id="recommended" role="tabpanel">
                <div class="books-grid">
                    @forelse ($recommendedBooks as $book)
                    @include('common.resource-card', [
                        'resource' => $book,
                        'variant' => 'compact',
                        'borrowBlockReason' => $borrowBlockReason ?? null
                    ])
                    @empty
                    <p class="empty-message">No recommended books available yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

</section>

@php
    $bookDetailsResources = collect($newArrivals)
        ->concat($mostBorrowed)
        ->concat($recommendedBooks)
        ->unique('id')
        ->values()
        ->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title ?? 'Untitled Resource',
                'isbn' => $book->isbn ?? 'N/A',
                'description' => $book->description ?? 'No description available.',
                'cover_url' => !empty($book->cover_image_path)
                    ? asset('storage/' . $book->cover_image_path)
                    : asset('images/default-book-cover.png'),
                'publication_year' => $book->publication_year ?? 'N/A',
                'category_name' => $book->category_name ?? 'N/A',
                'material_type_name' => $book->material_type_name ?? 'Library Material',
                'authors' => $book->authors ?? 'Unknown Author',
                'library_locations' => $book->library_locations ?? 'No location assigned',
                'availability_statuses' => $book->availability_statuses ?? 'No copies available',
                'total_copies' => $book->total_copies ?? 0,
                'available_copies' => $book->available_copies ?? 0,
            ];
        });
@endphp

@include('common.book-details-panel', [
    'bookDetailsResources' => $bookDetailsResources,
])

@endsection