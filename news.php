<?php
include_once 'connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Artikel tidak ditemukan.");
}

$id = (int) $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    die("Artikel tidak ditemukan.");
}

$categoryColors = [
    'Urgent' => 'bg-red-100 text-red-800',
    'Info' => 'bg-blue-100 text-blue-800',
    'Program' => 'bg-green-100 text-green-800'
];
$categoryClass = $categoryColors[$article['category']] ?? 'bg-gray-100 text-gray-800';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($article['title']); ?> | RS Salak</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg mt-10">
    <?php if($article['image']): ?>
    <div class="w-full mb-6">
        <img src="assets/news/<?php echo htmlspecialchars($article['image']); ?>" 
            alt="<?php echo htmlspecialchars($article['title']); ?>" 
            class="w-full max-h-[500px] object-contain rounded-lg mx-auto">
    </div>
    <?php endif; ?>
    <div class="flex items-center mb-4">
        <span class="px-2 py-1 rounded text-xs font-medium <?php echo $categoryClass; ?>">
            <?php echo htmlspecialchars($article['category']); ?>
        </span>
        <span class="text-gray-500 text-sm ml-3">
            <?php echo date("d M Y", strtotime($article['published_at'])); ?>
        </span>
    </div>
    <h1 class="text-3xl font-bold mb-4 break-words">
      <?php echo htmlspecialchars($article['title']); ?>
    </h1>
    <p class="text-gray-500 mb-6">
      Ditulis oleh <?php echo htmlspecialchars($article['author'] ?: "Admin"); ?>
    </p>
    <div class="prose max-w-none break-words whitespace-normal leading-relaxed text-gray-700">
      <?php echo nl2br($article['content']); ?>
    </div>
    <a href="index.php#berita"
       class="mt-8 inline-block text-primary text-teal-700 font-semibold">
      ← Kembali ke Beranda
    </a>
  </div>
</body>
</html>
