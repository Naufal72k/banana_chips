<nav class="sticky top-0 z-50 bg-white shadow-md">
  <div class="container mx-auto px-4 py-3 flex items-center justify-between">

    <div class="flex items-center">
      <h1 class="text-3xl font-['Pacifico'] text-primary">Banana Chips</h1>
    </div>

    <div class="hidden md:flex items-center justify-center space-x-6 absolute left-1/2 transform -translate-x-1/2">
      <a href="#home" class="text-primary font-medium">Beranda</a>
      <a href="#products" class="text-gray-700 hover:text-primary transition">Produk</a>
      <a href="#promo" class="text-gray-700 hover:text-primary transition">Promo</a>
      <a href="#testimonials" class="text-gray-700 hover:text-primary transition">Testimoni</a>
      <a href="#location" class="text-gray-700 hover:text-primary transition">Lokasi</a>
      <a href="#contact" class="text-gray-700 hover:text-primary transition">Kontak</a>
      <?php if (isLoggedIn()): ?>
        <a href="history.php" class="text-gray-700 hover:text-primary transition">Riwayat Pesanan</a>
      <?php endif; ?>
    </div>


    <div class="flex items-center space-x-4">
      <div class="relative w-10 h-10 flex items-center justify-center cursor-pointer cart-icon">
        <i class="ri-shopping-cart-2-line text-xl text-gray-700"></i>
        <span class="cart-count absolute -top-1 -right-1 bg-primary text-xs text-white rounded-full w-5 h-5 flex items-center justify-center">0</span>
      </div>

      <?php if (isLoggedIn()): ?>
        <div class="hidden md:flex items-center space-x-2">
          <span class="text-gray-700 text-sm font-medium">Halo, <?= htmlspecialchars(getUsername()) ?></span>
          <a href="component/logout.php" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-full text-sm transition">Logout</a>
        </div>
      <?php else: ?>
        <div class="hidden md:flex items-center space-x-2">
          <a href="component/login.php" class="bg-primary hover:bg-primary/90 text-white py-2 px-4 rounded-full text-sm transition">Login</a>
          <a href="component/register.php" class="border border-primary text-primary hover:bg-primary/10 py-2 px-4 rounded-full text-sm transition">Register</a>
        </div>
      <?php endif; ?>

      <button class="md:hidden w-10 h-10 flex items-center justify-center">
        <i class="ri-menu-line text-2xl text-gray-700"></i>
      </button>
    </div>
  </div>
</nav>
