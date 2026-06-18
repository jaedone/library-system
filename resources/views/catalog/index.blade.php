@extends('common.main')

@section('title', 'Catalog | PUP Library')

@section('content')

<section
    class="catalog-page"
    x-data="catalogSearch({
        resources: {{ Js::from($catalogResources) }},
        initialSearch: @js(request('q', ''))
    })"
>
    {{-- CATALOG HERO --}}
    <section
        class="catalog-hero"
        style="--catalog-bg: url('{{ asset('images/auth-bg/bg5.jpg') }}');"
    >
        <div class="catalog-hero-content">
            <span class="catalog-eyebrow">Library Catalog</span>

            <h1>Search books, journals, theses, and library materials.</h1>

            <p>
                Find resources by title, author, ISBN, subject, category,
                material type, availability, publication year, and location.
            </p>

            <div class="catalog-search-filter-form">
                <div class="catalog-hero-search">
                    <i class="bi bi-search"></i>

                    <input
                        type="search"
                        x-model.debounce.200ms="search"
                        placeholder="Search title, author, ISBN, subject, or keyword..."
                        autocomplete="off"
                    >

                    <button type="button" @click="currentPage = 1; scrollToResults()">
                        Search
                    </button>
                </div>

                <div class="catalog-filter-row">
                    <div class="catalog-filter-field">
                        <label for="isbn">ISBN</label>

                        <input
                            type="text"
                            id="isbn"
                            x-model.debounce.200ms="isbn"
                            placeholder="Enter ISBN"
                        >
                    </div>

                    <div class="catalog-filter-field">
                        <label for="category_id">Category</label>

                        <select id="category_id" x-model="categoryId">
                            <option value="">All categories</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="catalog-filter-field">
                        <label for="material_type_id">Material Type</label>

                        <select id="material_type_id" x-model="materialTypeId">
                            <option value="">All material types</option>

                            @foreach ($materialTypes as $type)
                                <option value="{{ $type->id }}">
                                    {{ $type->material_type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="catalog-filter-field">
                        <label for="availability">Availability</label>

                        <select id="availability" x-model="availabilityId">
                            <option value="">Any availability</option>

                            @foreach ($copyStatuses as $status)
                                <option value="{{ $status->id }}">
                                    {{ $status->status_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="catalog-filter-field">
                        <label for="publication_year">Publication Year</label>

                        <input
                            type="number"
                            id="publication_year"
                            x-model.debounce.200ms="publicationYear"
                            min="1900"
                            max="{{ date('Y') }}"
                            placeholder="e.g. 2026"
                        >
                    </div>

                    <div class="catalog-filter-field">
                        <label for="branch_id">Library Location</label>

                        <select id="branch_id" x-model="branchId">
                            <option value="">All locations</option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="catalog-filter-actions">
                    <button
                        type="button"
                        class="catalog-filter-btn"
                        @click="currentPage = 1; scrollToResults()"
                    >
                        Apply Filters
                    </button>

                    <button
                        type="button"
                        class="catalog-clear-btn"
                        @click="clearFilters()"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- RESULTS SECTION --}}
    <section class="catalog-results-section home-section" id="catalogResults">
        <div class="home-section-header catalog-results-header">
            <div>
                <span
                    class="catalog-results-kicker"
                    x-text="hasActiveSearch ? 'Search Results' : 'Browse Catalog'"
                ></span>

                <h2>
                    <span class="section-kicker"></span>

                    <span x-show="hasActiveSearch" x-cloak>
                        <span x-text="filteredResults.length"></span>
                        result<span x-show="filteredResults.length !== 1">s</span>
                        found
                        <span x-show="search.trim() !== ''">
                            for "<span x-text="search"></span>"
                        </span>
                    </span>

                    <span x-show="!hasActiveSearch" x-cloak>
                        All Library Resources
                    </span>
                </h2>
            </div>

            <div class="catalog-result-count" x-show="filteredResults.length > 0" x-cloak>
                Showing
                <strong x-text="startItem"></strong>
                to
                <strong x-text="endItem"></strong>
                of
                <strong x-text="filteredResults.length"></strong>
                results
            </div>
        </div>

        {{-- NO RESULTS --}}
        <div x-show="filteredResults.length === 0" x-cloak class="catalog-no-results">
            <img src="{{ asset('images/no-results.gif') }}" alt="No results found">

            <h3>No results found</h3>

            <p>
                No resources matched
                "<span x-text="search || isbn || 'your filters'"></span>".
            </p>

            <button type="button" class="catalog-filter-btn" @click="clearFilters()">
                Clear Filters
            </button>
        </div>

        {{-- KEEP OLD RESOURCE CARD UI --}}
        <div x-show="filteredResults.length > 0" x-cloak class="catalog-results-grid">
            @foreach ($resources as $resource)
                <div
                    x-show="visibleIds.includes({{ $resource->id }})"
                    x-cloak
                    class="catalog-card-shell"
                >
                    @include('common.resource-card', [
                        'resource' => $resource,
                        'variant' => 'compact',
                        'borrowBlockReason' => $borrowBlockReason ?? null
                    ])
                </div>
            @endforeach
        </div>

        {{-- CLIENT-SIDE PAGINATION --}}
        <div class="catalog-client-pagination" x-show="totalPages > 1" x-cloak>
            <button
                type="button"
                class="catalog-page-btn"
                :disabled="currentPage === 1"
                @click="previousPage()"
            >
                Previous
            </button>

            <template x-for="page in visiblePages" :key="page">
                <button
                    type="button"
                    class="catalog-page-btn"
                    :class="{ 'active': currentPage === page }"
                    @click="goToPage(page)"
                    x-text="page"
                ></button>
            </template>

            <button
                type="button"
                class="catalog-page-btn"
                :disabled="currentPage === totalPages"
                @click="nextPage()"
            >
                Next
            </button>
        </div>
    </section>
</section>

<script>
    function catalogSearch(config) {
        return {
            search: config.initialSearch || '',
            isbn: '',
            categoryId: '',
            materialTypeId: '',
            availabilityId: '',
            publicationYear: '',
            branchId: '',

            currentPage: 1,
            perPage: 9,

            allResources: config.resources.map(resource => ({
                ...resource,
                _title: (resource.title || '').toLowerCase(),
                _desc: (resource.description || '').toLowerCase(),
                _isbn: (resource.isbn || '').toLowerCase(),
                _authors: (resource.authors || '').toLowerCase(),
                _category: (resource.category_name || '').toLowerCase(),
                _material: (resource.material_type_name || '').toLowerCase(),
                _locations: (resource.library_locations || '').toLowerCase(),
                _availability: (resource.availability_statuses || '').toLowerCase(),
                _copyStatusIds: (resource.copy_status_ids || '').split(',').filter(Boolean),
                _branchIds: (resource.branch_ids || '').split(',').filter(Boolean),
            })),

            get hasActiveSearch() {
                return this.search.trim() !== ''
                    || this.isbn.trim() !== ''
                    || this.categoryId !== ''
                    || this.materialTypeId !== ''
                    || this.availabilityId !== ''
                    || this.publicationYear !== ''
                    || this.branchId !== '';
            },

            get filteredResults() {
                const q = this.search.toLowerCase().trim();
                const isbn = this.isbn.toLowerCase().trim();

                const results = this.allResources
                    .filter(resource => {
                        if (isbn && !resource._isbn.includes(isbn)) {
                            return false;
                        }

                        if (this.categoryId && String(resource.category_id) !== String(this.categoryId)) {
                            return false;
                        }

                        if (this.materialTypeId && String(resource.material_type_id) !== String(this.materialTypeId)) {
                            return false;
                        }

                        if (this.availabilityId && !resource._copyStatusIds.includes(String(this.availabilityId))) {
                            return false;
                        }

                        if (this.publicationYear && String(resource.publication_year) !== String(this.publicationYear)) {
                            return false;
                        }

                        if (this.branchId && !resource._branchIds.includes(String(this.branchId))) {
                            return false;
                        }

                        return true;
                    })
                    .map(resource => {
                        let score = q ? -1 : 10;

                        if (!q) {
                            return {
                                ...resource,
                                _score: score,
                            };
                        }

                        if (resource._title === q) {
                            score = 100;
                        } else if (resource._title.startsWith(q)) {
                            score = 90;
                        } else if (resource._title.split(/\s+/).some(word => word.startsWith(q))) {
                            score = 80;
                        } else if (resource._title.includes(q)) {
                            score = 70;
                        } else if (resource._authors.includes(q)) {
                            score = 65;
                        } else if (resource._isbn.includes(q)) {
                            score = 60;
                        } else if (resource._category.includes(q)) {
                            score = 50;
                        } else if (resource._material.includes(q)) {
                            score = 40;
                        } else if (resource._desc.includes(q)) {
                            score = 30;
                        } else if (resource._locations.includes(q)) {
                            score = 20;
                        } else if (resource._availability.includes(q)) {
                            score = 10;
                        }

                        return {
                            ...resource,
                            _score: score,
                        };
                    })
                    .filter(resource => resource._score >= 0)
                    .sort((a, b) => {
                        if (b._score !== a._score) {
                            return b._score - a._score;
                        }

                        return a._title.localeCompare(b._title);
                    });

                if (this.currentPage > Math.max(1, Math.ceil(results.length / this.perPage))) {
                    this.currentPage = 1;
                }

                return results;
            },

            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredResults.length / this.perPage));
            },

            get paginatedResults() {
                const start = (this.currentPage - 1) * this.perPage;

                return this.filteredResults.slice(start, start + this.perPage);
            },

            get visibleIds() {
                return this.paginatedResults.map(resource => resource.id);
            },

            get startItem() {
                if (this.filteredResults.length === 0) {
                    return 0;
                }

                return ((this.currentPage - 1) * this.perPage) + 1;
            },

            get endItem() {
                return Math.min(this.currentPage * this.perPage, this.filteredResults.length);
            },

            get visiblePages() {
                const pages = [];
                const start = Math.max(1, this.currentPage - 2);
                const end = Math.min(this.totalPages, this.currentPage + 2);

                for (let page = start; page <= end; page++) {
                    pages.push(page);
                }

                return pages;
            },

            goToPage(page) {
                this.currentPage = page;
                this.scrollToResults();
            },

            previousPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.scrollToResults();
                }
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.scrollToResults();
                }
            },

            clearFilters() {
                this.search = '';
                this.isbn = '';
                this.categoryId = '';
                this.materialTypeId = '';
                this.availabilityId = '';
                this.publicationYear = '';
                this.branchId = '';
                this.currentPage = 1;
                this.scrollToResults();
            },

            scrollToResults() {
                const el = document.getElementById('catalogResults');

                if (!el) {
                    return;
                }

                const y = el.getBoundingClientRect().top + window.pageYOffset - 110;

                window.scrollTo({
                    top: y,
                    behavior: 'smooth',
                });
            },
        };
    }
</script>

@endsection