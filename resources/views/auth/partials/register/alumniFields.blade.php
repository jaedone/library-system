<div data-roles="employee">

    <div class="field">
        <label for="employee_number">Employee number</label>

        <input
            type="text"
            id="employee_number"
            name="employee_number"
            value="{{ old('employee_number') }}"
            placeholder="Enter your employee number"
        >
    </div>

    <div class="field">
        <label for="employee_id_number">Employee ID number</label>

        <input
            type="text"
            id="employee_id_number"
            name="employee_id_number"
            value="{{ old('employee_id_number') }}"
            placeholder="Enter your employee ID number"
        >
    </div>

    <div class="field">
        <label for="id_file">Employee ID file</label>

        <input
            type="file"
            id="id_file"
            name="id_file"
            accept=".pdf,.jpg,.jpeg,.png"
        >
    </div>

    <div class="field">
        <label for="employee_type_id">Employee type</label>

        <select id="employee_type_id" name="employee_type_id">
            <option value="">Select employee type</option>

            @foreach (($employeeTypes ?? []) as $type)
                <option
                    value="{{ $type->id }}"
                    data-employee-type="{{ $type->employee_type_key }}"
                    {{ (string) old('employee_type_id') === (string) $type->id ? 'selected' : '' }}
                >
                    {{ $type->employee_type_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="department_id">Department / Unit</label>

        <select id="department_id" name="department_id">
            <option value="">Select department / unit</option>

            @foreach (($departments ?? []) as $department)
                <option
                    value="{{ $department->id }}"
                    {{ (string) old('department_id') === (string) $department->id ? 'selected' : '' }}
                >
                    {{ $department->department_name }}
                    @if (!empty($department->college_name))
                        — {{ $department->college_name }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>

    <div data-employee-type-section="faculty">

        <div class="field">
            <label for="academic_title_id">Academic Rank / Title</label>

            <select id="academic_title_id" name="academic_title_id">
                <option value="">Select academic rank / title</option>

                @foreach (($academicTitles ?? []) as $title)
                    <option
                        value="{{ $title->id }}"
                        {{ (string) old('academic_title_id') === (string) $title->id ? 'selected' : '' }}
                    >
                        {{ $title->display_title }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

    <div data-employee-type-section="staff">

        <div class="field">
            <label for="job_title_id">Job Title / Role</label>

            <select id="job_title_id" name="job_title_id">
                <option value="">Select job title / role</option>

                @foreach (($jobTitles ?? []) as $jobTitle)
                    <option
                        value="{{ $jobTitle->id }}"
                        {{ (string) old('job_title_id') === (string) $jobTitle->id ? 'selected' : '' }}
                    >
                        {{ $jobTitle->job_title_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="division_id">Administrative Division</label>

            <select id="division_id" name="division_id">
                <option value="">Select administrative division</option>

                @foreach (($divisions ?? []) as $division)
                    <option
                        value="{{ $division->id }}"
                        {{ (string) old('division_id') === (string) $division->id ? 'selected' : '' }}
                    >
                        {{ $division->division_name }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

</div>