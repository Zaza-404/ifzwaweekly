<?php
$host = "localhost";
$user = "root";
$password = "root";
$database = "mahasiswa";

$conn = mysqli_connect($host, $user, $password, $database);
$uploadMessage = '';
$uploadError = '';
$updateMessage = '';
$updateError = '';
$regMessage = '';
$regError = '';
$studentList = [];

if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

$resultStudents = mysqli_query($conn, "SELECT id, nama FROM data_mahasiswa ORDER BY nama ASC");
if ($resultStudents) {
    while ($student = mysqli_fetch_assoc($resultStudents)) {
        $studentList[] = $student;
    }
}

if (!function_exists('uploadFoto')) {
    function uploadFoto($fieldName, &$fotoNama, &$uploadError)
    {
        $fotoNama = '';

        if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            $uploadError = 'Silakan pilih file foto.';
            return false;
        }

        if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            $uploadError = 'Upload foto gagal. Silakan coba lagi.';
            return false;
        }

        $uploadDir = __DIR__ . '/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = $_FILES[$fieldName]['name'];
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowed, true)) {
            $uploadError = 'Format foto tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.';
            return false;
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $targetName = $safeName . '_' . time() . '.' . $fileExt;
        $targetPath = $uploadDir . '/' . $targetName;

        if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetPath)) {
            $uploadError = 'Gagal menyimpan foto di server.';
            return false;
        }

        $fotoNama = $targetName;
        return true;
    }
}

