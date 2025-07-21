<?php
// testimoni.php
function getInitials($name)
{
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    return $initials;
}

function getRandomColor()
{
    $colors = [
        'bg-blue-200',
        'bg-green-200',
        'bg-red-200',
        'bg-purple-200',
        'bg-indigo-200',
        'bg-pink-200',
        'bg-yellow-200',
        'bg-teal-200'
    ];
    return $colors[array_rand($colors)];
}

// Ambil data testimoni dari database
$testimonials = [];
$query = mysqli_query($conn, "SELECT t.*, u.username as nama_lengkap 
                      FROM testimonials t 
                      JOIN users u ON t.user_id = u.id 
                      WHERE t.status = 'published' 
                      ORDER BY t.created_at DESC");

if ($query && mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $testimonials[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Testimonials</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .testimonial-card {
            scroll-snap-align: start;
            flex: 0 0 calc(100% - 2rem);
        }

        @media (min-width: 768px) {
            .testimonial-card {
                flex: 0 0 calc(50% - 2rem);
            }
        }

        @media (min-width: 1024px) {
            .testimonial-card {
                flex: 0 0 calc(33.333% - 2rem);
            }
        }

        .carousel-container {
            scroll-behavior: smooth;
        }

        .hide-scrollbar {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body>
    <section id="testimonials" class="py-16 bg-gray-50 relative">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Apa Kata Pelanggan Kami
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Lihat bagaimana produk kami telah menjadi favorit banyak orang.
                </p>
            </div>

            <div class="relative">
                <!-- Navigation buttons -->
                <button
                    class="absolute -left-4 top-1/2 -translate-y-1/2 bg-white rounded-full shadow-md p-3 z-10 hidden md:block"
                    id="carousel-prev">
                    <i class="ri-arrow-left-s-line text-gray-700 text-xl"></i>
                </button>
                <button
                    class="absolute -right-4 top-1/2 -translate-y-1/2 bg-white rounded-full shadow-md p-3 z-10 hidden md:block"
                    id="carousel-next">
                    <i class="ri-arrow-right-s-line text-gray-700 text-xl"></i>
                </button>

                <!-- Dot indicators -->
                <div class="flex justify-center gap-2 mt-6" id="carousel-dots"></div>

                <!-- Carousel container -->

                <div class="carousel-container flex overflow-x-auto snap-x snap-mandatory gap-8 pb-8 px-1 -mx-4 hide-scrollbar"
                    id="carousel">
                    <?php if (!empty($testimonials)): ?>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <div class="testimonial-card bg-white rounded-lg shadow-md p-6 flex flex-col">
                                <div class="flex items-center mb-4">
                                    <div
                                        class="w-12 h-12 rounded-full overflow-hidden mr-4 flex items-center justify-center <?= getRandomColor() ?> text-gray-700 font-bold text-xl">
                                        <?= getInitials(htmlspecialchars($testimonial['nama_lengkap'])) ?>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">
                                            <?= htmlspecialchars($testimonial['nama_lengkap']) ?></h4>
                                    </div>
                                </div>
                                <div class="flex mb-4 text-yellow-400">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $testimonial['rating']): ?>
                                            <i class="ri-star-fill"></i>
                                        <?php else: ?>
                                            <i class="ri-star-line"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-gray-700 mb-4">"<?= htmlspecialchars($testimonial['comment']) ?>"</p>
                                <p class="text-sm text-gray-500 mt-auto">
                                    <?= date('d M Y', strtotime($testimonial['created_at'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div
                            class="testimonial-card bg-white rounded-lg shadow-md p-6 flex flex-col items-center justify-center">
                            <p class="text-gray-500">Belum ada testimoni yang tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const carousel = document.getElementById('carousel');
            const prevBtn = document.getElementById('carousel-prev');
            const nextBtn = document.getElementById('carousel-next');
            const dotsContainer = document.getElementById('carousel-dots');
            const testimonials = document.querySelectorAll('.testimonial-card');

            let currentIndex = 0;
            let autoScrollInterval;
            const scrollDuration = 300;
            const autoScrollDelay = 5000; // 5 seconds
            let isAutoScrolling = true;

            // Create dot indicators
            testimonials.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.className = `w-3 h-3 rounded-full ${index === 0 ? 'bg-blue-500' : 'bg-gray-300'}`;
                dot.addEventListener('click', () => {
                    goToSlide(index);
                });
                dotsContainer.appendChild(dot);
            });

            const dots = document.querySelectorAll('#carousel-dots button');

            // Function to scroll to specific slide
            function goToSlide(index) {
                currentIndex = index;
                const cardWidth = testimonials[0].offsetWidth + 32; // including gap
                carousel.scrollTo({
                    left: index * cardWidth,
                    behavior: 'smooth'
                });
                updateDots();
                resetAutoScroll();
            }

            // Update active dot
            function updateDots() {
                dots.forEach((dot, index) => {
                    dot.className = `w-3 h-3 rounded-full ${index === currentIndex ? 'bg-blue-500' : 'bg-gray-300'}`;
                });
            }

            // Auto-scroll functionality
            function startAutoScroll() {
                autoScrollInterval = setInterval(() => {
                    if (isAutoScrolling) {
                        currentIndex = (currentIndex + 1) % testimonials.length;
                        goToSlide(currentIndex);
                    }
                }, autoScrollDelay);
            }

            function resetAutoScroll() {
                clearInterval(autoScrollInterval);
                startAutoScroll();
            }

            // Pause auto-scroll on hover
            carousel.addEventListener('mouseenter', () => {
                isAutoScrolling = false;
            });

            carousel.addEventListener('mouseleave', () => {
                isAutoScrolling = true;
            });

            // Manual navigation
            function handlePrevClick() {
                currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
                goToSlide(currentIndex);
            }

            function handleNextClick() {
                currentIndex = (currentIndex + 1) % testimonials.length;
                goToSlide(currentIndex);
            }

            prevBtn.addEventListener('click', handlePrevClick);
            nextBtn.addEventListener('click', handleNextClick);

            // Handle touch events for mobile swipe
            let touchStartX = 0;
            let touchEndX = 0;

            carousel.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].clientX;
            });

            carousel.addEventListener('touchmove', (e) => {
                touchEndX = e.touches[0].clientX;
            });

            carousel.addEventListener('touchend', () => {
                if (touchEndX < touchStartX - 50) {
                    handleNextClick(); // Swipe left
                } else if (touchEndX > touchStartX + 50) {
                    handlePrevClick(); // Swipe right
                }
            });

            // Initialize
            if (testimonials.length > 0) {
                startAutoScroll();
            }

            // Handle responsive resizing
            window.addEventListener('resize', () => {
                goToSlide(currentIndex);
            });
        });
    </script>
</body>

</html>