<?php
// view/menu_icedrink.php

require_once '../includes/koneksi_db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Cek sesi login
if (empty($_SESSION['user_id'])) {
    header('Location: ../akun/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data menu milik user yang sedang login dengan jenis "Icedrink"
$stmt = $conn->prepare("SELECT id, gambar, nama, jenis 
                        FROM menu 
                        WHERE user_id = ? AND jenis = 'Icedrink' 
                        ORDER BY created_at DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
  <h2 class="text-center fw-bold display-5 mb-4">Daftar Menu Icedrink</h2>

  <div class="text-end mb-3">
    <a class="btn btn-success" href="../data/add_menu.php">
      <i class="bi bi-plus-circle"></i> Tambah Menu Baru
    </a>
  </div>

  <?php if ($result): ?>
    <?php if ($result->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-primary">
            <tr>
              <th scope="col">Gambar</th>
              <th scope="col">Nama</th>
              <th scope="col">Jenis</th>
              <th scope="col" class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td>
                  <?php if (!empty($row['gambar'])): ?>
                    <img src="../<?php echo htmlspecialchars($row['gambar']); ?>" 
                         alt="Gambar Menu" 
                         class="rounded" 
                         style="width: 80px; height: 64px; object-fit: cover;">
                  <?php else: ?>
                    <img src="https://via.placeholder.com/80x64?text=No+Image" 
                         alt="No Image" 
                         class="rounded">
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <td><?php echo htmlspecialchars($row['jenis']); ?></td>
                <td class="text-end">
                  <a href="../data/edit_menu.php?id=<?php echo (int)$row['id']; ?>" 
                     class="btn btn-sm btn-warning me-2">Edit</a>
                  <button class="btn btn-sm btn-danger" 
                          onclick="confirmDelete(<?php echo (int)$row['id']; ?>)">Hapus</button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <script>
        function confirmDelete(id) {
          if (confirm("Apakah kamu yakin menghapus menu ini?")) {
            window.location.href = "../data/delete_menu.php?id=" + id;
          }
        }
      </script>

    <?php else: ?>
      <div class="alert alert-info text-center">Belum ada menu icedrink yang ditambahkan oleh Anda.</div>
    <?php endif; ?>
  <?php else: ?>
    <div class="alert alert-danger">
      Terjadi kesalahan saat mengambil data menu. Pesan error: <?php echo $conn->error; ?>
    </div>
  <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>