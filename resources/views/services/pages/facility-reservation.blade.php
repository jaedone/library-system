<section class="service-workspace">

  <div class="service-rules-grid">
    <article class="service-rule-card">
      <h2>Facility Reservation Rules</h2>

      <ul>
        <li><i class="bi bi-check2-circle"></i> Facilities must be used for academic, educational, or university-related activities.</li>
        <li><i class="bi bi-check2-circle"></i> Reservations are subject to availability and approval.</li>
        <li><i class="bi bi-check2-circle"></i> Users must follow the reserved schedule.</li>
        <li><i class="bi bi-check2-circle"></i> The number of participants must match the selected facility capacity.</li>
      </ul>
    </article>

    <article class="service-rule-card">
      <h2>Facility Reservation Period</h2>

      <ul>
        <li><i class="bi bi-calendar-check"></i> Reservations must be made at least 1 day before the intended use.</li>
        <li><i class="bi bi-calendar-range"></i> Reservations can only be made up to 14 days in advance.</li>
        <li><i class="bi bi-clock"></i> Each reservation is limited to a maximum of 2 hours.</li>
        <li><i class="bi bi-people"></i> Participants must match the selected facility capacity.</li>
      </ul>
    </article>

    <article class="service-rule-card warning">
      <h2>Penalties and Restrictions</h2>

      <ul>
        <li><i class="bi bi-exclamation-circle"></i> Late cancellation may affect future reservation requests.</li>
        <li><i class="bi bi-exclamation-circle"></i> Misuse of facilities may result in reservation restrictions.</li>
        <li><i class="bi bi-exclamation-circle"></i> Damaged equipment or facilities may be subject to assessment.</li>
      </ul>
    </article>
  </div>

  <section class="service-form-panel">
    <div class="service-form-header">
      <span class="service-detail-eyebrow">Reserve Facility</span>
      <h2>Facility Reservation Form</h2>
      <p>
        Reserve library spaces such as discussion rooms, tables, or desks.
        Requests must follow the advance booking and duration rules.
      </p>
    </div>

    <form class="service-form" method="POST" action="{{ route('services.store', $serviceKey) }}">
      @csrf

      <div class="service-form-grid">
        @php
        $selectedFacilityId = old('facility_id', request('facility_id'));
        @endphp

        <div class="service-field service-field-full">
          <label for="facility_id">Facility</label>

          <select id="facility_id" name="facility_id" required>
            <option value="">Select facility</option>

            @forelse ($facilities ?? [] as $facility)
            <option
              value="{{ $facility->id }}"
              @selected((string) $selectedFacilityId===(string) $facility->id)
              >
              {{ $facility->facility_name }}

              @if (!empty($facility->capacity))
              — Capacity: {{ $facility->capacity }}
              @endif

              @if (!empty($facility->location))
              — {{ $facility->location }}
              @endif
            </option>
            @empty
            <option value="" disabled>No active facilities found</option>
            @endforelse
          </select>
        </div>

        <div class="service-field">
          <label for="reservation_date">Reservation Date</label>

          <input
            type="date"
            id="reservation_date"
            name="reservation_date"
            required>
        </div>

        <div class="service-field">
          <label for="start_time">Start Time</label>

          <input
            type="time"
            id="start_time"
            name="start_time"
            required>
        </div>

        <div class="service-field">
          <label for="end_time">End Time</label>

          <input
            type="time"
            id="end_time"
            name="end_time"
            required>
        </div>

        <div class="service-field">
          <label for="participants">Number of Participants</label>

          <input
            type="number"
            id="participants"
            name="participants"
            min="1"
            placeholder="Enter number"
            required>
        </div>

        <div class="service-field service-field-full">
          <label for="facility_purpose">Purpose</label>

          <textarea
            id="facility_purpose"
            name="facility_purpose"
            rows="4"
            placeholder="State the purpose of the facility reservation"
            required></textarea>
        </div>
      </div>

      <button type="submit" class="service-submit-btn">
        <i class="bi bi-calendar-plus"></i>
        Submit Facility Reservation
      </button>
    </form>
  </section>

</section>