@props(['size' => 28])

<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 32 32" fill="none" {{ $attributes->class('text-primary') }}>
    <rect width="32" height="32" rx="8" fill="currentColor" />
    <path d="M9 10v12M9 16h6a4 4 0 100-6H9" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="22" cy="20" r="3" stroke="#fff" stroke-width="2" fill="none"/>
    <line x1="22" y1="10" x2="22" y2="17" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
</svg>
