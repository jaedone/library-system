@extends('common.main')

@section('title', 'Website Information Management | PUP Library')

@section('content')

<section class="admin-page admin-website-page">
    <div class="admin-page-header">
        <div>
            <span class="admin-eyebrow">Admin Dashboard</span>
            <h1>Website Information Management</h1>
            <p>Manage user-facing announcements, resources, and facilities in one workspace.</p>
        </div>
    </div>

    <div class="admin-push-layout" data-admin-push-layout>
        <main class="admin-push-main">
            <section class="admin-website-grid">
                {{-- ANNOUNCEMENTS --}}
                <article class="admin-website-card" data-module-card="announcement">
                    <div class="admin-website-card-header">
                        <div>
                            <span class="admin-eyebrow">Website Content</span>
                            <h2>News & Announcements</h2>
                        </div>

                        <button type="button" class="admin-card-add-btn" data-website-action="add" data-module="announcement">
                            <i class="bi bi-plus-lg"></i>
                            Add
                        </button>
                    </div>

                    <div class="admin-card-search">
                        <i class="bi bi-search"></i>
                        <input type="search" placeholder="Search announcements..." data-card-search="announcement">
                    </div>

                    <div class="admin-website-list" data-card-list="announcement">
                        @forelse ($announcements as $announcement)
                        <div
                            class="admin-website-item"
                            data-item-row="announcement-{{ $announcement->id }}"
                            data-search-text="{{ Str::lower($announcement->title . ' ' . $announcement->description) }}">
                            <div class="admin-website-item-main">
                                <i class="bi bi-megaphone"></i>

                                <div>
                                    <strong>{{ $announcement->title }}</strong>
                                    <span>{{ $announcement->is_archived ? 'Archived' : 'Visible' }}</span>
                                </div>
                            </div>

                            <button type="button" class="admin-website-more-btn">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>

                            <div class="admin-website-item-actions">
                                <button type="button" data-website-action="view" data-module="announcement" data-id="{{ $announcement->id }}">View</button>
                                <button type="button" data-website-action="edit" data-module="announcement" data-id="{{ $announcement->id }}">Update</button>
                                <button type="button" data-website-action="archive" data-module="announcement" data-id="{{ $announcement->id }}">
                                    {{ $announcement->is_archived ? 'Restore' : 'Archive' }}
                                </button>
                                <button type="button" data-website-action="delete" data-module="announcement" data-id="{{ $announcement->id }}">Delete</button>
                            </div>
                        </div>
                        @empty
                        <p class="admin-empty-text">No announcements yet.</p>
                        @endforelse
                    </div>

                    <p class="admin-website-no-results">No announcements match your search.</p>
                </article>

                {{-- RESOURCES --}}
                <article class="admin-website-card" data-module-card="resource">
                    <div class="admin-website-card-header">
                        <div>
                            <span class="admin-eyebrow">Library Catalog</span>
                            <h2>Resources</h2>
                        </div>

                        <button type="button" class="admin-card-add-btn" data-website-action="add" data-module="resource">
                            <i class="bi bi-plus-lg"></i>
                            Add
                        </button>
                    </div>

                    <div class="admin-card-search">
                        <i class="bi bi-search"></i>
                        <input type="search" placeholder="Search resources..." data-card-search="resource">
                    </div>

                    <div class="admin-website-list" data-card-list="resource">
                        @forelse ($resources as $resource)
                        <div
                            class="admin-website-item"
                            data-item-row="resource-{{ $resource->id }}"
                            data-search-text="{{ Str::lower($resource->title . ' ' . ($resource->authors ?? '') . ' ' . ($resource->isbn ?? '') . ' ' . ($resource->material_type_name ?? '')) }}">
                            <div class="admin-website-item-main">
                                <i class="bi bi-book"></i>

                                <div>
                                    <strong>{{ $resource->title }}</strong>
                                    <span>
                                        {{ $resource->is_archived ? 'Archived' : ($resource->material_type_name ?? 'Library Material') }}
                                    </span>
                                </div>
                            </div>

                            <div class="admin-website-item-actions">
                                <button type="button" data-website-action="view" data-module="resource" data-id="{{ $resource->id }}">View</button>
                                <button type="button" data-website-action="edit" data-module="resource" data-id="{{ $resource->id }}">Update</button>
                                <button type="button" data-website-action="archive" data-module="resource" data-id="{{ $resource->id }}">
                                    {{ $resource->is_archived ? 'Restore' : 'Archive' }}
                                </button>
                                <button type="button" data-website-action="delete" data-module="resource" data-id="{{ $resource->id }}">Delete</button>
                            </div>
                        </div>
                        @empty
                        <p class="admin-empty-text">No resources yet.</p>
                        @endforelse
                    </div>

                    <p class="admin-website-no-results">No resources match your search.</p>
                </article>

                {{-- FACILITIES --}}
                <article class="admin-website-card" data-module-card="facility">
                    <div class="admin-website-card-header">
                        <div>
                            <span class="admin-eyebrow">Facilities</span>
                            <h2>Library Facilities</h2>
                        </div>

                        <button type="button" class="admin-card-add-btn" data-website-action="add" data-module="facility">
                            <i class="bi bi-plus-lg"></i>
                            Add
                        </button>
                    </div>

                    <div class="admin-card-search">
                        <i class="bi bi-search"></i>
                        <input type="search" placeholder="Search facilities..." data-card-search="facility">
                    </div>

                    <div class="admin-website-list" data-card-list="facility">
                        @forelse ($facilities as $facility)
                        <div
                            class="admin-website-item"
                            data-item-row="facility-{{ $facility->id }}"
                            data-search-text="{{ Str::lower($facility->facility_name . ' ' . ($facility->description ?? '') . ' ' . ($facility->location ?? '')) }}">
                            <div class="admin-website-item-main">
                                <i class="bi bi-building"></i>

                                <div>
                                    <strong>{{ $facility->facility_name }}</strong>
                                    <span>{{ $facility->is_archived ? 'Archived' : 'Visible' }}</span>
                                </div>
                            </div>

                            <div class="admin-website-item-actions">
                                <button type="button" data-website-action="view" data-module="facility" data-id="{{ $facility->id }}">View</button>
                                <button type="button" data-website-action="edit" data-module="facility" data-id="{{ $facility->id }}">Update</button>
                                <button type="button" data-website-action="archive" data-module="facility" data-id="{{ $facility->id }}">
                                    {{ $facility->is_archived ? 'Restore' : 'Archive' }}
                                </button>
                                <button type="button" data-website-action="delete" data-module="facility" data-id="{{ $facility->id }}">Delete</button>
                            </div>
                        </div>
                        @empty
                        <p class="admin-empty-text">No facilities yet.</p>
                        @endforelse
                    </div>

                    <p class="admin-website-no-results">No facilities match your search.</p>
                </article>
            </section>
        </main>

        {{-- REUSABLE PUSH PANEL --}}
        <aside class="admin-push-panel" data-admin-push-panel aria-hidden="true">
            <div class="admin-push-panel-inner">
                <div class="admin-push-panel-top">
                    <div>
                        <span class="admin-eyebrow" data-panel-eyebrow>Website Information</span>
                        <h2 data-panel-title>Select an item</h2>
                        <p data-panel-subtitle>View, add, update, archive, or delete selected information.</p>
                    </div>

                    <button type="button" class="admin-push-panel-close" data-panel-close>
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="admin-push-panel-body">
                    {{-- VIEW --}}
                    <section class="admin-push-panel-section" data-panel-section="view">
                        <div data-view-output></div>
                    </section>

                    {{-- FORM --}}
                    <section class="admin-push-panel-section" data-panel-section="form" hidden>
                        <form method="POST" action="#" enctype="multipart/form-data" data-website-form>
                            @csrf
                            <input type="hidden" name="_method" value="POST" data-website-method>

                            {{-- ANNOUNCEMENT --}}
                            <div data-form-group="announcement" hidden>
                                <input type="hidden" name="is_active" value="1">

                                <div class="admin-form-grid two">
                                    <div class="admin-field">
                                        <label>Announcement Title</label>
                                        <input type="text" name="title" data-field="title">
                                    </div>

                                    <div class="admin-field">
                                        <label>Schedule Publish Date</label>
                                        <input type="datetime-local" name="published_at" data-field="published_at">
                                    </div>
                                </div>

                                <div class="admin-field">
                                    <label>Description</label>
                                    <textarea name="description" rows="4" data-field="description"></textarea>
                                </div>

                                <div class="admin-field">
                                    <label>Announcement Image</label>
                                    <input type="file" name="announcement_image" accept="image/png,image/jpeg,image/jpg,image/webp" data-field="announcement_image">
                                </div>
                            </div>

                            {{-- RESOURCE --}}
                            <div data-form-group="resource" hidden>
                                <div class="admin-field">
                                    <label>Resource Title</label>
                                    <input type="text" name="title" data-field="title">
                                </div>

                                <div class="admin-form-grid two">
                                    <div class="admin-field">
                                        <label>ISBN</label>
                                        <input type="text" name="isbn" data-field="isbn">
                                    </div>

                                    <div class="admin-field">
                                        <label>Publication Year</label>
                                        <input type="number" name="publication_year" data-field="publication_year">
                                    </div>
                                </div>

                                <div class="admin-form-grid two">
                                    <div class="admin-field">
                                        <label>Material Type</label>
                                        <select name="material_type_id" data-field="material_type_id">
                                            <option value="">Select material type</option>
                                            @foreach ($materialTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->material_type_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="admin-field">
                                        <label>Category</label>
                                        <select name="category_id" data-field="category_id">
                                            <option value="">Select category</option>
                                            @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="admin-field">
                                    <label>Description</label>
                                    <textarea name="description" rows="4" data-field="description"></textarea>
                                </div>

                                <div class="admin-field">
                                    <label>Authors</label>
                                    <textarea name="author_names" rows="3" data-field="authors"></textarea>
                                </div>

                                <div class="admin-field">
                                    <label>Cover Image</label>
                                    <input type="file" name="cover_image" accept="image/png,image/jpeg,image/jpg,image/webp" data-field="cover_image">
                                </div>

                                <label class="admin-check-field">
                                    <input type="checkbox" name="is_reference_only" value="1" data-field="is_reference_only">
                                    <span>Room use only</span>
                                </label>

                                <label class="admin-check-field">
                                    <input type="checkbox" name="is_digital" value="1" data-field="is_digital">
                                    <span>Digital resource</span>
                                </label>

                                <div class="admin-field">
                                    <label>Digital URL</label>
                                    <input type="url" name="digital_url" data-field="digital_url">
                                </div>
                            </div>

                            {{-- FACILITY --}}
                            <div data-form-group="facility" hidden>
                                <input type="hidden" name="is_active" value="1">

                                <div class="admin-field">
                                    <label>Facility Name</label>
                                    <input type="text" name="facility_name" data-field="facility_name">
                                </div>

                                <div class="admin-form-grid two">
                                    <div class="admin-field">
                                        <label>Capacity</label>
                                        <input type="number" name="capacity" min="1" data-field="capacity">
                                    </div>

                                    <div class="admin-field">
                                        <label>Location</label>
                                        <input type="text" name="location" data-field="location">
                                    </div>
                                </div>

                                <div class="admin-field">
                                    <label>Description</label>
                                    <textarea name="description" rows="4" data-field="description"></textarea>
                                </div>

                                <div class="admin-form-grid two">
                                    <div class="admin-field">
                                        <label>Available Dates</label>

                                        <div class="input-group admin-date-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-calendar-check"></i>
                                            </span>

                                            <input
                                                type="text"
                                                name="availability_days"
                                                class="form-control facility-available-dates-picker"
                                                data-field="availability_days"
                                                placeholder="Select available dates"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="admin-field">
                                        <label>Availability Hours</label>

                                        <input
                                            type="text"
                                            name="availability_hours"
                                            data-field="availability_hours"
                                            placeholder="Example: 8:00 AM to 5:00 PM">
                                    </div>
                                </div>

                                <div class="admin-field">
                                    <label>Equipment</label>
                                    <textarea name="equipment" rows="3" data-field="equipment"></textarea>
                                </div>

                                <div class="admin-field">
                                    <label>Usage For</label>
                                    <textarea name="usage_for" rows="3" data-field="usage_for"></textarea>
                                </div>

                                <div class="admin-field">
                                    <label>Facility Image</label>
                                    <input type="file" name="facility_image" accept="image/png,image/jpeg,image/jpg,image/webp" data-field="facility_image">
                                </div>
                            </div>

                            <div class="admin-panel-divider"></div>

                            <div class="admin-push-preview-card">
                                <span class="admin-eyebrow">Live Preview</span>
                                <div data-live-preview-output></div>
                            </div>

                            <button type="submit" class="admin-primary-btn">
                                <i class="bi bi-save"></i>
                                Save Changes
                            </button>
                        </form>
                    </section>

                    {{-- ARCHIVE --}}
                    <section class="admin-push-panel-section" data-panel-section="archive" hidden>
                        <div class="admin-delete-warning">
                            <i class="bi bi-archive"></i>
                            <h3 data-archive-title>Archive item?</h3>
                            <p data-archive-message>This will remove the item from the homepage but keep it in the database.</p>
                        </div>

                        <form method="POST" action="#" data-archive-form>
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="archive" value="1" data-archive-value>

                            <button type="submit" class="admin-primary-btn">
                                <i class="bi bi-archive"></i>
                                Confirm
                            </button>
                        </form>
                    </section>

                    {{-- DELETE --}}
                    <section class="admin-push-panel-section" data-panel-section="delete" hidden>
                        <div class="admin-delete-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <h3 data-delete-title>Delete item?</h3>
                            <p>This action cannot be undone.</p>
                        </div>

                        <form method="POST" action="#" data-delete-form>
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="admin-danger-btn">
                                <i class="bi bi-trash"></i>
                                Confirm Delete
                            </button>
                        </form>
                    </section>
                </div>
            </div>
        </aside>
    </div>
</section>

<script type="application/json" data-website-data>
    {
        !!json_encode([
            'announcement' => $announcements,
            'resource' => $resources,
            'facility' => $facilities,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!
    }
</script>

@endsection