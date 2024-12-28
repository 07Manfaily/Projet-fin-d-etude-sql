<?php
session_start();
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Configuration de la base de données
$host = 'localhost';
$dbname = 'rh';
$username = 'root';
$password = '';
$message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ajout d'un grade
    if (isset($_POST['add'])) {
        $code = trim($_POST['code']);
        $salaire = floatval($_POST['sal']);

        if (!empty($code) && $salaire > 0) {
            $stmt = $pdo->prepare("INSERT INTO grade (codeGr, salaireBase) VALUES (:code, :salaire)");
            $stmt->execute([
                ':code' => $code,
                ':salaire' => $salaire
            ]);
            $message = "Grade ajouté avec succès!";
        } else {
            $message = "Erreur: Veuillez remplir tous les champs correctement.";
        }
    }

    // Modification d'un grade
    if (isset($_POST['edit'])) {
        $code = trim($_POST['edit_code']);
        $salaire = floatval($_POST['edit_salaire']);

        if (!empty($code) && $salaire > 0) {
            $stmt = $pdo->prepare("UPDATE grade SET salaireBase = :salaire WHERE codeGr = :code");
            $stmt->execute([
                ':code' => $code,
                ':salaire' => $salaire
            ]);
            $message = "Grade modifié avec succès!";
        } else {
            $message = "Erreur: Veuillez remplir tous les champs correctement.";
        }
    }

    // Suppression d'un grade
    if (isset($_POST['delete'])) {
        $code = $_POST['delete_code'];
        $stmt = $pdo->prepare("DELETE FROM grade WHERE codeGr = :code");
        $stmt->execute([':code' => $code]);
        $message = "Grade supprimé avec succès!";
    }

    // Affichage des grades
    $stmt_select = $pdo->query("SELECT * FROM grade ORDER BY codeGr");
} catch (PDOException $e) {
    $message = "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="RH - Gestion des grades">
    <meta name="keywords" content="admin, grades, rh, gestion">
    <meta name="author" content="Your Name">
    <meta name="robots" content="noindex, nofollow">
    <title>Gestion des grades - RH</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    
    <!-- Lineawesome CSS -->
    <link rel="stylesheet" href="assets/css/line-awesome.min.css">
    
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="assets/css/select2.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <?php include('header.php'); ?>
        <?php include('sidebar.php'); ?>
        
        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            <div class="content container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Grades</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Grades</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_grade">
                                <i class="fa fa-plus"></i> Ajouter un grade
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <?php if (!empty($message)): ?>
                    <div class="alert <?php echo strpos($message, 'Erreur') === 0 ? 'alert-danger' : 'alert-success'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <div class="row staff-grid-row">
                    <?php while ($row = $stmt_select->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
                            <div class="profile-widget">
                                <div class="profile-img">
                                    <a href="#" class="avatar"><img src="assets/img/grade.png" alt="Grade"></a>
                                </div>
                                <div class="dropdown profile-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#edit_<?php echo htmlspecialchars($row['codeGr']); ?>">
                                            <i class="fa fa-pencil m-r-5"></i> Modifier
                                        </a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_<?php echo htmlspecialchars($row['codeGr']); ?>">
                                            <i class="fa fa-trash-o m-r-5"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                                <h4 class="user-name m-t-10 mb-0 text-ellipsis">
                                    <?php echo htmlspecialchars($row['codeGr']); ?>
                                </h4>
                                <div class="small text-muted">
                                    Salaire base: <?php echo number_format($row['salaireBase'], 2, ',', ' '); ?> fcfa
                                </div>
                            </div>

                            <!-- Edit Grade Modal -->
                            <div id="edit_<?php echo htmlspecialchars($row['codeGr']); ?>" class="modal custom-modal fade" role="dialog">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Modifier le grade</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="">
                                                <input type="hidden" name="edit_code" value="<?php echo htmlspecialchars($row['codeGr']); ?>">
                                                <div class="form-group">
                                                    <label>Code du grade</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['codeGr']); ?>" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label>Salaire de base</label>
                                                    <input class="form-control" type="number" step="0.01" name="edit_salaire" 
                                                           value="<?php echo htmlspecialchars($row['salaireBase']); ?>" required>
                                                </div>
                                                <div class="submit-section">
                                                    <button class="btn btn-primary submit-btn" type="submit" name="edit">Modifier</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Edit Grade Modal -->

                            <!-- Delete Grade Modal -->
                            <div class="modal custom-modal fade" id="delete_<?php echo htmlspecialchars($row['codeGr']); ?>" role="dialog">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="form-header">
                                                <h3>Supprimer le grade</h3>
                                                <p>Êtes-vous sûr de vouloir supprimer ce grade ?</p>
                                            </div>
                                            <div class="modal-btn delete-action">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="delete_code" value="<?php echo htmlspecialchars($row['codeGr']); ?>">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <button type="submit" name="delete" class="btn btn-primary continue-btn">Supprimer</button>
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="button" data-dismiss="modal" class="btn btn-primary cancel-btn">Annuler</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Delete Grade Modal -->
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <!-- /Page Content -->

            <!-- Add Grade Modal -->
            <div id="add_grade" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter un grade</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label class="col-form-label">Code du grade <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="code" required>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Salaire de base <span class="text-danger">*</span></label>
                                    <input class="form-control" type="number" step="0.01" name="sal" required>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn" type="submit" name="add">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Add Grade Modal -->
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.5.1.min.js"></script>
    
    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    
    <!-- Slimscroll JS -->
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="assets/js/select2.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
</body>
</html>