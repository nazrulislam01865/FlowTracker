@extends('layouts.app')
@section('content')
<livewire:workflow-setup.form :workflow-id="$workflowId" :source-workflow-id="$sourceWorkflowId" />
@endsection
