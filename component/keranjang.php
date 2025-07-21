<div id="cartModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
  <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-900">
            <i class="ri-shopping-cart-2-line mr-2 text-primary"></i> Keranjang Belanja
          </h3>
          <button id="closeCartModal" class="text-gray-500 hover:text-gray-700">
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>

        <div id="cartItemsContainer" class="max-h-96 overflow-y-auto">
          <div class="text-center py-8 text-gray-500">Keranjang belanja kosong</div>
        </div>

        <div id="cartSummary" class="border-t border-gray-200 mt-4 pt-4">
          <div class="flex justify-between mb-2">
            <span class="text-gray-700">Subtotal</span>
            <span class="font-medium" id="cartSubtotal">Rp 0</span>
          </div>
          <div class="flex justify-between mb-2">
            <span class="text-gray-700">Ongkir</span>
            <span class="font-medium" id="cartShipping">Rp 10.000</span>
          </div>
          <div class="flex justify-between text-lg font-bold">
            <span>Total</span>
            <span class="text-primary" id="cartTotal">Rp 10.000</span>
          </div>
        </div>
      </div>

      <!-- Perubahan di sini: Hapus kelas 'sm:flex' dan 'sm:flex-row-reverse' dari div cartActions -->
      <div id="cartActions" class="bg-gray-50 px-4 py-3 flex flex-row-reverse">
        <button type="button" id="checkoutBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
          Bayar Sekarang
        </button>
        <button type="button" id="continueShoppingBtn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
          Lanjut Belanja
        </button>
      </div>

      <div id="checkoutFormContainer" class="hidden"></div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const cart = {
    items: [],
    addItem: function(product) {
      const existingItem = this.items.find(item => item.id == product.id);
      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        this.items.push({
          id: product.id,
          name: product.name,
          price: product.price,
          image: product.image,
          size: product.size || '-',
          category: product.category || '-',
          quantity: 1
        });
      }
      this.updateCart();
      this.showNotification(`${product.name} ditambahkan ke keranjang`);
    },
    removeItem: function(productId) {
      const removedItem = this.items.find(item => item.id == productId);
      this.items = this.items.filter(item => item.id != productId);
      this.updateCart();
      if (removedItem) {
        this.showNotification(`${removedItem.name} dihapus dari keranjang`);
      }
    },
    updateQuantity: function(productId, newQuantity) {
      const item = this.items.find(item => item.id == productId);
      if (item) {
        item.quantity = parseInt(newQuantity);
        if (item.quantity <= 0) {
          this.removeItem(productId);
        } else {
          this.updateCart();
        }
      }
    },
    getTotalItems: function() {
      return this.items.reduce((total, item) => total + item.quantity, 0);
    },
    getSubtotal: function() {
      return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    },
    updateCart: function() {
      const totalItems = this.getTotalItems();
      document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = totalItems;
        el.classList.toggle('hidden', totalItems === 0);
      });

      const cartItemsContainer = document.getElementById('cartItemsContainer');
      const checkoutBtn = document.getElementById('checkoutBtn');
      const cartSummary = document.getElementById('cartSummary');
      const cartActions = document.getElementById('cartActions');
      const checkoutFormContainer = document.getElementById('checkoutFormContainer');

      checkoutFormContainer.classList.add('hidden');
      cartItemsContainer.classList.remove('hidden');
      cartSummary.classList.remove('hidden');
      // Pastikan cartActions selalu terlihat saat updateCart dipanggil,
      // karena ini adalah tampilan default keranjang.
      cartActions.classList.remove('hidden'); 

      if (this.items.length === 0) {
        cartItemsContainer.innerHTML = '<div class="text-center py-8 text-gray-500">Keranjang belanja kosong</div>';
        checkoutBtn.disabled = true;
      } else {
        cartItemsContainer.innerHTML = this.items.map(item => `
          <div class="flex items-center py-4 border-b border-gray-100">
            <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-md overflow-hidden">
              <img src="${item.image.startsWith('http') ? item.image : 'crudAdmin/product/' + item.image}" alt="${item.name}" class="w-full h-full object-cover" loading="lazy">
            </div>
            <div class="ml-4 flex-grow">
              <h4 class="text-sm font-medium text-gray-900">${item.name}</h4>
              <p class="text-xs text-gray-500">${item.size} • ${item.category}</p>
              <p class="text-sm text-primary font-semibold">Rp ${item.price.toLocaleString('id-ID')}</p>
              <div class="flex items-center mt-2">
                <button class="quantity-btn minus w-6 h-6 flex items-center justify-center border border-gray-300 rounded" data-id="${item.id}">-</button>
                <input type="number" class="quantity-input w-10 text-center border-t border-b border-gray-300" value="${item.quantity}" min="1" data-id="${item.id}">
                <button class="quantity-btn plus w-6 h-6 flex items-center justify-center border border-gray-300 rounded" data-id="${item.id}">+</button>
              </div>
            </div>
            <button class="remove-item ml-4 text-gray-500 hover:text-red-500" data-id="${item.id}">
              <i class="ri-delete-bin-line"></i>
            </button>
          </div>
        `).join('');

        checkoutBtn.disabled = false;
      }

      const subtotal = this.getSubtotal();
      const shipping = 10000;
      const total = subtotal + shipping;

      document.getElementById('cartSubtotal').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
      document.getElementById('cartShipping').textContent = `Rp ${shipping.toLocaleString('id-ID')}`;
      document.getElementById('cartTotal').textContent = `Rp ${total.toLocaleString('id-ID')}`;

      localStorage.setItem('shoppingCart', JSON.stringify(this.items));
      this.saveCartToSession();
    },
    loadCart: function() {
      const clearCartFlag = sessionStorage.getItem('clearCartOnLoad');
      if (clearCartFlag === 'true') {
          this.items = [];
          localStorage.removeItem('shoppingCart');
          sessionStorage.removeItem('clearCartOnLoad');
          this.updateCart();
          return;
      }

      const tempCartData = <?php echo isset($_SESSION['temp_cart_data']) ? json_encode($_SESSION['temp_cart_data']) : 'null'; ?>;
      if (tempCartData !== null) {
          this.items = tempCartData;
          <?php unset($_SESSION['temp_cart_data']); ?>
          this.updateCart();
          return;
      }

      const savedCart = localStorage.getItem('shoppingCart');
      if (savedCart) {
        try {
          this.items = JSON.parse(savedCart);
          this.updateCart();
        } catch (e) {
          console.error('Error parsing cart data:', e);
          localStorage.removeItem('shoppingCart');
        }
      }
    },
    saveCartToSession: function() {
      const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
      if (isLoggedIn) {
          const userId = <?php echo getUserId() ?? 'null'; ?>;
          if (userId) {
              fetch('save_cart_to_session.php', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                  },
                  body: JSON.stringify({ cart: this.items, user_id: userId }),
              })
              .then(response => response.json())
              .then(data => {
                  if (!data.success) {
                      console.error('Failed to save cart to session:', data.message);
                  }
              })
              .catch(error => {
                  console.error('Error saving cart to session:', error);
              });
          }
      }
    },
    showNotification: function(message) {
      const notification = document.createElement('div');
      notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg flex items-center animate-fade-in z-[1000]';
      notification.innerHTML = `
        <i class="ri-checkbox-circle-fill mr-2"></i>
        <span>${message}</span>
      `;
      document.body.appendChild(notification);
      setTimeout(() => {
        notification.classList.add('animate-fade-out');
        setTimeout(() => notification.remove(), 300);
      }, 3000);
    }
  };

  cart.loadCart();

  document.querySelectorAll('a[href="component/logout.php"]').forEach(link => {
      link.addEventListener('click', function(e) {
          e.preventDefault();
          cart.saveCartToSession();
          sessionStorage.setItem('clearCartOnLoad', 'true');
          setTimeout(() => {
              window.location.href = this.href;
          }, 100);
      });
  });

  document.addEventListener('click', function(e) {
    if (e.target.closest('.add-to-cart') || e.target.classList.contains('ri-shopping-cart-add-line')) {
      const productCard = e.target.closest('.product-card') || e.target.closest('.add-to-cart').closest('.product-card');
      if (productCard) {
        const product = {
          id: productCard.dataset.id || Math.random().toString(36).substr(2, 9),
          name: productCard.dataset.name || 'Produk',
          price: parseInt(productCard.dataset.price) || 0,
          image: productCard.dataset.image || 'placeholder.jpg',
          size: productCard.dataset.size,
          category: productCard.dataset.category
        };
        cart.addItem(product);
      }
    }

    if (e.target.closest('.add-to-cart-promo')) {
      const productCard = e.target.closest('.product-card');
      if (productCard) {
        const product = {
          id: productCard.dataset.id,
          name: productCard.dataset.name,
          price: parseInt(productCard.dataset.price),
          image: productCard.dataset.image,
          size: productCard.dataset.size,
          category: productCard.dataset.category
        };
        cart.addItem(product);
      }
    }

    if (e.target.closest('.cart-icon') || e.target.classList.contains('ri-shopping-cart-2-line')) {
      document.getElementById('cartModal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    if (e.target.id === 'closeCartModal' || e.target.closest('#closeCartModal')) {
      document.getElementById('cartModal').classList.add('hidden');
      document.body.style.overflow = 'auto';
      document.getElementById('cartItemsContainer').classList.remove('hidden');
      document.getElementById('cartSummary').classList.remove('hidden');
      document.getElementById('cartActions').classList.remove('hidden');
      document.getElementById('checkoutFormContainer').classList.add('hidden');
    }

    if (e.target.closest('#continueShoppingBtn')) {
      document.getElementById('cartModal').classList.add('hidden');
      document.body.style.overflow = 'auto';
      document.getElementById('cartItemsContainer').classList.remove('hidden');
      document.getElementById('cartSummary').classList.remove('hidden');
      document.getElementById('cartActions').classList.remove('hidden');
      document.getElementById('checkoutFormContainer').classList.add('hidden');
    }

    if (e.target.closest('.remove-item')) {
      const productId = e.target.closest('.remove-item').dataset.id;
      cart.removeItem(productId);
    }

    if (e.target.classList.contains('quantity-btn')) {
      const btn = e.target;
      const input = btn.parentElement.querySelector('.quantity-input');
      let newValue = parseInt(input.value);

      if (btn.classList.contains('minus')) {
        newValue = Math.max(1, newValue - 1);
      } else if (btn.classList.contains('plus')) {
        newValue += 1;
      }

      input.value = newValue;
      cart.updateQuantity(btn.dataset.id, newValue);
    }

    if (e.target.id === 'checkoutBtn') {
      const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;

      if (!isLoggedIn) {
        alert('Anda harus login terlebih dahulu untuk melanjutkan pemesanan.');
        fetch('set_redirect.php?url=' + encodeURIComponent(window.location.href))
          .then(() => {
            window.location.href = 'component/login.php';
          });
        return;
      }

      // Sembunyikan elemen-elemen keranjang saat formulir checkout ditampilkan
      document.getElementById('cartItemsContainer').classList.add('hidden');
      document.getElementById('cartSummary').classList.add('hidden');
      document.getElementById('cartActions').classList.add('hidden'); // Ini yang utama

      // Tampilkan formulir checkout
      document.getElementById('checkoutFormContainer').classList.remove('hidden');

      const checkoutFormHtml = `
        <form id="checkoutForm" action="crudAdmin/orders/insert.php" method="POST">
          <div class="p-6">
            <h4 class="text-lg font-bold mb-4">Informasi Pelanggan</h4>
            <div class="space-y-4">
              <div>
                <label class="block text-gray-700 mb-2">Nama Lengkap*</label>
                <input type="text" name="customer_name" class="w-full px-4 py-2 border rounded" value="<?= htmlspecialchars(getUsername()) ?>" required>
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Nomor WhatsApp*</label>
                <input type="tel" name="customer_phone" class="w-full px-4 py-2 border rounded" required>
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Alamat Lengkap*</label>
                <textarea name="customer_address" class="w-full px-4 py-2 border rounded" rows="3" required></textarea>
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Metode Pembayaran*</label>
                <select name="payment_method" class="w-full px-4 py-2 border rounded" required>
                  <option value="transfer">Transfer Bank</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" class="w-full px-4 py-2 border rounded" rows="2"></textarea>
              </div>
              <input type="hidden" name="cart_items" value='${JSON.stringify(cart.items)}'>
              <input type="hidden" name="subtotal" value="${cart.getSubtotal()}">
              <input type="hidden" name="shipping" value="10000">
              <input type="hidden" name="total" value="${cart.getSubtotal() + 10000}">
              <input type="hidden" name="user_id" value="<?= getUserId() ?>">
            </div>
            <div class="mt-6 flex space-x-4">
              <button type="button" id="backToCartBtn" class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded hover:bg-gray-300">
                Kembali
              </button>
              <button type="submit" class="flex-1 bg-primary text-white py-2 px-4 rounded hover:bg-primary/90">
                Konfirmasi Pesanan
              </button>
            </div>
          </div>
        </form>
      `;
      document.getElementById('checkoutFormContainer').innerHTML = checkoutFormHtml;

      document.getElementById('backToCartBtn').addEventListener('click', function() {
        document.getElementById('checkoutFormContainer').classList.add('hidden');
        document.getElementById('cartItemsContainer').classList.remove('hidden');
        document.getElementById('cartSummary').classList.remove('hidden');
        document.getElementById('cartActions').classList.remove('hidden'); // Tampilkan kembali
      });
    }
  });

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('quantity-input')) {
      const input = e.target;
      const newValue = Math.max(1, parseInt(input.value) || 1);
      input.value = newValue;
      cart.updateQuantity(input.dataset.id, newValue);
    }
  });

  const style = document.createElement('style');
  style.textContent = `
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
      from { opacity: 1; transform: translateY(0); }
      to { opacity: 0; transform: translateY(-20px); }
    }
    .animate-fade-in {
      animation: fadeIn 0.3s ease-out forwards;
    }
    .animate-fade-out {
      animation: fadeOut 0.3s ease-out forwards;
    }
  `;
  document.head.appendChild(style);

  function formatRupiah(angka) {
    if (!angka) return 'Rp 0';
    const reverse = angka.toString().split('').reverse().join('');
    let ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return 'Rp ' + ribuan;
  }
});
</script>
