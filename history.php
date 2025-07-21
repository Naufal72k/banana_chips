<?php
require_once 'config/database.php';
require_once 'component/auth.php';

requireLogin();

$user_id = getUserId();
$orders = [];
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    $order_id_to_cancel = $_POST['order_id'] ?? 0;

    if ($order_id_to_cancel > 0) {
        $stmt = mysqli_prepare($conn, "SELECT status FROM orders WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $order_id_to_cancel, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        // Hanya izinkan pembatalan jika statusnya 'pending'
        if ($order && $order['status'] === 'pending') {
            $update_stmt = mysqli_prepare($conn, "UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ?");
            mysqli_stmt_bind_param($update_stmt, "ii", $order_id_to_cancel, $user_id);
            if (mysqli_stmt_execute($update_stmt)) {
                $success_message = "Pesanan berhasil dibatalkan.";
            } else {
                $error_message = "Gagal membatalkan pesanan: " . mysqli_stmt_error($update_stmt);
            }
            mysqli_stmt_close($update_stmt);
        } else {
            $error_message = "Pesanan tidak dapat dibatalkan atau tidak ditemukan.";
        }
    } else {
        $error_message = "ID pesanan tidak valid.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_history') {
    $order_id_to_delete = $_POST['order_id'] ?? 0;

    if ($order_id_to_delete > 0) {
        $stmt = mysqli_prepare($conn, "SELECT status FROM orders WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $order_id_to_delete, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($order && $order['status'] === 'cancelled') {
            mysqli_begin_transaction($conn);
            try {
                $delete_items_stmt = mysqli_prepare($conn, "DELETE FROM order_items WHERE order_id = ?");
                mysqli_stmt_bind_param($delete_items_stmt, "i", $order_id_to_delete);
                mysqli_stmt_execute($delete_items_stmt);
                mysqli_stmt_close($delete_items_stmt);

                $delete_order_stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE id = ? AND user_id = ?");
                mysqli_stmt_bind_param($delete_order_stmt, "ii", $order_id_to_delete, $user_id);
                mysqli_stmt_execute($delete_order_stmt);
                mysqli_stmt_close($delete_order_stmt);

                mysqli_commit($conn);
                $success_message = "Riwayat pesanan berhasil dihapus.";
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error_message = "Gagal menghapus riwayat pesanan: " . $e->getMessage();
            }
        } else {
            $error_message = "Hanya pesanan yang dibatalkan yang dapat dihapus dari riwayat.";
        }
    } else {
        $error_message = "ID pesanan tidak valid.";
    }
}

$stmt = mysqli_prepare($conn, "SELECT o.id, o.order_code, o.total, o.status, o.created_at, 
                               (SELECT COUNT(*) FROM testimonials WHERE order_id = o.id AND user_id = ?) as has_testimonial
                        FROM orders o 
                        WHERE o.user_id = ? 
                        ORDER BY o.created_at DESC");
mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
mysqli_stmt_close($stmt);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Pesanan - Banana Chips</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: "#FACC15", secondary: "#15803D" },
                    borderRadius: {
                        none: "0px",
                        sm: "4px",
                        DEFAULT: "8px",
                        md: "12px",
                        lg: "16px",
                        xl: "20px",
                        "2xl": "24px",
                        "3xl": "32px",
                        full: "9999px",
                        button: "8px",
                    },
                },
            },
        };
    </script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
        }

        .primary-color {
            color: #FACC15;
        }

        .bg-primary-color {
            background-color: #FACC15;
        }

        .hover-bg-primary-color:hover {
            background-color: #E5B800;
        }

        .modal {
            transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
        }

        .modal.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .modal.visible {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="sticky top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <h1 class="text-3xl font-['Pacifico'] text-primary">Banana Chips</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="index.php"
                    class="bg-primary hover:bg-primary/90 text-white py-2 px-4 rounded-full text-sm transition">
                    <i class="ri-arrow-left-line mr-2"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </nav>

    <?php include "component/keranjang.php"; ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Riwayat Pesanan Anda</h2>

        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?= $error_message ?></span>
            </div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline"><?= $success_message ?></span>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="text-center py-12 bg-white rounded-lg shadow-md">
                <p class="text-gray-500 text-lg">Anda belum memiliki riwayat pesanan.</p>
                <a href="index.php#products"
                    class="mt-4 inline-block bg-primary-color text-white py-2 px-4 rounded-lg hover-bg-primary-color transition">Mulai
                    Belanja</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-6">
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-gray-800">Pesanan
                                #<?= htmlspecialchars($order['order_code']) ?></h3>
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                <?php
                                if ($order['status'] === 'pending')
                                    echo 'bg-yellow-100 text-yellow-800';
                                elseif ($order['status'] === 'processing')
                                    echo 'bg-blue-100 text-blue-800';
                                elseif ($order['status'] === 'shipped')
                                    echo 'bg-purple-100 text-purple-800';
                                elseif ($order['status'] === 'completed')
                                    echo 'bg-green-100 text-green-800';
                                elseif ($order['status'] === 'cancelled')
                                    echo 'bg-red-100 text-red-800';
                                else
                                    echo 'bg-gray-100 text-gray-800';
                                ?>
                            ">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                        <p class="text-gray-600 mb-2">Tanggal Pesan: <?= date('d M Y H:i', strtotime($order['created_at'])) ?>
                        </p>
                        <p class="text-gray-600 mb-4">Total Pembayaran: <span class="font-bold text-primary-color">Rp
                                <?= number_format($order['total'], 0, ',', '.') ?></span></p>

                        <div class="flex flex-wrap gap-3">
                            <button
                                class="view-details-btn bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg transition"
                                data-order-id="<?= $order['id'] ?>">
                                <i class="ri-eye-line mr-2"></i>Lihat Detail
                            </button>
                            <?php if ($order['status'] === 'pending'): ?>
                                <form action="" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Pembatalan tidak dapat dibatalkan.');">
                                    <input type="hidden" name="action" value="cancel_order">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg transition">
                                        <i class="ri-close-circle-line mr-2"></i>Batalkan Pesanan
                                    </button>
                                </form>
                            <?php elseif ($order['status'] === 'cancelled'): ?>
                                <form action="" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat pesanan ini? Ini tidak dapat dikembalikan.');">
                                    <input type="hidden" name="action" value="delete_history">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit"
                                        class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition">
                                        <i class="ri-delete-bin-line mr-2"></i>Hapus Riwayat
                                    </button>
                                </form>
                            <?php elseif ($order['status'] === 'completed' && !$order['has_testimonial']): ?>
                                <button
                                    class="give-testimonial-btn bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition"
                                    data-order-id="<?= $order['id'] ?>"
                                    data-order-code="<?= htmlspecialchars($order['order_code']) ?>">
                                    <i class="ri-star-line mr-2"></i>Beri Testimoni
                                </button>
                            <?php elseif ($order['status'] === 'completed' && $order['has_testimonial']): ?>
                                <button class="bg-gray-400 text-white py-2 px-4 rounded-lg cursor-not-allowed" disabled>
                                    <i class="ri-check-double-line mr-2"></i>Testimoni Diberikan
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="testimonialModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] hidden modal">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto p-6">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Beri Testimoni untuk Pesanan <span id="modalOrderCode"
                        class="primary-color"></span></h3>
                <button id="closeTestimonialModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <form id="testimonialForm" action="crudAdmin/testimonial/insert.php" method="POST">
                <input type="hidden" name="order_id" id="testimonialOrderId">
                <div class="mb-4">
                    <label for="rating" class="block text-gray-700 text-sm font-medium mb-2">Rating</label>
                    <div class="flex items-center space-x-1" id="ratingStars">
                        <i class="ri-star-line text-2xl text-gray-400 cursor-pointer" data-value="1"></i>
                        <i class="ri-star-line text-2xl text-gray-400 cursor-pointer" data-value="2"></i>
                        <i class="ri-star-line text-2xl text-gray-400 cursor-pointer" data-value="3"></i>
                        <i class="ri-star-line text-2xl text-gray-400 cursor-pointer" data-value="4"></i>
                        <i class="ri-star-line text-2xl text-gray-400 cursor-pointer" data-value="5"></i>
                    </div>
                    <input type="hidden" name="rating" id="testimonialRating" value="0" required>
                </div>
                <div class="mb-4">
                    <label for="comment" class="block text-gray-700 text-sm font-medium mb-2">Komentar Anda</label>
                    <textarea name="comment" id="testimonialComment" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-color"
                        placeholder="Bagikan pengalaman Anda tentang produk dan layanan kami..." required></textarea>
                </div>
                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-300 shadow-md">
                        Kirim Testimoni
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="orderDetailsModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden modal">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Detail Pesanan <span id="detailOrderCode"
                        class="primary-color"></span></h3>
                <button id="closeOrderDetailsModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="orderDetailsContent" class="max-h-96 overflow-y-auto">
                <p class="text-center text-gray-500">Memuat detail pesanan...</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const testimonialModal = document.getElementById('testimonialModal');
            const closeTestimonialModal = document.getElementById('closeTestimonialModal');
            const giveTestimonialButtons = document.querySelectorAll('.give-testimonial-btn');
            const testimonialForm = document.getElementById('testimonialForm');
            const testimonialOrderId = document.getElementById('testimonialOrderId');
            const modalOrderCode = document.getElementById('modalOrderCode');
            const ratingStars = document.getElementById('ratingStars');
            const testimonialRatingInput = document.getElementById('testimonialRating');

            giveTestimonialButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const orderId = this.dataset.orderId;
                    const orderCode = this.dataset.orderCode;
                    testimonialOrderId.value = orderId;
                    modalOrderCode.textContent = orderCode;
                    testimonialModal.classList.remove('hidden');
                    testimonialModal.classList.add('visible');
                    resetRating();
                });
            });

            closeTestimonialModal.addEventListener('click', function () {
                testimonialModal.classList.remove('visible');
                testimonialModal.classList.add('hidden');
            });

            testimonialModal.addEventListener('click', function (e) {
                if (e.target === testimonialModal) {
                    testimonialModal.classList.remove('visible');
                    testimonialModal.classList.add('hidden');
                }
            });

            let currentRating = 0;
            ratingStars.addEventListener('mouseover', function (e) {
                if (e.target.classList.contains('ri-star-line') || e.target.classList.contains('ri-star-fill')) {
                    const value = parseInt(e.target.dataset.value);
                    highlightStars(value);
                }
            });

            ratingStars.addEventListener('mouseout', function () {
                highlightStars(currentRating);
            });

            ratingStars.addEventListener('click', function (e) {
                if (e.target.classList.contains('ri-star-line') || e.target.classList.contains('ri-star-fill')) {
                    currentRating = parseInt(e.target.dataset.value);
                    testimonialRatingInput.value = currentRating;
                    highlightStars(currentRating);
                }
            });

            function highlightStars(value) {
                Array.from(ratingStars.children).forEach(star => {
                    const starValue = parseInt(star.dataset.value);
                    if (starValue <= value) {
                        star.classList.remove('ri-star-line', 'text-gray-400');
                        star.classList.add('ri-star-fill', 'text-primary-color');
                    } else {
                        star.classList.remove('ri-star-fill', 'text-primary-color');
                        star.classList.add('ri-star-line', 'text-gray-400');
                    }
                });
            }

            function resetRating() {
                currentRating = 0;
                testimonialRatingInput.value = 0;
                highlightStars(0);
            }

            const orderDetailsModal = document.getElementById('orderDetailsModal');
            const closeOrderDetailsModal = document.getElementById('closeOrderDetailsModal');
            const viewDetailsButtons = document.querySelectorAll('.view-details-btn');
            const orderDetailsContent = document.getElementById('orderDetailsContent');
            const detailOrderCode = document.getElementById('detailOrderCode');

            viewDetailsButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const orderId = this.dataset.orderId;
                    fetch('component/fetch_order_details.php?order_id=' + orderId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                detailOrderCode.textContent = data.order.order_code;
                                let itemsHtml = '<h4 class="text-lg font-semibold text-gray-800 mb-3">Produk:</h4><ul class="list-disc pl-5 mb-4">';
                                data.items.forEach(item => {
                                    itemsHtml += `<li>${item.product_name} (x${item.quantity}) - Rp ${formatRupiah(item.product_price * item.quantity)}</li>`;
                                });
                                itemsHtml += '</ul>';

                                orderDetailsContent.innerHTML = `
                                    <div class="bg-green-50 rounded-lg p-4 mb-4">
                                        <h4 class="font-medium text-green-800 mb-2">
                                            <i class="ri-file-list-line align-middle mr-2"></i>
                                            Ringkasan Pesanan
                                        </h4>
                                        <p class="text-gray-700 mb-1"><strong>Kode Pesanan:</strong> ${data.order.order_code}</p>
                                        <p class="text-gray-700 mb-1"><strong>Status:</strong> <span class="font-semibold text-green-700">${data.order.status}</span></p>
                                        <p class="text-gray-700"><strong>Tanggal Pesan:</strong> ${new Date(data.order.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                                    </div>

                                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                                        <h4 class="font-medium text-blue-800 mb-2">
                                            <i class="ri-user-line align-middle mr-2"></i>
                                            Informasi Pelanggan
                                        </h4>
                                        <p class="text-gray-700 mb-1"><strong>Nama:</strong> ${data.order.customer_name}</p>
                                        <p class="text-gray-700 mb-1"><strong>No. WhatsApp:</strong> ${data.order.customer_phone}</p>
                                        <p class="text-gray-700"><strong>Alamat:</strong> ${data.order.customer_address}</p>
                                    </div>

                                    <div class="bg-yellow-50 rounded-lg p-4 mb-4">
                                        <h4 class="font-medium text-yellow-800 mb-2">
                                            <i class="ri-wallet-line align-middle mr-2"></i>
                                            Detail Pembayaran
                                        </h4>
                                        <p class="text-gray-700 mb-1"><strong>Metode Pembayaran:</strong> ${data.order.payment_method}</p>
                                        <p class="text-gray-700 mb-1"><strong>Subtotal:</strong> Rp ${formatRupiah(data.order.subtotal)}</p>
                                        <p class="text-gray-700 mb-1"><strong>Ongkir:</strong> Rp ${formatRupiah(data.order.shipping_cost)}</p>
                                        <p class="text-xl font-bold text-primary-color mt-2"><strong>Total:</strong> Rp ${formatRupiah(data.order.total)}</p>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                        <h4 class="font-medium text-gray-800 mb-2">
                                            <i class="ri-shopping-bag-line align-middle mr-2"></i>
                                            Item Pesanan
                                        </h4>
                                        ${itemsHtml}
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-medium text-gray-800 mb-2">
                                            <i class="ri-file-text-line align-middle mr-2"></i>
                                            Catatan
                                        </h4>
                                        <p class="text-gray-700">${data.order.notes || 'Tidak ada catatan.'}</p>
                                    </div>
                                `;
                            } else {
                                orderDetailsContent.innerHTML = `<p class="text-red-500">${data.message}</p>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching order details:', error);
                            orderDetailsContent.innerHTML = `<p class="text-red-500">Terjadi kesalahan saat memuat detail pesanan.</p>`;
                        });

                    orderDetailsModal.classList.remove('hidden');
                    orderDetailsModal.classList.add('visible');
                });
            });

            closeOrderDetailsModal.addEventListener('click', function () {
                orderDetailsModal.classList.remove('visible');
                orderDetailsModal.classList.add('hidden');
            });

            orderDetailsModal.addEventListener('click', function (e) {
                if (e.target === orderDetailsModal) {
                    orderDetailsModal.classList.remove('visible');
                    orderDetailsModal.classList.add('hidden');
                }
            });

            function formatRupiah(angka) {
                if (!angka) return '0';
                const reverse = angka.toString().split('').reverse().join('');
                let ribuan = reverse.match(/\d{1,3}/g);
                ribuan = ribuan.join('.').split('').reverse().join('');
                return ribuan;
            }
        });
    </script>
</body>

</html>