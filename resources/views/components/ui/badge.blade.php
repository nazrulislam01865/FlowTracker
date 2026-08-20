@props(['label'])
@php
$label = (string) $label;
$class = match (true) {
    preg_match('/Completed|Approved|Paid|Delivered|On Track|Active/i', $label) === 1 => 'b-green',
    preg_match('/Blocked|Critical|Overdue|Revision|Delayed|At Risk/i', $label) === 1 => 'b-red',
    preg_match('/Waiting|Negotiation|Partially|Needs Attention/i', $label) === 1 => 'b-amber',
    preg_match('/In Progress|Submitted|Ready|Transit|Artwork|Shipment|Invoice/i', $label) === 1 => 'b-blue',
    default => 'b-gray',
};
@endphp
<span {{ $attributes->class(['badge', $class]) }}>{{ $label }}</span>
