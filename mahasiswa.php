 <?php
 require_once "fungsi.php";

 $koneksi = mysqli_connect("localhost", "root", "root", "mahasiswa");

 $query = "SELECT * FROM data_mahasiswa ORDER BY id ASC";
 $result = mysqli_query($koneksi, $query);

 /// ambil data (fetch) dari lemari mahasiswa
 // mysqli_fetch_row() -> mengembalikan data dalam bentuk array numerik
 // mysqli_fetch_assoc() -> mengembalikan data dalam bentuk array asosiatif (lebih mudah dibaca)
 // mysqli_fetch_array() -> mengembalikan data dalam bentuk array numerik dan asosiatif
 // mysqli_fetch_object() -> mengembalikan data dalam bentuk objek


 

 
 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa | Informatika 2026</title>
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
        .btn-tambah {
            display: inline-block;
            margin: 10px 0;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-tambah:hover {
            background-color: #45a049;
        }
        .btn-edit {
            background-color: #2196F3;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-hapus {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-edit:hover, .btn-hapus:hover {
            opacity: 0.8;
        }
        .error {
            color: red;
            background: #ffe6e6;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
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
        </tr>
    </table>
    
    <hr/>

    <h2>Data Mahasiswa</h2>
    <a href="tambahdata.php" class="btn-tambah">+ Tambah Data Mahasiswa</a>

    <?php
    // 🔧 GANTI PASSWORD DI SINI (coba root, atau kosong, atau laragon)
    $host = "localhost";     // atau "127.0.0.1"
    $user = "root";
    $password = "root";      // 🔥 GANTI INI dengan password MySQL-mu
    $database = "mahasiswa";

    // Koneksi ke database
    $conn = mysqli_connect($host, $user, $password, $database);

    // Cek koneksi
    if (!$conn) {
        echo '<div class="error">';
        echo "<strong>⚠️ Koneksi database gagal!</strong><br>";
        echo "Error: " . mysqli_connect_error() . "<br>";
        echo "Cek password MySQL di Laragon:<br>";
        echo "1. Buka terminal Laragon<br>";
        echo "2. Ketik: mysql -u root -p<br>";
        echo "3. Coba password: root / laragon / (kosong)<br>";
        echo "</div>";
        die();
    }

    // Query ambil data mahasiswa
    $query = "SELECT * FROM data_mahasiswa ORDER BY id ASC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo '<div class="error">';
        echo "⚠️ Query error: " . mysqli_error($conn);
        echo "</div>";
    } else {
        // Cek apakah ada data
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
                echo '<td align="center">
                        <a href="edit.php?id=' . $row['id'] . '" class="btn-edit">Edit</a>
                        <a href="hapus.php?id=' . $row['id'] . '" class="btn-hapus" onclick="return confirm(\'Yakin ingin menghapus data ini?\')">Hapus</a>
                       </td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p style="color: red; margin-top: 20px;">⚠️ Belum ada data mahasiswa. Silakan tambah data terlebih dahulu.</p>';
        }
    }

    // Tutup koneksi
    mysqli_close($conn);
    ?>

    <hr/>
    
    <?php
    // Hitung total mahasiswa
    $conn2 = mysqli_connect($host, $user, $password, $database);
    if ($conn2) {
        $countQuery = "SELECT COUNT(*) as total FROM data_mahasiswa";
        $countResult = mysqli_query($conn2, $countQuery);
        if ($countResult) {
            $total = mysqli_fetch_assoc($countResult);
            echo '<p><small>Total mahasiswa: ' . $total['total'] . '</small></p>';
        }
        mysqli_close($conn2);
    }
    ?>
</body>
</html>