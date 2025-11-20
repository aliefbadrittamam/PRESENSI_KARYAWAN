@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="container-desktop">
    <!-- Header Component -->
    @include('user.components.header', [
        'karyawan' => $karyawan
    ])

    <!-- Welcome Card Component -->
    @include('user.components.welcome-card', [
        'greeting' => $greeting,
        'karyawan' => $karyawan,
        'shift' => $shift
    ])

    <!-- Status Cards Component -->
    @include('user.components.status-cards', [
        'presensiHariIni' => $presensiHariIni
    ])

    <!-- Attendance Summary Component -->
    @include('user.components.attendance-summary', [
        'rekapBulan' => $rekapBulan,
        'months' => $months
    ])

    <!-- Weekly Summary Component -->
    @include('user.components.weekly-summary', [
        'presensiMingguIni' => $presensiMingguIni
    ])
</div>

<!-- Bottom Navigation Component -->
@include('user.components.bottom-nav')

<!-- Sidebar Menu Component -->
@include('user.components.sidebar-menu')
@endsection

@push('scripts')
<script>
    // Month filter change
    document.getElementById('monthFilter')?.addEventListener('change', function() {
        window.location.href = '{{ route('karyawan.dashboard') }}?month=' + this.value;
    });
</script>
@endpush