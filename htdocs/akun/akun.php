<?php
require_once '../includes/koneksi_db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$success = $error = '';
$userId = $_SESSION['user_id'];
// Ambil data
$stmt = $conn->prepare('SELECT username,email,profile FROM user WHERE id=?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email    = trim($_POST['email'] ?? $user['email']);
  $profileFile = $_FILES['profile'] ?? null;

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Email tidak valid.';
  } else {
    $profilePath = $user['profile'];
    if ($profileFile && $profileFile['tmp_name']) {
      $ext = strtolower(pathinfo($profileFile['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg','jpeg','png','gif'];
      if (!in_array($ext, $allowed)) {
        $error = 'Format foto profil harus jpg/jpeg/png/gif.';
      } else {
        $destDir = __DIR__ . '/../uploads/profile';
        if (!is_dir($destDir)) mkdir($destDir, 0777, true);
        $fname = 'pf_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $profilePath = 'uploads/profile/' . $fname;
        move_uploaded_file($profileFile['tmp_name'], __DIR__ . '/../' . $profilePath);
      }
    }
    if (!$error) {
      $stmt = $conn->prepare('UPDATE user SET email=?, profile=? WHERE id=?');
      $stmt->bind_param('ssi', $email, $profilePath, $userId);
      if ($stmt->execute()) {
        $success = 'Profil diperbarui.';
        $user['email'] = $email;
        $user['profile'] = $profilePath;
      } else {
        $error = 'Gagal memperbarui profil.';
      }
    }
  }
}
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h3 class="mb-3">Pengaturan Akun</h3>
      <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
      <div class="card p-3">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
        </div>
        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Foto profil</label>
            <input type="file" name="profile" class="form-control" accept=".jpg,.jpeg,.png,.gif">
            <?php if ($user['profile']): ?>
              <img src="../<?php echo $user['profile']; ?>" class="mt-2 rounded" height="80">
            <?php endif; ?>
          </div>
          <button class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
