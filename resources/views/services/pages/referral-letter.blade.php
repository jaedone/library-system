<section class="service-workspace">

    <div class="service-rules-grid">
        <article class="service-rule-card">
            <h2>Referral Letter Guidelines</h2>

            <ul>
                <li><i class="bi bi-check2-circle"></i> Referral letters are for accessing other libraries for academic or research purposes.</li>
                <li><i class="bi bi-check2-circle"></i> The requested material should be unavailable at PUP Library.</li>
                <li><i class="bi bi-check2-circle"></i> Updates will be sent through email.</li>
                <li><i class="bi bi-check2-circle"></i> The approved referral letter will be processed by library staff.</li>
            </ul>
        </article>

        <article class="service-rule-card warning">
            <h2>Request Information Needed</h2>

            <ul>
                <li><i class="bi bi-building"></i> Destination Library</li>
                <li><i class="bi bi-journal-text"></i> Material Needed</li>
                <li><i class="bi bi-pencil-square"></i> Purpose of Request</li>
                <li><i class="bi bi-envelope"></i> Active Email Address</li>
            </ul>
        </article>
    </div>

    <section class="service-form-panel">
        <div class="service-form-header">
            <span class="service-detail-eyebrow">Request Referral Letter</span>
            <h2>Referral Letter Request Form</h2>

            <p>
                Submit a request to access another library when the needed material is unavailable at PUP Library.
                Updates will be sent through email.
            </p>
        </div>

        <form
            class="service-form"
            method="POST"
            action="{{ route('services.store', $serviceKey) }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="service-form-grid">
                <div class="service-field">
                    <label for="full_name">Full Name</label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        value="{{ old('full_name', auth()->check() && auth()->user()->profile ?? false ? auth()->user()->profile->first_name ?? '' : '') }}"
                        placeholder="Enter your full name"
                    >
                </div>

                <div class="service-field">
                    <label for="email">Email Address</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}"
                        placeholder="Enter your active email"
                        required
                    >
                </div>

                <div class="service-field">
                    <label for="library_account_number">Library Account Number / Student Number</label>

                    <input
                        type="text"
                        id="library_account_number"
                        name="library_account_number"
                        value="{{ old('library_account_number') }}"
                        placeholder="Enter account or student number"
                    >
                </div>

                <div class="service-field">
                    <label for="destination_library">Destination Library</label>

                    <input
                        type="text"
                        id="destination_library"
                        name="destination_library"
                        value="{{ old('destination_library') }}"
                        placeholder="Enter destination library"
                        required
                    >
                </div>

                <div class="service-field">
                    <label for="material_needed">Material Needed</label>

                    <input
                        type="text"
                        id="material_needed"
                        name="material_needed"
                        value="{{ old('material_needed') }}"
                        placeholder="Book, thesis, journal, article, etc."
                        required
                    >
                </div>

                <div class="service-field">
                    <label for="valid_id">Valid ID / Supporting Document</label>

                    <input
                        type="file"
                        id="valid_id"
                        name="valid_id"
                        accept=".jpg,.jpeg,.png,.pdf"
                    >
                </div>

                <div class="service-field service-field-full">
                    <label for="request_purpose">Purpose of Request</label>

                    <textarea
                        id="request_purpose"
                        name="request_purpose"
                        rows="4"
                        placeholder="Explain why you need the referral letter"
                        required
                    >{{ old('request_purpose') }}</textarea>
                </div>
            </div>

            <button type="submit" class="service-submit-btn">
                <i class="bi bi-file-earmark-plus"></i>
                Submit Referral Request
            </button>
        </form>
    </section>

</section>