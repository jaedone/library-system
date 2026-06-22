@extends('common.main')

@section('title', 'Facilities | PUP Library')

@section('content')

<section class="facilities-showcase-page" data-facility-slider>

    @if ($facilities->count() > 0)
        <section class="facilities-showcase-hero">

            @foreach ($facilities as $facility)
                @php
                    $equipment = is_array($facility->equipment ?? null)
                        ? $facility->equipment
                        : json_decode($facility->equipment ?? '[]', true);

                    $usageFor = is_array($facility->usage_for ?? null)
                        ? $facility->usage_for
                        : json_decode($facility->usage_for ?? '[]', true);

                    $equipmentText = !empty($equipment)
                        ? implode(', ', $equipment)
                        : 'Tables, Chairs';

                    $usageForText = !empty($usageFor)
                        ? implode(', ', $usageFor)
                        : 'Academic use';

                    $imagePath = $facility->image_path ?? 'images/facilities/discussion-room.jpg';
                @endphp

                <article
                    class="facilities-showcase-slide {{ $loop->first ? 'is-active' : '' }}"
                    data-facility-slide
                >
                    <div class="facilities-showcase-copy">
                        <span class="facilities-showcase-eyebrow">Available Facilities</span>

                        <h1>
                            Spaces for
                            <strong>{{ $facility->facility_name }}</strong>
                        </h1>

                        <p>
                            {{ $facility->description ?? 'Reserve this library facility for academic, educational, and university-related activities.' }}
                        </p>

                        <div class="facilities-showcase-actions">
                            <a
                                href="{{ route('services.show', 'facility-reservation') }}?facility_id={{ $facility->id }}"
                                class="facilities-showcase-primary-btn"
                            >
                                <i class="bi bi-calendar-plus"></i>
                                Reserve Now
                            </a>

                            <button type="button" class="facilities-showcase-next-btn" data-facility-next>
                                <span>
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                                Next Facility
                            </button>
                        </div>
                    </div>

                    <div class="facilities-showcase-visual">
                        <div
                            class="facilities-showcase-image"
                            style="background-image: url('{{ asset($imagePath) }}');"
                        ></div>
                    </div>

                    <div class="facilities-showcase-details">
                        <div class="facilities-showcase-detail-item">
                            <i class="bi bi-geo-alt"></i>

                            <div>
                                <span>Location</span>
                                <strong>{{ $facility->location ?? 'Main Library' }}</strong>
                            </div>
                        </div>

                        <div class="facilities-showcase-detail-item">
                            <i class="bi bi-people"></i>

                            <div>
                                <span>Capacity</span>
                                <strong>
                                    {{ $facility->capacity ? $facility->capacity . ' users' : 'Subject to approval' }}
                                </strong>
                            </div>
                        </div>

                        <div class="facilities-showcase-detail-item">
                            <i class="bi bi-tools"></i>

                            <div>
                                <span>Equipment</span>
                                <strong>{{ $equipmentText }}</strong>
                            </div>
                        </div>

                        <div class="facilities-showcase-detail-item">
                            <i class="bi bi-calendar-week"></i>

                            <div>
                                <span>Availability</span>
                                <strong>
                                    {{ $facility->availability_days ?? 'Monday to Friday' }},
                                    {{ $facility->availability_hours ?? '8:00 AM – 5:00 PM' }}
                                </strong>
                            </div>
                        </div>

                        <div class="facilities-showcase-detail-item">
                            <i class="bi bi-journal-check"></i>

                            <div>
                                <span>Usage For</span>
                                <strong>{{ $usageForText }}</strong>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach

        </section>

        <div class="facilities-showcase-counter">
            <span data-facility-current>1</span>
            /
            <span>{{ $facilities->count() }}</span>
        </div>
    @else
        <section class="facility-empty-state">
            <i class="bi bi-building-x"></i>
            <h3>No facilities available</h3>
            <p>Please seed the facilities table first.</p>
        </section>
    @endif

</section>

@endsection