@extends('layouts.app')

@section('content')
<script>
    function showDetail(data) {
        let pemasukan = JSON.parse(data);

        document.getElementById('detail_nomor_invoice').textContent = pemasukan.nomor_invoice;
        document.getElementById('detail_tanggal').textContent = pemasukan.tanggal;
        document.getElementById('detail_customer').textContent = pemasukan.customer;
        document.getElementById('detail_no_hp').textContent = pemasukan.no_hp;
        document.getElementById('detail_social_media').textContent = pemasukan.social_media;
        document.getElementById('detail_pengiriman').textContent = pemasukan.pengiriman;
        document.getElementById('detail_sub_total').textContent = pemasukan.sub_total;
        document.getElementById('detail_diskon').textContent = pemasukan.diskon;
        document.getElementById('detail_ongkir').textContent = pemasukan.ongkir;
        document.getElementById('detail_total_transaksi').textContent = pemasukan.total_transaksi;
        document.getElementById('detail_jadwal_pickup').textContent = pemasukan.jadwal_pickup || "";

        if (pemasukan.pengiriman === 'self pickup') {
            document.getElementById('detail_jadwal_pickup_div').style.display = 'block';
        } else {
            document.getElementById('detail_jadwal_pickup_div').style.display = 'none';
        }

        var myModal = new bootstrap.Modal(document.getElementById('detailPemasukanModal'));
        myModal.show();
    }

    function showEdit(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nomor_invoice').value = data.nomor_invoice;
        document.getElementById('edit_tanggal').value = data.tanggal;
        document.getElementById('edit_customer').value = data.customer;
        document.getElementById('edit_no_hp').value = data.no_hp;
        document.getElementById('edit_social_media').value = data.social_media;
        document.getElementById('edit_pengiriman').value = data.pengiriman;
        document.getElementById('edit_sub_total').value = data.sub_total;
        document.getElementById('edit_diskon').value = data.diskon;
        document.getElementById('edit_ongkir').value = data.ongkir;
        document.getElementById('edit_total_transaksi').value = data.total_transaksi;
        document.getElementById('edit_jadwal_pickup').value = data.jadwal_pickup || "";

        const editJadwalDiv = document.getElementById('editJadwalPickupDiv');
        if (data.pengiriman === 'self pickup') {
            editJadwalDiv.style.display = 'block';
        } else {
            editJadwalDiv.style.display = 'none';
        }

        document.getElementById('edit_pengiriman').addEventListener('change', function () {
            if (this.value === 'self pickup') {
                editJadwalDiv.style.display = 'block';
            } else {
                editJadwalDiv.style.display = 'none';
            }
        });

        document.getElementById('editPemasukanForm').action = `/pemasukan/${data.id}`;

        new bootstrap.Modal(document.getElementById('editPemasukanModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const pengirimanSelect = document.querySelector('select[name="pengiriman"]');
        const jadwalPickupDiv = document.getElementById('jadwalPickupDiv');

        function togglePickupSchedule() {
            if (pengirimanSelect.value.toLowerCase() === 'self pickup') {
                jadwalPickupDiv.style.display = 'block';
            } else {
                jadwalPickupDiv.style.display = 'none';
            }
        }

        pengirimanSelect.addEventListener('change', togglePickupSchedule);

        // Panggil saat load juga
        togglePickupSchedule();
    });
</script>
<div class="container">
    <h2>Pemasukan</h2>
    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPemasukanModal">Tambah Pemasukan</a>
    <a href="{{ route('pemasukan.report') }}" class="btn btn-success">Unduh Laporan PDF</a>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Invoice</th>
                <th>Customer</th>
                <th>No HP/WA</th>
                <th>Social Media Account</th>
                <th>Pengiriman</th>
                {{-- <th>Subtotal</th>
                <th>Diskon</th>
                <th>Ongkir</th> --}}
                <th>Total Transaksi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pemasukan as $item)
                <tr>
                    {{-- <td>{{ $loop->iteration }}</td> --}}
                    <td>{{ ($pemasukan->currentPage() - 1) * $pemasukan->perPage() + $loop->iteration }}</td>
                    <td>{{ $item->nomor_invoice }}</td>
                    <td>{{ $item->customer }}</td>
                    <td>{{ $item->no_hp }}</td>
                    <td>{{ $item->social_media }}</td>
                    <td>{{ $item->pengiriman }}</td>
                    <td>Rp {{ number_format($item->total_transaksi, 2) }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-info btn-sm" onclick="showDetail('{{ json_encode($item) }}')">Detail</button>
                            <button class="btn btn-warning btn-sm" onclick="showEdit({{ $item }})">Edit</button>
                            <form action="{{ route('pemasukan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $pemasukan->links() }}
    </div>
</div>

<!-- Modal Tambah Pemasukan -->
<div class="modal fade" id="tambahPemasukanModal" tabindex="-1" aria-labelledby="tambahPemasukanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPemasukanModalLabel">Tambah Pemasukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pemasukan.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Nomor Invoice</label>
                            <input type="text" id="nomor_invoice" name="nomor_invoice" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Customer</label>
                            <input type="text" name="customer" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>No HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Social Media</label>
                            <input type="text" name="social_media" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Pengiriman</label>
                            <select class="form-control" name="pengiriman" required>
                                <option value="delivery">Delivery</option>
                                <option value="gosend">Gosend</option>
                                <option value="grapExpress">GrabExpress</option>
                                <option value="paxel">Paxel</option>
                                <option value="self pickup">Self-Pickup</option>
                                <option value="travel">Travel</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6" id="jadwalPickupDiv" style="display: none;">
                            <label>Jadwal Pickup</label>
                            <input type="datetime-local" class="form-control" name="jadwal_pickup">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Subtotal</label>
                            <input type="number" id="sub_total" name="sub_total" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Diskon</label>
                            <input type="number" id="diskon" name="diskon" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Ongkir</label>
                            <input type="number" id="ongkir" name="ongkir" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label>Total Transaksi</label>
                            <input type="number" id="total_transaksi" name="total_transaksi" class="form-control" readonly>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pemasukan -->
<div class="modal fade" id="detailPemasukanModal" tabindex="-1" aria-labelledby="detailPemasukanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailPemasukanModalLabel">Detail Pemasukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Nomor Invoice</label>
                        <p id="detail_nomor_invoice" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Tanggal</label>
                        <p id="detail_tanggal" class="form-control" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Customer</label>
                        <p id="detail_customer" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>No HP</label>
                        <p id="detail_no_hp" class="form-control" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Social Media</label>
                        <p id="detail_social_media" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Pengiriman</label>
                        <p id="detail_pengiriman" class="form-control" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6" id="detail_jadwal_pickup_div" style="display: none;">
                        <label>Jadwal Pickup</label>
                        <p id="detail_jadwal_pickup" class="form-control" readonly></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Subtotal</label>
                        Rp. <p id="detail_sub_total" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Diskon</label>
                        <p id="detail_diskon" class="form-control" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Ongkir</label>
                        Rp. <p id="detail_ongkir" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Total Transaksi</label>
                        Rp. <p id="detail_total_transaksi" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Pemasukan -->
<div class="modal fade" id="editPemasukanModal" tabindex="-1" aria-labelledby="editPemasukanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPemasukanModalLabel">Edit Pemasukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPemasukanForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Nomor Invoice</label>
                            <input type="text" id="edit_nomor_invoice" name="nomor_invoice" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Tanggal</label>
                            <input type="date" id="edit_tanggal" name="tanggal" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Customer</label>
                            <input type="text" id="edit_customer" name="customer" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>No HP</label>
                            <input type="text" id="edit_no_hp" name="no_hp" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Social Media</label>
                            <input type="text" id="edit_social_media" name="social_media" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Pengiriman</label>
                            <select class="form-control" id="edit_pengiriman" name="pengiriman" required>
                                <option value="delivery">Delivery</option>
                                <option value="gosend">Gosend</option>
                                <option value="grapExpress">GrabExpress</option>
                                <option value="paxel">Paxel</option>
                                <option value="self pickup">Self-Pickup</option>
                                <option value="travel">Travel</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6" id="editJadwalPickupDiv" >
                            <label>Jadwal Pickup</label>
                            <input type="datetime-local" id="edit_jadwal_pickup" name="jadwal_pickup" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Subtotal</label>
                            <input type="number" id="edit_sub_total" name="sub_total" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Diskon</label>
                            <input type="number" id="edit_diskon" name="diskon" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Ongkir</label>
                            <input type="number" id="edit_ongkir" name="ongkir" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label>Total Transaksi</label>
                            <input type="number" id="edit_total_transaksi" name="total_transaksi" class="form-control" readonly>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tanggalInput = document.getElementById('tanggal');
        const nomorInvoiceInput = document.getElementById('nomor_invoice');
        const subTotalInput = document.getElementById('sub_total');
        const diskonInput = document.getElementById('diskon');
        const ongkirInput = document.getElementById('ongkir');
        const totalTransaksiInput = document.getElementById('total_transaksi');

        const pengirimanSelect = document.querySelector('[name="pengiriman"]');
        const jadwalDiv = document.getElementById('jadwalPickupDiv');



        pengirimanSelect.addEventListener('change', function () {
            if (this.value === 'self pickup') {
                jadwalDiv.style.display = 'block';
            } else {
                jadwalDiv.style.display = 'none';
            }
        });

        tanggalInput.addEventListener('change', function () {
            if (this.value) {
                const date = new Date(this.value);
                const bulan = String(date.getMonth() + 1).padStart(2, '0');
                const tahun = String(date.getFullYear()).slice(-2);

                fetch(`/get-latest-invoice/${bulan}/${tahun}`)
                    .then(response => response.json())
                    .then(data => {
                        let nomorUrut = String(data.latest + 1).padStart(2, '0');
                        nomorInvoiceInput.value = `${nomorUrut}${bulan}${tahun}`;
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        function hitungTotal() {
            let subTotal = parseFloat(subTotalInput.value) || 0;
            let diskon = parseFloat(diskonInput.value) || 0;
            let ongkir = parseFloat(ongkirInput.value) || 0;
            let total = subTotal - diskon + ongkir;
            totalTransaksiInput.value = total.toFixed(2);
        }

        subTotalInput.addEventListener('input', hitungTotal);
        diskonInput.addEventListener('input', hitungTotal);
        ongkirInput.addEventListener('input', hitungTotal);
    });

    function hitungTotalEdit() {
        const subTotal = parseFloat(document.getElementById('edit_sub_total').value) || 0;
        const diskon = parseFloat(document.getElementById('edit_diskon').value) || 0;
        const ongkir = parseFloat(document.getElementById('edit_ongkir').value) || 0;
        const total = subTotal - diskon + ongkir;
        document.getElementById('edit_total_transaksi').value = total;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('edit_sub_total').addEventListener('input', hitungTotalEdit);
        document.getElementById('edit_diskon').addEventListener('input', hitungTotalEdit);
        document.getElementById('edit_ongkir').addEventListener('input', hitungTotalEdit);
    });
    </script>

@endsection
