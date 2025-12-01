<?php
require_once '../includes/koneksi_db.php';
session_start();

// Pastikan user sudah login
if (empty($_SESSION['user_id'])) {
  header('Location: ../akun/login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);

// Validasi: hanya boleh menghapus menu milik sendiri
$stmt = $conn->prepare('SELECT gambar FROM menu WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $id, $user_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
  $_SESSION['flash'] = 'Menu tidak ditemukan atau Anda tidak memiliki akses untuk menghapusnya.';
  header('Location: ../view/semua_menu.php');
  exit;
}

// Hapus file gambar jika ada
if (!empty($row['gambar'])) {
  $path = __DIR__ . '/../' . $row['gambar'];
  if (is_file($path)) @unlink($path);
}

// Hapus data dari database
$stmt = $conn->prepare('DELETE FROM menu WHERE id = ? AND user_id = ?');
$stmt->bind_param('ii', $id, $user_id);
$stmt->execute();

// Redirect dengan notifikasi
$_SESSION['flash'] = 'Menu berhasil dihapus dari database.';
header('Location: ../view/semua_menu.php');
exit;
