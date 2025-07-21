<?php


$sql = "SELECT * FROM products WHERE status = 'promo' ORDER BY created_at DESC LIMIT 2";
$result = mysqli_query($conn, $sql);
?>

<section id="promo" class="py-16 bg-gray-50">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold text-gray-900 mb-2">
        <span class="inline-block relative">
          <span class="relative z-10">Promo Spesial</span>
          <span class="absolute bottom-0 left-0 w-full h-2 bg-yellow-300 transform -rotate-1 z-0"></span>
        </span>
      </h2>
      <p class="text-gray-600 max-w-2xl mx-auto text-lg">
        ✨ <span class="font-semibold text-primary">Penawaran Terbatas!</span> Manfaatkan diskon spesial untuk produk
        favorit Anda ✨
      </p>
      <div class="mt-4">
        <div id="countdown-timer"
          class="inline-block bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium animate-pulse">
          WAKTU TERBATAS: <span id="countdown">10:00</span>
        </div>
      </div>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php while ($product = mysqli_fetch_assoc($result)):

          $discount = '';
          $original_price = '';
          $badge_color = 'bg-secondary';

          if (isset($product['original_price']) && $product['original_price'] > $product['price']) {
            $discount_percent = round(($product['original_price'] - $product['price']) / $product['original_price'] * 100);
            $discount = 'Diskon ' . $discount_percent . '%';
            $original_price = $product['original_price'];
            $badge_color = 'bg-red-500';
          } else {
            $discount = 'Promo Spesial';
          }
          ?>
          <div
            class="product-card bg-white rounded-lg shadow-md overflow-hidden flex flex-col md:flex-row transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
            data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>"
            data-price="<?= $product['price'] ?>" data-image="<?= htmlspecialchars($product['image']) ?>"
            data-size="<?= htmlspecialchars($product['size'] ?? '') ?>"
            data-category="<?= htmlspecialchars($product['category'] ?? 'promo') ?>">
            <div class="md:w-2/5 h-64 md:h-auto overflow-hidden">
              <img src="crudAdmin/product/<?= htmlspecialchars($product['image']) ?>"
                alt="<?= htmlspecialchars($product['name']) ?>"
                class="w-full h-full object-cover object-top transition-transform duration-500 hover:scale-105"
                loading="lazy" />
            </div>
            <div class="p-6 md:w-3/5 flex flex-col justify-center">
              <div
                class="inline-block <?= $badge_color ?> text-white px-4 py-1 rounded-full text-sm font-medium mb-4 animate-bounce">
                <?= $discount ?>
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-3 hover:text-primary transition-colors duration-300">
                <?= htmlspecialchars($product['name']) ?>
              </h3>
              <p class="text-gray-600 mb-4 hover:text-gray-800 transition-colors duration-300">
                <?= htmlspecialchars($product['description']) ?>
              </p>
              <div class="flex items-center mb-4">
                <?php if ($original_price): ?>
                  <p class="text-lg line-through text-gray-400 mr-2">Rp <?= number_format($original_price, 0, ',', '.') ?></p>
                <?php endif; ?>
                <p class="text-2xl font-bold text-primary hover:text-red-600 transition-colors duration-300">Rp
                  <?= number_format($product['price'], 0, ',', '.') ?></p>
              </div>
              <div class="flex flex-wrap gap-3">
                <button
                  class="add-to-cart-promo bg-primary hover:bg-primary/90 text-white py-2 px-6 !rounded-button whitespace-nowrap flex items-center transition-all duration-300 hover:shadow-md hover:scale-105">
                  <i class="ri-shopping-cart-add-line mr-2"></i>
                  Beli Sekarang
                </button>
                <!-- Tombol Detail dihapus sesuai permintaan -->
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="text-center py-8">
        <p class="text-gray-500">Tidak ada promo saat ini</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
  function startCountdown() {
    let totalSeconds = 600; // 10 minutes
    const countdownElement = document.getElementById('countdown');
    const countdownTimerDiv = document.getElementById('countdown-timer');
    const buyButtons = document.querySelectorAll('.add-to-cart-promo');

    const countdownInterval = setInterval(() => {
      const minutes = Math.floor(totalSeconds / 60);
      const seconds = totalSeconds % 60;

      countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

      if (totalSeconds <= 0) {
        clearInterval(countdownInterval);
        countdownElement.textContent = "PROMO BERAKHIR!";
        countdownTimerDiv.classList.add('bg-gray-500');
        countdownTimerDiv.classList.remove('animate-pulse', 'bg-red-500', 'bg-red-600');
        // Menonaktifkan tombol beli
        buyButtons.forEach(button => {
          button.disabled = true;
          button.classList.add('opacity-50', 'cursor-not-allowed');
          button.classList.remove('hover:bg-primary/90');
        });
        return;
      }

      totalSeconds--;

      if (totalSeconds < 120) {
        countdownTimerDiv.classList.add('bg-red-600');
        countdownTimerDiv.classList.remove('bg-red-500');
      }

      if (totalSeconds < 60) {
        countdownElement.classList.toggle('text-white');
        countdownElement.classList.toggle('text-yellow-300');
      }
    }, 1000);
  }

  window.addEventListener('DOMContentLoaded', startCountdown);
</script>