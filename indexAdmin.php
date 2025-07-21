<?php
include "config/database.php";

$query_products = "SELECT * FROM products ORDER BY created_at DESC";
$result_products = mysqli_query($conn, $query_products);
$products = [];
if (mysqli_num_rows($result_products) > 0) {
  while ($row = mysqli_fetch_assoc($result_products)) {
    $products[] = $row;
  }
}

$query_orders = "SELECT * FROM orders ORDER BY created_at DESC";
$result_orders = mysqli_query($conn, $query_orders);
$orders = [];
if (mysqli_num_rows($result_orders) > 0) {
  while ($row = mysqli_fetch_assoc($result_orders)) {
    $orders[] = $row;
  }
}

$query_testimonials = "SELECT t.*, u.username, o.order_code 
                       FROM testimonials t
                       JOIN users u ON t.user_id = u.id
                       JOIN orders o ON t.order_id = o.id
                       ORDER BY t.created_at DESC";
$result_testimonials = mysqli_query($conn, $query_testimonials);
$testimonials = [];
if (mysqli_num_rows($result_testimonials) > 0) {
  while ($row = mysqli_fetch_assoc($result_testimonials)) {
    $testimonials[] = $row;
  }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <style>
    .sidebar-icon {
      transition: all 0.3s ease;
    }

    nav a:hover .sidebar-icon {
      transform: scale(1.1);
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    ::-webkit-scrollbar {
      width: 6px;
    }

    ::-webkit-scrollbar-thumb {
      background-color: rgba(100, 116, 139, 0.4);
      border-radius: 3px;
    }

    .chart-container {
      background: white;
      border-radius: 0.75rem;
      padding: 1.5rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    #pendingOrdersChart,
    #monthlyRevenueChart {
      max-height: 300px;
    }

    .order-status-chip {
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
    }

    .order-status-chip i {
      margin-right: 0.25rem;
    }

    .status-dropdown-menu {
      position: absolute;
      right: 0;
      z-index: 1000;
      /* Ensure it's above other content */
      background-color: white;
      border-radius: 0.375rem;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      min-width: 10rem;
      /* Adjust width as needed */
    }

    .status-dropdown-menu button {
      padding: 0.5rem 1rem;
      width: 100%;
      text-align: left;
      display: block;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-900 font-sans antialiased">
  <div class="flex min-h-screen">
    <aside
      class="w-64 bg-white shadow-lg px-6 py-8 flex flex-col justify-between sticky top-0 h-screen overflow-y-auto">
      <div>
        <h1 class="text-2xl font-bold text-yellow-500 mb-12 select-none">
          Hai, Naufal<span class="text-gray-700">!</span><i class="ri-hand-heart-line ml-2 text-yellow-500"></i>
        </h1>
        <nav class="space-y-1 text-gray-600">
          <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-yellow-100 text-yellow-700 font-semibold"
            onclick="showSection('dashboard')">
            <i class="ri-dashboard-line text-lg"></i>
            Dashboard
          </a>
          <a href="#"
            class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-yellow-50 hover:text-yellow-600 transition-colors duration-200"
            onclick="showSection('produk')">
            <i class="ri-box-3-line text-lg"></i>
            Products
          </a>
          <a href="#"
            class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-yellow-50 hover:text-yellow-600 transition-colors duration-200"
            onclick="showSection('orders')">
            <i class="ri-shopping-bag-line text-lg"></i>
            Orders
          </a>
          <a href="#"
            class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-yellow-50 hover:text-yellow-600 transition-colors duration-200"
            onclick="showSection('testimonial')">
            <i class="ri-chat-smile-line text-lg"></i>
            Testimonial
          </a>
        </nav>
      </div>
      <div class="mt-12">
        <button
          class="block w-full bg-yellow-500 text-white px-6 py-3 rounded-xl shadow-lg hover:bg-yellow-600 transition-colors duration-300 font-semibold"><a
            href="component/logout.php">
            Logout <i class="ri-vip-diamond-line ml-2"></i></a>
        </button>
      </div>
    </aside>

    <div class="flex-1 overflow-auto">
      <header class="bg-white shadow-sm">
        <div class="flex justify-between items-center p-4">
          <h2 id="header-title" class="text-xl font-semibold text-gray-800">
            Dashboard
          </h2>
          <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-2 cursor-pointer group">
              <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile"
                class="w-8 h-8 rounded-full border-2 border-transparent group-hover:border-yellow-500 transition" />
              <span class="text-sm font-medium group-hover:text-yellow-600 transition">Naufal</span>
              <i class="ri-arrow-down-s-line text-lg text-gray-500 group-hover:text-yellow-600 transition"></i>
            </div>
          </div>
        </div>
      </header>

      <main class="p-6">
        <div id="dashboard" class="content-section">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div
              class="stat-card bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl shadow-md p-6 transition duration-300 lg:col-span-1">
              <div class="flex justify-between items-center">
                <div>
                  <p class="text-sm opacity-80">Total Produk</p>
                  <p class="text-2xl font-bold mt-1"><?= count($products) ?></p>
                  <p class="text-xs opacity-80 mt-2 flex items-center">
                    <i class="ri-information-line mr-1"></i>
                    <?= count(array_filter($products, function ($p) {
                      return $p['status'] === 'unggulan'; })) ?> Produk
                    Unggulan
                  </p>
                </div>
                <i class="ri-box-3-fill text-3xl opacity-70"></i>
              </div>
              <div class="mt-4">

              </div>
            </div>

            <div
              class="stat-card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-md p-6 transition duration-300 lg:col-span-1">
              <div class="flex justify-between items-center">
                <div>
                  <p class="text-sm opacity-80">Total Pesanan</p>
                  <p class="text-2xl font-bold mt-1"><?= count($orders) ?></p>
                  <div class="flex space-x-2 mt-2">
                    <span class="order-status-chip bg-blue-200 text-blue-800">
                      <i class="ri-time-line"></i>
                      <?= count(array_filter($orders, function ($o) {
                        return $o['status'] === 'pending'; })) ?>
                    </span>
                    <span class="order-status-chip bg-green-200 text-green-800">
                      <i class="ri-check-line"></i>
                      <?= count(array_filter($orders, function ($o) {
                        return $o['status'] === 'completed'; })) ?>
                    </span>
                  </div>
                </div>
                <i class="ri-shopping-bag-3-fill text-3xl opacity-70"></i>
              </div>
              <div class="mt-4">

              </div>
            </div>

            <div
              class="stat-card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-md p-6 transition duration-300 lg:col-span-1">
              <div class="flex justify-between items-center">
                <div>
                  <p class="text-sm opacity-80">Total Testimonial</p>
                  <p class="text-2xl font-bold mt-1"><?= count($testimonials) ?></p>
                  <div class="mt-2">
                    <span class="order-status-chip bg-yellow-200 text-yellow-800">
                      <i class="ri-time-line"></i>
                      <?= count(array_filter($testimonials, function ($t) {
                        return $t['status'] === 'pending'; })) ?>
                      Pending
                    </span>
                  </div>
                </div>
                <i class="ri-chat-smile-2-fill text-3xl opacity-70"></i>
              </div>
              <div class="mt-4">

                </a>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="chart-container">
              <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg">Status Pesanan</h3>
                <span class="text-xs text-gray-500 flex items-center">
                  <i class="ri-pie-chart-line mr-1"></i> Distribusi
                </span>
              </div>
              <div class="relative h-64">
                <canvas id="pendingOrdersChart"></canvas>
              </div>
            </div>

            <div class="chart-container">
              <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg">Pendapatan Bulanan</h3>
                <span class="text-xs text-gray-500 flex items-center">
                  <i class="ri-line-chart-line mr-1"></i> Trend
                </span>
              </div>
              <div class="relative h-64">
                <canvas id="monthlyRevenueChart"></canvas>
              </div>
            </div>
          </div>

          <div class="chart-container mb-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="font-semibold text-lg">Pesanan Terbaru</h3>

            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode
                      Pesanan</th>
                    <th scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan
                    </th>
                    <th scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                    <tr class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                        <?= htmlspecialchars($order['order_code']) ?></td>
                      <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($order['customer_name']) ?></td>
                      <td class="px-6 py-4 whitespace-nowrap">Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="order-status-chip
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
                          <i class="
                            <?php
                            if ($order['status'] === 'pending')
                              echo 'ri-time-line';
                            elseif ($order['status'] === 'processing')
                              echo 'ri-refresh-line';
                            elseif ($order['status'] === 'shipped')
                              echo 'ri-truck-line';
                            elseif ($order['status'] === 'completed')
                              echo 'ri-check-line';
                            elseif ($order['status'] === 'cancelled')
                              echo 'ri-close-line';
                            else
                              echo 'ri-question-line';
                            ?>
                          "></i>
                          <?= ucfirst($order['status']) ?>
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= date('d M Y', strtotime($order['created_at'])) ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="chart-container">
            <div class="flex justify-between items-center mb-4">
              <h3 class="font-semibold text-lg">Testimonial Terbaru</h3>

            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <?php foreach (array_slice($testimonials, 0, 3) as $t): ?>
                <div class="bg-white p-4 rounded-lg border border-gray-200 hover:border-yellow-300 transition">
                  <div class="flex items-start mb-3">
                    <div class="flex-1 min-w-0">
                      <h4 class="font-medium text-gray-900"><?= htmlspecialchars($t['username']) ?></h4>
                      <p class="text-sm text-gray-500">Pesanan #<?= htmlspecialchars($t['order_code']) ?></p>
                    </div>
                    <div class="flex items-center ml-2">
                      <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                        <i class="ri-star-fill text-yellow-400"></i>
                      <?php endfor; ?>
                    </div>
                  </div>
                  <p class="text-gray-700 mb-3">"<?= htmlspecialchars($t['comment']) ?>"</p>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500"><?= date('d M Y', strtotime($t['created_at'])) ?></span>
                    <span class="text-xs px-2 py-1 rounded-full
                      <?= $t['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>
                    ">
                      <?= ucfirst($t['status']) ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div id="produk" class="content-section hidden">
          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
              <caption
                class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white dark:text-white dark:bg-gray-800 relative">
                Our products
                <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">
                  Daftar produk banana chips yang tersedia.
                </p>
                <button id="tambahProdukBtn"
                  class="px-4 py-2 right-5 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 absolute right-2 top-8">
                  <i class="ri-add-line mr-1"></i> Tambah Produk
                </button>
              </caption>
              <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                  <th scope="col" class="px-6 py-3">Gambar</th>
                  <th scope="col" class="px-6 py-3">Nama Produk</th>
                  <th scope="col" class="px-6 py-3">Deskripsi</th>
                  <th scope="col" class="px-6 py-3">Size</th>
                  <th scope="col" class="px-6 py-3">Harga</th>
                  <th scope="col" class="px-6 py-3">Status</th>
                  <th scope="col" class="px-6 py-3">Kategori</th>
                  <th scope="col" class="px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $product): ?>
                  <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    <td class="px-6 py-4">
                      <img src="<?= 'crudAdmin/product/' . htmlspecialchars($product['image']) ?>"
                        class="w-16 h-16 object-cover rounded" alt="<?= htmlspecialchars($product['name']) ?>" />
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                      <?= htmlspecialchars($product['name']) ?>
                    </th>
                    <td class="px-6 py-4">
                      <?= htmlspecialchars($product['description']) ?>
                    </td>
                    <td class="px-6 py-4">
                      <?= htmlspecialchars($product['size']) ?>
                    </td>
                    <td class="px-6 py-4">
                      Rp
                      <?= number_format($product['price'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4">
                      <?=
                        $product['status'] === 'promo' ? 'Promo' :
                        ($product['status'] === 'unggulan' ? 'Unggulan' : 'Biasa')
                        ?>
                    </td>
                    <td class="px-6 py-4">
                      <?=
                        $product['category'] === 'original' ? 'Original' :
                        ($product['category'] === 'manis' ? 'Manis' :
                          ($product['category'] === 'gurih' ? 'Gurih' : 'Pedas'))
                        ?>
                    </td>
                    <td class="px-2 py-4 h-full">
                      <div class="flex px-8 justify-between items-center h-full">
                        <button type="button"
                          class="edit-btn font-medium text-yellow-600 dark:text-yellow-500 hover:underline"
                          data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>"
                          data-price="<?= $product['price'] ?>" data-size="<?= $product['size'] ?>"
                          data-desc="<?= htmlspecialchars($product['description']) ?>"
                          data-image="<?= htmlspecialchars($product['image']) ?>" data-status="<?= $product['status'] ?>"
                          data-category="<?= $product['category'] ?>">
                          <i class="ri-edit-line mr-1"></i> Edit
                        </button>
                        <form action="crudAdmin/product/delete.php" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                          <input type="hidden" name="id" value="<?= $product['id'] ?>" />
                          <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">
                            <i class="ri-delete-bin-line mr-1"></i> Hapus
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>


        </div>

        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center border-b p-4">
              <h3 class="text-lg font-semibold">Edit Produk</h3>
              <button id="closeEditModalBtn" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line"></i>
              </button>
            </div>
            <div class="p-6">
              <form id="editForm" action="crudAdmin/product/edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="gambarProduk_lama" id="editGambarLama">

                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Nama Produk</label>
                  <input type="text" name="namaProduk" id="editNamaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Harga</label>
                  <input type="number" name="hargaProduk" id="editHargaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Size (gram)</label>
                  <input type="text" name="sizeProduk" id="editSizeProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Status</label>
                  <select name="statusProduk" id="editStatusProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="unggulan">Unggulan</option>
                    <option value="promo">Promo</option>
                    <option value="biasa">Biasa</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Kategori</label>
                  <select name="kategoriProduk" id="editKategoriProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="original">Original</option>
                    <option value="manis">Manis</option>
                    <option value="gurih">Gurih</option>
                    <option value="pedas">Pedas</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Deskripsi</label>
                  <textarea name="descProduk" id="editDescProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 h-32"
                    required></textarea>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Gambar Produk</label>
                  <input type="file" name="gambarProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                  <div class="mt-2">
                    <img id="editPreviewGambar" src="" class="w-24 h-24 object-cover rounded">
                  </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                  <button type="button" id="cancelEditBtn"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Batal</button>
                  <button type="submit" name="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Simpan Perubahan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div id="orders" class="content-section hidden">
          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <caption class="p-5 text-lg font-semibold text-left text-gray-900 bg-white relative">
              Daftar Pesanan
              <p class="mt-1 text-sm font-normal text-gray-500">Kelola pesanan pelanggan.</p>
              <div class="flex items-center space-x-4 mt-4">
                <div class="relative w-full max-w-xs">
                  <input type="text" id="orderSearch" placeholder="Cari kode pesanan..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 w-full">
                  <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="relative flex items-center w-full max-w-xs">
                  <input type="text" id="orderDateFilter" placeholder="Filter berdasarkan tanggal"
                    class="pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 w-full">
                  <i class="ri-calendar-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                  <button id="resetDateFilter"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    title="Reset Filter">
                    <i class="ri-close-circle-line"></i>
                  </button>
                </div>
              </div>
            </caption>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500" id="ordersTable">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3">Kode Pesanan</th>
                  <th scope="col" class="px-6 py-3">Pelanggan</th>
                  <th scope="col" class="px-6 py-3">Total</th>
                  <th scope="col" class="px-6 py-3">Status</th>
                  <th scope="col" class="px-6 py-3">Tanggal</th>
                  <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order): ?>
                  <tr class="bg-white border-b" data-order-code="<?= htmlspecialchars($order['order_code']) ?>"
                    data-order-date="<?= date('Y-m-d', strtotime($order['created_at'])) ?>">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                      <?= htmlspecialchars($order['order_code']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td class="px-6 py-4">Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                    <td class="px-6 py-4">
                      <span class="px-2 py-1 rounded-full text-xs font-semibold
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
                    </td>
                    <td class="px-6 py-4"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                    <td class="px-6 py-4">
                      <button class="view-order-details-btn font-medium text-blue-600 hover:underline mr-2"
                        data-id="<?= $order['id'] ?>"><i class="ri-eye-line mr-1"></i>Detail</button>
                      <div class="relative inline-block text-left">
                        <button type="button"
                          class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                          id="menu-button-<?= $order['id'] ?>" aria-expanded="true" aria-haspopup="true">
                          Ubah Status <i class="ri-arrow-down-s-line ml-1"></i>
                        </button>
                        <div class="status-dropdown-menu hidden" role="menu" aria-orientation="vertical"
                          aria-labelledby="menu-button-<?= $order['id'] ?>" tabindex="-1"
                          id="status-dropdown-<?= $order['id'] ?>">
                          <div class="py-1" role="none">
                            <form action="crudAdmin/orders/update_status.php" method="POST">
                              <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                              <?php if ($order['status'] === 'pending'): ?>
                                <button type="submit" name="status" value="processing"
                                  class="text-gray-700 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Processing</button>
                                <button type="submit" name="status" value="cancelled"
                                  class="text-red-600 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Cancelled</button>
                              <?php elseif ($order['status'] === 'processing'): ?>
                                <button type="submit" name="status" value="shipped"
                                  class="text-gray-700 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Shipped</button>
                                <button type="submit" name="status" value="completed"
                                  class="text-gray-700 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Completed</button>
                              <?php elseif ($order['status'] === 'shipped'): ?>
                                <button type="submit" name="status" value="completed"
                                  class="text-gray-700 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Completed</button>
                              <?php else: ?>
                                <button type="button"
                                  class="text-gray-500 block px-4 py-2 text-sm w-full text-left cursor-not-allowed"
                                  disabled>Tidak Ada Aksi</button>
                              <?php endif; ?>
                            </form>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div id="testimonial" class="content-section hidden">
          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
              <caption class="p-5 text-lg font-semibold text-left text-gray-900 bg-white relative">
                Daftar Testimonial
                <p class="mt-1 text-sm font-normal text-gray-500">Kelola testimonial pelanggan.</p>
              </caption>
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3">ID</th>
                  <th scope="col" class="px-6 py-3">Pengguna</th>
                  <th scope="col" class="px-6 py-3">Kode Pesanan</th>
                  <th scope="col" class="px-6 py-3">Rating</th>
                  <th scope="col" class="px-6 py-3">Komentar</th>
                  <th scope="col" class="px-6 py-3">Status</th>
                  <th scope="col" class="px-6 py-3">Tanggal</th>
                  <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($testimonials as $t): ?>
                  <tr class="bg-white border-b">
                    <td class="px-6 py-4"><?= $t['id'] ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($t['username']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($t['order_code']) ?></td>
                    <td class="px-6 py-4">
                      <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                        <i class="ri-star-fill text-yellow-400"></i>
                      <?php endfor; ?>
                    </td>
                    <td class="px-6 py-4 max-w-xs truncate"><?= htmlspecialchars($t['comment']) ?></td>
                    <td class="px-6 py-4">
                      <span class="px-2 py-1 rounded-full text-xs font-semibold
                        <?php
                        if ($t['status'] === 'pending')
                          echo 'bg-yellow-100 text-yellow-800';
                        elseif ($t['status'] === 'published')
                          echo 'bg-green-100 text-green-800';
                        else
                          echo 'bg-gray-100 text-gray-800';
                        ?>
                      ">
                        <?= ucfirst($t['status']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4"><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
                    <td class="px-6 py-4">
                      <div class="flex items-center space-x-2">
                        <form action="crudAdmin/testimonial/update.php" method="POST" class="inline">
                          <input type="hidden" name="id" value="<?= $t['id'] ?>">
                          <input type="hidden" name="comment" value="<?= htmlspecialchars($t['comment']) ?>">
                          <input type="hidden" name="rating" value="<?= $t['rating'] ?>">
                          <?php if ($t['status'] === 'pending'): ?>
                            <button type="submit" name="status" value="published"
                              class="font-medium text-green-600 hover:underline"><i
                                class="ri-check-line mr-1"></i>Publish</button>
                          <?php else: ?>
                            <button type="submit" name="status" value="pending"
                              class="font-medium text-yellow-600 hover:underline"><i
                                class="ri-time-line mr-1"></i>Pending</button>
                          <?php endif; ?>
                        </form>
                        <form action="crudAdmin/testimonial/delete.php" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus testimoni ini?');">
                          <input type="hidden" name="id" value="<?= $t['id'] ?>">
                          <button type="submit" class="font-medium text-red-600 hover:underline"><i
                              class="ri-delete-bin-line mr-1"></i>Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div id="produkModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center border-b p-4">
              <h3 class="text-lg font-semibold">Tambah Produk Baru</h3>
              <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line"></i>
              </button>
            </div>
            <div class="p-6">
              <form id="produkForm" action="crudAdmin/product/insert.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Nama Produk</label>
                  <input type="text" name="namaProduk" id="namaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Harga</label>
                  <input type="number" name="hargaProduk" id="hargaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Size (gram)</label>
                  <input type="text" name="sizeProduk" id="sizeProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    placeholder="250 gr" required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Status</label>
                  <select name="statusProduk" id="statusProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="unggulan">Unggulan</option>
                    <option value="promo">Promo</option>
                    <option value="biasa">Biasa</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Kategori</label>
                  <select name="kategoriProduk" id="kategoriProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="original">Original</option>
                    <option value="manis">Manis</option>
                    <option value="gurih">Gurih</option>
                    <option value="pedas">Pedas</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Deskripsi Produk</label>
                  <textarea name="descProduk" id="descProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 h-56"
                    required></textarea>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Gambar Produk</label>
                  <input type="file" name="gambarProduk" id="gambarProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    accept="image/*" required>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                  <button type="button" id="cancelBtn"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Batal</button>
                  <button type="submit" name="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Simpan Produk</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center border-b p-4">
              <h3 class="text-lg font-semibold">Edit Produk</h3>
              <button id="closeEditModalBtn" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line"></i>
              </button>
            </div>
            <div class="p-6">
              <form id="editForm" action="crudAdmin/product/edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="gambarProduk_lama" id="editGambarLama">

                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Nama Produk</label>
                  <input type="text" name="namaProduk" id="editNamaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Harga</label>
                  <input type="number" name="hargaProduk" id="editHargaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Size (gram)</label>
                  <input type="text" name="sizeProduk" id="editSizeProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Status</label>
                  <select name="statusProduk" id="editStatusProduuk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="unggulan">Unggulan</option>
                    <option value="promo">Promo</option>
                    <option value="biasa">Biasa</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Kategori</label>
                  <select name="kategoriProduk" id="editKategoriProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="original">Original</option>
                    <option value="manis">Manis</option>
                    <option value="gurih">Gurih</option>
                    <option value="pedas">Pedas</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Deskripsi</label>
                  <textarea name="descProduk" id="editDescProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 h-32"
                    required></textarea>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Gambar Produk</label>
                  <input type="file" name="gambarProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                  <div class="mt-2">
                    <img id="editPreviewGambar" src="" class="w-24 h-24 object-cover rounded">
                  </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                  <button type="button" id="cancelEditBtn"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Batal</button>
                  <button type="submit" name="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Simpan Perubahan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div id="orders" class="content-section hidden">
          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <caption class="p-5 text-lg font-semibold text-left text-gray-900 bg-white relative">
              Daftar Pesanan
              <p class="mt-1 text-sm font-normal text-gray-500">Kelola pesanan pelanggan.</p>
              <div class="flex items-center space-x-4 mt-4">
                <div class="relative w-full max-w-xs">
                  <input type="text" id="orderSearch" placeholder="Cari kode pesanan..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 w-full">
                  <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="relative flex items-center w-full max-w-xs">
                  <input type="text" id="orderDateFilter" placeholder="Filter berdasarkan tanggal"
                    class="pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 w-full">
                  <i class="ri-calendar-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                  <button id="resetDateFilter"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    title="Reset Filter">
                    <i class="ri-close-circle-line"></i>
                  </button>
                </div>
              </div>
            </caption>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500" id="ordersTable">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3">Kode Pesanan</th>
                  <th scope="col" class="px-6 py-3">Pelanggan</th>
                  <th scope="col" class="px-6 py-3">Total</th>
                  <th scope="col" class="px-6 py-3">Status</th>
                  <th scope="col" class="px-6 py-3">Tanggal</th>
                  <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order): ?>
                  <tr class="bg-white border-b" data-order-code="<?= htmlspecialchars($order['order_code']) ?>"
                    data-order-date="<?= date('Y-m-d', strtotime($order['created_at'])) ?>">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                      <?= htmlspecialchars($order['order_code']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td class="px-6 py-4">Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                    <td class="px-6 py-4">
                      <span class="px-2 py-1 rounded-full text-xs font-semibold
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
                    </td>
                    <td class="px-6 py-4"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                    <td class="px-6 py-4">
                      <button class="view-order-details-btn font-medium text-blue-600 hover:underline mr-2"
                        data-id="<?= $order['id'] ?>"><i class="ri-eye-line mr-1"></i>Detail</button>
                      <div class="relative inline-block text-left">
                        <button type="button"
                          class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                          id="menu-button-<?= $order['id'] ?>" aria-expanded="true" aria-haspopup="true">
                          Ubah Status <i class="ri-arrow-down-s-line ml-1"></i>
                        </button>
                        <div class="status-dropdown-menu hidden" role="menu" aria-orientation="vertical"
                          aria-labelledby="menu-button-<?= $order['id'] ?>" tabindex="-1"
                          id="status-dropdown-<?= $order['id'] ?>">
                          <div class="py-1" role="none">
                            <form action="crudAdmin/orders/update_status.php" method="POST">
                              <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                              <?php if ($order['status'] === 'pending'): ?>
                                <button type="submit" name="status" value="processing"
                                  class="text-gray-700 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Processing</button>
                                <button type="submit" name="status" value="cancelled"
                                  class="text-red-600 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Cancelled</button>
                              <?php elseif ($order['status'] === 'processing'): ?>
                                <button type="submit" name="status" value="shipped"
                                  class="text-gray-700 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Shipped</button>
                                <button type="submit" name="status" value="completed"
                                  class="text-gray-700 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Completed</button>
                              <?php elseif ($order['status'] === 'shipped'): ?>
                                <button type="submit" name="status" value="completed"
                                  class="text-gray-700 block px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                                  role="menuitem" tabindex="-1">Completed</button>
                              <?php else: ?>
                                <button type="button"
                                  class="text-gray-500 block px-4 py-2 text-sm w-full text-left cursor-not-allowed"
                                  disabled>Tidak Ada Aksi</button>
                              <?php endif; ?>
                            </form>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div id="testimonial" class="content-section hidden">
          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
              <caption class="p-5 text-lg font-semibold text-left text-gray-900 bg-white relative">
                Daftar Testimonial
                <p class="mt-1 text-sm font-normal text-gray-500">Kelola testimonial pelanggan.</p>
              </caption>
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3">ID</th>
                  <th scope="col" class="px-6 py-3">Pengguna</th>
                  <th scope="col" class="px-6 py-3">Kode Pesanan</th>
                  <th scope="col" class="px-6 py-3">Rating</th>
                  <th scope="col" class="px-6 py-3">Komentar</th>
                  <th scope="col" class="px-6 py-3">Status</th>
                  <th scope="col" class="px-6 py-3">Tanggal</th>
                  <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($testimonials as $t): ?>
                  <tr class="bg-white border-b">
                    <td class="px-6 py-4"><?= $t['id'] ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($t['username']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($t['order_code']) ?></td>
                    <td class="px-6 py-4">
                      <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                        <i class="ri-star-fill text-yellow-400"></i>
                      <?php endfor; ?>
                    </td>
                    <td class="px-6 py-4 max-w-xs truncate"><?= htmlspecialchars($t['comment']) ?></td>
                    <td class="px-6 py-4">
                      <span class="px-2 py-1 rounded-full text-xs font-semibold
                        <?php
                        if ($t['status'] === 'pending')
                          echo 'bg-yellow-100 text-yellow-800';
                        elseif ($t['status'] === 'published')
                          echo 'bg-green-100 text-green-800';
                        else
                          echo 'bg-gray-100 text-gray-800';
                        ?>
                      ">
                        <?= ucfirst($t['status']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4"><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
                    <td class="px-6 py-4">
                      <div class="flex items-center space-x-2">
                        <form action="crudAdmin/testimonial/update.php" method="POST" class="inline">
                          <input type="hidden" name="id" value="<?= $t['id'] ?>">
                          <input type="hidden" name="comment" value="<?= htmlspecialchars($t['comment']) ?>">
                          <input type="hidden" name="rating" value="<?= $t['rating'] ?>">
                          <?php if ($t['status'] === 'pending'): ?>
                            <button type="submit" name="status" value="published"
                              class="font-medium text-green-600 hover:underline"><i
                                class="ri-check-line mr-1"></i>Publish</button>
                          <?php else: ?>
                            <button type="submit" name="status" value="pending"
                              class="font-medium text-yellow-600 hover:underline"><i
                                class="ri-time-line mr-1"></i>Pending</button>
                          <?php endif; ?>
                        </form>
                        <form action="crudAdmin/testimonial/delete.php" method="POST" class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus testimoni ini?');">
                          <input type="hidden" name="id" value="<?= $t['id'] ?>">
                          <button type="submit" class="font-medium text-red-600 hover:underline"><i
                              class="ri-delete-bin-line mr-1"></i>Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div id="produkModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center border-b p-4">
              <h3 class="text-lg font-semibold">Tambah Produk Baru</h3>
              <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line"></i>
              </button>
            </div>
            <div class="p-6">
              <form id="produkForm" action="crudAdmin/product/insert.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Nama Produk</label>
                  <input type="text" name="namaProduk" id="namaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Harga</label>
                  <input type="number" name="hargaProduk" id="hargaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Size (gram)</label>
                  <input type="text" name="sizeProduk" id="sizeProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    placeholder="250 gr" required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Status</label>
                  <select name="statusProduk" id="statusProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="unggulan">Unggulan</option>
                    <option value="promo">Promo</option>
                    <option value="biasa">Biasa</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Kategori</label>
                  <select name="kategoriProduk" id="kategoriProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="original">Original</option>
                    <option value="manis">Manis</option>
                    <option value="gurih">Gurih</option>
                    <option value="pedas">Pedas</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Deskripsi Produk</label>
                  <textarea name="descProduk" id="descProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 h-56"
                    required></textarea>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Gambar Produk</label>
                  <input type="file" name="gambarProduk" id="gambarProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    accept="image/*" required>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                  <button type="button" id="cancelBtn"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Batal</button>
                  <button type="submit" name="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Simpan Produk</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center border-b p-4">
              <h3 class="text-lg font-semibold">Edit Produk</h3>
              <button id="closeEditModalBtn" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line"></i>
              </button>
            </div>
            <div class="p-6">
              <form id="editForm" action="crudAdmin/product/edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="gambarProduk_lama" id="editGambarLama">

                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Nama Produk</label>
                  <input type="text" name="namaProduk" id="editNamaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Harga</label>
                  <input type="number" name="hargaProduk" id="editHargaProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Size (gram)</label>
                  <input type="text" name="sizeProduk" id="editSizeProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Status</label>
                  <select name="statusProduk" id="editStatusProduuk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="unggulan">Unggulan</option>
                    <option value="promo">Promo</option>
                    <option value="biasa">Biasa</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Kategori</label>
                  <select name="kategoriProduk" id="editKategoriProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    required>
                    <option value="original">Original</option>
                    <option value="manis">Manis</option>
                    <option value="gurih">Gurih</option>
                    <option value="pedas">Pedas</option>
                  </select>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Deskripsi</label>
                  <textarea name="descProduk" id="editDescProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 h-32"
                    required></textarea>
                </div>
                <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-medium mb-2">Gambar Produk</label>
                  <input type="file" name="gambarProduk"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                  <div class="mt-2">
                    <img id="editPreviewGambar" src="" class="w-24 h-24 object-cover rounded">
                  </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                  <button type="button" id="cancelEditBtn"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Batal</button>
                  <button type="submit" name="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Simpan Perubahan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div id="orderDetailsModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
              <h3 class="text-xl font-semibold text-gray-800">Detail Pesanan <span id="detailOrderCode"
                  class="text-yellow-600"></span></h3>
              <button id="closeOrderDetailsModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="orderDetailsContent" class="max-h-96 overflow-y-auto">
              <p class="text-center text-gray-500">Memuat detail pesanan...</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const datePicker = flatpickr("#orderDateFilter", {
        dateFormat: "Y-m-d",
        onChange: function (selectedDates, dateStr, instance) {
          filterOrders();
        }
      });

      document.getElementById('resetDateFilter').addEventListener('click', function (e) {
        e.preventDefault();
        datePicker.clear();
        filterOrders();
      });

      const ctx1 = document.getElementById('pendingOrdersChart').getContext('2d');
      const pendingOrdersChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
          labels: ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'],
          datasets: [{
            data: [
              <?= count(array_filter($orders, function ($o) {
                return $o['status'] === 'pending'; })) ?>,
              <?= count(array_filter($orders, function ($o) {
                return $o['status'] === 'processing'; })) ?>,
              <?= count(array_filter($orders, function ($o) {
                return $o['status'] === 'shipped'; })) ?>,
              <?= count(array_filter($orders, function ($o) {
                return $o['status'] === 'completed'; })) ?>,
              <?= count(array_filter($orders, function ($o) {
                return $o['status'] === 'cancelled'; })) ?>
            ],
            backgroundColor: [
              'rgba(234, 179, 8, 0.7)',
              'rgba(59, 130, 246, 0.7)',
              'rgba(168, 85, 247, 0.7)',
              'rgba(16, 185, 129, 0.7)',
              'rgba(239, 68, 68, 0.7)'
            ],
            borderColor: [
              'rgba(234, 179, 8, 1)',
              'rgba(59, 130, 246, 1)',
              'rgba(168, 85, 247, 1)',
              'rgba(16, 185, 129, 1)',
              'rgba(239, 68, 68, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  let label = context.label || '';
                  if (label) {
                    label += ': ';
                  }
                  label += context.raw + ' pesanan';
                  return label;
                }
              }
            }
          }
        }
      });

      const ctx2 = document.getElementById('monthlyRevenueChart').getContext('2d');
      const monthlyRevenueChart = new Chart(ctx2, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
          datasets: [{
            label: 'Pendapatan',
            data: [12000000, 18500000, 15000000, 22000000, 25000000, 20000000],
            fill: false,
            backgroundColor: 'rgba(234, 179, 8, 0.2)',
            borderColor: 'rgba(234, 179, 8, 1)',
            tension: 0.4,
            borderWidth: 2,
            pointBackgroundColor: 'rgba(234, 179, 8, 1)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function (context) {
                  return 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
              }
            }
          }
        }
      });

      const urlParams = new URLSearchParams(window.location.search);
      const tab = urlParams.get('tab');
      if (tab) {
        showSection(tab);
        document.querySelectorAll("nav a").forEach((link) => {
          link.classList.remove("bg-yellow-100", "text-yellow-700", "font-semibold");
          link.classList.add("hover:bg-yellow-50", "hover:text-yellow-600");
          if (link.onclick.toString().includes(`showSection('${tab}')`)) {
            link.classList.add("bg-yellow-100", "text-yellow-700", "font-semibold");
            link.classList.remove("hover:bg-yellow-50", "hover:text-yellow-600");
          }
        });
      } else {
        showSection("dashboard");
      }

      document.querySelectorAll("nav a").forEach((link) => {
        link.addEventListener("click", function (e) {
          e.preventDefault();
          const sectionId = this.onclick.toString().match(/showSection\('([^']+)'\)/)[1];
          showSection(sectionId);
          document.querySelectorAll("nav a").forEach((item) => {
            item.classList.remove("bg-yellow-100", "text-yellow-700", "font-semibold");
            item.classList.add("hover:bg-yellow-50", "hover:text-yellow-600");
          });
          this.classList.add("bg-yellow-100", "text-yellow-700", "font-semibold");
          this.classList.remove("hover:bg-yellow-50", "hover:text-yellow-600");
        });
      });

      function showSection(sectionId) {
        document.querySelectorAll(".content-section").forEach((section) => {
          section.classList.add("hidden");
        });
        document.getElementById(sectionId).classList.remove("hidden");
        document.getElementById("header-title").textContent =
          sectionId.charAt(0).toUpperCase() + sectionId.slice(1);
      }

      const tambahBtn = document.getElementById("tambahProdukBtn");
      const produkModal = document.getElementById("produkModal");
      const closeModalBtn = document.getElementById("closeModalBtn");
      const cancelBtn = document.getElementById("cancelBtn");

      tambahBtn.onclick = function () {
        produkModal.classList.remove("hidden");
      };

      function closeProdukModal() {
        produkModal.classList.add("hidden");
      }

      closeModalBtn.onclick = closeProdukModal;
      cancelBtn.onclick = closeProdukModal;

      produkModal.onclick = function (event) {
        if (event.target === produkModal) {
          closeProdukModal();
        }
      };

      const editModal = document.getElementById("editModal");
      const closeEditModalBtn = document.getElementById("closeEditModalBtn");
      const cancelEditBtn = document.getElementById("cancelEditBtn");
      const editButtons = document.querySelectorAll(".edit-btn");

      function openEditModal(productData) {
        document.getElementById("editId").value = productData.id;
        document.getElementById("editNamaProduk").value = productData.name;
        document.getElementById("editHargaProduk").value = productData.price;
        document.getElementById("editSizeProduk").value = productData.size;
        document.getElementById("editKategoriProduk").value = productData.category;
        document.getElementById("editStatusProduk").value = productData.status;
        document.getElementById("editDescProduk").value = productData.description;
        document.getElementById("editGambarLama").value = productData.image;
        document.getElementById("editPreviewGambar").src = 'crudAdmin/product/' + productData.image;

        editModal.classList.remove("hidden");
      }

      editButtons.forEach((button) => {
        button.addEventListener("click", function () {
          const productData = {
            id: this.getAttribute("data-id"),
            name: this.getAttribute("data-name"),
            price: this.getAttribute("data-price"),
            size: this.getAttribute("data-size"),
            category: this.getAttribute("data-category"),
            status: this.getAttribute("data-status"),
            description: this.getAttribute("data-desc"),
            image: this.getAttribute("data-image"),
          };
          openEditModal(productData);
        });
      });

      function closeEditModal() {
        editModal.classList.add("hidden");
      }

      closeEditModalBtn.addEventListener("click", closeEditModal);
      cancelEditBtn.addEventListener("click", closeEditModal);

      editModal.addEventListener("click", function (event) {
        if (event.target === editModal) {
          closeEditModal();
        }
      });

      const orderDetailsModal = document.getElementById('orderDetailsModal');
      const closeOrderDetailsModal = document.getElementById('closeOrderDetailsModal');
      const viewOrderDetailsButtons = document.querySelectorAll('.view-order-details-btn');
      const orderDetailsContent = document.getElementById('orderDetailsContent');
      const detailOrderCode = document.getElementById('detailOrderCode');

      viewOrderDetailsButtons.forEach(button => {
        button.addEventListener('click', function () {
          const orderId = this.dataset.id;
          fetch('crudAdmin/orders/fetch_order_details.php?order_id=' + orderId)
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
                                <p class="text-gray-700 mb-2"><strong>Nama Pelanggan:</strong> ${data.order.customer_name}</p>
                                <p class="text-gray-700 mb-2"><strong>No. WhatsApp:</strong> ${data.order.customer_phone}</p>
                                <p class="text-gray-700 mb-2"><strong>Alamat:</strong> ${data.order.customer_address}</p>
                                <p class="text-gray-700 mb-2"><strong>Metode Pembayaran:</strong> ${data.order.payment_method}</p>
                                <p class="text-gray-700 mb-2"><strong>Catatan:</strong> ${data.order.notes || '-'}</p>
                                <p class="text-gray-700 mb-4"><strong>Status:</strong> ${data.order.status}</p>
                                ${itemsHtml}
                                <div class="border-t border-gray-200 pt-4">
                                    <p class="text-gray-700 mb-2"><strong>Subtotal:</strong> Rp ${formatRupiah(data.order.subtotal)}</p>
                                    <p class="text-gray-700 mb-2"><strong>Ongkir:</strong> Rp ${formatRupiah(data.order.shipping_cost)}</p>
                                    <p class="text-xl font-bold text-yellow-600"><strong>Total:</strong> Rp ${formatRupiah(data.order.total)}</p>
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
        });
      });

      closeOrderDetailsModal.addEventListener('click', function () {
        orderDetailsModal.classList.add('hidden');
      });

      orderDetailsModal.addEventListener('click', function (e) {
        if (e.target === orderDetailsModal) {
          orderDetailsModal.classList.add('hidden');
        }
      });

      document.querySelectorAll('[id^="menu-button-"]').forEach(button => {
        button.addEventListener('click', function () {
          const dropdownId = 'status-dropdown-' + this.id.split('-')[2];
          const dropdown = document.getElementById(dropdownId);
          // Close all other dropdowns
          document.querySelectorAll('.status-dropdown-menu').forEach(otherDropdown => {
            if (otherDropdown.id !== dropdownId) {
              otherDropdown.classList.add('hidden');
            }
          });
          dropdown.classList.toggle('hidden');
        });
      });

      window.addEventListener('click', function (e) {
        document.querySelectorAll('.status-dropdown-menu').forEach(dropdown => {
          const button = document.getElementById('menu-button-' + dropdown.id.split('-')[2]);
          if (!dropdown.contains(e.target) && !button.contains(e.target)) {
            dropdown.classList.add('hidden');
          }
        });
      });

      const orderSearchInput = document.getElementById('orderSearch');
      const orderDateFilterInput = document.getElementById('orderDateFilter');
      const ordersTableBody = document.querySelector('#ordersTable tbody');
      const allOrderRows = Array.from(ordersTableBody.querySelectorAll('tr'));

      function filterOrders() {
        const searchTerm = orderSearchInput.value.toLowerCase();
        const filterDate = orderDateFilterInput._flatpickr.selectedDates[0] ?
          orderDateFilterInput._flatpickr.formatDate(orderDateFilterInput._flatpickr.selectedDates[0], "Y-m-d") : '';

        allOrderRows.forEach(row => {
          const orderCode = row.dataset.orderCode.toLowerCase();
          const orderDate = row.dataset.orderDate;

          const matchesSearch = orderCode.includes(searchTerm);
          const matchesDate = filterDate === '' || orderDate === filterDate;

          if (matchesSearch && matchesDate) {
            row.classList.remove('hidden');
          } else {
            row.classList.add('hidden');
          }
        });
      }

      orderSearchInput.addEventListener('keyup', filterOrders);

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