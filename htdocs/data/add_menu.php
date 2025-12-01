<?php
require_once '../includes/koneksi_db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Pastikan user sudah login
if (empty($_SESSION['user_id'])) {
  header('Location: ../akun/login.php');
  exit;
}

$user_id = $_SESSION['user_id']; // Ambil ID user dari session
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama  = trim($_POST['nama'] ?? '');
  $jenis = $_POST['jenis'] ?? '';
  $file  = $_FILES['gambar'] ?? null;

  $allowedJenis = ['Makanan','Cemilan','Icedrink','Hotdrink'];
  if ($nama === '' || !in_array($jenis, $allowedJenis)) {
    $error = 'Nama atau jenis menu tidak valid.';
  } else {
    $imgPath = null;

    // Proses upload gambar jika ada
    if ($file && $file['tmp_name']) {
      $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg','jpeg','png','gif'];
      if (!in_array($ext, $allowed)) {
        $error = 'Format gambar harus jpg/jpeg/png/gif.';
      } elseif ($file['size'] > 2 * 1024 * 1024) {
        $error = 'Ukuran gambar maksimal 2MB.';
      } else {
        $destDir = __DIR__ . '/../uploads/menu';
        if (!is_dir($destDir)) mkdir($destDir, 0777, true);
        $fname = 'mn_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $imgPath = 'uploads/menu/' . $fname;
        move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $imgPath);
      }
    }

    // Simpan ke database jika tidak ada error
    if (!$error) {
      $stmt = $conn->prepare('INSERT INTO menu (user_id, gambar, nama, jenis) VALUES (?,?,?,?)');
      $stmt->bind_param('isss', $user_id, $imgPath, $nama, $jenis);
      if ($stmt->execute()) {
        $success = 'Menu berhasil ditambahkan.';
      } else {
        $error = 'Gagal menambahkan menu.';
      }
    }
  }
}
?>

<div class="container my-5">
  <h3 class="text-center fw-bold">Tambah Menu Baru</h3>
  <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="card p-3">
    <div class="mb-3">
      <label class="form-label">Nama Menu</label>
      <input name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Jenis</label>
      <select name="jenis" class="form-select" required>
        <option value="">-- pilih --</option>
        <option>Makanan</option>
        <option>Cemilan</option>
        <option>Icedrink</option>
        <option>Hotdrink</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Gambar (opsional)</label>
      <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.gif">
    </div>
    <button class="btn btn-success">Simpan</button>
    <a class="btn btn-secondary" href="../view/semua_menu.php">Batal</a>
  </form>
</div>

<?php require_once '../includes/footer.php'; ?>
