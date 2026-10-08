@extends('layouts.app')

@section('content')
<script>
    function showDetail(data) {
        let pengeluaran = JSON.parse(data);
        console.log("Pengeluaran Data:", pengeluaran);

        document.getElementById('detail_nomor_bill').textContent = pengeluaran.nomor_bill;
        document.getElementById('detail_tanggal').textContent = pengeluaran.tanggal;
        document.getElementById('detail_jenis').textContent = pengeluaran.jenis;
        document.getElementById('detail_penjual').textContent = pengeluaran.penjual;
        document.getElementById('detail_tujuan').textContent = pengeluaran.tujuan;
        document.getElementById('detail_ongkir_ppn_disc').textContent = pengeluaran.ongkir_ppn_disc;
        document.getElementById('detail_total').textContent = pengeluaran.total;

        let tableBody = document.getElementById("rincianTableBody");

        if (!tableBody) {
            console.error("Element <tbody> rincianTableBody tidak ditemukan.");
            return;
        }

        tableBody.innerHTML = "";

        fetch(`/pengeluaran/${pengeluaran.id}/detail`)
            .then(response => response.json())
            .then(data => {
                console.log("API Response:", data);
                if (data.rincian && data.rincian.length > 0) {
                    data.rincian.forEach(item => {
                        let row = `<tr>
                            <td>${item.nama_barang}</td>
                            <td>${item.quantity}</td>
                            <td>${item.subtotal}</td>
                        </tr>`;
                        tableBody.innerHTML += row;
                    });
                } else {
                    console.warn("Tidak ada rincian pengeluaran untuk ID ini.");
                }
            })
            .catch(error => console.error('Error fetching rincian:', error));

        var myModal = new bootstrap.Modal(document.getElementById('detailPengeluaranModal'));
        myModal.show();

    }

    function editPengeluaran(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nomor_bill').value = data.nomor_bill;
        document.getElementById('edit_tanggal').value = data.tanggal;
        document.getElementById('edit_jenis').value = data.jenis;
        document.getElementById('edit_penjual').value = data.penjual;
        document.getElementById('edit_tujuan').value = data.tujuan;
        document.getElementById('edit_ongkir_ppn_disc').value = data.ongkir_ppn_disc;
        document.getElementById('edit_total').value = data.total;
        document.getElementById('editPengeluaranForm').action = `/pengeluaran/${data.id}`;

        const rincianContainer = document.getElementById("editRincianPengeluaranContainer");
        rincianContainer.innerHTML = "";

        fetch(`/pengeluaran/${data.id}/detail`)
            .then(response => response.json())
            .then(res => {
                if (res.rincian && res.rincian.length > 0) {
                    res.rincian.forEach((item, index) => {
                        let row = `
                            <div class="row rincian-item mb-2">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="rincian[${index}][nama_barang]" value="${item.nama_barang}" placeholder="Nama Barang" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control rincian-qty" name="rincian[${index}][quantity]" value="${item.quantity}" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control rincian-subtotal" name="rincian[${index}][subtotal]" value="${item.subtotal}" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-rincian">X</button>
                                </div>
                            </div>
                        `;
                        rincianContainer.innerHTML += row;
                    });
                }
            });

        new bootstrap.Modal(document.getElementById('editPengeluaranModal')).show();
    }

</script>

