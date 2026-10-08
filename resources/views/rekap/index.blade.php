@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Rekap Pemasukan & Pengeluaran</h2>
    
    <form method="GET" action="{{ route('rekap.index') }}" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="bulan" class="col-form-label">Filter Bulan:</label>
            </div>
            <div class="col-auto">
                <input type="month" id="bulan" name="bulan" class="form-control"
                       value="{{ request('bulan') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('rekap.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
                <th>Margin</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekap as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $item->bulan)->translatedFormat('F Y') }}</td>
                    <td>Rp {{ number_format($item->pemasukan, 2) }}</td>
                    <td>Rp {{ number_format($item->pengeluaran, 2) }}</td>
                    <td>Rp {{ number_format($item->margin, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
