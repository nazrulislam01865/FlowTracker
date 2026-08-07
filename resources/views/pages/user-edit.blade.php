@extends('layouts.app')
@section('content')
<livewire:user-editor.index :user-id="$user->id" :profile-mode="$profileMode ?? false" />
@endsection
