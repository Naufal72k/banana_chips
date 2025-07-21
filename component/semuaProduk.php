<?php

?>

<section id="products" class="py-16 bg-white">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold text-gray-900 mb-4">Semua Produk</h2>
      <p class="text-gray-600 max-w-2xl mx-auto">
        Temukan berbagai varian rasa keripik pisang yang kami tawarkan.
      </p>
    </div>

    <!-- Filter Buttons -->
    <div class="flex justify-center mb-8">
      <div class="inline-flex bg-gray-100 p-1 rounded-full">
        <button data-filter="all" class="filter-btn px-4 py-2 rounded-full bg-primary text-white font-medium">
          Semua
        </button>
        <button data-filter="original"
          class="filter-btn px-4 py-2 rounded-full text-gray-700 hover:bg-gray-200 font-medium">
          Original
        </button>
        <button data-filter="manis"
          class="filter-btn px-4 py-2 rounded-full text-gray-700 hover:bg-gray-200 font-medium">
          Manis
        </button>
        <button data-filter="gurih"
          class="filter-btn px-4 py-2 rounded-full text-gray-700 hover:bg-gray-200 font-medium">
          Gurih
        </button>
        <button data-filter="pedas"
          class="filter-btn px-4 py-2 rounded-full text-gray-700 hover:bg-gray-200 font-medium">
          Pedas
        </button>
      </div>
    </div>

    <!-- Products Container -->
    <div id="products-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <!-- Ini akan diisi oleh JavaScript -->
    </div>

    <!-- Pagination -->
    <div id="pagination-container" class="flex justify-center mt-10">

    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const productsContainer = document.getElementById('products-container');
    const paginationContainer = document.getElementById('pagination-container');
    const productsPerPage = 8;

    let currentPage = 1;
    let currentFilter = 'all';
    let totalProducts = 0;
    let totalPages = 1;

    // Initialize
    updateProducts(currentPage, currentFilter);
    updateFilterButtons();

    // Filter products
    filterButtons.forEach(button => {
      button.addEventListener('click', function () {
        currentFilter = this.getAttribute('data-filter');
        currentPage = 1;
        updateProducts(currentPage, currentFilter);
        updateFilterButtons();
      });
    });

    function updateFilterButtons() {
      filterButtons.forEach(btn => {
        btn.classList.remove('bg-primary', 'text-white');
        btn.classList.add('text-gray-700', 'hover:bg-gray-200');
        if (btn.getAttribute('data-filter') === currentFilter) {
          btn.classList.add('bg-primary', 'text-white');
          btn.classList.remove('text-gray-700', 'hover:bg-gray-200');
        }
      });
    }

    function updateProducts(page, filter) {
      const formData = new FormData();
      formData.append('page', page);
      formData.append('filter', filter);
      formData.append('per_page', productsPerPage);

      fetch('fetchProducts.php', {
        method: 'POST',
        body: formData
      })
        .then(response => {
          if (!response.ok) throw new Error('Network response was not ok');
          return response.json();
        })
        .then(data => {
          currentPage = page;
          totalProducts = data.total_products;
          totalPages = Math.ceil(totalProducts / productsPerPage);

          productsContainer.innerHTML = data.products_html || '';

          updatePagination();

          attachAddToCartListeners();
        })
        .catch(error => {
          console.error('Error:', error);
          productsContainer.innerHTML = '<div class="text-center py-12 col-span-full"><p class="text-gray-500">Gagal memuat produk</p></div>';
        });
    }

    function updatePagination() {
      let paginationHTML = `
      <div class="inline-flex">
        <button onclick="changePage(${currentPage - 1})" class="px-4 py-2 border border-gray-300 rounded-l-lg hover:bg-gray-100 ${currentPage === 1 ? 'pointer-events-none opacity-50' : ''}">
          <i class="ri-arrow-left-s-line"></i>
        </button>
    `;

      const maxVisibleButtons = 5;
      let startPage, endPage;

      if (totalPages <= maxVisibleButtons) {
        startPage = 1;
        endPage = totalPages;
      } else {
        const maxButtonsBeforeCurrentPage = Math.floor(maxVisibleButtons / 2);
        const maxButtonsAfterCurrentPage = Math.ceil(maxVisibleButtons / 2) - 1;

        if (currentPage <= maxButtonsBeforeCurrentPage) {
          startPage = 1;
          endPage = maxVisibleButtons;
        } else if (currentPage + maxButtonsAfterCurrentPage >= totalPages) {
          startPage = totalPages - maxVisibleButtons + 1;
          endPage = totalPages;
        } else {
          startPage = currentPage - maxButtonsBeforeCurrentPage;
          endPage = currentPage + maxButtonsAfterCurrentPage;
        }
      }

      if (startPage > 1) {
        paginationHTML += `
        <button onclick="changePage(1)" class="px-4 py-2 border-t border-b border-gray-300 hover:bg-gray-100">
          1
        </button>
        <span class="px-4 py-2 border-t border-b border-gray-300">...</span>
      `;
      }

      for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
        <button onclick="changePage(${i})" class="px-4 py-2 border-t border-b border-gray-300 ${i === currentPage ? 'bg-primary text-white' : 'hover:bg-gray-100'}">
          ${i}
        </button>
      `;
      }

      if (endPage < totalPages) {
        paginationHTML += `
        <span class="px-4 py-2 border-t border-b border-gray-300">...</span>
        <button onclick="changePage(${totalPages})" class="px-4 py-2 border-t border-b border-gray-300 hover:bg-gray-100">
          ${totalPages}
        </button>
      `;
      }

      paginationHTML += `
        <button onclick="changePage(${currentPage + 1})" class="px-4 py-2 border border-gray-300 rounded-r-lg hover:bg-gray-100 ${currentPage === totalPages ? 'pointer-events-none opacity-50' : ''}">
          <i class="ri-arrow-right-s-line"></i>
        </button>
      </div>
    `;

      paginationContainer.innerHTML = paginationHTML;
    }

    function attachAddToCartListeners() {
      document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function (e) {
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

            if (typeof cart !== 'undefined') {
              cart.addItem(product);
            } else {
              console.error('Cart system not initialized');
            }
          }
        });
      });
    }

    window.changePage = function (newPage) {
      if (newPage >= 1 && newPage <= totalPages) {
        updateProducts(newPage, currentFilter);
      }
    };
  });
</script>