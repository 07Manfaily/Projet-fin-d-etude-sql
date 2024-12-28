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

    // Ajout d'une entrée dans ADroit
    if (isset($_POST['add'])) {
        $codeGr = trim($_POST['codeGr']);
        $codeInd = trim($_POST['codeInd']);
        $montant = floatval($_POST['montant']);

        if (!empty($codeGr) && !empty($codeInd) && $montant > 0) {
            $stmt = $pdo->prepare("INSERT INTO ADroit (codeGr, codeInd, montant) VALUES (:codeGr, :codeInd, :montant)");
            $stmt->execute([
                ':codeGr' => $codeGr,
                ':codeInd' => $codeInd,
                ':montant' => $montant
            ]);
            $message = "Indemnité ajoutée avec succès!";
        } else {
            $message = "Erreur: Veuillez remplir tous les champs correctement.";
        }
    }

    // Modification d'une entrée dans ADroit
    if (isset($_POST['edit'])) {
        $codeGr = trim($_POST['edit_codeGr']);
        $codeInd = trim($_POST['edit_codeInd']);
        $montant = floatval($_POST['edit_montant']);

        if (!empty($codeGr) && !empty($codeInd) && $montant > 0) {
            $stmt = $pdo->prepare("UPDATE ADroit SET montant = :montant WHERE codeGr = :codeGr AND codeInd = :codeInd");
            $stmt->execute([
                ':codeGr' => $codeGr,
                ':codeInd' => $codeInd,
                ':montant' => $montant
            ]);
            $message = "Indemnité modifiée avec succès!";
        } else {
            $message = "Erreur: Veuillez remplir tous les champs correctement.";
        }
    }

    // Suppression d'une entrée dans ADroit
    if (isset($_POST['delete'])) {
        $codeGr = $_POST['delete_codeGr'];
        $codeInd = $_POST['delete_codeInd'];
        $stmt = $pdo->prepare("DELETE FROM ADroit WHERE codeGr = :codeGr AND codeInd = :codeInd");
        $stmt->execute([
            ':codeGr' => $codeGr,
            ':codeInd' => $codeInd
        ]);
        $message = "Indemnité supprimée avec succès!";
    }

    // Affichage des entrées dans ADroit avec jointures sur Grade et Indemnite
    $stmt_select = $pdo->query("
        SELECT ADroit.*,
               Grade.codeGr AS gradeCode,
               Grade.salaireBase,
               Indemnite.codeInd AS indemniteCode,
               Indemnite.libelle
        FROM ADroit
        JOIN Grade ON ADroit.codeGr = Grade.codeGr
        JOIN Indemnite ON ADroit.codeInd = Indemnite.codeInd
        ORDER BY ADroit.codeGr, ADroit.codeInd
    ");

    // Récupération des données pour les formulaires (listes déroulantes)
    $grades = $pdo->query("SELECT codeGr, salaireBase FROM Grade")->fetchAll(PDO::FETCH_ASSOC);
    $indemnites = $pdo->query("SELECT codeInd, libelle FROM Indemnite")->fetchAll(PDO::FETCH_ASSOC);

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
                                    Salaire base: <?php echo number_format($row['montant'], 2, ',', ' '); ?> fcfa
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
                                              <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="edit_codeGr" value="<?php echo $row['codeGr']; ?>">
                                <input type="hidden" name="edit_codeInd" value="<?php echo $row['codeInd']; ?>">
                                <input type="number" class="form-control mb-2" name="edit_montant"
                                       value="<?php echo $row['montant']; ?>" step="0.01" required>
                                <button type="submit" class="btn btn-warning btn-sm" name="edit">Modifier</button>
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
                                                                           <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="delete_codeGr" value="<?php echo $row['codeGr']; ?>">
                                <input type="hidden" name="delete_codeInd" value="<?php echo $row['codeInd']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" name="delete">Supprimer</button>
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
            <div class="form-group mb-3">
                <label for="codeGr">Code du grade</label>
                <select class="form-control" id="codeGr" name="codeGr" required>
                    <option value="">Sélectionnez un grade</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?php echo $grade['codeGr']; ?>">
                            <?php echo $grade['codeGr'] . ' (Salaire: ' . $grade['salaireBase'] . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="codeInd">Code de l'indemnité</label>
                <select class="form-control" id="codeInd" name="codeInd" required>
                    <option value="">Sélectionnez une indemnité</option>
                    <?php foreach ($indemnites as $indemnite): ?>
                        <option value="<?php echo $indemnite['codeInd']; ?>">
                            <?php echo $indemnite['codeInd'] . ' (' . $indemnite['libelle'] . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="montant">Montant</label>
                <input class="form-control" type="number" id="montant" name="montant" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add">Ajouter</button>
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