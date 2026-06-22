@extends('common.main')

@section('title', $service['title'] . ' | PUP Library')

@section('content')

<section class="service-detail-page">

    {{-- HERO --}}
    <section class="service-detail-hero">
        <a href="{{ route('services.index') }}" class="service-back-link">
            <i class="bi bi-arrow-left"></i>
            Back to Services
        </a>

        <div class="service-detail-hero-content">
            <div class="service-detail-icon">
                <i class="bi {{ $service['icon'] }}"></i>
            </div>

            <span class="service-detail-eyebrow">
                {{ $service['section'] }}
            </span>

            <h1>{{ $service['title'] }}</h1>

            <p>{{ $service['description'] }}</p>

            @if (!empty($service['notice']))
                <div class="service-detail-notice">
                    <i class="bi bi-info-circle"></i>
                    <span>{{ $service['notice'] }}</span>
                </div>
            @endif

            @if ($service['is_public'])
                <div class="service-access-pill public">
                    <i class="bi bi-unlock"></i>
                    Available to visitors
                </div>
            @else
                <div class="service-access-pill protected">
                    <i class="bi bi-lock"></i>
                    Sign-in required
                </div>
            @endif
        </div>
    </section>

    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div class="service-success-alert">
            <i class="bi bi-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ERROR MESSAGE --}}
    @if ($errors->any())
        <div class="service-error-alert">
            <i class="bi bi-exclamation-circle"></i>

            <div>
                <strong>Please check the form.</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- SERVICE SPECIFIC PAGE --}}
    @include('services.pages.' . $serviceKey)

</section>

@endsection