<?php
include_once 'connect.php';

// Fetch all doctors grouped by specialization
$doctors_query = "SELECT * FROM doctors ORDER BY specialization ASC, name ASC";
$doctors_result = $conn->query($doctors_query);

// Group doctors by specialization
$doctors_by_specialization = [];
if ($doctors_result->num_rows > 0) {
    while ($doctor = $doctors_result->fetch_assoc()) {
        $specialization = $doctor['specialization'];
        if (!isset($doctors_by_specialization[$specialization])) {
            $doctors_by_specialization[$specialization] = [];
        }
        $doctors_by_specialization[$specialization][] = $doctor;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Rumah Sakit Salak dr. H. Sadjiman Bogor - Daftar Dokter">
    <meta name="keywords" content="rumah sakit, bogor, dokter, kesehatan">
    <meta name="author" content="RS Salak dr. H. Sadjiman">
    
    <title>Daftar Dokter - RS Salak dr. H. Sadjiman</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f766e',
                        secondary: '#06b6d4',
                        accent: '#f59e0b'
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
    </style>
</head>
<body class="bg-gray-50 scroll-smooth font-sans">

    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <img src="assets/img/logo.png" 
                         alt="Logo RS Salak dr. H. Sadjiman"
                         class="h-10 w-10 object-contain"
                         loading="lazy">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 leading-tight">
                            <a href="index.php" class="hover:text-primary transition-colors focus-visible">
                                RS Salak <span class="hidden sm:inline">dr. H. Sadjiman</span>
                            </a>
                        </h1>
                        <p class="text-xs text-gray-600 hidden sm:block">Bogor</p>
                    </div>
                </div>

                <nav class="hidden lg:flex items-center space-x-8" role="navigation">
                    <a href="index.php#home" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Beranda</a>
                    <a href="index.php#layanan" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Layanan</a>
                    <a href="index.php#visi-misi" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Visi & Misi</a>
                    <a href="doctors.php" class="text-primary font-semibold">Dokter</a>
                    <a href="index.php#berita" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Berita</a>
                    <a href="index.php#kontak" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Kontak</a>
                </nav>

                <div class="flex items-center space-x-4">
                    <a href="https://wa.me/6281281900808" 
                       class="hidden md:inline-flex items-center bg-primary text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors focus-visible">
                        <i class="fab fa-whatsapp mr-2"></i>
                        WhatsApp
                    </a>
                    
                    <button id="mobile-menu-toggle" 
                            class="lg:hidden flex items-center justify-center w-10 h-10 text-gray-700 hover:text-primary focus-visible rounded-md"
                            aria-label="Toggle mobile menu"
                            aria-expanded="false">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav id="mobile-menu" 
             class="lg:hidden bg-white border-t border-gray-200 hidden"
             role="navigation">
            <div class="container mx-auto px-4 py-4 space-y-4">
                <a href="index.php#home" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-home mr-3 w-5"></i>Beranda
                </a>
                <a href="index.php#layanan" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-hospital-alt mr-3 w-5"></i>Layanan
                </a>
                <a href="index.php#visi-misi" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-bullseye mr-3 w-5"></i>Visi & Misi
                </a>
                <a href="doctors.php" class="block text-primary font-semibold py-2">
                    <i class="fas fa-user-md mr-3 w-5"></i>Dokter
                </a>
                <a href="index.php#berita" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-newspaper mr-3 w-5"></i>Berita
                </a>
                <a href="index.php#kontak" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-phone mr-3 w-5"></i>Kontak
                </a>
                <div class="border-t border-gray-200 pt-4">
                    <a href="https://wa.me/081281900808" 
                       class="flex items-center justify-center bg-primary text-white px-4 py-3 rounded-lg hover:bg-teal-700 transition-colors focus-visible">
                        <i class="fab fa-whatsapp mr-2"></i>
                        Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main id="main-content" class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">
            <i class="fas fa-user-md mr-2 text-primary"></i> Daftar Dokter
        </h2>

        <?php if (!empty($doctors_by_specialization)): ?>
            <?php foreach ($doctors_by_specialization as $specialization => $doctors): ?>
                <!-- Specialization Section -->
                <section class="mb-16">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-stethoscope mr-3 text-primary"></i>
                            <?php echo htmlspecialchars($specialization); ?>
                        </h3>
                        <div class="h-1 w-20 bg-primary rounded-full"></div>
                        <p class="text-gray-600 mt-2">
                            <?php echo count($doctors); ?> dokter tersedia
                        </p>
                    </div>

                    <!-- Doctors Grid for this specialization -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        <?php foreach ($doctors as $doctor): ?>
                            <div class="bg-white rounded-2xl shadow-lg p-6 text-center card-hover">
                                <div class="h-48 bg-gray-200 overflow-hidden rounded-xl mb-4">
                                    <?php 
                                    // Handle both old image_url and new image columns
                                    $image_src = '';
                                    if (!empty($doctor['image'])) {
                                        // New file-based system
                                        $image_path = 'assets/doctors/' . $doctor['image'];
                                        $image_src = file_exists($image_path) ? $image_path : '';
                                    } elseif (!empty($doctor['image_url'])) {
                                        // Legacy URL-based system
                                        $image_src = $doctor['image_url'];
                                    }
                                    
                                    // Fallback to default image if no image found
                                    if (empty($image_src)) {
                                        $image_src = 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=300&h=300&fit=crop&crop=face';
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($image_src) ?>" 
                                        alt="Dr. <?= htmlspecialchars($doctor['name']) ?> - <?= htmlspecialchars($doctor['specialization']) ?>"
                                        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                        loading="lazy"
                                        onerror="this.src='https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=300&h=300&fit=crop&crop=face'">
                                </div>
                                
                                <h4 class="text-xl font-semibold text-gray-800 mb-2">
                                    <?php echo htmlspecialchars($doctor['name']); ?>
                                </h4>
                                
                                <!-- <?php if (!empty($doctor['experience'])): ?>
                                    <p class="text-sm text-gray-500 mb-3 flex items-center justify-center">
                                        <i class="fas fa-award mr-2 text-primary"></i>
                                        <?php echo htmlspecialchars($doctor['experience']); ?> tahun pengalaman
                                    </p>
                                <?php endif; ?> -->

                                <?php if (!empty($doctor['schedule'])): ?>
                                    <p class="text-sm text-gray-500 flex items-center justify-center">
                                        <i class="fas fa-clock mr-2 text-primary"></i>
                                        <?php echo htmlspecialchars($doctor['schedule']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-16">
                <i class="fas fa-user-md text-6xl text-gray-300 mb-4"></i>
                <p class="text-xl text-gray-600">Belum ada data dokter tersedia.</p>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Toggle mobile menu
        const toggle = document.getElementById('mobile-menu-toggle');
        const menu = document.getElementById('mobile-menu');
        toggle.addEventListener('click', () => {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', !expanded);
            menu.classList.toggle('hidden');
        });
    </script>

</body>
</html>