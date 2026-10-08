<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pemasukan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }

        @media print {
            @page {
                size: landscape;
            }
            body {
                transform: rotate(270deg);
                transform-origin: top left;
                width: 100vh;
                height: 100vw;
                position: absolute;
                top: 100%;
                left: 0;
            }
        }

    </style>
</head>
<body>
    <h2>Laporan Pemasukan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Invoice</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>NO HP</th>
                <th>Sosial Media</th>
                <th>Pengiriman</th>
                <th>Subtotal</th>
                <th>Diskon</th>
                <th>Ongkir</th>
                <th>Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pemasukan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nomor_invoice }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->customer }}</td>
                    <td>{{ $item->no_hp }}</td>
                    <td>{{ $item->social_media }}</td>
                    <td>{{ $item->pengiriman }}</td>
                    <td>{{ $item->sub_total }}</td>
                    <td>{{ $item->diskon }}</td>
                    <td>{{ $item->ongkir }}</td>
                    <td>Rp {{ number_format($item->total_transaksi, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
