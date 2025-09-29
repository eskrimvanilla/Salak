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

$edit_doctor = null;
$messages = [];
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?? '';
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

// Create upload directory if it doesn't exist
$upload_dir = 'assets/doctors/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Validation function
function validateInput($data, $type = 'string', $maxLength = 255) {
    if (empty($data)) return false;
    $data = trim($data);
    
    switch ($type) {
        case 'string':
            return strlen($data) <= $maxLength ? htmlspecialchars($data, ENT_QUOTES, 'UTF-8') : false;
        case 'text':
            return strlen($data) <= $maxLength ? htmlspecialchars($data, ENT_QUOTES, 'UTF-8') : false;
        default:
            return false;
    }
}

// File upload validation and processing (simplified without GD)
function handleImageUpload($file, $old_image = null) {
    global $upload_dir;
    
    // If no file uploaded, keep old image or use default
    if (empty($file['tmp_name'])) {
        return $old_image ?: 'default-doctor.jpg';
    }
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Check file type by MIME type
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.');
    }
    
    // Double-check by extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_extensions)) {
        throw new Exception('Ekstensi file tidak didukung. Gunakan .jpg, .png, .gif, atau .webp.');
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
    }
    
    // Validate that it's actually an image
    $imageinfo = getimagesize($file['tmp_name']);
    if ($imageinfo === false) {
        throw new Exception('File yang diupload bukan file gambar yang valid.');
    }
    
    // Generate unique filename
    $filename = 'doctor_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Gagal menyimpan file gambar.');
    }
    
    // Delete old image if exists and different
    if ($old_image && $old_image !== 'default-doctor.jpg' && $old_image !== $filename) {
        $old_path = $upload_dir . $old_image;
        if (file_exists($old_path)) {
            unlink($old_path);
        }
    }
    
    return $filename;
}

// Advanced version with GD (use this if you enable GD extension)
function handleImageUploadAdvanced($file, $old_image = null) {
    global $upload_dir;
    
    // Check if GD is available
    if (!extension_loaded('gd')) {
        return handleImageUpload($file, $old_image); // Fall back to simple version
    }
    
    // If no file uploaded, keep old image or use default
    if (empty($file['tmp_name'])) {
        return $old_image ?: 'default-doctor.jpg';
    }
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.');
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
    }
    
    // Generate unique filename
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'doctor_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Create image resource based on type
    $image = false;
    switch ($file['type']) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($file['tmp_name']);
            break;
        case 'image/gif':
            $image = @imagecreatefromgif($file['tmp_name']);
            break;
        case 'image/webp':
            if (function_exists('imagecreatefromwebp')) {
                $image = @imagecreatefromwebp($file['tmp_name']);
            }
            break;
    }
    
    if (!$image) {
        // Fall back to simple file move if image processing fails
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Gagal menyimpan file gambar.');
        }
        return $filename;
    }
    
    // Get original dimensions
    $original_width = imagesx($image);
    $original_height = imagesy($image);
    
    // Calculate new dimensions (max 800x800, maintain aspect ratio)
    $max_dimension = 800;
    if ($original_width > $max_dimension || $original_height > $max_dimension) {
        if ($original_width > $original_height) {
            $new_width = $max_dimension;
            $new_height = intval(($original_height / $original_width) * $max_dimension);
        } else {
            $new_height = $max_dimension;
            $new_width = intval(($original_width / $original_height) * $max_dimension);
        }
    } else {
        $new_width = $original_width;
        $new_height = $original_height;
    }
    
    // Create resized image
    $resized_image = imagecreatetruecolor($new_width, $new_height);
    
    // Preserve transparency for PNG and GIF
    if ($file['type'] == 'image/png' || $file['type'] == 'image/gif') {
        imagealphablending($resized_image, false);
        imagesavealpha($resized_image, true);
        $transparent = imagecolorallocatealpha($resized_image, 255, 255, 255, 127);
        imagefill($resized_image, 0, 0, $transparent);
    }
    
    // Resize image
    imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
    
    // Save resized image
    $saved = false;
    switch ($file['type']) {
        case 'image/jpeg':
            $saved = imagejpeg($resized_image, $filepath, 90);
            break;
        case 'image/png':
            $saved = imagepng($resized_image, $filepath, 8);
            break;
        case 'image/gif':
            $saved = imagegif($resized_image, $filepath);
            break;
        case 'image/webp':
            if (function_exists('imagewebp')) {
                $saved = imagewebp($resized_image, $filepath, 90);
            }
            break;
    }
    
    // Clean up memory
    imagedestroy($image);
    imagedestroy($resized_image);
    
    if (!$saved) {
        // Fall back to simple file move
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Gagal menyimpan file gambar.');
        }
    }
    
    // Delete old image if exists and different
    if ($old_image && $old_image !== 'default-doctor.jpg' && $old_image !== $filename) {
        $old_path = $upload_dir . $old_image;
        if (file_exists($old_path)) {
            unlink($old_path);
        }
    }
    
    return $filename;
}

