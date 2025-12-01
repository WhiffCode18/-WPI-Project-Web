<?php
$host = 'sql100.infinityfree.com';
$user = 'if0_40563567';
$pass = 'dwikristian123';
$db   = 'if0_40563567_kelola_warung';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die('Koneksi gagal: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
