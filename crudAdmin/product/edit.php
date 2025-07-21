<?php
include "config/database.php";

// Ambil data produk yang akan diedit
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 0) {
        die("Produk tidak ditemukan");
    }

    $product = mysqli_fetch_assoc($result);
}

// Handle form submission untuk update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $id = intval($_POST['id']);
    $namaProduk = mysqli_real_escape_string($conn, $_POST['namaProduk']);
    $hargaProduk = floatval($_POST['hargaProduk']);
    $sizeProduk = mysqli_real_escape_string($conn, $_POST['sizeProduk']);
    $kategoriProduk = mysqli_real_escape_string($conn, $_POST['kategoriProduk']);
    $statusProduk = mysqli_real_escape_string($conn, $_POST['statusProduk']);
    $descProduk = mysqli_real_escape_string($conn, $_POST['descProduk']);

    // Handle file upload (jika ada gambar baru diupload)
    $gambarProduk = $_POST['gambarProduk_lama']; // default ke gambar lama

    if (isset($_FILES['gambarProduk']) && $_FILES['gambarProduk']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['gambarProduk']['name']);
        $targetFile = $targetDir . uniqid() . '_' . $fileName;

        // Check if image file is actual image
        $check = getimagesize($_FILES['gambarProduk']['tmp_name']);
        if ($check !== false) {
            // Move uploaded file
            if (move_uploaded_file($_FILES['gambarProduk']['tmp_name'], $targetFile)) {
                $gambarProduk = $targetFile;
                // Hapus gambar lama jika ada
                if (!empty($_POST['gambarProduk_lama']) && file_exists($_POST['gambarProduk_lama'])) {
                    unlink($_POST['gambarProduk_lama']);
                }
            } else {
                die("Gagal mengupload gambar");
            }
        } else {
            die("File bukan gambar");
        }
    }

    // Update database
    $query = "UPDATE products SET
                name = '$namaProduk', 
                price = $hargaProduk, 
                size = '$sizeProduk', 
                category = '$kategoriProduk',
                status = '$statusProduk',
                description = '$descProduk', 
                image = '$gambarProduk'
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../indexAdmin.php");
        exit();
    } else {
        die("Error: " . mysqli_error($conn));
    }
}

mysqli_close($conn);
