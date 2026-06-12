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

// Ambil info foto sebelum dihapus
$query_foto = "SELECT foto, nama FROM data_mahasiswa WHERE id = " . intval($id);
$result_foto = mysqli_query($conn, $query_foto);

$nama = "Data";
if ($result_foto && mysqli_num_rows($result_foto) > 0) {
    $row_foto = mysqli_fetch_assoc($result_foto);
    $foto = $row_foto['foto'];
    $nama = htmlspecialchars($row_foto['nama']);
    
    // Hapus file foto jika ada
    if ($foto && $foto != 'NULL' && file_exists('uploads/' . $foto)) {
        unlink('uploads/' . $foto);
    }
}

// Hapus data secara permanen dari database
$delete_query = "DELETE FROM data_mahasiswa WHERE id = " . intval($id);

if (mysqli_query($conn, $delete_query)) {
    echo '<script>
        alert("Data ' . $nama . ' berhasil dihapus secara permanen!");
        window.location.href = "trash.php";
    </script>';
} else {
    echo '<script>
        alert("Gagal menghapus data: ' . mysqli_error($conn) . '");
        window.location.href = "trash.php";
    </script>';
}

mysqli_close($conn);
?>
