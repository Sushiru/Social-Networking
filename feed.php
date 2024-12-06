<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'social_network');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $content);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT p.content, u.username, p.created_at FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>Welcome to the Feed</h1>
</header>

<div class="container">
    <form method="POST" action="">
        <textarea name="content" placeholder="What's on your mind?" required></textarea>
        <button type="submit">Post</button>
    </form>

    <div class="feed">
        <h3>Latest Posts</h3>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post">
                <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                <p><?php echo htmlspecialchars($row['content']); ?></p>
                <small><?php echo $row['created_at']; ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<footer>
    &copy; 2024 My Social Network. All rights reserved. <a href="#">Privacy Policy</a>
</footer>
</body>
</html>

<?php
$conn->close();
?>