<div class="container">
    <h2>Daftar Pengeluaran</h2>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPengeluaranModal">
        Tambah Pengeluaran
    </button>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Bill</th>
                <th>Tanggal</th>
                <th>Penjual</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengeluaran as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nomor_bill }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->penjual }}</td>
                    <td>Rp {{ number_format($item->total, 2) }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-info btn-sm" onclick="showDetail('{{ json_encode($item) }}')">Detail</button>

                            <button class="btn btn-warning btn-sm" onclick="editPengeluaran({{ $item }})">Edit</button>

                            <form action="{{ route('pengeluaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah Pengeluaran -->
<div class="modal fade" id="tambahPengeluaranModal" tabindex="-1" aria-labelledby="tambahPengeluaranModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPengeluaranModalLabel">Tambah Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pengeluaran.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nomor Bill</label>
                            <input type="text" id="nomor_bill" name="nomor_bill" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tanggal</label>
                            <input type="date" id="tanggal_pengeluaran" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tipe Pembelian</label>
                            <select class="form-control" name="jenis" required>
                                <option value="ecommerce">Ecommerce</option>
                                <option value="offline Store">Offline Store</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Penjual</label>
                            <input type="text" name="penjual" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tujuan</label>
                            <select name="tujuan" class="form-control" required>
                                <option value="" disabled selected>Pilih Tujuan</option>
                                <option value="Restock Bahan">Restock Bahan</option>
                                <option value="Restock Packaging">Restock Packaging</option>
                                <option value="Restock Decorative">Restock Decorative</option>
                                <option value="Pengadaan">Pengadaan</option>

                                <option value="RnD">RnD</option>
                                <option value="Promote">Promote</option>
                                <option value="WFC">WFC</option>
                                <option value="Reward Staf">Reward Staf</option>
                                <option value="Diskon Pelanggan">Diskon Pelanggan</option>
                                <option value="Operasional">Operasional</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Ongkir/PPN/Disc</label>
                            <input type="number" id="ongkir_ppn_disc" name="ongkir_ppn_disc" class="form-control" value="0">
                        </div>
                    </div>

                    <div id="offline-extra" style="display: none;">
                        <div class="mb-3">
                            <label for="cp_offline" class="form-label">Kontak Person (Offline Store)</label>
                            <input type="text" name="cp_offline" class="form-control" placeholder="Nama / No HP toko offline">
                        </div>

                        {{-- <div class="mb-3">
                            <label class="form-label">Lokasi Toko Rekomendasi (Malang)</label>
                            <iframe
                                src="https://www.google.com/maps/embed/v1/search?q=Toko+Bahan+Kue+Malang&key=YOUR_API_KEY"
                                width="100%"
                                height="300"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                            ></iframe>
                        </div> --}}
                    </div>


                    <h5>Rincian Pesanan</h5>
                    <div id="rincianPengeluaranContainer">
                        <div class="row rincian-item mb-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="rincian[0][nama_barang]" placeholder="Nama Barang" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control rincian-qty" name="rincian[0][quantity]" placeholder="Qty" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control rincian-subtotal" name="rincian[0][subtotal]" placeholder="Subtotal" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-rincian">X</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-secondary" id="tambahRincianPengeluaran">Tambah Rincian</button>

                    <div class="mb-3 mt-3">
                        <label>Total Transaksi</label>
                        <input type="number" id="total_pengeluaran" name="total" class="form-control" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailPengeluaranModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Nomor Bill</label>
                        <p id="detail_nomor_bill" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Tanggal</label>
                        <p id="detail_tanggal" class="form-control" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Jenis</label>
                        <p id="detail_jenis" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Penjual</label>
                        <p id="detail_penjual" class="form-control" readonly>
                    </div>
                </div>


                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Tujuan</label>
                        <p id="detail_tujuan" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Ongkir/PPN/Disc</label>
                        <p id="detail_ongkir_ppn_disc" class="form-control" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label>Total</label>
                        <p id="detail_total" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="modal-header">
                <div class="col-md-12">
                    <label>Rincian Produk</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="rincianTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editPengeluaranModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPengeluaranForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <label>Nomor Bill:</label>
                    <input type="text" id="edit_nomor_bill" name="nomor_bill" class="form-control">
                    <label>Tanggal:</label>
                    <input type="date" id="edit_tanggal" name="tanggal" class="form-control">
                    <label>Jenis:</label>
                    <input type="text" id="edit_jenis" name="jenis" class="form-control">
                    <label>Penjual:</label>
                    <input type="text" id="edit_penjual" name="penjual" class="form-control">
                    <label>Tujuan</label>
                    <select id="edit_tujuan" name="tujuan" class="form-control" required>
                        <option value="" disabled selected>Pilih Tujuan</option>
                        <option value="Restock Bahan">Restock Bahan</option>
                        <option value="Restock Packaging">Restock Packaging</option>
                        <option value="Restock Decorative">Restock Decorative</option>
                        <option value="Pengadaan">Pengadaan</option>

                        <option value="RnD">RnD</option>
                        <option value="Promote">Promote</option>
                        <option value="WFC">WFC</option>
                        <option value="Reward Staf">Reward Staf</option>
                        <option value="Diskon Pelanggan">Diskon Pelanggan</option>
                        <option value="Operasional">Operasional</option>
                    </select>
                    <label>Ongkir/PPN/Disc:</label>
                    <input type="text" id="edit_ongkir_ppn_disc" name="ongkir_ppn_disc" class="form-control">
                    <label>Total:</label>
                    <input type="text" id="edit_total" name="total" class="form-control">

                    <h5>Rincian Pengeluaran</h5>
                    <div id="editRincianPengeluaranContainer">
                    </div>

                    <button type="button" class="btn btn-sm btn-secondary" id="tambahEditRincianPengeluaran">Tambah Rincian</button>
                    <button type="submit" class="btn btn-primary mt-2">Simpan Perubahan</button>
                </form>


            </div>
        </div>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function () {
    const jenisSelect = document.querySelector('select[name="jenis"]');
    const offlineExtra = document.getElementById('offline-extra');

    function toggleOfflineExtra() {
        if (jenisSelect.value.toLowerCase() === 'offline store') {
            offlineExtra.style.display = 'block';
        } else {
            offlineExtra.style.display = 'none';
        }
    }

    if (jenisSelect) {
        jenisSelect.addEventListener('change', toggleOfflineExtra);
        toggleOfflineExtra();
    }

    let rincianIndex = 1;

    const tambahRincianBtn = document.getElementById("tambahRincianPengeluaran");
    const rincianContainer = document.getElementById("rincianPengeluaranContainer");
    const ongkirInput = document.getElementById("ongkir_ppn_disc");
    const totalPengeluaranInput = document.getElementById("total_pengeluaran");

    if (tambahRincianBtn && rincianContainer) {
        tambahRincianBtn.addEventListener("click", function () {
            const newRincian = document.createElement("div");
            newRincian.classList.add("row", "rincian-item", "mb-2");
            newRincian.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control" name="rincian[${rincianIndex}][nama_barang]" placeholder="Nama Barang" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control rincian-qty" name="rincian[${rincianIndex}][quantity]" placeholder="Qty" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control rincian-subtotal" name="rincian[${rincianIndex}][subtotal]" placeholder="Subtotal" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-rincian">X</button>
                </div>
            `;
            rincianContainer.appendChild(newRincian);
            rincianIndex++;
        });

        rincianContainer.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-rincian")) {
                e.target.closest(".rincian-item").remove();
                hitungTotalPengeluaran();
            }
        });

        rincianContainer.addEventListener("input", hitungTotalPengeluaran);
    }

    if (ongkirInput) {
        ongkirInput.addEventListener("input", hitungTotalPengeluaran);
    }

    function hitungTotalPengeluaran() {
        let total = 0;
        document.querySelectorAll('.rincian-subtotal').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        let ongkir = parseFloat(ongkirInput?.value) || 0;
        if (totalPengeluaranInput) {
            totalPengeluaranInput.value = (total + ongkir).toFixed(2);
        }
    }

    let rincianEditIndex = 0;
    const tambahEditBtn = document.getElementById('tambahEditRincianPengeluaran');
    const editContainer = document.getElementById("editRincianPengeluaranContainer");

    if (tambahEditBtn && editContainer) {
        tambahEditBtn.addEventListener('click', function () {
            let html = `
                <div class="row rincian-item mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="rincian[${rincianEditIndex}][nama_barang]" placeholder="Nama Barang" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control rincian-qty" name="rincian[${rincianEditIndex}][quantity]" placeholder="Qty" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control rincian-subtotal" name="rincian[${rincianEditIndex}][subtotal]" placeholder="Subtotal" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-rincian">X</button>
                    </div>
                </div>
            `;
            editContainer.insertAdjacentHTML('beforeend', html);
            rincianEditIndex++;
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-rincian')) {
            e.target.closest('.rincian-item').remove();
            hitungTotalPengeluaran();
        }
    });
});
</script>

@endsection
