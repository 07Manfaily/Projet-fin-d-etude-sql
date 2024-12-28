<?php
session_start(); // Démarre la session
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Redirige vers la page de login si non connecté
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

    // Récupération de la liste des grades pour le select
    $stmt_grades = $pdo->query("SELECT codeGr FROM grade ORDER BY codeGr");
    $all_grade = $stmt_grades->fetchAll(PDO::FETCH_ASSOC);

    // Ajout d'un employé
    if (isset($_POST['add'])) {
        $matricule = $_POST['matricule'];
        $nom = $_POST['username'];
        $tel = $_POST['phone'];
        $grad = $_POST['grad'];

        // Préparation de la requête INSERT
        $stmt = $pdo->prepare("INSERT INTO employe (matricule, nom, tel, codeGr) VALUES (:matricule, :nom, :tel, :grad)");
        $stmt->execute([
            ':matricule' => $matricule,
            ':nom' => $nom,
            ':tel' => $tel,
            ':grad' => $grad
        ]);
        $message = "Employé ajouté avec succès!";
    }

  if (isset($_POST['edit'])) {
    $matricule = $_POST['edit_matricule'];
    $nom = $_POST['edit_username'];
    $tel = $_POST['edit_phone'];
    $grad = $_POST['grad'];  // Assurez-vous que grad est bien transmis par le formulaire

    // Préparation de la requête UPDATE
    $stmt = $pdo->prepare("UPDATE employe SET nom = :nom, tel = :tel, codeGr = :grad WHERE matricule = :matricule");
    $stmt->execute([
        ':nom' => $nom,
        ':tel' => $tel,
        ':grad' => $grad,
        ':matricule' => $matricule
    ]);
    $message = "Employé modifié avec succès!";


}


    // Suppression d'un employé
    if (isset($_POST['delete'])) {
        $matricule = $_POST['delete_id'];
        // Préparation de la requête DELETE
        $stmt = $pdo->prepare("DELETE FROM employe WHERE matricule = :matricule");
        $stmt->execute([':matricule' => $matricule]);
        $message = "Employé supprimé avec succès!";
    }

    // Affichage des employés
    $stmt_select = $pdo->query("SELECT * FROM employe");

} catch (PDOException $e) {
    echo "<p>Erreur : " . $e->getMessage() . "</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="Smarthr - Bootstrap Admin Template">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
        <meta name="author" content="Dreamguys - Bootstrap Admin Template">
        <meta name="robots" content="noindex, nofollow">
        <title>Employees - HRMS admin template</title>

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

		<!-- Datetimepicker CSS -->
		<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/css/style.css">

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
    </head>
    <body>
		<!-- Main Wrapper -->
        <div class="main-wrapper">

		<?php include('header.php') ?>

			<?php include('sidebar.php') ?>

			<!-- Page Wrapper -->
            <div class="page-wrapper">

				<!-- Page Content -->
                <div class="content container-fluid">

					<!-- Page Header -->
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col">
								<h3 class="page-title">Employee</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
									<li class="breadcrumb-item active">Employee</li>
								</ul>
							</div>
							<div class="col-auto float-right ml-auto">
								<a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_employee"><i class="fa fa-plus"></i> Ajouter un employé</a>

							</div>
						</div>
					</div>
					<!-- /Page Header -->


					<?php if (!empty($message)): ?>
                                <div class="alert alert-success">
                                    <?php echo htmlspecialchars($message); ?>
                                </div>
                            <?php endif; ?>
					<div class="row staff-grid-row">


					<?php
    // Vérification supplémentaire avant la boucle
    if ($stmt_select !== null) :
        while ($row = $stmt_select->fetch(PDO::FETCH_ASSOC)) : ?>
			<!-- Edit Employee Modal -->
			<!-- Edit Employee Modal -->
<div id="edit_<?php echo $row['matricule']; ?>" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'employé</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="edit_matricule" value="<?php echo $row['matricule']; ?>">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Matricule</label>
                                <input class="form-control" type="text" name="edit_matricule" disabled="disabled"
                                       value="<?php echo htmlspecialchars($row['matricule']); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Nom et prénoms</label>
                                <input class="form-control" type="text" name="edit_username"
                                       value="<?php echo htmlspecialchars($row['nom']); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input class="form-control" type="text" name="edit_phone"
                                       value="<?php echo htmlspecialchars($row['tel']); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Grade</label>
                                <select class="form-control" name="grad" required>
                                    <option value="<?php echo htmlspecialchars($row['codeGr']); ?>"><?php echo htmlspecialchars($row['codeGr']); ?></option>
                                    <?php foreach ($all_grade as $grad): ?>
                                        <option value="<?php echo htmlspecialchars($grad['codeGr']); ?>"><?php echo htmlspecialchars($grad['codeGr']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn" type="submit" name="edit">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Employee Modal -->

				<!-- /Edit Employee Modal -->
		<!-- Delete Employee Modal -->
		<div class="modal custom-modal fade" id="delete_<?php echo $row['matricule']; ?>" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
                            <div class="modal-body">
                                <div class="form-header">
                                    <h3>Supprimer l'employé</h3>
                                    <p>Êtes-vous sûr de vouloir supprimer cet employé ?</p>
                                </div>
                                <div class="modal-btn delete-action">
                                    <form method="POST" action="">
                                        <input type="hidden" name="delete_id" value="<?php echo $row['matricule']; ?>">
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
				<!-- /Delete Employee Modal -->
            <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
                <div class="profile-widget">
                    <div class="profile-img">
                        <a href="" class="avatar"><img src="assets/img/user.jpg" alt=""></a>
                    </div>
                    <div class="dropdown profile-action">
                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal"  data-target="#edit_<?php echo $row['matricule']; ?>"><i class="fa fa-pencil m-r-5"></i> Modifier</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_<?php echo $row['matricule']; ?>"><i class="fa fa-trash-o m-r-5"></i> Supprimer</a>
                        </div>
                    </div>
                    <!-- Affichage des informations de l'employé -->
                    <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a href=""><?php echo htmlspecialchars($row['matricule']); ?></a></h4>
                    <div class="small text-muted"><?php echo htmlspecialchars($row['nom']); ?></div>
                    <div class="small text-muted"><?php echo htmlspecialchars($row['tel']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($row['codeGr']); ?></div>

                </div>
            </div>
        <?php endwhile;
    else :
        echo "<p>Aucun employé trouvé ou erreur de requête</p>";
    endif;
    ?>

					</div>
                </div>
				<!-- /Page Content -->

				<!-- Add Employee Modal -->
				<div id="add_employee" class="modal custom-modal fade" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg">
					<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Formulaire d'ajout d'un employer</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<form  method="POST" action="">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="col-form-label">Matricule <span class="text-danger">*</span></label>
												<input required class="form-control" type="text" name="matricule">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="col-form-label">Nom et prenoms</label>
												<input required class="form-control" type="text" name="username">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label class="col-form-label">Numero de telephone<span class="text-danger">*</span></label>
												<input required class="form-control" type="text" name="phone">
											</div>
										</div>


										<div class="form-group">
                                <label>Grade <span class="text-danger">*</span></label>
                                <select class="form-control" name="grad" required>
                                    <option value="">Sélectionnez le grade</option>
                                    <?php foreach ($all_grade as $grad): ?>
                                        <option value="<?php echo htmlspecialchars($grad['codeGr']); ?>">
                                            <?php echo htmlspecialchars($grad['codeGr'] ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                                        <div class="col-sm-6" style="margin-top:30px;">
                                        <button class="btn btn-primary submit-btn" type="submit" name="add">Enregistrer</button>
										</div>



									</div>


								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /Add Employee Modal -->





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

		<!-- Datetimepicker JS -->
		<script src="assets/js/moment.min.js"></script>
		<script src="assets/js/bootstrap-datetimepicker.min.js"></script>

		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>

    </body>
</html>