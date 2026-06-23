<section class="service-workspace">
    <section class="service-form-panel">
        <div class="service-form-header">
            <span class="service-detail-eyebrow">Request Referral Letter</span>
            <h2>Referral Letter Request Form</h2>

            <p>
                Submit a request to access another library branch when the needed material is unavailable at PUP Library.
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
                    <label for="destination_library">Destination Library Branch</label>

                    <select
                        id="destination_library"
                        name="destination_library"
                        class="form-select referral-select"
                        required
                    >
                        <option value="">Select destination branch</option>

                        @forelse ($libraryBranches ?? [] as $branch)
                            <option
                                value="{{ $branch->branch_name }}"
                                @selected(old('destination_library') === $branch->branch_name)
                            >
                                {{ $branch->branch_name }}

                                @if (!empty($branch->location))
                                    ( {{ $branch->location }} )
                                @endif
                            </option>
                        @empty
                            <option value="" disabled>No library branches found</option>
                        @endforelse
                    </select>
                </div>

                <div class="service-field">
                    <label for="material_needed">Material Needed</label>

                    <select
                        id="material_needed"
                        name="material_needed"
                        class="form-select referral-select"
                        required
                    >
                        <option value="">Select material type</option>

                        @forelse ($materialTypes ?? [] as $type)
                            <option
                                value="{{ $type->material_type_name }}"
                                @selected(old('material_needed') === $type->material_type_name)
                            >
                                {{ $type->material_type_name }}
                            </option>
                        @empty
                            <option value="" disabled>No material types found</option>
                        @endforelse
                    </select>
                </div>

                <div class="service-field service-field-full">
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