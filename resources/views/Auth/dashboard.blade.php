@extends('layouts.app')
@section('title','Dashboard')

@section('content')

{{-- HERO --}}
<section class="hero">
    <div class="hero-left">
        <p class="subtitle">Welcome To Tracking</p>
        <h1 class="title">{{ $stats['user_name'] }}</h1>
    </div>

    {{-- kanan hero: kotak ilustrasi dengan border halus, sesuai CSS --}}
    <div class="hero-right">
        <img
            src="{{ asset('images/dashboard-background.png') }}"
            alt="Dashboard background"
            class="hero-illustration"
            loading="lazy">
    </div>
</section>

{{-- METRIC CARDS --}}
<section class="cards">
    <div class="card">
        <div class="card-title">Total complete project</div>
        <div class="card-value">{{ $stats['total_complete'] }}</div>
        <div class="card-footer">Filter</div>
    </div>
    <div class="card">
        <div class="card-title">Total incomplete project</div>
        <div class="card-value">{{ $stats['total_incomplete'] }}</div>
        <div class="card-footer">Filter</div>
    </div>
    <div class="card">
        <div class="card-title">Total overdue project</div>
        <div class="card-value">{{ $stats['total_overdue'] }}</div>
        <div class="card-footer">Filter</div>
    </div>
    <div class="card">
        <div class="card-title">Total project</div>
        <div class="card-value">{{ $stats['total_project'] }}</div>
        <div class="card-footer">Filter</div>
    </div>
</section>
    
<script>
    window.DASHBOARD_DATA = {
        incompleteBreakdown: @json(array_values($stats['incomplete_breakdown'])),
        taskCompletion: @json(array_values($stats['task_completion'])),
    };
</script>
@endsection
