<?php
include_once 'connect.php';

$month = isset($_GET['month']) ? trim($_GET['month']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Arsip Berita<?php echo $month ? " - " . htmlspecialchars($month) : ''; ?> | RS Salak</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
  <div class="max-w-6xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-3xl font-bold mb-6 text-gray-800 flex items-center">
        <i class="fas fa-archive mr-3 text-primary"></i>
        Arsip Berita<?php echo $month ? ': ' . htmlspecialchars($month) : ''; ?>
      </h2>

<?php
if ($month === ''):
?>
      <p class="text-gray-600 mb-6">
        Parameter bulan tidak diberikan. Kembali ke 
        <a href="index.php#berita" class="text-primary hover:underline">Berita & Artikel</a>.
      </p>
<?php
else:
    $stmt = $conn->prepare("SELECT * FROM news WHERE DATE_FORMAT(published_at, '%M %Y') = ? ORDER BY published_at DESC");
    if ($stmt === false) {
        echo '<p class="text-red-600">Terjadi kesalahan pada query. Silakan coba lagi.</p>';
    } else {
        $stmt->bind_param("s", $month);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0):
?>
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
<?php
            while ($row = $result->fetch_assoc()):
                $id = (int)$row['id'];
                $title = htmlspecialchars($row['title']);
                $date = date('d M Y', strtotime($row['published_at']));
                $excerpt = htmlspecialchars(substr(strip_tags($row['content']), 0, 120)) . '...';
                $category = !empty($row['category']) ? $row['category'] : 'Info';

                switch ($category) {
                    case 'Urgent':
                        $badgeClass = "bg-red-100 text-red-800";
                        break;
                    case 'Program':
                        $badgeClass = "bg-green-100 text-green-800";
                        break;
                    default:
                        $badgeClass = "bg-blue-100 text-blue-800";
                        break;
                }

                $image = !empty($row['image']) ? "assets/news/" . $row['image'] : "https://picsum.photos/600/400?random=" . $id;
?>
        <div class="card-hover bg-gradient-to-b from-gray-50 to-white rounded-xl shadow-md overflow-hidden">
          <div class="h-48 bg-gray-200 overflow-hidden">
            <img src="<?= $image ?>" 
                 alt="<?= $title ?>" 
                 class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                 loading="lazy">
          </div>
          <div class="p-5">
            <div class="flex items-center mb-2">
              <span class="<?= $badgeClass ?> px-2 py-1 rounded text-xs font-medium"><?= htmlspecialchars($category) ?></span>
              <span class="text-gray-500 text-xs ml-3 flex items-center">
                <i class="far fa-calendar-alt mr-1"></i><?= $date ?>
              </span>
            </div>
            <h3 class="text-lg font-semibold mb-2 text-gray-800 leading-snug line-clamp-2">
              <a href="news.php?id=<?= $id ?>" class="hover:text-primary"><?= $title ?></a>
            </h3>
            <p class="text-sm text-gray-600 mb-3 leading-relaxed line-clamp-3"><?= $excerpt ?></p>
            <a href="news.php?id=<?= $id ?>" class="inline-flex items-center text-primary text-teal-700 font-medium text-sm hover:underline mt-2 inline-block">
              Baca selengkapnya
            </a>
          </div>
        </div>
<?php
            endwhile;
?>
      </div>
<?php
        else:
            echo '<p class="text-gray-600">Belum ada berita untuk bulan ini.</p>';
        endif;

        $stmt->close();
    }
endif;
?>
      <div class="mt-8">
        <a href="index.php#berita" class="inline-flex items-center text-primary text-teal-700 font-semibold">
          ← Kembali ke Beranda
        </a>
      </div>
    </div>
  </div>
  <script src="https://kit.fontawesome.com/a2e0e6ad65.js" crossorigin="anonymous"></script>
</body>
</html>
