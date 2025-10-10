<?php
include '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM blogposts WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
