<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservasi Kamar</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Reservasi Kamar</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Tamu</th>
                <th>Nomor Kamar</th>
                <th>Jenis Kamar</th>
                <th>Lama Sewa</th>
                <th>Harga Sewa Per Hari</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; $total = 0; foreach ($detail as $r) { ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $r->nama_tamu; ?></td>
                <td>No. (<?php echo $r->nama_barang; ?>)</td>
                <td><?php echo $r->nama_kategori; ?></td>
                <td><?php echo $r->qty; ?> Hari</td>
                <td>Rp. <?php echo number_format($r->harga, 2); ?></td>
                <td>Rp. <?php echo number_format($r->qty * $r->harga, 2); ?></td>
            </tr>
        <?php $total = $total + ($r->qty * $r->harga); $no++; } ?>
            <tr>
                <td colspan="6"><b>TOTAL</b></td>
                <td><b>Rp. <?php echo number_format($total, 2); ?></b></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
