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

// Validation function
function validateInput($data, $type = 'string', $maxLength = 255) {
    if (empty($data)) return false;
    $data = trim($data);
    
    switch ($type) {
        case 'url':
            return filter_var($data, FILTER_VALIDATE_URL) ? $data : false;
        case 'string':
            return strlen($data) <= $maxLength ? htmlspecialchars($data, ENT_QUOTES, 'UTF-8') : false;
        case 'text':
            return strlen($data) <= $maxLength ? htmlspecialchars($data, ENT_QUOTES, 'UTF-8') : false;
        default:
            return false;
    }
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
function addDoctor($conn, $data) {
    $name = validateInput($data['name']);
    $specialization = validateInput($data['specialization']);
    $experience = validateInput($data['experience'], 'text', 1000);
    $image_url = !empty($data['image_url']) ? validateInput($data['image_url'], 'url') : 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=300&h=300&fit=crop&crop=face';
    
    if (!$name || !$specialization || !$experience) {
        return ['success' => false, 'message' => 'Data tidak valid. Pastikan semua field diisi dengan benar.'];
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, experience, image_url) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssss", $name, $specialization, $experience, $image_url);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Dokter berhasil ditambahkan!'];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'];
    }
}

// Update doctor function
function updateDoctor($conn, $data) {
    $id = filter_var($data['id'], FILTER_VALIDATE_INT);
    $name = validateInput($data['name']);
    $specialization = validateInput($data['specialization']);
    $experience = validateInput($data['experience'], 'text', 1000);
    $image_url = !empty($data['image_url']) ? validateInput($data['image_url'], 'url') : 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=300&h=300&fit=crop&crop=face';
    
    if (!$id || !$name || !$specialization || !$experience) {
        return ['success' => false, 'message' => 'Data tidak valid. Pastikan semua field diisi dengan benar.'];
    }
    
    try {
        $stmt = $conn->prepare("UPDATE doctors SET name = ?, specialization = ?, experience = ?, image_url = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssi", $name, $specialization, $experience, $image_url, $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Data dokter berhasil diperbarui!'];
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'];
    }
}

// Delete doctor function
function deleteDoctor($conn, $id) {
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if (!$id) {
        return ['success' => false, 'message' => 'ID tidak valid.'];
    }
    
    try {
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
        $result = addDoctor($conn, $_POST);
        $messages[] = ['type' => $result['success'] ? 'success' : 'error', 'text' => $result['message']];
    }

    // Process update doctor
    if (isset($_POST['update_doctor'])) {
        $result = updateDoctor($conn, $_POST);
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
        <?php foreach ($messages as $message): ?>
            <div class="mb-6 p-4 rounded-lg border-l-4 <?= $message['type'] === 'success' ? 'bg-green-50 border-purple-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700' ?> animate-pulse">
                <div class="flex items-center">
                    <i class="fas <?= $message['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-2"></i>
                    <?= htmlspecialchars($message['text']) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" action="" class="flex gap-4">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Cari dokter berdasarkan nama atau spesialisasi..." 
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

        <div class="bg-white rounded-lg shadow-lg p-8 mb-8 border-t-4 border-primary">
            <?php if ($edit_doctor): ?>
                <form method="POST" action="" id="doctorForm">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-edit text-2xl text-yellow-500 mr-3"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Edit Dokter</h2>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
                    <input type="hidden" name="id" value="<?= htmlspecialchars($edit_doctor['id']) ?>" />
                    
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">URL Gambar (opsional)</label>
                            <input type="url" name="image_url" 
                                   value="<?= htmlspecialchars($edit_doctor['image_url']) ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200" />
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
                <form method="POST" action="" id="doctorForm">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pengalaman *</label>
                            <textarea name="experience" required rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">URL Gambar (opsional)</label>
                            <input type="url" name="image_url" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200" />
                        </div>
                    </div>
                    
                    <button type="submit" name="add_doctor" class="mt-8 bg-primary hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>Tambah Dokter
                    </button>
                </form>
            <?php endif; ?>
        </div>

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
                    <?php while($row = $doctors->fetch_assoc()): ?>
                        <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                            <div class="h-48 bg-gray-200 overflow-hidden">
                                <img src="<?= htmlspecialchars($row['image_url']) ?>" 
                                     alt="Dr. <?= htmlspecialchars($row['name']) ?>"
                                     class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                     onerror="this.src='https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=300&h=300&fit=crop&crop=face'" />
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">
                                    Dr. <?= htmlspecialchars($row['name']) ?>
                                </h3>
                                <p class="text-primary font-semibold mb-3 flex items-center">
                                    <i class="fas fa-stethoscope mr-2"></i>
                                    <?= htmlspecialchars($row['specialization']) ?>
                                </p>
                                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                    <?= nl2br(htmlspecialchars(substr($row['experience'], 0, 100))) ?>
                                    <?= strlen($row['experience']) > 100 ? '...' : '' ?>
                                </p>
                                <div class="flex gap-2">
                                    <a href="?edit=<?= intval($row['id']) ?>" 
                                       class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 text-center text-sm flex items-center justify-center">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </a>
                                    <a href="?delete=<?= intval($row['id']) ?>" 
                                       class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 text-center text-sm flex items-center justify-center"
                                       onclick="return confirm('Yakin ingin menghapus Dr. <?= addslashes($row['name']) ?>?')">
                                        <i class="fas fa-trash mr-2"></i>Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

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
                            this.classList.add('border-purple-500');
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

            // Image preview for URL input
            const imageUrlInput = document.querySelector('input[name="image_url"]');
            if (imageUrlInput) {
                imageUrlInput.addEventListener('blur', function() {
                    const url = this.value.trim();
                    if (url) {
                        // Create a preview if it doesn't exist
                        let preview = document.getElementById('image-preview');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.id = 'image-preview';
                            preview.className = 'mt-2';
                            this.parentNode.appendChild(preview);
                        }
                        
                        preview.innerHTML = `
                            <img src="${url}" alt="Preview" class="w-32 h-32 object-cover rounded-lg border" 
                                 onerror="this.parentNode.innerHTML='<div class=&quot;text-red-500 text-sm&quot;>URL gambar tidak valid</div>'" />
                        `;
                    }
                });
            }
        });
    </script>

    <?php $conn->close(); ?>
</body>
</html