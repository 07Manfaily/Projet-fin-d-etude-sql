<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$dbname = 'rh';
$username = 'root';
$password = '';
$message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ajout d'une indemnité
    if (isset($_POST['add'])) {
        $libelle = trim($_POST['libelle']);
        $code = trim($_POST['code']);

        if (!empty($libelle) && !empty($code)) {
            $stmt = $pdo->prepare("INSERT INTO indemnite (codeInd, libelle) VALUES (:code, :libelle)");
            $stmt->execute([
                ':code' => $code,
                ':libelle' => $libelle,
            ]);
            $message = "Indemnité ajoutée avec succès!";
        } else {
            $message = "Erreur: Veuillez remplir tous les champs correctement.";
        }
    }

    // Modification d'une indemnité
    if (isset($_POST['edit'])) {
        $id = intval($_POST['edit_id']);
        $code = trim($_POST['edit_code']);
        $libelle = trim($_POST['edit_libelle']);

        if (!empty($libelle) && !empty($code)) {
            $stmt = $pdo->prepare("UPDATE indemnite SET codeInd = :code, libelle = :libelle WHERE codeInd = :id");
            $stmt->execute([
                ':id' => $id,
                ':code' => $code,
                ':libelle' => $libelle,
            ]);
            $message = "Indemnité modifiée avec succès!";
        } else {
            $message = "Erreur: Veuillez remplir tous les champs correctement.";
        }
    }

    // Suppression d'une indemnité
    if (isset($_POST['delete'])) {
        $id = $_POST['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM indemnite WHERE codeInd = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $message = "Indemnité supprimée avec succès!";
    }

    // Affichage des indemnités
    $stmt_select = $pdo->query("SELECT * FROM indemnite");
    $indemnites = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

    // Vérification si $indemnites est vide
    if ($indemnites === false) {
        $indemnites = [];
    }

} catch (PDOException $e) {
    $message = "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des indemnités - RH</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include('header.php'); ?>
        <?php include('sidebar.php'); ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Indemnités</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Indemnités</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_indemnite">
                                <i class="fa fa-plus"></i> Ajouter une indemnité
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert <?php echo strpos($message, 'Erreur') === 0 ? 'alert-danger' : 'alert-success'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <div class="row staff-grid-row">
                    <?php if (!empty($indemnites)): ?>
                        <?php foreach ($indemnites as $row): ?>
                            <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
                                <div class="profile-widget">
                                    <div class="profile-img">
                                        <a href="#" class="avatar"><img src="assets/img/indemnite.png" alt="Indemnité"></a>
                                    </div>
                                    <div class="dropdown profile-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                               data-target="#edit_<?php echo htmlspecialchars($row['codeInd']); ?>">
                                                <i class="fa fa-pencil m-r-5"></i> Modifier
                                            </a>
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                               data-target="#delete_<?php echo htmlspecialchars($row['codeInd']); ?>">
                                                <i class="fa fa-trash-o m-r-5"></i> Supprimer
                                            </a>
                                        </div>
                                    </div>
                                    <h4 class="user-name m-t-10 mb-0 text-ellipsis">
                                        <?php echo htmlspecialchars($row['libelle']); ?>
                                    </h4>
                                    <div class="small text-muted">
                                        Code: <?php echo htmlspecialchars($row['codeInd']); ?>
                                    </div>

                                </div>

                                <!-- Edit Modal -->
                                <div id="edit_<?php echo htmlspecialchars($row['codeInd']); ?>" class="modal custom-modal fade" role="dialog">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier l'indemnité</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($row['codeInd']); ?>">
                                                    <div class="form-group">
                                                        <label>Code</label>
                                                        <input class="form-control" type="text" name="edit_code"
                                                               value="<?php echo htmlspecialchars($row['codeInd']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Libellé</label>
                                                        <input class="form-control" type="text" name="edit_libelle"
                                                               value="<?php echo htmlspecialchars($row['libelle']); ?>" required>
                                                    </div>

                                                    <div class="submit-section">
                                                        <button class="btn btn-primary submit-btn" type="submit" name="edit">Modifier</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal custom-modal fade" id="delete_<?php echo htmlspecialchars($row['codeInd']); ?>" role="dialog">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div class="form-header">
                                                    <h3>Supprimer l'indemnité</h3>
                                                    <p>Êtes-vous sûr de vouloir supprimer cette indemnité ?</p>
                                                </div>
                                                <div class="modal-btn delete-action">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($row['codeInd']); ?>">
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
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucune indemnité trouvée.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div id="add_indemnite" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une indemnité</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label>Code <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="code" required>
                            </div>
                            <div class="form-group">
                                <label>Libellé <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="libelle" required>
                            </div>

                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn" type="submit" name="add">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
