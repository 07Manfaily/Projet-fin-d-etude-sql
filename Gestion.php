<?php
class GestionEmployes {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=localhost;dbname=rh;charset=utf8",
                "root",
                "",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Calcule le nombre d'indemnités d'un employé
    public function nbIndeminite() {
         $sql = "SELECT
                e.matricule,
                e.nom,
                g.salaireBase,
                COUNT(a.codeInd) as nb_indemnites,
                COALESCE(SUM(a.montant), 0) as total_indemnites
            FROM Employe e
            JOIN Grade g ON e.codeGr = g.codeGr
            LEFT JOIN ADroit a ON g.codeGr = a.codeGr
            GROUP BY e.matricule, e.nom, g.salaireBase
            ORDER BY nb_indemnites DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   public function totalIndeminites() {
    $sql = "SELECT codeGr, SUM(montant) as total
            FROM adroit
            GROUP BY codeGr";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


  public function salairesNets() {
    $sql = "SELECT e.matricule, e.nom, g.salaireBase, SUM(a.montant) as totalIndemnites
            FROM employe e
            JOIN grade g ON e.codeGr = g.codeGr
            LEFT JOIN adroit a ON g.codeGr = a.codeGr
            GROUP BY e.matricule, g.salaireBase";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Retourne le nom de l'employé le plus payé
    public function salaireMax() {
        $sql = "SELECT e.nom,
                (g.salaireBase + COALESCE(SUM(a.montant), 0) - (0.05 * g.salaireBase)) as salaire_net
                FROM employe e
                JOIN grade g ON e.codeGr = g.codeGr
                LEFT JOIN adroit a ON g.codeGr = a.codeGr
                GROUP BY e.matricule, e.nom, g.salaireBase
                ORDER BY salaire_net DESC
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nom'] : null;
    }

    // Calcule la somme totale des salaires nets
    public function totalSalaire() {
        $sql = "SELECT SUM(g.salaireBase + COALESCE(subquery.total_indemnites, 0) - (0.05 * g.salaireBase)) as total
                FROM employe e
                JOIN grade g ON e.codeGr = g.codeGr
                LEFT JOIN (
                    SELECT codeGr, SUM(montant) as total_indemnites
                    FROM adroit
                    GROUP BY codeGr
                ) subquery ON g.codeGr = subquery.codeGr";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function totalEmployer() {
    $sql = "SELECT COUNT(matricule) as nb FROM employe";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['nb'];
}
}

