@php
    $item = $resource ?? $book ?? null;

    $variant = $variant ?? 'full';

    $coverValue = $item?->cover_image_path ?? null;

    $cover = !empty($coverValue)
        ? (\Illuminate\Support\Str::startsWith($coverValue, ['http://', 'https://'])
            ? $coverValue
            : asset('storage/' . $coverValue))
        : asset('images/auth-bg/bg4.jpg');

    $isAvailable = (int) ($item?->available_copies ?? 0) > 0;
    $isDigital = (bool) ($item?->is_digital ?? false);
    $isReferenceOnly = (bool) ($item?->is_reference_only ?? false);

    $borrowBlockReason = $borrowBlockReason ?? null;

    $canReserveBorrow =
        empty($borrowBlockReason)
        && $isAvailable
        && !$isReferenceOnly;

    if (!$borrowBlockReason) {
        if (!$isAvailable && !$isDigital) {
            $borrowBlockReason = 'Not Available';
        } elseif ($isReferenceOnly) {
            $borrowBlockReason = 'Room Use Only';
        }
    }
@endphp

@if ($item)
    <article class="resource-card {{ $variant === 'compact' ? 'resource-card-compact' : 'resource-card-full' }}">

        <div class="resource-card-cover" style="background-image: url('{{ $cover }}');">
            <span class="resource-card-type">
                {{ $item->material_type_name ?? 'Library Material' }}
            </span>
        </div>

        <div class="resource-card-body">
            <h3 class="resource-card-title">
                {{ $item->title }}
            </h3>

            <p class="resource-card-author">
                {{ $item->authors ?? 'Unknown Author' }}
            </p>

            <p class="resource-card-description">
                {{ \Illuminate\Support\Str::limit($item->description ?? 'No description available.', $variant === 'compact' ? 85 : 115) }}
            </p>

            @if ($variant === 'full')
                <div class="resource-card-details">
                    <span>
                        <i class="bi bi-bookmark"></i>
                        {{ $item->category_name ?? 'Uncategorized' }}
                    </span>

                    <span>
                        <i class="bi bi-calendar3"></i>
                        {{ $item->publication_year ?? 'No year' }}
                    </span>

                    <span>
                        <i class="bi bi-geo-alt"></i>
                        {{ $item->library_locations ?? 'No location' }}
                    </span>
                </div>
            @endif

            <div class="resource-card-meta">
                @if ($isAvailable)
                    <span class="resource-status available">
                        {{ $item->available_copies }} available
                    </span>
                @elseif ($isDigital)
                    <span class="resource-status digital">
                        Digital Access
                    </span>
                @elseif ($isReferenceOnly)
                    <span class="resource-status room-use">
                        Room Use Only
                    </span>
                @else
                    <span class="resource-status unavailable">
                        {{ $item->availability_statuses ?? 'Not Available' }}
                    </span>
                @endif

                <span class="resource-borrow-count">
                    <i class="bi bi-arrow-repeat"></i>
                    {{ $item->borrow_count ?? 0 }}
                </span>
            </div>

            <div class="resource-card-actions">
                <a href="{{ url('/catalog/' . $item->id) }}" class="resource-action secondary">
                    View Details
                </a>

                @if ($isDigital && !empty($item->digital_url))
                    <a href="{{ $item->digital_url }}" target="_blank" class="resource-action primary">
                        Access Online
                    </a>
                @elseif ($canReserveBorrow)
                    <a href="{{ url('/reservations/create?resource_id=' . $item->id) }}" class="resource-action primary">
                        Reserve/Borrow
                    </a>
                @else
                    <span class="resource-action disabled" title="{{ $borrowBlockReason }}">
                        {{ $borrowBlockReason }}
                    </span>
                @endif
            </div>
        </div>

    </article>
@endif
