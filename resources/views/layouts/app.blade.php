<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pingvelcake</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40 !important;
        }

        .container {
            margin: 0px;
            min-width: 100%;
        }

        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 30px;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table th, .table td {
            padding: 12px 15px;
            text-align: center;
            vertical-align: middle;
        }

        .table thead {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .table tbody tr {
            border-bottom: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .table tbody tr:nth-of-type(even) {
            background-color: #e9ecef;
        }

        .table td {
            border-right: 1px solid #dee2e6;
        }

        .table td:last-child {
            border-right: none;
        }

        .btn-primary, .btn-success {
            border-radius: 5px;
            padding: 8px 12px;
        }

        .btn-danger {
            border-radius: 5px;
            padding: 6px 10px;
        }

        .modal-content {
            border-radius: 10px;
            padding: 15px;
        }

        .table tbody tr:hover {
            background-color: #d6e4ff;
            transition: 0.3s;
        }

        .modal-content {
            border-radius: 10px;
        }

        #detailPengeluaranModal .form-control {
            background-color: #f9f9f9;
            border: none;
            padding: 6px 10px;
            font-size: 14px;
        }

        #detailPengeluaranModal label {
            font-weight: bold;
            margin-bottom: 3px;
        }

        #detailPengeluaranModal .modal-body {
            padding: 20px;
        }

        #detailPengeluaranModal table {
            margin-top: 15px;
            border-collapse: collapse;
            width: 100%;
        }

        #detailPengeluaranModal table th,
        #detailPengeluaranModal table td {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }

        #detailPengeluaranModal table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        .pagination li svg {
            width: 6px;
            height: 6px;
        }


    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">pingvelcake</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pemasukan.index') }}">Pemasukan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pengeluaran.index') }}">Pengeluaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('rekap.index') }}">Rekap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cashflow.index') }}">Cashflow Note</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
