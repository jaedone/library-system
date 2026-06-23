@php
    $facilityData = collect($facilities ?? [])->map(fn ($facility) => [
        'id' => $facility->id,
        'name' => $facility->facility_name,
        'capacity' => $facility->capacity,
    ])->values();
@endphp

<section class="service-form-panel facility-booking-panel"
    data-facilities='@json($facilityData)'
    data-availability-url="{{ route('services.facility-availability') }}"
>
    <div class="service-form-header">
        <span class="service-detail-eyebrow">Reserve Facility</span>
        <h2>Facility Reservation Form</h2>
        <p>Select a room, date, and available time slot. Fully booked dates will not be selectable.</p>
    </div>

    <form class="service-form" method="POST" action="{{ route('services.store', $serviceKey) }}">
        @csrf

        <div class="service-form-grid">
            <div class="service-field service-field-full">
                <label for="facility_id">Facility / Room Area</label>
                <select id="facility_id" name="facility_id" required>
                    <option value="">Select facility</option>
                    @foreach ($facilities ?? [] as $facility)
                        <option
                            value="{{ $facility->id }}"
                            data-capacity="{{ $facility->capacity }}"
                        >
                            {{ $facility->facility_name }}
                        </option>
                    @endforeach
                </select>
                <small id="facilityCapacityHint" class="service-field-hint"></small>
            </div>

            <div class="service-field">
                <label for="participants">Number of Participants</label>
                <input type="number" id="participants" name="participants" min="1" required>
                <small id="participantsHint" class="service-field-hint"></small>
            </div>

            <input type="hidden" id="reservation_date" name="reservation_date">
            <input type="hidden" id="start_time" name="start_time">
            <input type="hidden" id="end_time" name="end_time">

            <div class="service-field service-field-full">
                <label>Date & Time</label>

                <div class="facility-scheduler">
                    <div class="facility-calendar-box">
                        <div id="facilityCalendar"></div>
                    </div>

                    <div class="facility-times-box">
                        <h3 id="selectedDateLabel">Select a date</h3>
                        <div id="facilityTimeSlots" class="facility-time-slots">
                            <p class="facility-empty-message">Choose a facility first.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service-field service-field-full">
                <label for="facility_purpose">Purpose</label>
                <textarea
                    id="facility_purpose"
                    name="facility_purpose"
                    rows="4"
                    placeholder="Academic activity, group study, meeting, seminar, etc."
                    required
                ></textarea>
            </div>
        </div>

        <button type="submit" class="service-submit-btn">
            <i class="bi bi-building-check"></i>
            Submit Facility Reservation
        </button>
    </form>
</section>