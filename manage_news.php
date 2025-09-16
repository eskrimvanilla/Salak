<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'connect.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$messages = [];
$edit_news = null;

// Search & Pagination
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?? '';
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

// Validation helper
function validateInput($data, $type = 'string', $maxLength = 255) {
    if (empty($data)) return false;
    $data = trim($data);
    switch ($type) {
        case 'string':
        case 'text':
            return strlen($data) <= $maxLength ? htmlspecialchars($data, ENT_QUOTES, 'UTF-8') : false;
        default:
            return false;
    }
}

// CSRF check
function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false;
        }
    }
    return true;
}

// Add news
function addNews($conn, $data, $file) {
    $title = validateInput($data['title']);
    $content = validateInput($data['content'], 'text', 5000);
    $author = validateInput($data['author']);
    $category = validateInput($data['category']);
    
    if (!$title || !$content || !$category) {
        return ['success' => false, 'message' => 'Data tidak valid.'];
    }

    $image = null;
    if (!empty($file['name'])) {
        $image = time() . "_" . basename($file['name']);
        $targetDir = "assets/news/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0775, true);
        move_uploaded_file($file['tmp_name'], $targetDir . $image);
    }

    $stmt = $conn->prepare("INSERT INTO news (title, content, image, author, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $content, $image, $author, $category);

    return $stmt->execute() 
        ? ['success' => true, 'message' => 'Berita berhasil ditambahkan!'] 
        : ['success' => false, 'message' => 'Gagal menambahkan berita.'];
}

// Delete news
function deleteNews($conn, $id) {
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if (!$id) return ['success' => false, 'message' => 'ID tidak valid.'];
    
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute()
        ? ['success' => true, 'message' => 'Berita berhasil dihapus!']
        : ['success' => false, 'message' => 'Gagal menghapus berita.'];
}

// Handle POST/GET
if (!validateCSRF()) {
    $messages[] = ['type' => 'error', 'text' => 'Token keamanan tidak valid.'];
} else {
    if (isset($_POST['add_news'])) {
        $result = addNews($conn, $_POST, $_FILES['image']);
        $messages[] = ['type' => $result['success'] ? 'success' : 'error', 'text' => $result['message']];
    }
    if (isset($_GET['delete'])) {
        $result = deleteNews($conn, $_GET['delete']);
        $messages[] = ['type' => $result['success'] ? 'success' : 'error', 'text' => $result['message']];
    }
}

// Build query with search
$where_clause = '';
$params = [];
$types = '';

if ($search) {
    $where_clause = "WHERE title LIKE ? OR content LIKE ? OR category LIKE ?";
    $search_term = "%$search%";
    $params = [$search_term, $search_term, $search_term];
    $types = 'sss';
}

// Count total
$count_sql = "SELECT COUNT(*) as total FROM news $where_clause";
$count_stmt = $conn->prepare($count_sql);
if ($params) $count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();
$total_pages = ceil($total_records / $per_page);

// Get news
$sql = "SELECT * FROM news $where_clause ORDER BY published_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($params) {
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $per_page, $offset);
}
$stmt->execute();
$news = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Kelola Berita - RS Salak</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
  <header class="bg-white shadow-lg border-b-4 border-blue-600">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800">Kelola Berita</h1>
      <div class="flex gap-3">
        <a href="admin_dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
          <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Admin
        </a>
        <a href="index.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
          <i class="fas fa-home mr-2"></i>Halaman Utama
        </a>
      </div>
    </div>
  </header>


  <div class="container mx-auto px-6 py-8">
    <?php foreach ($messages as $message): ?>
      <div class="mb-6 p-4 rounded-lg border-l-4 <?= $message['type'] === 'success' ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700' ?>">
        <i class="fas <?= $message['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-2"></i>
        <?= htmlspecialchars($message['text']) ?>
      </div>
    <?php endforeach; ?>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
      <form method="GET" class="flex gap-4">
        <div class="flex-1 relative">
          <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
          <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari berita..." 
            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"/>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center">
          <i class="fas fa-search mr-2"></i>Cari
        </button>
        <?php if ($search): ?>
          <a href="manage_news.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg flex items-center">
            <i class="fas fa-times mr-2"></i>Reset
          </a>
        <?php endif; ?>
      </form>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8 mb-8 border-t-4 border-blue-600">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
        <div class="flex items-center mb-6">
          <i class="fas fa-plus-circle text-2xl text-blue-600 mr-3"></i>
          <h2 class="text-2xl font-bold text-gray-800">Tambah Berita Baru</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block mb-2 text-sm font-medium">Judul *</label>
            <input type="text" name="title" required class="w-full p-3 border rounded-lg"/>
          </div>
          <div>
            <label class="block mb-2 text-sm font-medium">Kategori *</label>
            <select name="category" class="w-full p-3 border rounded-lg" required>
              <option value="Info">Info</option>
              <option value="Urgent">Urgent</option>
              <option value="Program">Program</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-medium">Isi Berita *</label>
            <textarea name="content" rows="5" required class="w-full p-3 border rounded-lg"></textarea>
          </div>
          <div>
            <label class="block mb-2 text-sm font-medium">Penulis</label>
            <input type="text" name="author" class="w-full p-3 border rounded-lg"/>
          </div>
          <div>
            <label class="block mb-2 text-sm font-medium">Gambar</label>
            <input type="file" name="image" class="w-full"/>
          </div>
        </div>
        <button type="submit" name="add_news" class="mt-6 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center">
          <i class="fas fa-plus mr-2"></i>Tambah Berita
        </button>
      </form>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-blue-600">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <i class="fas fa-list mr-3 text-blue-600"></i>Daftar Berita
      </h2>
      <?php if ($news->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php while($row = $news->fetch_assoc()): ?>
            <div class="border rounded-lg shadow-md overflow-hidden hover:shadow-lg">
              <div class="h-40 bg-gray-200 overflow-hidden">
                <img src="assets/news/<?= htmlspecialchars($row['image'] ?: 'default.jpg') ?>" 
                     alt="<?= htmlspecialchars($row['title']) ?>" 
                     class="w-full h-full object-cover"/>
              </div>
              <div class="p-4">
                <span class="inline-block px-2 py-1 text-xs font-medium rounded 
                  <?= $row['category']==='Urgent' ? 'bg-red-100 text-red-700' : ($row['category']==='Program' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') ?>">
                  <?= htmlspecialchars($row['category']) ?>
                </span>
                <h3 class="text-lg font-bold mt-2"><?= htmlspecialchars($row['title']) ?></h3>
                <p class="text-sm text-gray-600 mt-1"><?= substr(strip_tags($row['content']),0,100) ?>...</p>
                <p class="text-xs text-gray-500 mt-2">Ditulis oleh: <?= htmlspecialchars($row['author'] ?: '-') ?>, <?= date("d M Y", strtotime($row['published_at'])) ?></p>
                <div class="flex gap-2 mt-4">
                  <a href="news.php?id=<?= intval($row['id']) ?>" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm text-center">Lihat</a>
                  <a href="?delete=<?= intval($row['id']) ?>" onclick="return confirm('Hapus berita ini?')" 
                     class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm text-center">Hapus</a>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>

        <div class="mt-8 flex justify-center gap-2">
          <?php for($i=1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?><?= $search ? '&search='.urlencode($search) : '' ?>" 
               class="px-4 py-2 rounded <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
               <?= $i ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php else: ?>
        <p class="text-gray-600">Belum ada berita.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
