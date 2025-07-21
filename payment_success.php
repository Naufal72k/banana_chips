<?php
session_start();

$order_code = $_GET['order'] ?? '';

if(empty($order_code)) {
    header('Location: index.php');
    exit;
}

$whatsapp_number = '6285738979920';
$bank_info = [
    'bank_name' => 'BNI',
    'account_number' => '12345123',
    'account_name' => 'Naufal Ihsanul Islam'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - Banana Chips</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .success-animation {
            animation: bounce 0.5s ease-in-out;
        }
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden w-full max-w-md">
        <div class="bg-green-500 text-white p-6 text-center">
            <div class="success-animation inline-block mb-4">
                <i class="ri-checkbox-circle-fill text-5xl"></i>
            </div>
            <h1 class="text-2xl font-bold">Pembayaran Berhasil</h1>
            <p class="opacity-90 mt-2">Terima kasih telah berbelanja di Banana Chips</p>
        </div>

        <div class="p-6">
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Kode Order</span>
                    <span class="font-medium"><?= htmlspecialchars($order_code) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status</span>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">Diterima</span>
                </div>
            </div>

            <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                <h3 class="font-medium text-yellow-800 mb-2">
                    <i class="ri-bank-card-line align-middle mr-2"></i>
                    Informasi Pembayaran
                </h3>
                <p class="text-gray-700 mb-1">Bank: <?= htmlspecialchars($bank_info['bank_name']) ?></p>
                <p class="text-gray-700 mb-1">Nomor Rekening: <span class="font-semibold"><?= htmlspecialchars($bank_info['account_number']) ?></span></p>
                <p class="text-gray-700">Atas Nama: <?= htmlspecialchars($bank_info['account_name']) ?></p>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="font-medium text-blue-800 mb-2">
                    <i class="ri-customer-service-2-line align-middle mr-2"></i>
                    Butuh Bantuan?
                </h3>
                <p class="text-gray-700 mb-3">Hubungi kami via WhatsApp untuk konfirmasi pembayaran:</p>
                <a href="https://wa.me/<?= $whatsapp_number ?>?text=Konfirmasi%20Pembayaran%20Order%20<?= urlencode($order_code) ?>"
                   class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg w-full transition-colors">
                    <i class="ri-whatsapp-line mr-2"></i>
                    Hubungi via WhatsApp (0857-3897-9920)
                </a>
            </div>

            <div class="text-center text-gray-500 text-sm mb-4">
                <p>Kami akan segera memproses order Anda</p>
            </div>

            <a href="index.php" class="block text-center text-green-500 hover:text-green-600 font-medium">
                <i class="ri-arrow-left-line align-middle mr-1"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>
    <script>
    <?php if(isset($_SESSION['should_clear_cart']) && $_SESSION['should_clear_cart']): ?>
        sessionStorage.setItem('clearCartOnLoad', 'true');
        <?php unset($_SESSION['should_clear_cart']); ?>
    <?php endif; ?>
    </script>
</body>
</html>
