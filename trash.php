<?php
require_once "fungsi.php";

$host = "localhost";
$user = "root";
$password = "root";
$database = "mahasiswa";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trash - Data Mahasiswa | Informatika 2026</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .btn-restore {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-permanent-delete {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-restore:hover, .btn-permanent-delete:hover {
            opacity: 0.8;
        }
        .btn-kembali {
            display: inline-block;
            margin: 10px 0;
            padding: 8px 15px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-kembali:hover {
            background-color: #0b7dda;
        }
        .info-trash {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <h1>Informatika 2026</h1>
    
    <!-- Navigasi -->
    <table border="1" cellspacing="0" cellpadding="10px">
        <tr>
            <td><a href="index.php">Home</a></td>
            <td><a href="profile.php">Profile</a></td>
            <td><a href="contact.php">Contact</a></td>
            <td><a href="mahasiswa.php">Data Mahasiswa</a></td>
            <td><a href="trash.php" style="color: #f44336; font-weight: bold;">🗑️ Trash</a></td>
        </tr>
    </table>
    
    <hr/>

    <h2>🗑️ Trash - Data Terhapus</h2>
    <a href="mahasiswa.php" class="btn-kembali">← Kembali ke Data Mahasiswa</a>

    <div class="info-trash">
        <strong>ℹ️ Info:</strong> Data yang ada di sini adalah data yang telah dihapus. Anda dapat memulihkan atau menghapus secara permanen.
    </div>

    <?php
    // Query ambil data mahasiswa yang sudah dihapus
    $query = "SELECT * FROM data_mahasiswa WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo '<div style="color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0;">';
        echo "⚠️ Query error: " . mysqli_error($conn);
        echo "</div>";
    } else {
        // Cek apakah ada data di trash
        if (mysqli_num_rows($result) > 0) {
            echo '<table border="1" cellpadding="10px">
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Prodi</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Foto</th>
                <th>Terhapus Pada</th>
                <th>Aksi</th>
            </tr>';

            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td align="center">' . $no++ . '</td>';
                echo '<td>' . htmlspecialchars($row['nim']) . '</td>';
                echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                echo '<td>' . htmlspecialchars($row['prodi']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td>' . htmlspecialchars($row['no_hp']) . '</td>';
                echo '<td align="center">';
                if ($row['foto'] && $row['foto'] != 'NULL' && file_exists('uploads/' . $row['foto'])) {
                    echo '<img src="uploads/' . htmlspecialchars($row['foto']) . '" alt="Foto">';
                } else {
                    echo '<img src="assets/images/default-avatar.png" alt="No Photo" style="opacity: 0.6; width: 60px; height: 60px;">';
                    echo '<br><small>Tidak ada foto</small>';
                }
                echo '</td>';
                echo '<td>' . htmlspecialchars($row['deleted_at']) . '</td>';
                echo '<td align="center">
                        <a href="restore.php?id=' . $row['id'] . '" class="btn-restore" onclick="return confirm(\'Pulihkan data ' . htmlspecialchars($row['nama']) . '?\')">Restore</a>
                        <a href="permanent-delete.php?id=' . $row['id'] . '" class="btn-permanent-delete" onclick="return confirm(\'Hapus permanen data ' . htmlspecialchars($row['nama']) . '? Tidak bisa dipulihkan lagi!\')">Hapus</a>
                       </td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p style="color: green; margin-top: 20px; font-size: 18px;">✅ Trash kosong. Tidak ada data yang dihapus.</p>';
        }
    }

    // Hitung data di trash
    $conn2 = mysqli_connect($host, $user, $password, $database);
    if ($conn2) {
        $countQuery = "SELECT COUNT(*) as total FROM data_mahasiswa WHERE deleted_at IS NOT NULL";
        $countResult = mysqli_query($conn2, $countQuery);
        if ($countResult) {
            $total = mysqli_fetch_assoc($countResult);
            echo '<p><small>Total data di trash: ' . $total['total'] . '</small></p>';
        }
        mysqli_close($conn2);
    }

    mysqli_close($conn);
    ?>
</body>
</html>
