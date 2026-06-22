@php
    $items = $items ?? collect();
    $icon = $icon ?? 'bi-list';
    $empty = $empty ?? 'No records found.';

    $actions = $actions ?? [];

    if (($actionLabel ?? null) && ($actionUrl ?? null)) {
        $actions[] = [
            'label' => $actionLabel,
            'url' => $actionUrl,
            'icon' => 'bi-arrow-right',
        ];
    }
@endphp

<article class="profile-soft-panel">
    <div class="profile-card-header">
        <div>
            <span class="profile-dashboard-eyebrow">Account Dashboard</span>

            <h2>
                <i class="bi {{ $icon }}"></i>
                {{ $title }}
            </h2>
        </div>

        @if (!empty($actions))
            <div class="profile-panel-actions">
                @foreach ($actions as $action)
                    <a href="{{ $action['url'] }}" class="profile-panel-action">
                        {{ $action['label'] }}
                        <i class="bi {{ $action['icon'] ?? 'bi-arrow-right' }}"></i>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="profile-record-list">
        @forelse ($items as $item)
            <div class="profile-record-item">
                <div>
                    <h3>{{ $item->title ?? 'Untitled Resource' }}</h3>

                    <p>
                        @if (!empty($item->reservation_type))
                            Type: {{ $item->reservation_type }}
                            <br>
                        @endif

                        @if (!empty($item->isbn))
                            ISBN: {{ $item->isbn }}
                            <br>
                        @endif

                        @if (!empty($item->accession_number))
                            Accession: {{ $item->accession_number }}
                            <br>
                        @endif

                        @if (!empty($item->status_name))
                            Status: {{ $item->status_name }}
                            <br>
                        @endif

                        @if (!empty($item->due_at))
                            Due: {{ \Carbon\Carbon::parse($item->due_at)->format('M d, Y') }}
                            <br>
                        @endif

                        @if (!empty($item->expires_at))
                            Expires: {{ \Carbon\Carbon::parse($item->expires_at)->format('M d, Y') }}
                            <br>
                        @endif

                        @if (!empty($item->reservation_date))
                            Date: {{ \Carbon\Carbon::parse($item->reservation_date)->format('M d, Y') }}
                            <br>
                        @endif

                        @if (!empty($item->start_time) && !empty($item->end_time))
                            Time:
                            {{ \Carbon\Carbon::parse($item->start_time)->format('g:i A') }}
                            –
                            {{ \Carbon\Carbon::parse($item->end_time)->format('g:i A') }}
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <div class="profile-empty-state">
                <i class="bi {{ $icon }}"></i>
                <p>{{ $empty }}</p>
            </div>
        @endforelse
    </div>
</article>