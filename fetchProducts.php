<?php


require_once 'config/database.php';

header('Content-Type: application/json');

$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$filter = isset($_POST['filter']) ? $_POST['filter'] : 'all';
$productsPerPage = isset($_POST['per_page']) ? (int) $_POST['per_page'] : 8;

if ($page < 1)
  $page = 1;
$offset = ($page - 1) * $productsPerPage;

$countSql = "SELECT COUNT(*) AS total FROM products WHERE status = 'biasa'";
if ($filter !== 'all') {
  $countSql .= " AND category = '" . mysqli_real_escape_string($conn, $filter) . "'";
}
$countResult = mysqli_query($conn, $countSql);
$totalProducts = mysqli_fetch_assoc($countResult)['total'];


$sql = "SELECT * FROM products WHERE status = 'biasa'";
if ($filter !== 'all') {
  $sql .= " AND category = '" . mysqli_real_escape_string($conn, $filter) . "'";
}
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $productsPerPage, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


$productsHtml = '';
if (mysqli_num_rows($result) > 0) {
  while ($product = mysqli_fetch_assoc($result)) {
    $productsHtml .= '
        <div class="product-card bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105 group"
             data-category="' . htmlspecialchars($product['category']) . '"
             data-id="' . $product['id'] . '"
             data-name="' . htmlspecialchars($product['name']) . '"
             data-price="' . $product['price'] . '"
             data-image="' . htmlspecialchars($product['image']) . '"
             data-size="' . htmlspecialchars($product['size']) . '">
          <div class="relative h-48 overflow-hidden">
            <img
              src="crudAdmin/product/' . htmlspecialchars($product['image']) . '"
              alt="' . htmlspecialchars($product['name']) . '"
              class="w-full h-full object-cover object-top transition-transform duration-300 group-hover:scale-110"
              loading="lazy" <!-- TAMBAHKAN INI -->
            />
          </div>
          <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              ' . htmlspecialchars($product['name']) . '
            </h3>
            <div class="flex items-center mb-2">
              <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded mr-2">
                ' . ucfirst($product['category']) . '
              </span>
              <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">
                ' . htmlspecialchars($product['size']) . '
              </span>
            </div>
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
              ' . htmlspecialchars($product['description']) . '
            </p>
            <div class="flex justify-between items-center">
              <p class="text-lg font-bold text-gray-900">Rp ' . number_format($product['price'], 0, ',', '.') . '</p>
              <button class="add-to-cart bg-primary hover:bg-primary/90 text-white p-2 rounded-full whitespace-nowrap transition-colors">
                <i class="fa-solid fa-basket-shopping"></i>
              </button>
            </div>
          </div>
        </div>';
  }
} else {
  $productsHtml = '<div class="text-center py-12 col-span-full"><p class="text-gray-500">Tidak ada produk yang tersedia</p></div>';
}

echo json_encode([
  'success' => true,
  'total_products' => $totalProducts,
  'products_html' => $productsHtml
]);
