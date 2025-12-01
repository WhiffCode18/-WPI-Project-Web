<?php
$base = '/'; // sesuaikan jika bukan root
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?php echo $base; ?>view/dashboard.php">KelolaWarung</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>view/semua_menu.php">Semua</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>view/menu_makanan.php">Makanan</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>view/menu_cemilan.php">Cemilan</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>view/menu_icedrink.php">Icedrink</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>view/menu_hotdrink.php">Hotdrink</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base; ?>akun/akun.php">
            <i class="bi bi-person-circle"></i> Akun
          </a>
        </li>
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>akun/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>akun/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