// CSRF token validation for POST requests
function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false;
        }
    }
    return true;
}

// Add doctor function
function addDoctor($conn, $data, $file) {
    $name = validateInput($data['name']);
    $specialization = validateInput($data['specialization']);
    $experience = validateInput($data['experience'], 'text', 1000);
    
    if (!$name || !$specialization || !$experience) {
        return ['success' => false, 'message' => 'Data tidak valid. Pastikan semua field diisi dengan benar.'];
    }
    
    try {
        // Handle image upload
        $image_filename = handleImageUpload($file);
        
        $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, experience, image) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssss", $name, $specialization, $experience, $image_filename);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Dokter berhasil ditambahkan!'];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Update doctor function
function updateDoctor($conn, $data, $file, $old_image) {
    $id = filter_var($data['id'], FILTER_VALIDATE_INT);
    $name = validateInput($data['name']);
    $specialization = validateInput($data['specialization']);
    $experience = validateInput($data['experience'], 'text', 1000);
    
    if (!$id || !$name || !$specialization || !$experience) {
        return ['success' => false, 'message' => 'Data tidak valid. Pastikan semua field diisi dengan benar.'];
    }
    
    try {
        // Handle image upload
        $image_filename = handleImageUpload($file, $old_image);
        
        $stmt = $conn->prepare("UPDATE doctors SET name = ?, specialization = ?, experience = ?, image = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssi", $name, $specialization, $experience, $image_filename, $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Data dokter berhasil diperbarui!'];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Delete doctor function
function deleteDoctor($conn, $id) {
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if (!$id) {
        return ['success' => false, 'message' => 'ID tidak valid.'];
    }
    
    try {
        // Get image filename before deleting
        $stmt = $conn->prepare("SELECT image FROM doctors WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        $stmt->close();
        
        if ($doctor && $doctor['image'] && $doctor['image'] !== 'default-doctor.jpg') {
            $image_path = 'assets/doctors/' . $doctor['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Dokter berhasil dihapus!'];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'];
    }
}

// Process form submissions
if (!validateCSRF()) {
    $messages[] = ['type' => 'error', 'text' => 'Token keamanan tidak valid. Silakan coba lagi.'];
} else {
    // Process add doctor
    if (isset($_POST['add_doctor'])) {
        $result = addDoctor($conn, $_POST, $_FILES['image'] ?? []);
        $messages[] = ['type' => $result['success'] ? 'success' : 'error', 'text' => $result['message']];
    }

    // Process update doctor
    if (isset($_POST['update_doctor'])) {
        $old_image = $_POST['current_image'] ?? null;
        $result = updateDoctor($conn, $_POST, $_FILES['image'] ?? [], $old_image);
        $messages[] = ['type' => $result['success'] ? 'success' : 'error', 'text' => $result['message']];
        if ($result['success']) {
            header('Location: manage_doctors.php');
            exit;
        }
    }

    // Process delete doctor
    if (isset($_GET['delete'])) {
        $result = deleteDoctor($conn, $_GET['delete']);
        $messages[] = ['type' => $result['success'] ? 'success' : 'error', 'text' => $result['message']];
    }
}

// Get doctor for editing
if (isset($_GET['edit'])) {
    $id = filter_var($_GET['edit'], FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $edit_doctor = $result->fetch_assoc();
        $stmt->close();
    }
}

// Get doctors with search and pagination
$where_clause = '';
$params = [];
$types = '';

if ($search) {
    $where_clause = "WHERE name LIKE ? OR specialization LIKE ?";
    $search_term = "%$search%";
    $params = [$search_term, $search_term];
    $types = 'ss';
}

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM doctors $where_clause";
$count_stmt = $conn->prepare($count_sql);
if ($params) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

$total_pages = ceil($total_records / $per_page);

// Get doctors
$sql = "SELECT * FROM doctors $where_clause ORDER BY id DESC LIMIT ? OFFSET ?";
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
$doctors = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Dokter - Rumah Sakit Sehat Sentosa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#667eea',
                        secondary: '#764ba2',
                    }
                }
            }
        }
    </script>
    <style>
        .image-preview {
            transition: all 0.3s ease;
        }
        .image-preview:hover {
            transform: scale(1.05);
        }
        .drop-zone {
            border: 2px dashed #cbd5e0;
            transition: all 0.3s ease;
        }
        .drop-zone.dragover {
            border-color: #667eea;
            background-color: #f7fafc;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <header class="bg-white shadow-lg border-b-4 border-primary">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Kelola Dokter</h1>
                <div class="flex gap-3">
                    <a href="admin_dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Admin
                    </a>
                    <a href="index.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i>Halaman Utama
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        <!-- Messages -->
        <?php foreach ($messages as $message): ?>
            <div class="mb-6 p-4 rounded-lg border-l-4 <?= $message['type'] === 'success' ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700' ?> animate-pulse">
                <div class="flex items-center">
                    <i class="fas <?= $message['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-2"></i>
                    <?= htmlspecialchars($message['text']) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" action="" class="flex gap-4">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" placeholder="Cari dokter berdasarkan nama atau spesialisasi..." 
                           value="<?= htmlspecialchars($search) ?>"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"/>
                </div>
                <button type="submit" class="bg-primary hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <?php if ($search): ?>
                    <a href="manage_doctors.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center">
                        <i class="fas fa-times mr-2"></i>Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Add/Edit Doctor Form -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8 border-t-4 border-primary">
            <?php if ($edit_doctor): ?>
                <form method="POST" action="" enctype="multipart/form-data" id="doctorForm">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-edit text-2xl text-yellow-500 mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Edit Dokter</h2>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
                    <input type="hidden" name="id" value="<?= htmlspecialchars($edit_doctor['id']) ?>" />
                    <input type="hidden" name="current_image" value="<?= htmlspecialchars($edit_doctor['image']) ?>" />
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Dokter *</label>
                            <input type="text" name="name" required 
                                   value="<?= htmlspecialchars($edit_doctor['name']) ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Spesialisasi *</label>
                            <input type="text" name="specialization" required 
                                   value="<?= htmlspecialchars($edit_doctor['specialization']) ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pengalaman *</label>
                            <textarea name="experience" required rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"><?= htmlspecialchars($edit_doctor['experience']) ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Dokter</label>
                            
                            <!-- Current Image Preview -->
                            <?php if (!empty($edit_doctor['image'])): ?>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                                    <img src="assets/doctors/<?= htmlspecialchars($edit_doctor['image']) ?>"
                                         alt="Foto Dokter Saat Ini"
                                         class="h-32 w-32 object-cover rounded-lg border image-preview" />
                                </div>
                            <?php endif; ?>
                            
                            <!-- File Upload -->
                            <div class="drop-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer"
                                 onclick="document.getElementById('image-input').click()">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 mb-2">Klik untuk memilih foto baru atau drag & drop di sini</p>
                                <p class="text-sm text-gray-500">Mendukung: JPG, PNG, GIF, WebP (Maksimal 5MB)</p>
                            </div>
                            <input type="file" id="image-input" name="image" accept="image/*" class="hidden" />
                            
                            <!-- Preview New Image -->
                            <div id="new-image-preview" class="mt-4 hidden">
                                <p class="text-sm text-gray-600 mb-2">Preview foto baru:</p>
                                <img id="preview-img" src="" alt="Preview" class="h-32 w-32 object-cover rounded-lg border image-preview" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 mt-8">
                        <button type="submit" name="update_doctor" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fas fa-save mr-2"></i>Update Dokter
                        </button>
                        <a href="manage_doctors.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <!-- Add New Doctor Form -->
                <form method="POST" action="" enctype="multipart/form-data" id="doctorForm">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-plus-circle text-2xl text-primary mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Tambah Dokter Baru</h2>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Dokter *</label>
                            <input type="text" name="name" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Spesialisasi *</label>
                            <input type="text" name="specialization" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pengalaman</label>
                            <textarea name="experience" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Dokter</label>
                            
                            <!-- File Upload -->
                            <div class="drop-zone border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer"
                                 onclick="document.getElementById('image-input').click()">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 mb-2">Klik untuk memilih foto atau drag & drop di sini</p>
                                <p class="text-sm text-gray-500">Mendukung: JPG, PNG, GIF, WebP (Maksimal 5MB)</p>
                            </div>
                            <input type="file" id="image-input" name="image" accept="image/*" class="hidden" />
                            
                            <!-- Preview Image -->
                            <div id="image-preview" class="mt-4 hidden">
                                <p class="text-sm text-gray-600 mb-2">Preview:</p>
                                <img id="preview-img" src="" alt="Preview" class="h-32 w-32 object-cover rounded-lg border image-preview" />
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_doctor" class="mt-8 bg-primary hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>Tambah Dokter
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Doctors Grid -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-secondary">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <i class="fas fa-list text-2xl text-secondary mr-3"></i>
                    <h2 class="text-2xl font-bold text-gray-800">Daftar Dokter</h2>
                </div>
                <div class="text-sm text-gray-600">
                    Total: <?= $total_records ?> dokter
                </div>
            </div>

            <?php if ($doctors->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while($doctor = $doctors->fetch_assoc()): ?>
                        <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                            <div class="h-48 bg-gray-200 overflow-hidden">
                                <?php 
                                $image_path = 'assets/doctors/' . htmlspecialchars($doctor['image']);
                                $image_src = (file_exists($image_path) && !empty($doctor['image'])) ? $image_path : 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=300&h=300&fit=crop&crop=face';
                                ?>
                                <img src="<?= $image_src ?>"
                                     alt="Dr. <?= htmlspecialchars($doctor['name']) ?>"
                                     class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                     onerror="this.src='https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=300&h=300&fit=crop&crop=face'" />
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">
                                    <?= htmlspecialchars($doctor['name']) ?>
                                </h3>
                                <p class="text-primary font-semibold mb-3 flex items-center">
                                    <i class="fas fa-stethoscope mr-2"></i>
                                    <?= htmlspecialchars($doctor['specialization']) ?>
                                </p>
                                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                    <?= nl2br(htmlspecialchars(substr($doctor['experience'], 0, 100))) ?>
                                    <?= strlen($doctor['experience']) > 100 ? '...' : '' ?>
                                </p>
                                <div class="flex gap-2">
                                    <a href="?edit=<?= intval($doctor['id']) ?>"
                                       class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 text-center text-sm flex items-center justify-center">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </a>
                                    <a href="?delete=<?= intval($doctor['id']) ?>"
                                       class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 text-center text-sm flex items-center justify-center"
                                       onclick="return confirm('Yakin ingin menghapus Dr. <?= addslashes($doctor['name']) ?>?')">
                                        <i class="fas fa-trash mr-2"></i>Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="mt-8 flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                                   class="px-3 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition-colors duration-200">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                                   class="px-4 py-2 rounded-lg <?= $i === $page ? 'bg-primary text-white' : 'bg-gray-200 hover:bg-gray-300' ?> transition-colors duration-200">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                                   class="px-3 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition-colors duration-200">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-user-md text-6xl text-gray-400 mb-4"></i>
                    <p class="text-xl text-gray-600 mb-2">
                        <?= $search ? 'Tidak ada dokter yang sesuai dengan pencarian' : 'Belum ada dokter yang terdaftar' ?>
                    </p>
                    <?php if ($search): ?>
                        <a href="manage_doctors.php" class="text-primary hover:underline">
                            Lihat semua dokter
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image-input');
            const dropZone = document.querySelector('.drop-zone');
            const previewDiv = document.getElementById('image-preview') || document.getElementById('new-image-preview');
            const previewImg = document.getElementById('preview-img');

            // File input change handler
            imageInput.addEventListener('change', function(e) {
                handleFileSelect(e.target.files[0]);
            });

            // Drag and drop handlers
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    imageInput.files = files;
                    handleFileSelect(files[0]);
                }
            });

            function handleFileSelect(file) {
                if (!file) return;

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.');
                    imageInput.value = '';
                    return;
                }

                // Validate file size (5MB)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    imageInput.value = '';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewDiv.classList.remove('hidden');
                };
                reader.readAsDataURL(file);

                // Update drop zone text
                dropZone.innerHTML = `
                    <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
                    <p class="text-green-600 mb-2">File "${file.name}" siap diupload</p>
                    <p class="text-sm text-gray-500">Klik untuk mengganti file</p>
                `;
            }

            // Form validation
            const form = document.getElementById('doctorForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        const value = field.value.trim();
                        if (!value) {
                            field.classList.add('border-red-500', 'bg-red-50');
                            isValid = false;
                        } else {
                            field.classList.remove('border-red-500', 'bg-red-50');
                            field.classList.add('border-green-500');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        
                        // Show error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded';
                        errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Mohon isi semua field yang wajib diisi';
                        form.insertBefore(errorDiv, form.firstChild);
                        
                        // Remove error message after 5 seconds
                        setTimeout(() => errorDiv.remove(), 5000);
                        
                        // Scroll to top of form
                        form.scrollIntoView({ behavior: 'smooth' });
                    }
                });

                // Real-time validation
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    field.addEventListener('blur', function() {
                        if (this.value.trim()) {
                            this.classList.remove('border-red-500', 'bg-red-50');
                            this.classList.add('border-green-500');
                        }
                    });

                    field.addEventListener('input', function() {
                        if (this.classList.contains('border-red-500')) {
                            if (this.value.trim()) {
                                this.classList.remove('border-red-500', 'bg-red-50');
                                this.classList.add('border-green-500');
                            }
                        }
                    });
                });
            }

            // Auto-hide messages
            const messages = document.querySelectorAll('[class*="animate-pulse"]');
            messages.forEach(message => {
                setTimeout(() => {
                    message.style.transition = 'opacity 0.5s ease-out';
                    message.style.opacity = '0';
                    setTimeout(() => message.remove(), 500);
                }, 5000);
            });

            // Image error handling for existing images
            document.querySelectorAll('img').forEach(img => {
                img.addEventListener('error', function() {
                    if (!this.dataset.fallbackSet) {
                        this.dataset.fallbackSet = 'true';
                        this.src = 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=300&h=300&fit=crop&crop=face';
                    }
                });
            });
        });
    </script>

    <?php $conn->close(); ?>
</body>
</html>