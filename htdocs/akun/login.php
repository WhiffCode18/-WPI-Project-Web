<?php
require_once '../includes/koneksi_db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  $stmt = $conn->prepare('SELECT id, password, username FROM user WHERE username=? LIMIT 1');
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $res = $stmt->get_result();
  $user = $res->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header('Location: ../view/dashboard.php');
    exit;
  } else {
    $error = 'Username atau password salah. Belum punya akun? Registrasi dulu.';
  }
}
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <h3 class="text-center mb-4">Login</h3>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
      <form method="post" class="card p-3">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
        <div class="text-center mt-3">
          Belum registrasi? <a href="registrasi.php">Registrasi</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
