<div data-roles="student">

    <div class="field">
        <label for="student_number">Student number</label>

        <input
            type="text"
            id="student_number"
            name="student_number"
            value="{{ old('student_number') }}"
        >
    </div>

    <div class="field-row">
        <div class="field">
            <label for="college_id_student">College</label>

            <select id="college_id_student" name="college_id">
                <option value="">Select college</option>

                @foreach (($colleges ?? []) as $college)
                    <option
                        value="{{ $college->id }}"
                        {{ (string) old('college_id') === (string) $college->id ? 'selected' : '' }}
                    >
                        {{ $college->college_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="program_id">Program</label>

            <select id="program_id" name="program_id">
                <option value="">Select program</option>

                @foreach (($programs ?? []) as $program)
                    <option
                        value="{{ $program->id }}"
                        {{ (string) old('program_id') === (string) $program->id ? 'selected' : '' }}
                    >
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="field">
        <label for="year_level">Year level</label>

        <select id="year_level" name="year_level">
            <option value="">Select year level</option>

            @foreach (['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', 'Graduate'] as $level)
                <option value="{{ $level }}" {{ old('year_level') === $level ? 'selected' : '' }}>
                    {{ $level }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="cor_file">Certificate of Registration (CoR)</label>

        <input
            type="file"
            id="cor_file"
            name="cor_file"
            accept=".pdf,.jpg,.jpeg,.png"
        >
    </div>

</div>