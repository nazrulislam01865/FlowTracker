@props(['size' => 24])
@php
    $size = max(18, (int) $size);
    $style = "width:{$size}px;height:{$size}px;font-size:".max(9, round($size / 3.4)).'px';
@endphp
<span {{ $attributes->class(['avatar', 'ft-inline-live-avatar'])->merge(['style' => $style]) }}>
    <template x-if="avatarUrl">
        <img
            :src="avatarUrl"
            alt=""
            aria-hidden="true"
            decoding="async"
            x-on:error="avatarUrl = ''; savedAvatarUrl = ''"
        >
    </template>
    <span
        class="avatar-initials"
        x-show="!avatarUrl"
        x-text="initials(display)"
        aria-hidden="true"
    ></span>
</span>
