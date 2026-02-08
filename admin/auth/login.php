<?php
session_start();
require_once '../../core/config.php';

// Giriş yapılmış mı kontrol et
if (isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit;
}

// Hata mesajı
$error = '';
$username_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $username_value = $username;

    if (empty($username) || empty($password)) {
        $error = 'Kullanıcı adı ve şifre gereklidir.';
    } else {
        // Veritabanında kullanıcıyı ara
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Şifreleri kontrol et
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_role'] = $user['role'];
                header('Location: ../index.php');
                exit;
            } else {
                $error = 'Kullanıcı adı veya şifre yanlış.';
            }
        } else {
            $error = 'Kullanıcı adı veya şifre yanlış.';
        }
    }
}

function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - MasterStudio</title>
    <link rel="stylesheet" href="../../assets/css/admin-login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Admin Paneli</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username_value); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Şifre:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Giriş Yap</button>
            </form>
        </div>
    </div>
</body>
</html>
