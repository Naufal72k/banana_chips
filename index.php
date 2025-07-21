
    <?php
    require_once 'component/auth.php';
    ?>
    <!DOCTYPE html>
    <html lang="id" class="scroll-smooth">
      <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Banana Chips - Renyah, Manis, Bikin Nagih!</title>
        <script src="https://kit.fontawesome.com/812225c7d3.js" crossorigin="anonymous"></script>
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
        <link
          href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
          rel="stylesheet"
        />
        <link
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet"
        />
      
        <style>
         
          body {
            font-family: "Poppins", sans-serif;
          }
          .custom-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #facc15;
            border-radius: 50%;
            cursor: pointer;
          }
          .custom-checkbox:checked + .checkbox-icon {
            background-color: #facc15;
            border-color: #facc15;
          }
          .custom-checkbox:checked + .checkbox-icon:after {
            content: "";
            position: absolute;
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
          }
          .custom-switch:checked + .switch-slider {
            background-color: #facc15;
          }
          .custom-switch:checked + .switch-slider:before {
            transform: translateX(18px);
          }
          .switch-slider:before {
            content: "";
            position: absolute;
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
          }
          .carousel {
            scroll-behavior: smooth;
          }
          .carousel::-webkit-scrollbar {
            display: none;
          }
        </style>
      </head>
      <body class="bg-white">
        <!-- Navbar -->
        <?php include "component/navbar.php"?>

        <!-- Hero Section -->
        <?php include "component/heroSection.html"?>

        <!-- Products utamanya -->
        <?php include "component/produkUngulan.php"?>

        <!-- Promo Section -->
        <?php include "component/produkPromo.php"?>

        <!-- Semua Products -->
        <?php include "component/semuaProduk.php"?>

        <!-- lokasi -->
        <?php include "component/lokasi.html"?>

        <!-- Testimonials -->
        <?php include "component/testimoni.php"?>

        <!-- Footer -->
        <?php include "component/footer.html"?>

        <!-- Keranjang (Modal) -->
        <?php include "component/keranjang.php"?>

        
        <script>
          // Hapus bagian ini karena sudah tidak diperlukan
          // if(typeof(Storage) !== 'undefined') {
          //     sessionStorage.setItem('cartCleared', 'true');
          // }

          // Hapus bagian ini karena sudah tidak diperlukan
          // document.querySelector('a[href="index.php"]').addEventListener('click', function(e) {
          //     if(!e.ctrlKey && !e.metaKey) { 
          //         e.preventDefault();
                  
          //         window.location.href = 'index.php?cartCleared=true';
          //     }
          // });
        </script>
      </body>
    </html>
    