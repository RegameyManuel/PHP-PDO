<?php
$successMessage = '';
$errorMessage = '';

// Connexion à la base de données
$hostname = 'localhost';
$dbname = 'restaurant';
$username = 'admin';
$password = 'Afpa1234'; // Remplacez par le mot de passe correct

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Charger les catégories depuis la base de données
    $categories = $dbh->query("SELECT id_categorie, nom_categorie FROM categorie")->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validation des données côté serveur
        $name = trim($_POST['dishName']);
        $price = trim($_POST['dishPrice']);
        $category = trim($_POST['dishCategory']);

        if (empty($name) || empty($price) || empty($category)) {
            throw new Exception('Tous les champs sont obligatoires.');
        }

        if (floatval($price) <= 0) {
            throw new Exception('Le prix doit être un nombre positif.');
        }

        // Préparation et exécution de la requête
        $stmt = $dbh->prepare("INSERT INTO plat (nom_plat, id_categorie, prix) VALUES (?, ?, ?)");
        $stmt->execute([$name, $category, $price]);

        $successMessage = 'Nouveau plat ajouté avec succès!';
    }
} catch (PDOException $e) {
    $errorMessage = 'Erreur de connexion à la base de données: ' . $e->getMessage();
} catch (Exception $e) {
    $errorMessage = 'Erreur: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un nouveau plat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .form-control {
            background-color: #eef1f7;
            border: 1px solid #dfe4ea;
        }
        .btn-custom {
            background-color: #4e73df;
            color: white;
        }
        .btn-custom:hover {
            background-color: #375a7f;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ajouter un nouveau plat</h2>
        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= $successMessage ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
        <?php endif; ?>
        <form id="addDishForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="dishName" class="form-label">Nom du plat</label>
                <input type="text" class="form-control" id="dishName" name="dishName" required>
            </div>
            <div class="mb-3">
                <label for="dishPrice" class="form-label">Prix du plat (€)</label>
                <input type="number" step="0.01" class="form-control" id="dishPrice" name="dishPrice" required>
            </div>
            <div class="mb-3">
                <label for="dishCategory" class="form-label">Catégorie</label>
                <select class="form-control" id="dishCategory" name="dishCategory" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id_categorie'] ?>"><?= htmlspecialchars($category['nom_categorie']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-custom">Ajouter le plat</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        function validateForm() {
            var name = document.getElementById('dishName').value;
            var price = document.getElementById('dishPrice').value;
            var category = document.getElementById('dishCategory').value;

            if (name.trim() === '' || price.trim() === '' || category === '') {
                alert('Tous les champs doivent être remplis.');
                return false;
            }

            if (parseFloat(price) <= 0) {
                alert('Le prix doit être un nombre positif.');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
