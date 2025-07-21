<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'banana_chips';

$conn = mysqli_connect($host, $user, $pass, $db);

if (mysqli_connect_error()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Get form data
    $namaProduk = mysqli_real_escape_string($conn, $_POST['namaProduk']);
    $hargaProduk = floatval($_POST['hargaProduk']);
    $stokProduk = intval($_POST['stokProduk']);
    $descProduk = mysqli_real_escape_string($conn, $_POST['descProduk']);

    // Handle file upload
    $gambarProduk = '';
    if (isset($_FILES['gambarProduk']) && $_FILES['gambarProduk']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/products/";
        $fileName = basename($_FILES['gambarProduk']['name']);
        $targetFile = $targetDir . uniqid() . '_' . $fileName;

        // Check if image file is actual image
        $check = getimagesize($_FILES['gambarProduk']['tmp_name']);
        if ($check !== false) {
            // Move uploaded file
            if (move_uploaded_file($_FILES['gambarProduk']['tmp_name'], $targetFile)) {
                $gambarProduk = $targetFile;
            } else {
                die("Gagal mengupload gambar");
            }
        } else {
            die("File bukan gambar");
        }
    } else {
        die("Gambar produk diperlukan");
    }

    // Insert into database
    $query = "INSERT INTO products (
                product_name, 
                price, 
                stock, 
                description, 
                image
              ) VALUES (
                '$namaProduk', 
                $hargaProduk, 
                $stokProduk, 
                '$descProduk', 
                '$gambarProduk'
              )";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../index.php?tab=products&message=Produk+berhasil+ditambahkan");
        exit();
    } else {
        die("Error: " . mysqli_error($conn));
    }
}

mysqli_close($conn);
