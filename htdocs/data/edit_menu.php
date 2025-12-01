<?php
require_once '../includes/koneksi_db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Pastikan user sudah login
if (empty($_SESSION['user_id'])) {
  header('Location: ../akun/login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);

// Ambil data menu berdasarkan id dan user_id
$stmt = $conn->prepare('SELECT * FROM menu WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $id, $user_id);
$stmt->execute();
$menu = $stmt->get_result()->fetch_assoc();

// Jika menu tidak ditemukan atau bukan milik user
if (!$menu) {
  echo '<div class="container my-5"><div class="alert alert-danger text-center">Menu tidak ditemukan atau Anda tidak memiliki akses untuk mengedit menu ini.</div></div>';
  require_once '../includes/footer.php';
  exit;
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama  = trim($_POST['nama'] ?? '');
  $jenis = $_POST['jenis'] ?? $menu['jenis'];
  $file  = $_FILES['gambar'] ?? null;

  $allowedJenis = ['Makanan','Cemilan','Icedrink','Hotdrink'];
  if ($nama === '' || !in_array($jenis, $allowedJenis)) {
    $error = 'Nama atau jenis menu tidak valid.';
  } else {
    $imgPath = $menu['gambar'];

    // Proses upload gambar baru jika ada
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

    // Update data menu
    if (!$error) {
      $stmt = $conn->prepare('UPDATE menu SET gambar = ?, nama = ?, jenis = ? WHERE id = ? AND user_id = ?');
      $stmt->bind_param('sssii', $imgPath, $nama, $jenis, $id, $user_id);
      if ($stmt->execute()) {
        $success = 'Menu berhasil diperbarui.';
        // Refresh data
        $stmt = $conn->prepare('SELECT * FROM menu WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $id, $user_id);
        $stmt->execute();
        $menu = $stmt->get_result()->fetch_assoc();
      } else {
        $error = 'Gagal memperbarui menu.';
      }
    }
  }
}
?>

<div class="container my-5">
  <h3 class="text-center fw-bold">Edit Menu</h3>
  <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="card p-3">
    <div class="mb-3">
      <label class="form-label">Nama Menu</label>
      <input name="nama" class="form-control" value="<?php echo htmlspecialchars($menu['nama']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Jenis</label>
      <select name="jenis" class="form-select" required>
        <?php
        foreach (['Makanan','Cemilan','Icedrink','Hotdrink'] as $j) {
          $sel = $menu['jenis'] === $j ? 'selected' : '';
          echo "<option $sel>$j</option>";
        }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Gambar</label>
      <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.gif">
      <?php if ($menu['gambar']): ?>
        <img src="../<?php echo htmlspecialchars($menu['gambar']); ?>" class="mt-2 rounded" height="80">
      <?php endif; ?>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a class="btn btn-secondary" href="../view/semua_menu.php">Kembali</a>
  </form>
</div>

<?php require_once '../includes/footer.php'; ?>
