<?php
/**
 * Script Migrasi Database
 * Menambahkan kolom 'deleted_at' untuk soft delete
 * 
 * Jalankan file ini sekali di browser, kemudian bisa dihapus
 */

$host = "localhost";
$user = "root";
$password = "root";
$database = "mahasiswa";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek apakah kolom deleted_at sudah ada
$check_query = "SHOW COLUMNS FROM data_mahasiswa LIKE 'deleted_at'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) == 0) {
    // Tambahkan kolom deleted_at
    $alter_query = "ALTER TABLE data_mahasiswa ADD COLUMN deleted_at DATETIME DEFAULT NULL";
    
    if (mysqli_query($conn, $alter_query)) {
        echo '<div style="padding: 20px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px;">';
        echo '<strong>✅ Sukses!</strong> Kolom deleted_at berhasil ditambahkan ke tabel data_mahasiswa.<br>';
        echo '<small>Sekarang Anda bisa menghapus file migration.php ini.</small>';
        echo '</div>';
    } else {
        echo '<div style="padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;">';
        echo '<strong>❌ Error:</strong> ' . mysqli_error($conn);
        echo '</div>';
    }
} else {
    echo '<div style="padding: 20px; background: #cfe2ff; color: #084298; border: 1px solid #b6d4fe; border-radius: 5px; margin: 20px;">';
    echo '<strong>ℹ️ Info:</strong> Kolom deleted_at sudah ada. Migrasi sudah pernah dijalankan.';
    echo '</div>';
}

mysqli_close($conn);
?>
