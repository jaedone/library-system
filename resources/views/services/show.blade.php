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
        </div>
    </section>

    {{-- SERVICE MESSAGE POPUP --}}
@if (session('success') || session('error') || $errors->any())
    <div class="modal fade" id="serviceMessageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content reservation-success-modal">
                <div class="modal-body text-center p-5">
                    <div class="reservation-success-icon {{ $errors->any() || session('error') ? 'is-error' : '' }}">
                        <i class="bi {{ session('success') ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}"></i>
                    </div>

                    <h3>
                        {{ session('success') ? 'Request Submitted!' : 'Please check the form.' }}
                    </h3>

                    @if (session('success'))
                        <p>{{ session('success') }}</p>
                    @elseif (session('error'))
                        <p>{{ session('error') }}</p>
                    @else
                        <ul class="service-modal-error-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <button type="button" class="btn btn-primary reservation-success-btn" data-bs-dismiss="modal">
                        Okay
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

    {{-- SERVICE SPECIFIC PAGE --}}
    @include('services.pages.' . $serviceKey)

</section>

@endsection