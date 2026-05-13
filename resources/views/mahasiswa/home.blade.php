@extends('layouts.mahasiswa')

@section('title', 'Beranda')

@push('styles')
<style>
.hero {
    max-width: 1100px;
    margin: 48px auto;
    padding: 0 24px;
}

.hero-card {
    background: linear-gradient(135deg, #c8d3ee 0%, #b8c4e0 40%, #a8b6d4 100%);
    border-radius: 24px;
    padding: 72px 48px;
    text-align: center;
}

.hero-title {
    font-family: 'Sora', sans-serif;
    font-size: 52px;
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 16px;
}

.hero-subtitle {
    font-size: 15px;
    color: rgba(21,32,64,0.65);
    margin-bottom: 36px;
}

.hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--navy);
    color: white;
    padding: 14px 32px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
}
</style>
@endpush

@section('content')

<section class="hero">
    <div class="hero-card">

        <h1 class="hero-title">
            LAPORAN<br>
            DAN KELUHAN
        </h1>

        <p class="hero-subtitle">
            Sampaikan keluhan laboratorium kepada para asisten
        </p>

        <a href="{{ route('mahasiswa.form') }}" class="hero-btn">
            Buat Laporan
        </a>

    </div>
</section>

@endsection