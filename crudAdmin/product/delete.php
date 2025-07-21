<?php
include "config/database.php";

// Pastikan ini adalah request POST dan ada parameter id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Validasi dan sanitasi input
    $id = intval($_POST['id']);

    if ($id <= 0) {
        header("Location: ../../indexAdmin.php?status=error&message=ID produk tidak valid");
        exit();
    }

    try {
        // Mulai transaksi
        mysqli_begin_transaction($conn);

        // Pertama, ambil informasi gambar untuk dihapus dari server
        $query = "SELECT image FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $product = mysqli_fetch_assoc($result);
            $imagePath = $product['image'];

            // Hapus record dari database terlebih dahulu
            $deleteQuery = "DELETE FROM products WHERE id = ?";
            $deleteStmt = mysqli_prepare($conn, $deleteQuery);
            mysqli_stmt_bind_param($deleteStmt, "i", $id);
            $deleteSuccess = mysqli_stmt_execute($deleteStmt);

            if ($deleteSuccess) {
                // Hapus file gambar jika ada dan record berhasil dihapus
                if (!empty($imagePath) && file_exists($imagePath)) {
                    if (!unlink($imagePath)) {
                        // Jika gagal hapus file, rollback transaksi
                        mysqli_rollback($conn);
                        header("Location: ../../indexAdmin.php?status=error&message=Gagal menghapus gambar produk");
                        exit();
                    }
                }

                // Commit transaksi jika semua berhasil
                mysqli_commit($conn);
                header("Location: ../../indexAdmin.php?status=success&message=Produk berhasil dihapus");
            } else {
                mysqli_rollback($conn);
                header("Location: ../../indexAdmin.php?status=error&message=Gagal menghapus produk");
            }
        } else {
            header("Location: ../../indexAdmin.php?status=error&message=Produk tidak ditemukan");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: ../../indexAdmin.php?status=error&message=Terjadi kesalahan: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../indexAdmin.php?status=error&message=Request tidak valid");
}

mysqli_close($conn);
exit();
