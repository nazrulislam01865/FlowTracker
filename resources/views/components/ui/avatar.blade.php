@props(['name', 'dark' => false, 'size' => null])
@php($initials = collect(preg_split('/\s+/', trim($name)))->filter()->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode(''))
<span {{ $attributes->class(['avatar', 'dark' => $dark])->merge(['style' => $size ? "width:{$size}px;height:{$size}px;font-size:".max(9, round($size/3.4))."px" : null]) }}>{{ $initials ?: 'FT' }}</span>
