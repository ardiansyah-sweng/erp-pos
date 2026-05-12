<!DOCTYPE html>
<html lang="id">
<!-- triger -->
<head>
    <meta charset="UTF-8">
    <title>POS - Point of Sale</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
        }

        .header-box,
        .cart-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .logo-box {
            width: 50px;
            height: 50px;
            background: #0d6efd;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .product-card {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 15px;
            text-align: center;
            background: white;
        }

        .product-img img {
            height: 80px;
            object-fit: contain;
        }

        .empty-cart {
            text-align: center;
            color: #94a3b8;
            padding: 40px 0;
        }

        .empty-cart i {
            font-size: 60px;
        }

        .pay-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #16a34a;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container mt-4">

        <!-- HEADER -->
        <div class="header-box d-flex justify-content-between align-items-center mb-4">

            <div class="d-flex align-items-center gap-3">
                <div class="logo-box">
                    <i class="bi bi-cart3"></i>
                </div>

                <div>
                    <h4 class="mb-0">POS - Point of Sale</h4>
                    <small class="text-muted">Sistem Kasir</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-4">

                <div>
                    <i class="bi bi-calendar"></i>
                    <span id="date"></span>
                    <br>

                    <i class="bi bi-clock"></i>
                    <span id="time"></span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-light p-2">
                        <i class="bi bi-person"></i>
                    </div>

                    <div>
                        <b>Kasir</b><br>
                        <small>Admin</small>
                    </div>
                </div>

            </div>
        </div>

        <!-- INPUT SKU -->
        <div class="card p-3 mb-4">
            <label class="mb-2">Scan / Input SKU</label>

            <input
                type="text"
                class="form-control"
                placeholder="Masukkan SKU produk">
        </div>

        <div class="row">

            <!-- PRODUK -->
            <div class="col-md-8">
                <div class="card p-3">

                    <h5>Daftar Produk</h5>

                    <div class="row mt-3">

                        <!-- PRODUK 1 -->
                        <div class="col-md-4 mb-3">
                            <div class="product-card">

                                <div class="product-img">
                                    <img src="https://via.placeholder.com/80" alt="Air Mineral">
                                </div>

                                <h6>Air Mineral</h6>

                                <small>SKU: AM001</small>

                                <p class="text-primary fw-bold mt-2">
                                    Rp 4.000
                                </p>

                                <button class="btn btn-primary btn-sm w-100">
                                    Tambah
                                </button>

                            </div>
                        </div>

                        <!-- PRODUK 2 -->
                        <div class="col-md-4 mb-3">
                            <div class="product-card">

                                <div class="product-img">
                                    <img src="https://via.placeholder.com/80" alt="Indomie">
                                </div>

                                <h6>Indomie</h6>

                                <small>SKU: IND01</small>

                                <p class="text-primary fw-bold mt-2">
                                    Rp 3.500
                                </p>

                                <button class="btn btn-primary btn-sm w-100">
                                    Tambah
                                </button>

                            </div>
                        </div>

                        <!-- PRODUK 3 -->
                        <div class="col-md-4 mb-3">
                            <div class="product-card">

                                <div class="product-img">
                                    <img src="https://via.placeholder.com/80" alt="Coca Cola">
                                </div>

                                <h6>Coca Cola</h6>

                                <small>SKU: CC01</small>

                                <p class="text-primary fw-bold mt-2">
                                    Rp 6.500
                                </p>

                                <button class="btn btn-primary btn-sm w-100">
                                    Tambah
                                </button>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- KERANJANG -->
            <div class="col-md-4">
                <div class="cart-box">

                    <div class="d-flex justify-content-between mb-3">
                        <h5>
                            <i class="bi bi-cart"></i> Keranjang
                        </h5>

                        <button class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                    <div class="empty-cart">
                        <i class="bi bi-cart-x"></i>
                        <p>Keranjang masih kosong</p>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span>Rp 0</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Pajak</span>
                        <span>Rp 0</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>Rp 0</span>
                    </div>

                    <button class="pay-btn mt-3">
                        <i class="bi bi-cash-stack"></i> Bayar
                    </button>

                </div>
            </div>

        </div>
    </div>

    <!-- SCRIPT JAM -->
    <script>
        function updateDateTime() {
            const now = new Date();

            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };

            document.getElementById("date").innerText =
                now.toLocaleDateString('id-ID', options);

            document.getElementById("time").innerText =
                now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

</body>

</html>