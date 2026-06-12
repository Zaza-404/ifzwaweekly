<?php
require_once "fungsi.php";

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header("Location: trash.php");
    exit;
}

$id = $_GET['id'];

$host = "localhost";
$user = "root";
$password = "root";
$database = "mahasiswa";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Restore data: set deleted_at menjadi NULL
$restore_query = "UPDATE data_mahasiswa SET deleted_at = NULL WHERE id = " . intval($id);

if (mysqli_query($conn, $restore_query)) {
    // Ambil nama untuk ditampilkan di alert
    $query_nama = "SELECT nama FROM data_mahasiswa WHERE id = " . intval($id);
    $result_nama = mysqli_query($conn, $query_nama);
    
    if ($result_nama && mysqli_num_rows($result_nama) > 0) {
        $row_nama = mysqli_fetch_assoc($result_nama);
        $nama = htmlspecialchars($row_nama['nama']);
    } else {
        $nama = "Data";
    }
    
    echo '<script>
        alert("Data ' . $nama . ' berhasil dipulihkan!");
        window.location.href = "mahasiswa.php";
    </script>';
} else {
    echo '<script>
        alert("Gagal memulihkan data: ' . mysqli_error($conn) . '");
        window.location.href = "trash.php";
    </script>';
}

mysqli_close($conn);
?>
