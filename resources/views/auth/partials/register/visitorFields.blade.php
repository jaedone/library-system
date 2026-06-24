<div data-roles="visitor">

    <div class="field">
        <label for="institution">
            Institution / School / Organization
        </label>

        <input
            type="text"
            id="institution"
            name="institution"
            value="{{ old('institution') }}"
            placeholder="e.g. De La Salle University"
        >
    </div>

    <div class="field">
        <label for="research_topic">
            Research Topic
        </label>

        <input
            type="text"
            id="research_topic"
            name="research_topic"
            value="{{ old('research_topic') }}"
            placeholder="Enter your research topic"
        >
    </div>

    <div class="field">
        <label for="intended_visit_date">
            Intended Visit Date
        </label>

        <input
            type="date"
            id="intended_visit_date"
            name="intended_visit_date"
            value="{{ old('intended_visit_date') }}"
        >
    </div>

    <div class="field">
        <label for="purpose_of_visit">
            Purpose of Visit
        </label>

        <textarea
            id="purpose_of_visit"
            name="purpose_of_visit"
            rows="4"
            placeholder="Explain your purpose for visiting the library"
        >{{ old('purpose_of_visit') }}</textarea>
    </div>

    <div class="field">
        <label for="referral_letter_file">
            Referral Letter
        </label>

        <input
            type="file"
            id="referral_letter_file"
            name="referral_letter_file"
            accept=".pdf,.jpg,.jpeg,.png"
        >
    </div>

    <div class="field">
        <label for="valid_id_file">
            Valid ID
        </label>

        <input
            type="file"
            id="valid_id_file"
            name="valid_id_file"
            accept=".pdf,.jpg,.jpeg,.png"
        >
    </div>

</div>