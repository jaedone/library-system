<section class="service-workspace">

    <div class="service-rules-grid">
        <article class="service-rule-card">
            <h2>Reservation Rules</h2>

            <ul>
                <li><i class="bi bi-check2-circle"></i> Only available or reservable materials may be reserved.</li>
                <li><i class="bi bi-check2-circle"></i> The library may reject duplicate or invalid reservations.</li>
                <li><i class="bi bi-check2-circle"></i> Approved reservations must be claimed within the allowed claiming period.</li>
                <li><i class="bi bi-check2-circle"></i> Unclaimed reservations may expire automatically.</li>
            </ul>
        </article>

        <article class="service-rule-card">
            <h2>Reservation Claiming Period</h2>

            <ul>
                <li><i class="bi bi-calendar-check"></i> Approved reservations must be claimed within 2 days.</li>
                <li><i class="bi bi-calendar-x"></i> Reservations not claimed within 2 days may expire automatically.</li>
                <li><i class="bi bi-info-circle"></i> Reservation status starts as Pending until processed by library staff.</li>
            </ul>
        </article>

        <article class="service-rule-card warning">
            <h2>Reservation Status</h2>

            <ul>
                <li><i class="bi bi-hourglass-split"></i> Pending</li>
                <li><i class="bi bi-check-circle"></i> Approved</li>
                <li><i class="bi bi-bag-check"></i> Claimed</li>
                <li><i class="bi bi-calendar-x"></i> Expired</li>
                <li><i class="bi bi-x-circle"></i> Rejected</li>
            </ul>
        </article>
    </div>

    <section class="service-form-panel">
        <div class="service-form-header">
            <span class="service-detail-eyebrow">Reserve Book</span>
            <h2>Book Reservation Form</h2>
            <p>
                Reserve an available book before visiting the library.
                Once approved, the book must be claimed within 2 days.
            </p>
        </div>

        <form class="service-form" method="POST" action="{{ route('services.store', $serviceKey) }}">
            @csrf
            @php
    $selectedResourceId = old('resource_id', request('resource_id'));
@endphp
            <div class="service-form-grid">
                <div class="service-field service-field-full">
                    <label for="resource_id">Book / Resource</label>

                    <select id="resource_id" name="resource_id" required>
                        <option value="">Select book or resource</option>

                        @forelse ($reservableResources ?? [] as $resource)
                            <option
    value="{{ $resource->id }}"
    @selected((string) $selectedResourceId === (string) $resource->id)
>
    {{ $resource->title }}

    @if (!empty($resource->isbn))
        — ISBN: {{ $resource->isbn }}
    @endif
</option>
                        @empty
                            <option value="" disabled>No resources found</option>
                        @endforelse
                    </select>
                </div>

                <div class="service-field service-field-full">
                    <label for="copy_id">Specific Copy Optional</label>

                    <select id="copy_id" name="copy_id">
                        <option value="">No specific copy</option>

                        @forelse ($availableCopies ?? [] as $copy)
                            <option value="{{ $copy->copy_id }}">
                                {{ $copy->title }}
                                — Accession: {{ $copy->accession_number }}
                            </option>
                        @empty
                            <option value="" disabled>No available copies found</option>
                        @endforelse
                    </select>
                </div>

                <div class="service-field service-field-full">
                    <label for="reservation_notes">Notes</label>

                    <textarea
                        id="reservation_notes"
                        name="reservation_notes"
                        rows="4"
                        placeholder="Additional details or request notes"
                    ></textarea>
                </div>
            </div>

            <button type="submit" class="service-submit-btn">
                <i class="bi bi-bookmark-plus"></i>
                Submit Reservation
            </button>
        </form>
    </section>

</section>