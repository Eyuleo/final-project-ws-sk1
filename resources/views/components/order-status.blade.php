@props(['status', 'size' => 'md'])

@php
    $statusConfig = [
        'pending' => [
            'label' => 'Pending',
            'color' => 'yellow',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'accepted' => [
            'label' => 'Accepted',
            'color' => 'blue',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'in_progress' => [
            'label' => 'In Progress',
            'color' => 'indigo',
            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
        ],
        'delivered' => [
            'label' => 'Delivered',
            'color' => 'purple',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'completed' => [
            'label' => 'Completed',
            'color' => 'green',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'revision_requested' => [
            'label' => 'Revision Requested',
            'color' => 'orange',
            'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        ],
        'cancelled' => [
            'label' => 'Cancelled',
            'color' => 'red',
            'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'declined' => [
            'label' => 'Declined',
            'color' => 'red',
            'icon' => 'M6 18L18 6M6 6l12 12',
        ],
        'disputed' => [
            'label' => 'Disputed',
            'color' => 'red',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
    ];

    $config = $statusConfig[$status] ?? $statusConfig['pending'];
    $color = $config['color'];
    
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
        'lg' => 'px-3 py-1.5 text-base',
    ];
    
    $iconSizes = [
        'sm' => 'w-3 h-3',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
    ];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full font-medium {$sizeClasses[$size]} bg-{$color}-100 text-{$color}-800"]) }}>
    <svg class="{{ $iconSizes[$size] }} mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}" />
    </svg>
    {{ $config['label'] }}
</span>
