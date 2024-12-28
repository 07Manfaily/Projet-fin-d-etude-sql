<?php
session_start();

// Rediriger si déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Configuration de la base de données
$host = 'localhost';
$dbname = 'rh';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $name = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($name) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $name);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $password === $user['password']) {
                // Stockage des données importantes en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username']; // Assurez-vous que le champ correspond à votre BDD
                
                // Redirection
                header("Location: index.php");
                exit;
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $error = "Une erreur s'est produite. Veuillez réessayer plus tard.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Login - RH</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="account-page">
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="account-content">
            <div class="container">
                <!-- Logo -->
                <div class="account-logo">
                    <a href="#"><img src="assets/img/logo2.png" alt="Logo"></a>
                </div>
                <!-- /Logo -->
				<?php if (!empty($error)): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Connexion</h3>
                        <p class="account-subtitle">Accédez à votre tableau de bord</p>

                        <!-- Formulaire de connexion -->
                        <form action="login.php" method="POST">
                            <div class="form-group">
                                <label>Pseudo</label>
                                <input class="form-control" type="text" name="username" required>
                            </div>
                            <div class="form-group">
                                <label>Mot de passe</label>
                                <input class="form-control" type="password" name="password" required>
                            </div>
                            
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Se connecter</button>
                            </div>
                            <div class="account-footer">
                                <p>Pas encore de compte ? <a href="register.html">Créer un compte</a></p>
                            </div>
                        </form>
                        <!-- /Formulaire de connexion -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
</body>
</html>
