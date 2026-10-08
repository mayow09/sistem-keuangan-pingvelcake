@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center">Catatan Cashflow</h2>

    <!-- Form Filter -->
    <form action="{{ route('cashflow.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="start_date">Tanggal Mulai:</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label for="end_date">Tanggal Akhir:</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    {{-- <div class="col-md-4 d-flex align-items-end">
        <a href="{{ route('cashflow.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success mb-3">
            Export Excel
        </a>
    </div> --}}

    <div class="row mt-3">
        @php
            $allDates = collect(range(strtotime($startDate), strtotime($endDate), 86400))
                        ->map(fn($time) => date('Y-m-d', $time));
        @endphp

        @foreach ($allDates as $tanggal)
            @php
                $transactions = $cashflows->where('tanggal', $tanggal);
            @endphp

            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-header bg-light text-center">
                        <strong>{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</strong>
                    </div>
                    <div class="card-body p-2">
                        @foreach ($transactions as $trans)
                            <div class="p-2 mb-2 rounded
                                {{ $trans->tipe == 'Pemasukan' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                <small><strong>{{ $trans->tipe }}</strong></small><br>
                                {{ $trans->keterangan }}<br>
                                <strong>Rp {{ number_format($trans->jumlah, 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                        @if ($transactions->isEmpty())
                            <p class="text-muted text-center">Tidak ada transaksi</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
