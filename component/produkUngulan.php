<?php


include "config/database.php";

$sql = "SELECT * FROM products WHERE status = 'unggulan' ORDER BY created_at DESC LIMIT 3";
$result = mysqli_query($conn, $sql);
?>

<section id="featured" class="py-16 bg-white">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold text-gray-900 mb-4">
        Produk Unggulan Kami
      </h2>
      <p class="text-gray-600 max-w-2xl mx-auto">
        Rasakan kelezatan keripik pisang premium dengan berbagai pilihan rasa
        yang akan memanjakan lidah Anda.
      </p>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php while ($product = mysqli_fetch_assoc($result)):
          $badge = '';
          $badgeClass = '';
          if ($product['category'] === 'manis') {
            $badge = 'Terlaris';
            $badgeClass = 'bg-primary/10 text-primary';
          } elseif ($product['category'] === 'gurih') {
            $badge = 'Favorit';
            $badgeClass = 'bg-secondary/10 text-secondary';
          } else {
            $badge = 'Baru';
            $badgeClass = 'bg-primary/10 text-primary';
          }
          ?>
          <div class="product-card bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105"
            data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>"
            data-price="<?= $product['price'] ?>" data-image="<?= htmlspecialchars($product['image']) ?>"
            data-size="<?= htmlspecialchars($product['size']) ?>"
            data-category="<?= htmlspecialchars($product['category']) ?>">
            <div class="h-64 overflow-hidden">
              <img src="crudAdmin/product/<?= htmlspecialchars($product['image']) ?>"
                alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover" loading="lazy" />
            </div>
            <div class="p-6">
              <div class="flex justify-between items-center mb-3">
                <h3 class="text-xl font-semibold text-gray-900">
                  <?= htmlspecialchars($product['name']) ?>
                </h3>
                <?php if ($badge): ?>
                  <span class="<?= $badgeClass ?> px-3 py-1 rounded-full text-sm font-medium">
                    <?= $badge ?>
                  </span>
                <?php endif; ?>
              </div>
              <p class="text-gray-600 mb-4">
                <?= htmlspecialchars($product['description']) ?>
              </p>
              <div class="flex justify-between items-center">
                <div>
                  <p class="text-lg font-bold text-gray-900">Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
                  <p class="text-sm text-gray-500">per <?= htmlspecialchars($product['size']) ?></p>
                </div>
                <button class="add-to-cart bg-primary hover:bg-primary/90 text-white p-3 !rounded-button whitespace-nowrap">
                  <i class="fa-solid fa-basket-shopping"></i>
                </button>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="text-center py-8">
        <p class="text-gray-500">Tidak ada produk unggulan saat ini</p>
      </div>
    <?php endif; ?>

    <div class="text-center mt-10">
      <a href="#products" class="inline-flex items-center text-secondary font-medium hover:underline">
        Lihat Semua Produk
        <i class="ri-arrow-right-line ml-2"></i>
      </a>
    </div>
  </div>
</section>