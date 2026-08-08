@extends('layouts.app')
@section('content')
@if(request()->hasAny(['open', 'task', 'create']))
    <livewire:jobs.index />
@else
    <livewire:orders.index />
@endif
@endsection
