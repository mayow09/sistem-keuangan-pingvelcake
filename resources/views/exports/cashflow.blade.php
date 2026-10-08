<table>
    <tr>
        @foreach (range(strtotime($startDate), strtotime($endDate), 86400) as $time)
            <td><strong>{{ \Carbon\Carbon::parse($time)->format('d M Y') }}</strong></td>
        @endforeach
    </tr>
    <tr>
        @foreach (range(strtotime($startDate), strtotime($endDate), 86400) as $time)
            @php
                $date = date('Y-m-d', $time);
                $transactions = $cashflows->where('tanggal', $date);
            @endphp
            <td>
                @foreach ($transactions as $trans)
                    <div style="background-color: {{ $trans->tipe == 'Pemasukan' ? '#c6efce' : '#ffc7ce' }}; padding: 5px;">
                        <strong>{{ $trans->tipe }}</strong><br>
                        {{ $trans->keterangan }}<br>
                        Rp {{ number_format($trans->jumlah, 0, ',', '.') }}
                    </div>
                @endforeach
            </td>
        @endforeach
    </tr>
</table>