if (isset($_POST['submit_add'])) {
    $nama = trim($_POST['Nama'] ?? '');
    $uts = intval($_POST['UTS'] ?? 0);
    $uas = intval($_POST['UAS'] ?? 0);
    $tugas = intval($_POST['Tugas'] ?? 0);
    $fotoNama = '';

    if ($nama === '') {
        $uploadError = 'Nama harus diisi.';
    } elseif (uploadFoto('Foto', $fotoNama, $uploadError)) {
        $namaEsc = mysqli_real_escape_string($conn, $nama);
        $fotoEsc = mysqli_real_escape_string($conn, $fotoNama);
        $sql = "INSERT INTO data_mahasiswa (nama, foto, uts, uas, tugas) VALUES ('$namaEsc', '$fotoEsc', $uts, $uas, $tugas)";

        if (mysqli_query($conn, $sql)) {
            $uploadMessage = 'Data mahasiswa dan foto berhasil disimpan.';
        } else {
            $uploadError = 'Gagal menyimpan data: ' . mysqli_error($conn);
        }
    }
} elseif (isset($_POST['submit_reg'])) {
    $nama = trim($_POST['nama'] ?? '');
    $nim = trim($_POST['nim'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nohp = trim($_POST['nohp'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $tanggallahir = trim($_POST['tanggallahir'] ?? '');
    $warna = trim($_POST['warna'] ?? '');
    $kepuasan = intval($_POST['kepuasan'] ?? 0);
    $jeniskelamin = trim($_POST['jeniskelamin'] ?? '');
    $hobi = trim($_POST['hobi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $fotoNama = '';

    if ($nama === '' || $nim === '' || $jurusan === '') {
        $regError = 'Nama, NIM, dan Jurusan wajib diisi.';
    } elseif (uploadFoto('foto', $fotoNama, $regError)) {
        $namaEsc = mysqli_real_escape_string($conn, $nama);
        $nimEsc = mysqli_real_escape_string($conn, $nim);
        $emailEsc = mysqli_real_escape_string($conn, $email);
        $nohpEsc = mysqli_real_escape_string($conn, $nohp);
        $jurusanEsc = mysqli_real_escape_string($conn, $jurusan);
        $fotoEsc = mysqli_real_escape_string($conn, $fotoNama);

        $sql = "INSERT INTO data_mahasiswa (nim, nama, prodi, email, no_hp, foto) VALUES ('$nimEsc', '$namaEsc', '$jurusanEsc', '$emailEsc', '$nohpEsc', '$fotoEsc')";

        if (mysqli_query($conn, $sql)) {
            $regMessage = 'Registrasi mahasiswa berhasil disimpan.';
        } else {
            $regError = 'Gagal menyimpan registrasi: ' . mysqli_error($conn);
        }
    }
} elseif (isset($_POST['submit_upload'])) {
    $mahasiswaId = intval($_POST['MahasiswaId'] ?? 0);
    $fotoNama = '';

    if ($mahasiswaId <= 0) {
        $updateError = 'Pilih mahasiswa terlebih dahulu.';
    } elseif (uploadFoto('FotoFoto', $fotoNama, $updateError)) {
        $fotoEsc = mysqli_real_escape_string($conn, $fotoNama);
        $cekSql = "SELECT nama FROM data_mahasiswa WHERE id = " . $mahasiswaId . " LIMIT 1";
        $cekResult = mysqli_query($conn, $cekSql);

        if ($cekResult && mysqli_num_rows($cekResult) > 0) {
            $row = mysqli_fetch_assoc($cekResult);
            $updateSql = "UPDATE data_mahasiswa SET foto = '$fotoEsc' WHERE id = " . $mahasiswaId;
            if (mysqli_query($conn, $updateSql)) {
                $updateMessage = 'Foto berhasil diperbarui untuk ' . htmlspecialchars($row['nama']) . '.';
            } else {
                $updateError = 'Gagal memperbarui data: ' . mysqli_error($conn);
            }
        } else {
            $updateError = 'Mahasiswa tidak ditemukan. Pilih nama yang tersedia.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Tambah Data Mahasiswa</h2>
    <?php if ($uploadMessage): ?>
        <p style="color: green;"><?php echo htmlspecialchars($uploadMessage); ?></p>
    <?php elseif ($uploadError): ?>
        <p style="color: red;"><?php echo htmlspecialchars($uploadError); ?></p>
    <?php endif; ?>
    <form action="tambahdata.php" method="post" enctype="multipart/form-data">
        <table cellpadding="5px">
            <tr>
                <td><label for="Nama">Nama </label></td>
                <td>:</td>
                <td><input type="text" id="Nama" name="Nama"/></td>
            </tr>
            <tr>
                <td><label for="Foto">Foto </label></td>
                <td>:</td>
                <td><input type="file" id="Foto" name="Foto"/></td>
            </tr>
            <tr>
                <td><label for="UTS">UTS </label></td>
                <td>:</td>
                <td><input type="number" id="UTS" name="UTS"/></td>
            </tr>
            <tr>
                <td><label for="UAS">UAS </label></td>
                <td>:</td>
                <td><input type="number" id="UAS" name="UAS"/></td>
            </tr>
            <tr>
                <td><label for="Tugas">Tugas </label></td>
                <td>:</td>
                <td><input type="number" id="Tugas" name="Tugas"/></td>
            </tr>
            <tr>
                <td><button type="submit" name="submit_add"> Tambah </button></td>
            </tr>
        </table>
    </form>

    <h2>Update Foto Mahasiswa</h2>
    <?php if ($updateMessage): ?>
        <p style="color: green;"><?php echo htmlspecialchars($updateMessage); ?></p>
    <?php elseif ($updateError): ?>
        <p style="color: red;"><?php echo htmlspecialchars($updateError); ?></p>
    <?php endif; ?>
    <form action="tambahdata.php" method="post" enctype="multipart/form-data">
        <table cellpadding="5px">
            <tr>
                <td><label for="MahasiswaId">Pilih Mahasiswa </label></td>
                <td>:</td>
                <td>
                    <select id="MahasiswaId" name="MahasiswaId">
                        <option value="">-- Pilih Mahasiswa --</option>
                        <?php foreach ($studentList as $student): ?>
                            <option value="<?php echo htmlspecialchars($student['id']); ?>"><?php echo htmlspecialchars($student['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="FotoFoto">Foto Baru </label></td>
                <td>:</td>
                <td><input type="file" id="FotoFoto" name="FotoFoto"/></td>
            </tr>
            <tr>
                <td><button type="submit" name="submit_upload"> Update Foto </button></td>
            </tr>
        </table>
    </form>
    <br>
    <hr>
    <h2>Form Registrasi Mahasiswa</h2>
    <?php if ($regMessage): ?>
        <p style="color: green;"><?php echo htmlspecialchars($regMessage); ?></p>
    <?php elseif ($regError): ?>
        <p style="color: red;"><?php echo htmlspecialchars($regError); ?></p>
    <?php endif; ?>
    <form action="tambahdata.php" method="post" enctype="multipart/form-data">
        <table cellpadding="5px">
            <tr>
                <td><label for="nama">Nama </label></td>
                <td>:</td>
                <td><input type="text" id="nama" name="nama"/></td>
            </tr>
            <tr>
                <td><label for="nim">NIM </label></td>
                <td>:</td>
                <td><input type="number" id="nim" name="nim"/></td>
            </tr>
            <tr>
                <td><label for="password">Password </label></td>
                <td>:</td>
                <td><input type="password" id="password" name="password"/></td>
            </tr>
            <tr>
                <td><label for="email">Email </label></td>
                <td>:</td>
                <td><input type="email" id="email" name="email"/></td>
            </tr>
            <tr>
                <td><label for="nohp">No HP </label></td>
                <td>:</td>
                <td><input type="tel" id="nohp" name="nohp"/></td>
            </tr>
            <tr>
                <td><label for="website">Website Pribadi </label></td>
                <td>:</td>
                <td><input type="url" id="website" name="website"/></td>
            </tr>
            <tr>
                <td><label for="tanggallahir">Tanggal Lahir </label></td>
                <td>:</td>
                <td><input type="date" id="tanggallahir" name="tanggallahir"/></td>
            </tr>
            <tr>
                <td><label for="warna">Warna Favorit </label></td>
                <td>:</td>
                <td><input type="color" id="warna" name="warna"/></td>
            </tr>
            <tr>
                <td><label for="kepuasan">Tingkat Kepuasan </label></td>
                <td>:</td>
                <td><input type="range" id="kepuasan" name="kepuasan" min="0" max="100"/></td>
            </tr>
            <tr>
                <td><label>Jenis Kelamin </label></td>
                <td>:</td>
                <td>
                    <input type="radio" id="laki" name="jeniskelamin" value="Laki-laki"/>
                    <label for="laki">Laki-laki</label>
                    <input type="radio" id="perempuan" name="jeniskelamin" value="Perempuan"/>
                    <label for="perempuan">Perempuan</label>
                </td>
            </tr>
            <tr>
                <td><label>Hobi </label></td>
                <td>:</td>
                <td>
                    <input type="checkbox" id="membaca" name="hobi" value="Membaca"/>
                    <label for="membaca">Membaca</label><br>
                    <input type="checkbox" id="olahraga" name="hobi" value="Olahraga"/>
                    <label for="olahraga">Olahraga</label><br>
                    <input type="checkbox" id="musik" name="hobi" value="Musik"/>
                    <label for="musik">Musik</label><br>
                    <input type="checkbox" id="gaming" name="hobi" value="Gaming"/>
                    <label for="gaming">Gaming</label><br>
                    <input type="checkbox" id="traveling" name="hobi" value="Traveling"/>
                    <label for="traveling">Traveling</label>
                </td>
            </tr>
            <tr>
                <td><label for="foto">Upload Foto </label></td>
                <td>:</td>
                <td><input type="file" id="foto" name="foto"/></td>
            </tr>
            <tr>
                <td><label for="alamat">Alamat </label></td>
                <td>:</td>
                <td><textarea id="alamat" name="alamat" rows="4" cols="30"></textarea></td>
            </tr>
            <tr>
                <td><label for="jurusan">Jurusan </label></td>
                <td>:</td>
                <td><input type="text" id="jurusan" name="jurusan"/></td>
            </tr>
            <tr>
                <td><button type="submit" name="submit_reg">Submit</button></td>
            </tr>
        </table>
    </form>
</body>
</html>
