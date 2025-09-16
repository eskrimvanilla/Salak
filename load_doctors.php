<?php
include 'connect.php';

$sql = "SELECT * FROM doctors ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='team-card text-center'>
                <img src='" . htmlspecialchars($row["image_url"]) . "' alt='Foto " . htmlspecialchars($row["name"]) . ", dokter profesional' class='w-full h-64 rounded-full mx-auto mb-4' />
                <h4 class='text-lg font-bold'>" . htmlspecialchars($row["name"]) . "</h4>
                <p class='text-gray-600'>" . htmlspecialchars($row["specialization"]) . "</p>
                <p class='text-sm'>" . htmlspecialchars($row["experience"]) . "</p>
              </div>";
    }
} else {
    echo "<p>Tidak ada dokter terdaftar.</p>";
}

$conn->close();
?>
