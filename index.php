<?php
include_once 'connect.php';

// Pagination settings
$limit = 6; // number of doctors per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Preserve existing query parameters except 'page'
$queryParams = $_GET;
unset($queryParams['page']);
$baseUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query($queryParams);

// Count total doctors
$resultCount = $conn->query("SELECT COUNT(*) AS total FROM doctors");
$rowCount = $resultCount->fetch_assoc();
$totalDoctors = $rowCount['total'];
$totalPages = ceil($totalDoctors / $limit);

// Fetch doctors
$doctors_query = "SELECT * FROM doctors ORDER BY id DESC LIMIT $limit OFFSET $offset";
$doctors_result = $conn->query($doctors_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Rumah Sakit Salak dr. H. Sadjiman Bogor - Memberikan pelayanan kesehatan terbaik dengan fasilitas modern dan tenaga medis profesional">
    <meta name="keywords" content="rumah sakit, bogor, kesehatan, dokter, pelayanan medis">
    <meta name="author" content="RS Salak dr. H. Sadjiman">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="RS Salak dr. H. Sadjiman Bogor">
    <meta property="og:description" content="Pelayanan kesehatan terbaik di Bogor">
    <meta property="og:image" content="assets/img/og-image.jpg">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="RS Salak dr. H. Sadjiman Bogor">
    
    <title>Rumah Sakit Salak dr. H. Sadjiman - Pelayanan Kesehatan Terbaik di Bogor</title>
    
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
        
        .hero-gradient {
            background: linear-gradient(135deg, #0f766e 0%, #06b6d4 100%);
        }
        
        .scroll-smooth {
            scroll-behavior: smooth;
        }
        
        .loading {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        .focus-visible:focus {
            outline: 2px solid #0f766e;
            outline-offset: 2px;
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .mobile-menu-enter {
            animation: slideDown 0.3s ease-out forwards;
        }
        
        .mobile-menu-exit {
            animation: slideUp 0.3s ease-in forwards;
        }
        
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes slideUp {
            from { transform: translateY(0); opacity: 1; }
            to { transform: translateY(-100%); opacity: 0; }
        }
    </style>
</head>

<body class="bg-gray-50 scroll-smooth font-sans">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary text-white px-4 py-2 rounded-md z-50">
        Skip to main content
    </a>

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
                            <a href="#" class="hover:text-primary transition-colors focus-visible">
                                RS Salak <span class="hidden sm:inline">dr. H. Sadjiman</span>
                            </a>
                        </h1>
                        <p class="text-xs text-gray-600 hidden sm:block">Bogor</p>
                    </div>
                </div>

                <nav class="hidden lg:flex items-center space-x-8" role="navigation">
                    <a href="#home" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Beranda</a>
                    <a href="#layanan" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Layanan</a>
                    <a href="#visi-misi" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Visi & Misi</a>
                    <a href="#dokter" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Dokter</a>
                    <a href="#berita" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Berita</a>
                    <a href="#kontak" class="text-gray-700 hover:text-primary transition-colors focus-visible font-medium">Kontak</a>
                </nav>

                <div class="flex items-center space-x-4">
                    <a href="https://wa.me/081281900808" 
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
                <a href="#home" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-home mr-3 w-5"></i>Beranda
                </a>
                <a href="#layanan" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-hospital-alt mr-3 w-5"></i>Layanan
                </a>
                <a href="#visi-misi" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-bullseye mr-3 w-5"></i>Visi & Misi
                </a>
                <a href="#dokter" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-user-md mr-3 w-5"></i>Dokter
                </a>
                <a href="#berita" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
                    <i class="fas fa-newspaper mr-3 w-5"></i>Berita
                </a>
                <a href="#kontak" class="block text-gray-700 hover:text-primary transition-colors focus-visible font-medium py-2">
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

    <main id="main-content">
        <section id="home" class="hero-gradient text-white">
            <div class="container mx-auto px-4 py-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="text-center lg:text-left">
                        <h2 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                            Rumah Sakit Salak 
                            <span class="block text-secondary">dr. H. Sadjiman</span>
                        </h2>
                        <p class="text-xl lg:text-2xl mb-8 text-gray-100 leading-relaxed">
                            Komitmen Kami untuk Kesehatan dan Kenyamanan Anda.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="https://wa.me/081281900808" 
                               class="bg-white text-primary px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition-colors focus-visible inline-flex items-center justify-center">
                                <i class="fab fa-whatsapp mr-2"></i>
                                Hubungi Kami
                            </a>
                            <a href="#layanan" 
                               class="border-2 border-white text-white px-8 py-4 rounded-full font-semibold hover:bg-white hover:text-primary transition-colors focus-visible inline-flex items-center justify-center">
                                <i class="fas fa-hospital-alt mr-2"></i>
                                Lihat Layanan
                            </a>
                        </div>
                        
                        <!-- Contact Info -->
                        <div class="mt-8 flex flex-col sm:flex-row gap-4 text-sm text-gray-200 justify-center lg:justify-start">
                            <div class="flex items-center justify-center lg:justify-start">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Jl. Jend. Sudirman No.8, Bogor
                            </div>
                            <div class="flex items-center justify-center lg:justify-start">
                                <i class="fas fa-phone mr-2"></i>
                                0812-8190-0808
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <img src="assets/img/rssalak.jpg" 
                             alt="Ilustrasi modern rumah sakit dengan fasilitas medis terkini" 
                             class="max-w-full h-auto rounded-2xl shadow-2xl"
                             loading="lazy"
                             width="600"
                             height="400">
                    </div>
                </div>
            </div>
        </section>

        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-12">
                    <section id="layanan" class="bg-white rounded-2xl shadow-lg p-8">
                        <h3 class="text-3xl font-bold text-center mb-8 text-gray-800">
                            <i class="fas fa-hospital-alt mr-3 text-primary"></i>
                            Layanan Unggulan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="card-hover bg-gradient-to-br from-primary to-teal-600 text-white p-6 rounded-xl">
                                <i class="fas fa-heartbeat text-3xl mb-4"></i>
                                <h4 class="text-xl font-semibold mb-2">Fisioterapi</h4>
                                <p class="text-gray-100">Layanan poli Rehab Medik & Fisioterapi menjadi layanan yang unggul melampaui interaksi normal dengan pelanggan sehingga pasien dapat memastikan merasa didengarkan, dihargai, dipahami.</p>
                            </div>
                            <div class="card-hover bg-gradient-to-br from-secondary to-blue-500 text-white p-6 rounded-xl">
                                <i class="fas fa-stethoscope text-3xl mb-4"></i>
                                <h4 class="text-xl font-semibold mb-2">Medical Check Up</h4>
                                <p class="text-gray-100">Layanan Medical Check Up menjadi layanan yang unggul melampaui interaksi normal dengan pelanggan. Medical check up merupakan tindakan medis yang dilakukan untuk memeriksa kondisi tubuh karyawan atau calon karyawan pada suatu lingkungan kerja.</p>
                            </div>
                            <div class="card-hover bg-gradient-to-br from-accent to-orange-500 text-white p-6 rounded-xl">
                                <i class="fas fa-heart-circle-check text-3xl mb-4"></i>
                                <h4 class="text-xl font-semibold mb-2">Extracorporeal Shock Wave Lithotripsy</h4>
                                <p class="text-gray-100">Layanan ESWL atau Extracorporeal Shock Wave Lithotripsy menjadi layanan yang unggul kami karna melampaui interaksi normal dengan pelanggan. Prosedur untuk mengatasi penyakit batu ginjal dengan menggunakan gelombang kejut. Melalui ESWL, batu ginjal dapat dibuang tanpa melalui tindakan pembedahan (noninvasif).</p>
                            </div>
                            <div class="card-hover bg-gradient-to-br from-green-500 to-emerald-600 text-white p-6 rounded-xl">
                                <i class="fas fa-user-doctor text-3xl mb-4"></i>
                                <h4 class="text-xl font-semibold mb-2">Spesialisasi</h4>
                                <p class="text-gray-100">Layanan kami memiliki dokter spesialis yang siap membantu para pasien yang berkunjung yang terdiri dari 17 poli klinik diantaranya Spesialis Kandungan, Syaraf, Radiologi, Anak, Bedah Mulut, Gigi, Internis, Kulit, Bedah, Rehab medik, Mata, Jantung, THT, Paru, Orthopedi, Psikiatri, Urologi.</p>
                            </div>
                        </div>
                    </section>

                    <section id="berita" class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-3xl font-bold text-center mb-8 text-gray-800 flex items-center justify-center">
                            <i class="fas fa-newspaper mr-3 text-primary"></i>
                            Berita & Artikel
                        </h2>

                        <?php
                        $result = $conn->query("SELECT * FROM news ORDER BY published_at DESC LIMIT 2");
                        while($row = $result->fetch_assoc()):
                            $excerpt = substr(strip_tags($row['content']), 0, 200) . '...';
                            $date = !empty($row['published_at']) ? date('d M Y', strtotime($row['published_at'])) : date('d M Y');
                            $category = !empty($row['category']) ? $row['category'] : 'Info';

                            switch ($category) {
                                case 'Urgent':
                                    $badgeClass = "bg-red-100 text-red-800";
                                    break;
                                case 'Program':
                                    $badgeClass = "bg-green-100 text-green-800";
                                    break;
                                case 'Info':
                                default:
                                    $badgeClass = "bg-blue-100 text-blue-800";
                                    break;
                            }
                        ?>
                        <div class="card-hover bg-white rounded-xl shadow-md overflow-hidden mb-8 border border-gray-100">
                            <?php if($row['image']): ?>
                                <img src="assets/news/<?php echo htmlspecialchars($row['image']); ?>"
                                    alt="<?php echo htmlspecialchars($row['title']); ?>"
                                    class="w-full h-64 object-cover"
                                    loading="lazy"
                                    width="800"
                                    height="400">
                            <?php else: ?>
                                <img src="assets/news/placeholder.jpg"
                                    alt="placeholder"
                                    class="w-full h-64 object-cover"
                                    loading="lazy"
                                    width="800"
                                    height="400">
                            <?php endif; ?>

                            <div class="p-6">
                                <div class="flex items-center mb-3">
                                    <span class="<?php echo $badgeClass; ?> px-3 py-1 rounded-full text-xs font-medium">
                                        <?php echo htmlspecialchars($category); ?>
                                    </span>
                                    <span class="text-gray-500 text-sm ml-4"><?php echo $date; ?></span>
                                </div>

                                <h3 class="text-xl font-bold mb-3 text-gray-800 break-words">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </h3>

                                <p class="text-gray-600 leading-relaxed mb-3 break-words whitespace-normal">
                                    <?php echo htmlspecialchars($excerpt); ?>
                                </p>

                                <a href="news.php?id=<?php echo $row['id']; ?>"
                                class="inline-flex items-center text-primary hover:text-teal-700 font-medium">
                                    Baca Selengkapnya <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </section>

                    <section id="visi-misi" class="bg-white rounded-2xl shadow-lg p-8">
                        <h3 class="text-3xl font-bold text-center mb-8 text-gray-800">
                            <i class="fas fa-bullseye mr-3 text-primary"></i>
                            Visi, Misi & Motto
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            <div class="space-y-4">
                                <h4 class="text-2xl font-semibold text-primary flex items-center">
                                    <i class="fas fa-eye mr-3"></i>Visi
                                </h4>
                                <p class="text-gray-700 leading-relaxed">
                                    Rumah Sakit Salak menjadi kebanggaan bagi keluarga, ASN, Prajurit, serta Masyarakat umum yang memetingkan keselamatan pasien.
                                </p>
                            </div>
                            <div class="space-y-4">
                                <h4 class="text-2xl font-semibold text-secondary flex items-center">
                                    <i class="fas fa-tasks mr-3"></i>Misi
                                </h4>
                                <ul class="text-gray-700 space-y-2">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-primary mt-1 mr-3"></i>
                                        Mengutamakan pelayanan Paripurna
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-primary mt-1 mr-3"></i>
                                        Menyelenggarakan Dukungan Kesehatan yang handal
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center bg-white rounded-xl p-6 shadow-md">
                            <h4 class="text-2xl font-semibold text-accent mb-4 flex items-center justify-center">
                                <i class="fas fa-quote-left mr-3"></i>Motto
                            </h4>
                            <p class="text-xl italic text-gray-800 font-medium">
                                "Senyum, Antisipatif, Lembut, Aman, Kepuasan"
                            </p>
                        </div>
                    </section>

                    <section id="profil" class="bg-white rounded-2xl shadow-lg p-8">
                        <h3 class="text-3xl font-bold text-center mb-8 text-gray-800">
                            <i class="fas fa-hospital mr-3 text-primary"></i>
                            Profil Rumah Sakit
                        </h3>
                        <p class="text-center text-gray-700 leading-relaxed mb-12 text-lg">
                            Rumah Sakit Salak di dirikan pada tanggal 19 Juni 1925 diserahkan oleh pemerintah Belanda kepada Pemerintah TNI Angkatan Darat untuk dijadikan Rs TNI AD.
                            Rumah Sakit Salak saat ini memiliki tempat tidur Bed bagi pasien sebanyak 138 yang meliputi rawat jalan dan rawat inap.
                            Fasilitas dan pelayanan Rumah Sakit Salak terdiri dari Pelayanan IGD 24 Jam, MCU, dan 17 Poli Klinik Dokter Spesialis.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="card-hover text-center bg-gradient-to-b from-gray-50 to-white p-6 rounded-xl shadow-md">
                                <img src="https://placehold.co/200x300" 
                                     alt="Kepala Rumah Sakit Salak"
                                     class="w-32 h-32 mx-auto rounded-full object-cover mb-4 border-4 border-primary shadow-lg"
                                     loading="lazy"
                                     width="128"
                                     height="128">
                                <h4 class="text-xl font-semibold text-gray-800 mb-2">Letkol Ckm (K) Dr. dr. Nanik P, Sp.PK., M.H., M.A.R.S</h4>
                                <p class="text-primary font-medium mb-3">Kepala Rumah Sakit Salak</p>
                                <p class="text-sm text-gray-600">Memimpin dengan visi kesehatan terdepan</p>
                            </div>

                            <div class="card-hover text-center bg-gradient-to-b from-gray-50 to-white p-6 rounded-xl shadow-md">
                                <img src="https://placehold.co/200x300" 
                                     alt="dr. Siti Aminah - Wakil Direktur Pelayanan Medis dengan spesialisasi penyakit dalam"
                                     class="w-32 h-32 mx-auto rounded-full object-cover mb-4 border-4 border-secondary shadow-lg"
                                     loading="lazy"
                                     width="128"
                                     height="128">
                                <h4 class="text-xl font-semibold text-gray-800 mb-2">dr. </h4>
                                <p class="text-secondary font-medium mb-3">Peran </p>
                                <p class="text-sm text-gray-600">Desc</p>
                            </div>

                            <div class="card-hover text-center bg-gradient-to-b from-gray-50 to-white p-6 rounded-xl shadow-md">
                                <img src="https://placehold.co/200x300" 
                                     alt="Ns. Andi Prasetyo - Kepala Keperawatan dengan sertifikasi keperawatan tingkat lanjut"
                                     class="w-32 h-32 mx-auto rounded-full object-cover mb-4 border-4 border-accent shadow-lg"
                                     loading="lazy"
                                     width="128"
                                     height="128">
                                <h4 class="text-xl font-semibold text-gray-800 mb-2">dr. </h4>
                                <p class="text-accent font-medium mb-3">Peran</p>
                                <p class="text-sm text-gray-600">Desc</p>
                            </div>
                        </div>
                    </section>
   
                    <section id="dokter" class="bg-white rounded-2xl shadow-lg p-8">
                        <h3 class="text-3xl font-bold text-center mb-8 text-gray-800">
                            <i class="fas fa-user-md mr-3 text-primary"></i>
                            Dokter Spesialis Kami
                        </h3>
                        <p class="text-center text-gray-600 mb-8">
                            Tim dokter spesialis berpengalaman siap memberikan pelayanan terbaik
                        </p>
                        
                        <?php if ($doctors_result && $doctors_result->num_rows > 0): ?>
                            <div id="doctors-list" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                <?php while($doctor = $doctors_result->fetch_assoc()): ?>
                                    <div class="card-hover bg-gradient-to-b from-gray-50 to-white rounded-xl shadow-md overflow-hidden">
                                        <div class="h-48 bg-gray-200 overflow-hidden">
                                            <img src="<?= htmlspecialchars($doctor['image_url']) ?>" 
                                                alt="Dr. <?= htmlspecialchars($doctor['name']) ?> - <?= htmlspecialchars($doctor['specialization']) ?>"
                                                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                                loading="lazy"
                                                onerror="this.src='https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=300&h=300&fit=crop&crop=face'">
                                        </div>
                                        <div class="p-6">
                                            <h4 class="text-xl font-bold text-gray-800 mb-2">
                                                Dr. <?= htmlspecialchars($doctor['name']) ?>
                                            </h4>
                                            <p class="text-primary font-semibold mb-3 flex items-center">
                                                <i class="fas fa-stethoscope mr-2"></i>
                                                <?= htmlspecialchars($doctor['specialization']) ?>
                                            </p>
                                            <p class="text-gray-600 text-sm leading-relaxed">
                                                <?= htmlspecialchars(substr($doctor['experience'], 0, 100)) ?>
                                                <?= strlen($doctor['experience']) > 100 ? '...' : '' ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <div class="flex justify-center mt-8 space-x-2">
                                <?php if ($page > 1): ?>
                                    <a href="<?= $baseUrl . '&page=' . ($page - 1) ?>#dokter" 
                                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">&laquo; Prev</a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <?php if ($i == $page): ?>
                                        <span class="px-4 py-2 bg-primary text-white rounded-lg"><?= $i ?></span>
                                    <?php else: ?>
                                        <a href="<?= $baseUrl . '&page=' . $i ?>#dokter" 
                                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300"><?= $i ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="<?= $baseUrl . '&page=' . ($page + 1) ?>#dokter" 
                                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Next &raquo;</a>
                                <?php endif; ?>
                            </div>
                                <?php endif; ?>
                        </section>
                    </div>

                <aside class="space-y-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h4 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                            <i class="fas fa-bolt mr-3 text-accent"></i>
                            Layanan Cepat
                        </h4>
                        <nav class="space-y-3">
                            <a href="#" class="block p-3 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors focus-visible flex items-center">
                                <i class="fas fa-user-plus mr-3 w-5 text-primary"></i>
                                Pendaftaran Online
                            </a>
                            <a href="#" class="block p-3 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors focus-visible flex items-center">
                                <i class="fas fa-calendar-alt mr-3 w-5 text-secondary"></i>
                                Jadwal Dokter
                            </a>
                            <a href="#" class="block p-3 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors focus-visible flex items-center">
                                <i class="fas fa-money-bill-wave mr-3 w-5 text-accent"></i>
                                Informasi Biaya
                            </a>
                            <a href="#" class="block p-3 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors focus-visible flex items-center">
                                <i class="fas fa-video mr-3 w-5 text-green-500"></i>
                                E-Konsultasi
                            </a>
                        </nav>
                    </div>

                    <div class="card-hover bg-gradient-to-b from-gray-50 to-white rounded-xl shadow-md overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1551076805-e1869033e561?w=400&h=300&fit=crop" 
                             alt="Tim medis profesional di rumah sakit dengan teknologi medis terdepan" 
                             class="w-full h-48 object-cover"
                             loading="lazy"
                             width="400"
                             height="300">
                        <div class="p-6">
                            <h5 class="font-bold text-gray-800 mb-2">Teknologi Medis Terkini</h5>
                            <p class="text-sm text-gray-600">Perawatan kami sudah berbasis teknologi yang meiliputi big data analitik rekam medis dan pemberian obat kepada pasien yang sudah terintegrasi melalui teknologi (BiHealth) dan offline serta perangkat medis yang terhubung memungkinkan pemantauan pasien secara real-time dan respons yang lebih cepat dalam situasi darurat.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h4 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                            <i class="fas fa-newspaper mr-3 text-primary"></i>
                            Berita Terkini
                        </h4>
                        <div class="space-y-6">
                            <?php
                            $categories = ['Urgent', 'Info', 'Program'];
                            $sidebarNews = [];

                            foreach ($categories as $cat) {
                                $stmt = $conn->prepare("SELECT * FROM news WHERE category = ? ORDER BY published_at DESC LIMIT 1");
                                $stmt->bind_param("s", $cat);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if ($row = $result->fetch_assoc()) {
                                    $sidebarNews[] = $row;
                                }
                                $stmt->close();
                            }

                            foreach ($sidebarNews as $row):
                                $date = !empty($row['published_at']) ? date('d M', strtotime($row['published_at'])) : date('d M');
                                $category = !empty($row['category']) ? $row['category'] : 'Info';

                                switch ($category) {
                                    case 'Urgent':
                                        $badgeClass = "bg-red-100 text-red-800";
                                        $linkClass  = "text-primary";
                                        break;
                                    case 'Program':
                                        $badgeClass = "bg-green-100 text-green-800";
                                        $linkClass  = "text-accent";
                                        break;
                                    case 'Info':
                                    default:
                                        $badgeClass = "bg-blue-100 text-blue-800";
                                        $linkClass  = "text-secondary";
                                        break;
                                }

                                $excerpt = substr(strip_tags($row['content']), 0, 80) . '...';
                            ?>
                            <article class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-center mb-2">
                                    <span class="<?php echo $badgeClass; ?> px-2 py-1 rounded text-xs font-medium">
                                        <?php echo htmlspecialchars($category); ?>
                                    </span>
                                    <span class="text-gray-500 text-xs ml-2"><?php echo $date; ?></span>
                                </div>
                                <h5 class="font-semibold text-gray-800 mb-2 leading-tight break-words">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </h5>
                                <p class="text-sm text-gray-600 break-words whitespace-normal leading-relaxed">
                                    <?php echo htmlspecialchars($excerpt); ?>
                                </p>
                                <a href="news.php?id=<?php echo $row['id']; ?>" 
                                class="inline-flex items-center text-primary text-teal-700 font-medium text-sm hover:underline mt-2 inline-block">
                                Baca selengkapnya
                                </a>
                            </article>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 mt-8">
                        <h4 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                            <i class="fas fa-archive mr-3 text-primary"></i>
                            Arsip Berita
                        </h4>
                        <div class="space-y-2">
                            <?php
                            $archiveResult = $conn->query("
                                SELECT DATE_FORMAT(published_at, '%M %Y') as month_year, COUNT(*) as total 
                                FROM news 
                                WHERE published_at >= DATE_FORMAT(CURDATE() - INTERVAL 2 MONTH, '%Y-%m-01')
                                GROUP BY month_year 
                                ORDER BY MIN(published_at) DESC
                                LIMIT 3
                            ");


                            if ($archiveResult->num_rows > 0):
                                while ($archive = $archiveResult->fetch_assoc()):
                            ?>
                                    <a href="news_archive.php?month=<?php echo urlencode($archive['month_year']); ?>"
                                        class="block p-3 text-gray-700 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors flex items-center">
                                        <span class="flex-1"><?php echo htmlspecialchars($archive['month_year']); ?></span>
                                        <span class="text-xs text-gray-500 bg-gray-200 px-2 py-0.5 rounded-full">
                                            <?php echo $archive['total']; ?>
                                        </span>
                                    </a>
                                <?php endwhile;
                            else: ?>
                                <p class="text-gray-500 text-sm">Belum ada arsip berita.</p>
                            <?php endif; ?>
                        </div>
                    </div>



                    <!-- Emergency Contact -->
                    <!-- <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-2xl shadow-lg p-6">
                        <div class="text-center">
                            <i class="fas fa-ambulance text-4xl mb-4"></i>
                            <h4 class="text-xl font-bold mb-2">Gawat Darurat</h4>
                            <p class="text-red-100 mb-4 text-sm">Layanan 24 jam siap membantu Anda</p>
                            <a href="tel:081281900808" 
                               class="bg-white text-red-600 px-6 py-3 rounded-full font-bold hover:bg-gray-100 transition-colors focus-visible inline-flex items-center">
                                <i class="fas fa-phone mr-2"></i>
                                Hubungi Sekarang
                            </a>
                        </div> -->
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer id="kontak" class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <img src="assets/img/logo.png" 
                             alt="Logo RS Salak"
                             class="h-12 w-12 object-contain">
                        <div>
                            <h5 class="text-xl font-bold">RS Salak dr. H. Sadjiman</h5>
                            <p class="text-gray-400 text-sm">Bogor</p>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        Rumah Sakit Tk. III Salak dr. H. Sadjiman Bogor berkomitmen memberikan pelayanan kesehatan 
                        terbaik dengan teknologi modern dan tenaga medis professional untuk masyarakat Bogor dan sekitarnya.
                    </p>
                    
                    <div class="flex space-x-4">
                        <a href="https://www.youtube.com/@rumkitsalak4877" 
                           class="bg-red-600 hover:bg-red-700 p-3 rounded-full transition-colors focus-visible"
                           aria-label="YouTube Channel">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="https://www.threads.com/@rs_salakbogor" 
                           class="bg-gray-600 hover:bg-gray-700 p-3 rounded-full transition-colors focus-visible"
                           aria-label="Threads">
                            <i class="fab fa-threads"></i>
                        </a>
                        <a href="https://www.instagram.com/rs_salakbogor/" 
                           class="bg-pink-600 hover:bg-pink-700 p-3 rounded-full transition-colors focus-visible"
                           aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h5 class="text-lg font-bold mb-6">Kontak</h5>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-phone text-primary mt-1"></i>
                            <div>
                                <p class="font-medium">Telepon</p>
                                <a href="tel:081281900808" class="text-gray-300 hover:text-white transition-colors">
                                    0812-8190-0808
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-envelope text-secondary mt-1"></i>
                            <div>
                                <p class="font-medium">Email</p>
                                <a href="mailto:" class="text-gray-300 hover:text-white transition-colors">
                                    
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fab fa-whatsapp text-green-500 mt-1"></i>
                            <div>
                                <p class="font-medium">WhatsApp</p>
                                <a href="https://wa.me/081281900808" class="text-gray-300 hover:text-white transition-colors">
                                    Chat dengan kami
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h5 class="text-lg font-bold mb-6">Alamat</h5>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-accent mt-1"></i>
                            <div>
                                <p class="text-gray-300 leading-relaxed">
                                    Jl. Jend. Sudirman No.8, RT.03/RW.07<br>
                                    Sempur, Kecamatan Bogor Tengah<br>
                                    Kota Bogor, Jawa Barat
                                </p>
                            </div>
                        </div>
                        <a href="https://maps.app.goo.gl/cftA39wNUUL2tVxZ9" 
                           class="inline-flex items-center bg-primary hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition-colors focus-visible">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Lihat di Google Maps
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        © <span id="year"></span> RS Salak dr. H. Sadjiman. Semua hak dilindungi.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <button id="backToTop" 
            class="fixed bottom-6 right-6 bg-primary hover:bg-teal-700 text-white p-3 rounded-full shadow-lg transition-all duration-300 opacity-0 invisible focus-visible"
            aria-label="Kembali ke atas">
        <i class="fas fa-chevron-up"></i>
    </button>

    <div id="loading" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="loading w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-600">Memuat...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading spinner
            document.getElementById('loading').style.display = 'none';
            
            // Set current year
            document.getElementById('year').textContent = new Date().getFullYear();
            
            // Mobile menu toggle
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            
            mobileMenuToggle.addEventListener('click', function() {
                const isHidden = mobileMenu.classList.contains('hidden');
                
                if (isHidden) {
                    mobileMenu.classList.remove('hidden');
                    mobileMenu.classList.add('mobile-menu-enter');
                    this.setAttribute('aria-expanded', 'true');
                    this.querySelector('i').classList.replace('fa-bars', 'fa-times');
                } else {
                    mobileMenu.classList.add('mobile-menu-exit');
                    this.setAttribute('aria-expanded', 'false');
                    this.querySelector('i').classList.replace('fa-times', 'fa-bars');
                    
                    setTimeout(() => {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('mobile-menu-exit');
                    }, 300);
                }
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // Close mobile menu if open
                        if (!mobileMenu.classList.contains('hidden')) {
                            mobileMenu.classList.add('hidden');
                            mobileMenuToggle.setAttribute('aria-expanded', 'false');
                            mobileMenuToggle.querySelector('i').classList.replace('fa-times', 'fa-bars');
                        }
                    }
                });
            });

            // Back to top button
            const backToTopButton = document.getElementById('backToTop');
            
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.remove('opacity-0', 'invisible');
                    backToTopButton.classList.add('opacity-100', 'visible');
                } else {
                    backToTopButton.classList.add('opacity-0', 'invisible');
                    backToTopButton.classList.remove('opacity-100', 'visible');
                }
            });

            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, observerOptions);

            // Observe all sections for animation
            document.querySelectorAll('section').forEach(section => {
                observer.observe(section);
            });

            // Lazy loading for images
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('loading');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });

            // Add loading state to external links
            document.querySelectorAll('a[href^="http"]').forEach(link => {
                link.addEventListener('click', function() {
                    // Add loading indicator for external links
                    const icon = this.querySelector('i');
                    if (icon) {
                        const originalClass = icon.className;
                        icon.className = 'fas fa-spinner fa-spin';
                        setTimeout(() => {
                            icon.className = originalClass;
                        }, 2000);
                    }
                });
            });

            // Form validation enhancement
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('border-red-500');
                            isValid = false;
                        } else {
                            field.classList.remove('border-red-500');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        alert('Mohon lengkapi semua field yang wajib diisi');
                    }
                });
            });

            // Add accessibility improvements
            document.addEventListener('keydown', function(e) {
                // ESC key closes mobile menu
                if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    mobileMenuToggle.setAttribute('aria-expanded', 'false');
                    mobileMenuToggle.querySelector('i').classList.replace('fa-times', 'fa-bars');
                }
            });
        });
    </script>

    <?php $conn->close(); ?>
</body>
</html>