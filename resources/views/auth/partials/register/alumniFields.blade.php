<div data-roles="alumni">

    <div class="field">
        <label for="alumni_id_number">
            Alumni ID Number
        </label>

        <input
            type="text"
            id="alumni_id_number"
            name="alumni_id_number"
            value="{{ old('alumni_id_number') }}"
            placeholder="Enter your alumni ID number"
        >
    </div>

    <div class="field">
        <label for="graduated_program">
            Graduated Program
        </label>

        <input
            type="text"
            id="graduated_program"
            name="graduated_program"
            value="{{ old('graduated_program') }}"
            placeholder="e.g. Bachelor of Science in Computer Science"
        >
    </div>

    <div class="field">
        <label for="graduation_year">
            Graduation Year
        </label>

        <input
            type="number"
            id="graduation_year"
            name="graduation_year"
            value="{{ old('graduation_year') }}"
            min="1950"
            max="{{ date('Y') }}"
            placeholder="e.g. 2024"
        >
    </div>

    <div class="field">
        <label for="alumni_id_file">
            Alumni ID / Valid ID
        </label>

        <input
            type="file"
            id="alumni_id_file"
            name="alumni_id_file"
            accept=".pdf,.jpg,.jpeg,.png"
        >
    </div>

</div>