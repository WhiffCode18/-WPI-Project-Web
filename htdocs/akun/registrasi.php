<?php
require_once '../includes/koneksi_db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $profileFile = $_FILES['profile'] ?? null;

  // Validasi
  if (!preg_match('/^[A-Za-z0-9_]{3,32}$/', $username)) {
    $error = 'Username atau Email yang Anda masukkan tidak sesuai!';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Username atau Email yang Anda masukkan tidak sesuai!';
  } elseif (strlen($password) < 6) {
    $error = 'Password minimal 6 karakter.';
  } else {
    // Cek duplikasi
    $stmt = $conn->prepare('SELECT id FROM user WHERE username=? OR email=? LIMIT 1');
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $error = 'Username atau Email sudah terdaftar.';
    } else {
      // Upload profil (opsional)
      $profilePath = null;
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
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO user (username,email,password,profile) VALUES (?,?,?,?)');
        $stmt->bind_param('ssss', $username, $email, $hash, $profilePath);
        if ($stmt->execute()) {
          $success = 'Registrasi Berhasil';
        } else {
          $error = 'Terjadi kesalahan saat registrasi.';
        }
      }
    }
  }
}
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h3 class="text-center mb-4">Registrasi Akun</h3>
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <div class="text-center">
          <a class="btn btn-primary" href="login.php">Login</a>
        </div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" class="card p-3">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input name="username" class="form-control" placeholder="contoh: kelolawarung_01" required>
          <div class="form-text">Huruf/angka/underscore, 3–32 karakter.</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="nama@domain.com" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" minlength="6" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Foto profil (opsional)</label>
          <input type="file" name="profile" class="form-control" accept=".jpg,.jpeg,.png,.gif">
        </div>
        <button class="btn btn-success w-100">Daftar</button>
        <div class="text-center mt-3">
          Sudah punya akun? <a href="login.php">Login</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
