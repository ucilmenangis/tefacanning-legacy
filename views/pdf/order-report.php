
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pesanan - <?php echo htmlspecialchars(
      $order["order_number"],
    ); ?></title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.5;
        }

        .header {
            border-bottom: 3px solid #dc2626;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
        }

        .company-subtitle {
            font-size: 9px;
            color: #6b7280;
            margin-top: 1px;
        }

        .document-title {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }

        .document-number {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
        }

        .info-section {
            width: 100%;
            margin-bottom: 18px;
        }

        .info-section td {
            vertical-align: top;
            width: 50%;
        }

        .info-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 10px;
        }

        .info-box-neutral {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
        }

        .info-label {
            font-size: 8px;
            font-weight: bold;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .info-label-neutral {
            font-size: 8px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .info-row {
            margin-bottom: 2px;
            font-size: 10px;
        }

        .info-key {
            font-weight: bold;
            color: #374151;
            display: inline-block;
            width: 90px;
        }

        .info-value {
            color: #1f2937;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .products-table thead th {
            background-color: #dc2626;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .products-table thead th:first-child {
            border-radius: 4px 0 0 0;
        }

        .products-table thead th:last-child {
            border-radius: 0 4px 0 0;
            text-align: right;
        }

        .products-table tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .products-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .products-table .text-right {
            text-align: right;
        }

        .products-table .text-center {
            text-align: center;
        }

        .total-row {
            padding: 4px 8px;
            overflow: hidden;
            font-size: 10px;
        }

        .total-label {
            float: left;
            font-weight: bold;
            color: #374151;
        }

        .total-value {
            float: right;
            color: #1f2937;
        }

        .total-grand {
            background-color: #dc2626;
            color: #fff;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 12px;
            overflow: hidden;
            margin-top: 4px;
        }

        .total-grand .total-label,
        .total-grand .total-value {
            color: #fff;
        }

        .pickup-section {
            background-color: #f0fdf4;
            border: 2px dashed #22c55e;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            margin-bottom: 15px;
        }

        .pickup-code {
            font-size: 24px;
            font-weight: bold;
            color: #16a34a;
            letter-spacing: 4px;
            margin: 4px 0;
        }

        .pickup-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-processing { background-color: #dbeafe; color: #1e40af; }
        .status-ready { background-color: #d1fae5; color: #065f46; }
        .status-picked_up { background-color: #f3f4f6; color: #374151; }
    </style>
</head>

<body>
    <!-- Header with Polije logo -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 8%;">
                    <?php
                    $logoPath =
                      __DIR__ . "/../../assets/images/politeknik_logo.png";
                    if (file_exists($logoPath)) {
                      $logoData = base64_encode(file_get_contents($logoPath));
                      echo '<img src="data:image/png;base64,' .
                        $logoData .
                        '" alt="Polije" style="height: 40px; width: auto;">';
                    }
                    ?>
                </td>
                <td style="width: 52%;">
                    <div class="company-name">TEFA Canning SIP</div>
                    <div class="company-subtitle">Teaching Factory Sarden Ikan Perikanan</div>
                    <div class="company-subtitle">Politeknik Negeri Jember</div>
                </td>
                <td style="width: 40%;">
                    <div class="document-title">LAPORAN PESANAN</div>
                    <div class="document-number"><?php echo htmlspecialchars(
                      $order["order_number"],
                    ); ?></div>
                    <div class="document-number"><?php echo date(
                      "d M Y, H:i",
                      strtotime($order["created_at"]),
                    ); ?></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Customer & Order Info -->
    <table class="info-section">
        <tr>
            <td style="padding-right: 8px;">
                <div class="info-box">
                    <div class="info-label">Informasi Pelanggan</div>
                    <div class="info-row"><span class="info-key">Nama</span><span class="info-value">:
                            <?php echo htmlspecialchars(
                              $order["customer_name"],
                            ); ?></span></div>
                    <div class="info-row"><span class="info-key">Telepon</span><span class="info-value">:
                            <?php echo htmlspecialchars(
                              $order["phone"] ?? "-",
                            ); ?></span></div>
                    <div class="info-row"><span class="info-key">Email</span><span class="info-value">:
                            <?php echo htmlspecialchars(
                              $order["email"] ?? "-",
                            ); ?></span></div>
                    <div class="info-row"><span class="info-key">Organisasi</span><span class="info-value">:
                            <?php echo htmlspecialchars(
                              $order["organization"] ?? "-",
                            ); ?></span></div>
                    <div class="info-row"><span class="info-key">Alamat</span><span class="info-value">:
                            <?php echo htmlspecialchars(
                              $order["address"] ?? "-",
                            ); ?></span></div>
                </div>
            </td>
            <td style="padding-left: 8px;">
                <div class="info-box-neutral">
                    <div class="info-label-neutral">Informasi Pesanan</div>
                    <div class="info-row"><span class="info-key">No. Pesanan</span><span class="info-value">:
                            <?php echo htmlspecialchars(
                              $order["order_number"],
                            ); ?></span></div>
                    <div class="info-row"><span class="info-key">Batch</span><span class="info-value">:
                            <?php echo htmlspecialchars(
                              $order["batch_name"] ?? "-",
                            ); ?></span></div>
                    <div class="info-row"><span class="info-key">Event</span><span class="info-value">:
                            <?php echo htmlspecialchars(
                              $order["event_name"] ?? "-",
                            ); ?></span></div>
                    <div class="info-row"><span class="info-key">Tanggal Event</span><span class="info-value">:
                            <?php echo $order["event_date"]
                              ? date("d M Y", strtotime($order["event_date"]))
                              : "-"; ?></span></div>
                    <div class="info-row">
                        <span class="info-key">Status</span>
                        <span class="info-value">:
                            <span class="status-badge status-<?php echo $order[
                              "status"
                            ]; ?>">
                                <?php
                                $statusLabels = [
                                  "pending" => "Menunggu",
                                  "processing" => "Diproses",
                                  "ready" => "Siap Ambil",
                                  "picked_up" => "Sudah Diambil",
                                ];
                                echo $statusLabels[$order["status"]] ??
                                  $order["status"];
                                ?>
                            </span>
                        </span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Products Table -->
    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Produk</th>
                <th style="width: 12%;" class="text-center">Jumlah</th>
                <th style="width: 20%;" class="text-right">Harga Satuan</th>
                <th style="width: 28%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order["items"] as $index => $item): ?>
            <tr>
                <td class="text-center"><?php echo $index + 1; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars(
                      $item["name"],
                    ); ?></strong>
                    <?php if (!empty($item["sku"])): ?>
                        <br><span style="font-size: 8px; color: #6b7280;">SKU: <?php echo htmlspecialchars(
                          $item["sku"],
                        ); ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?php echo number_format(
                  $item["quantity"],
                  0,
                  ",",
                  ".",
                ); ?> kaleng</td>
                <td class="text-right">Rp <?php echo number_format(
                  $item["unit_price"],
                  0,
                  ",",
                  ".",
                ); ?></td>
                <td class="text-right"><strong>Rp <?php echo number_format(
                  $item["subtotal"],
                  0,
                  ",",
                  ".",
                ); ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Total Summary (outside table, right-aligned) -->
    <table style="width: 50%; margin-left: auto; margin-bottom: 18px;">
        <tr>
            <td style="padding: 4px 10px; font-size: 10px; font-weight: bold; color: #374151; width: 50%;">Total Jumlah</td>
            <td style="padding: 4px 10px; font-size: 10px; text-align: right; color: #1f2937; width: 50%;">
                <?php
                $totalQty = array_sum(
                  array_column($order["items"], "quantity"),
                );
                echo number_format($totalQty, 0, ",", ".");
                ?> kaleng
            </td>
        </tr>
        <tr>
            <td style="padding: 4px 10px; font-size: 10px; font-weight: bold; color: #374151; border-bottom: 2px solid #e5e7eb;">Total Subtotal</td>
            <td style="padding: 4px 10px; font-size: 10px; text-align: right; color: #1f2937; border-bottom: 2px solid #e5e7eb;">
                <strong>Rp <?php echo number_format(
                  $order["total_amount"],
                  0,
                  ",",
                  ".",
                ); ?></strong>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="total-grand" style="margin-top: 4px;">
                    <span class="total-label">TOTAL BAYAR</span>
                    <span class="total-value">Rp <?php echo number_format(
                      $order["total_amount"],
                      0,
                      ",",
                      ".",
                    ); ?></span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Pickup Code -->
    <div class="pickup-section">
        <div class="pickup-label">Kode Pengambilan</div>
        <div class="pickup-code"><?php echo htmlspecialchars(
          $order["pickup_code"],
        ); ?></div>
        <div class="pickup-label">Tunjukkan kode ini saat mengambil pesanan di kampus</div>
    </div>

    <!-- Footer with 3 logos -->
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem TEFA Canning SIP — Politeknik Negeri Jember</p>
        <p>Jl. Mastrip PO BOX 164, Jember, Jawa Timur 68121 — Dicetak: <?php echo date(
          "d M Y, H:i:s",
        ); ?></p>
        <div style="margin-top: 10px;">
            <?php
            $footerLogoPath = __DIR__ . "/../../assets/images/3_logo_in_1.png";
            if (file_exists($footerLogoPath)) {
              $footerLogoData = base64_encode(
                file_get_contents($footerLogoPath),
              );
              echo '<img src="data:image/png;base64,' .
                $footerLogoData .
                '" alt="Logo" style="height: 35px; width: auto; opacity: 0.7;">';
            }
            ?>
        </div>
    </div>
</body>

</html>
