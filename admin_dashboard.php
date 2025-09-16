<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
  <header class="bg-white shadow-lg border-b-4 border-blue-600">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold text-gray-800">Dashboard Admin</h1>
      <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center">
        <i class="fas fa-sign-out-alt mr-2"></i>Logout
      </a>
    </div>
  </header>

  <main class="container mx-auto px-6 py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <a href="manage_doctors.php" class="block p-6 bg-white rounded-lg shadow hover:shadow-md border border-gray-200 transition">
        <i class="fas fa-user-md text-blue-600 text-3xl mb-3"></i>
        <h2 class="text-lg font-bold text-gray-800">Kelola Dokter</h2>
        <p class="text-gray-600 mt-2 text-sm">Tambah, hapus, atau edit daftar dokter rumah sakit.</p>
      </a>

      <a href="manage_news.php" class="block p-6 bg-white rounded-lg shadow hover:shadow-md border border-gray-200 transition">
        <i class="fas fa-newspaper text-blue-600 text-3xl mb-3"></i>
        <h2 class="text-lg font-bold text-gray-800">Kelola Berita</h2>
        <p class="text-gray-600 mt-2 text-sm">Tambah, hapus, atau edit artikel berita.</p>
      </a>
    </div>
  </main>
</body>
</html>
