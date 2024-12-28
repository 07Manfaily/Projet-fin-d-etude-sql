<?php
session_start(); // Démarre la session
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Redirige vers la page de login si non connecté
    header("Location: login.php");
    exit;
}
require_once 'Gestion.php';
$gestion = new GestionEmployes();
$totalSalaire = $gestion->totalSalaire();
$employeMaxSalaire = $gestion->salaireMax();
$employeTotal = $gestion->totalEmployer();
$employes = $gestion->nbIndeminite();
$indeminites = $gestion->totalIndeminites();
$salairesNets = $gestion->salairesNets();

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
        <title>User Dashboard - HRMS admin template</title>

		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">

		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">

		<!-- Lineawesome CSS -->
        <link rel="stylesheet" href="assets/css/line-awesome.min.css">

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

			<!-- Header -->
            <?php include('header.php') ?>
			<!-- /Header -->

			<!-- Sidebar -->
			<?php include('sidebar.php') ?>
			<!-- /Sidebar -->

			<!-- Page Wrapper -->
            <div class="page-wrapper">

				<!-- Page Content -->
                <div class="content container-fluid">

					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col-sm-12">
								<h3 class="page-title">Tableau de bord</h3>

							</div>
						</div>
					</div>
					<!-- /Page Header -->

					<!-- Content Starts -->

					<div class="row">
    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-money"></i></span>
                <div class="dash-widget-info">
                    <h3><?php echo number_format($totalSalaire, 2, ',', ' '); ?> fcfa</h3>
                    <span>Total des Salaires</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-trophy"></i></span>
                <div class="dash-widget-info">
                    <h3><?php echo htmlspecialchars($employeMaxSalaire); ?></h3>
                    <span>Employé le mieux payé</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-4">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-users"></i></span>
                <div class="dash-widget-info">
                    <h3><?php echo $employeTotal; ?></h3>
                    <span>Total Employés</span>
                </div>
            </div>
        </div>
    </div>
</div>




					<div class="row">
					<div class="col-md-7 d-flex">
   <div class="card card-table flex-fill">
      <div class="card-header">
         <h3 class="card-title mb-0">Indemnités des Employés</h3>
      </div>
      <div class="card-body">
         <div class="table-responsive">
            <table class="table table-nowrap custom-table mb-0">
               <thead>
                  <tr>
                     <th>Matricule</th>
                     <th>Employé</th>
                     <th>Salaire Base</th>
                     <th>Nb. Indemnités</th>
                     <th>Total Indemnités</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($employes as $employe): ?>
                  <tr>
                     <td><a href="#"><?php echo htmlspecialchars($employe['matricule']); ?></a></td>
                     <td>
                        <h2><a href="#"><?php echo htmlspecialchars($employe['nom']); ?></a></h2>
                     </td>
                     <td><?php echo number_format($employe['salaireBase'], 2, ',', ' '); ?> fcfa</td>
                     <td><?php echo $employe['nb_indemnites']; ?></td>
                     <td>
                        <?php
                        $class = '';
                        if ($employe['nb_indemnites'] > 2) {
                            $class = 'bg-inverse-success';
                        } elseif ($employe['nb_indemnites'] > 0) {
                            $class = 'bg-inverse-warning';
                        } else {
                            $class = 'bg-inverse-danger';
                        }
                        ?>
                        <span class="badge <?php echo $class; ?>">
                            <?php echo number_format($employe['total_indemnites'], 2, ',', ' '); ?> fcfa
                        </span>
                     </td>
                  </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>

   </div>
</div>
						<div class="col-md-5 d-flex">
   <div class="card card-table flex-fill">
      <div class="card-header">
         <h3 class="card-title mb-0">Indemnités par Grade</h3>
      </div>
      <div class="card-body">
         <div class="table-responsive">
            <table class="table table-nowrap custom-table mb-0">
               <thead>
                  <tr>
                     <th>Grade</th>
                     <th>Total des Indemnités</th>
                     <th>Status</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($indeminites as $indeminite): ?>
                     <tr>
                        <td><?= htmlspecialchars($indeminite['codeGr']) ?></td>
                        <td><?= number_format($indeminite['total'], 2, ',', ' ') ?> fcfa</td>
                        <td>
                           <?php if ($indeminite['total'] > 0): ?>
                              <span class="badge bg-inverse-success">Indemnités calculées</span>
                           <?php else: ?>
                              <span class="badge bg-inverse-danger">Aucune indemnité</span>
                           <?php endif; ?>
                        </td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>

   </div>
</div>
					<div class="row">
						<div class="col-md-12 d-flex">
   <div class="card card-table flex-fill">
      <div class="card-header">
         <h3 class="card-title mb-0">Salaires Nets des Employés</h3>
      </div>
      <div class="card-body">
         <div class="table-responsive">
            <table class="table table-nowrap custom-table mb-0">
               <thead>
                  <tr>
                     <th>Matricule</th>
                       <th>Nom et prenoms</th>
                     <th>Salaire Net</th>
                     <th>Status</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($salairesNets as $salaire): ?>
                     <?php
                        // Calcul du salaire net
                        $salaireBase = $salaire['salaireBase'];
                        $totalIndemnites = $salaire['totalIndemnites'] ?? 0;
                        $salaireNet = $salaireBase + $totalIndemnites - (0.05 * $salaireBase);
                     ?>
                     <tr>
                        <td><?= htmlspecialchars($salaire['matricule']) ?></td>
                         <td><?= htmlspecialchars($salaire['nom']) ?></td>
                        <td><?= number_format($salaireNet, 2, ',', ' ') ?> fcfa</td>
                        <td>
                           <?php if ($salaireNet > 0): ?>
                              <span class="badge bg-inverse-success">Salaire calculé</span>
                           <?php else: ?>
                              <span class="badge bg-inverse-danger">Aucun salaire</span>
                           <?php endif; ?>
                        </td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>

   </div>
</div>

					</div>

					<!-- /Content End -->

                </div>
				<!-- /Page Content -->

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

		<!-- Chart JS -->
		<script src="assets/js/Chart.min.js"></script>
		<script src="assets/js/line-chart.js"></script>

		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>

    </body>
</html>