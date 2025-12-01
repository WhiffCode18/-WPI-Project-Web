<?php
require_once '../includes/koneksi_db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

if (empty($_SESSION['user_id'])) {
  header('Location: ../akun/login.php');
  exit;
}

$user_id = $_SESSION['user_id'];

function countJenis($conn, $jenis, $user_id) {
  $stmt = $conn->prepare('SELECT COUNT(*) AS c FROM menu WHERE jenis = ? AND user_id = ?');
  $stmt->bind_param('si', $jenis, $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_assoc()['c'] ?? 0;
}

$counts = [
  'Makanan'  => countJenis($conn, 'Makanan', $user_id),
  'Cemilan'  => countJenis($conn, 'Cemilan', $user_id),
  'Icedrink' => countJenis($conn, 'Icedrink', $user_id),
  'Hotdrink' => countJenis($conn, 'Hotdrink', $user_id),
];
?>

<style>
  body {
    background: url('../uploads/backgrounds/warung-bg.jpg') no-repeat center center fixed;
    background-size: cover;
  }
  .dashboard-overlay {
    background-color: rgba(255, 255, 255, 0.9);
    padding: 2rem;
    border-radius: 12px;
  }
  .card {
    box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.1);
  }
</style>

<div class="container my-5 dashboard-overlay">
  <h2 class="text-center fw-bold">Buat Daftar Menu Untuk Warung yang Anda Kelola</h2>
  <p class="text-center">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
  <p class="text-center small">Catat dan Tampilkan Daftar Menu dari Warung Anda dengan Cepat dan Mudah!</p>

  <h4 class="text-center fw-bold mt-4">Jumlah Menu dari Warung Ini</h4>
  <div class="row row-cols-1 row-cols-md-4 g-3 mt-2">
    <?php foreach ($counts as $jenis => $c): ?>
      <div class="col">
        <div class="card text-center h-100">
          <div class="card-body">
            <div class="display-6 mb-2">
              <?php
              $icons = [
                'Makanan'  => 'bi-basket',
                'Cemilan'  => 'bi-cookie',
                'Icedrink' => 'bi-cup-straw',
                'Hotdrink' => 'bi-cup-hot'
              ];
              echo '<i class="bi ' . $icons[$jenis] . '"></i>';
              ?>
            </div>
            <div class="fw-semibold">Menu <?php echo $jenis; ?></div>
            <div class="fs-4"><?php echo $c; ?></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <h4 class="text-center fw-bold mt-5">Catat & Lihat Daftar Menu di Warung Ini</h4>
  <div class="row row-cols-1 row-cols-md-4 g-3 mt-2">
    <?php
    $links = [
      ['Makanan','menu_makanan.php','Makanan yang dapat Anda pesan','bi-basket'],
      ['Cemilan','menu_cemilan.php','Cemilan yang dapat Anda pesan','bi-cookie'],
      ['Icedrink','menu_icedrink.php','Icedrink yang dapat Anda pesan','bi-cup-straw'],
      ['Hotdrink','menu_hotdrink.php','Hotdrink yang dapat Anda pesan','bi-cup-hot'],
    ];
    foreach ($links as [$label,$href,$desc,$icon]): ?>
      <div class="col">
        <div class="card h-100">
          <div class="card-body text-center">
            <div class="display-6 mb-2"><i class="bi <?php echo $icon; ?>"></i></div>
            <div class="fw-semibold">List Menu <?php echo $label; ?></div>
            <div class="text-muted small"><?php echo $desc; ?></div>
          </div>
          <div class="card-footer text-center">
            <a class="btn btn-outline-primary" href="<?php echo $href; ?>">Lihat</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <h4 class="text-center fw-bold mt-5">Lihat Semua Menu yang Tersedia</h4>
  <div class="d-flex justify-content-center">
    <a class="btn btn-primary" href="semua_menu.php">
      <i class="bi bi-list-ul me-2"></i>Daftar Semua Menu
    </a>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
