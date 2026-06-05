<?php

$koneksi = mysqli_connect("localhost", "root", "root", "mahasiswa");
if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

function tampildata($query)
{
    global $koneksi;
    $result = mysqli_query($koneksi, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}