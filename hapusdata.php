<?php
require_once "fungsi.php";

// Cek apakah ada parameter id
if (!isset($_GET['id'])) {
    header("Location: mahasiswa.php");
    exit;
}

$id = $_GET['id'];

// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "root";
$database = "mahasiswa";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil nama mahasiswa berdasarkan id
$query = "SELECT nama FROM data_mahasiswa WHERE id = " . intval($id);
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo '<script>
        alert("Data mahasiswa tidak ditemukan!");
        window.location.href = "mahasiswa.php";
    </script>';
    mysqli_close($conn);
    exit;
}

$row = mysqli_fetch_assoc($result);
$nama = htmlspecialchars($row['nama']);

// Tampilkan konfirmasi dengan alert
echo '<script>
    if (confirm("Yakin ingin menghapus data ' . $nama . '?")) {
        // Jika dikonfirmasi, hapus data
        window.location.href = "hapusdata.php?id=' . $id . '&confirm=yes";
    } else {
        // Jika dibatalkan, kembali ke halaman mahasiswa
        window.location.href = "mahasiswa.php";
    }
</script>';

// Jika konfirmasi = yes, lakukan soft delete
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    // Soft delete: update deleted_at dengan timestamp saat ini
    $delete_query = "UPDATE data_mahasiswa SET deleted_at = NOW() WHERE id = " . intval($id);
    
    if (mysqli_query($conn, $delete_query)) {
        echo '<script>
            alert("Data mahasiswa berhasil dihapus! Anda bisa memulihkannya di halaman Trash.");
            window.location.href = "mahasiswa.php";
        </script>';
    } else {
        echo '<script>
            alert("Gagal menghapus data: ' . mysqli_error($conn) . '");
            window.location.href = "mahasiswa.php";
        </script>';
    }
}

mysqli_close($conn);
?>
