<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <!-- Définit l'affichage pour différentes tailles d'écran, optimisant l'affichage mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Database</title>
    <!-- Intégration de Bootstrap pour utiliser ses composants et styles préconçus -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Définition du style de base du corps de la page */
        body {
            background-color: #1b1b1b;
            /* couleur de fond foncée pour le contraste */
            color: #eaeaea;
            /* couleur de texte claire pour le contraste */
            font-family: 'Arial', sans-serif;
            /* police de caractères globale */
        }

        /* Style du conteneur principal pour un effet visuel net et moderne */
        .container {
            background: #2b2b2b;
            /* couleur de fond du conteneur */
            padding: 30px;
            /* espacement interne pour éviter que le contenu touche les bords */
            border-radius: 10px;
            /* coins arrondis du conteneur */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            /* ombre portée pour un effet de profondeur */
        }

        /* Styles spécifiques pour les titres */
        h1 {
            font-size: 2.5rem;
            /* taille de la police pour les titres */
            margin-bottom: 1rem;
            /* marge sous le titre pour le séparer du contenu suivant */
        }

        /* Personnalisation des éléments de formulaire et boutons pour un thème cohérent */
        .form-select,
        .btn-primary {
            background-color: #3a3a3a;
            /* couleur de fond personnalisée */
            border: none;
            /* suppression de la bordure */
            color: #eaeaea;
            /* couleur de texte pour le contraste */
        }

        /* Style appliqué lorsque les éléments de formulaire sont sélectionnés ou actifs */
        .form-select:focus,
        .btn-primary:focus {
            box-shadow: none;
            /* suppression de l'ombre de mise en avant pour un style épuré */
        }

        /* Styles supplémentaires pour le bouton principal */
        .btn-primary {
            background-color: #007bff;
            /* couleur de fond bleu vif pour le bouton d'action principal */
        }

        .btn-primary:hover {
            background-color: #0056b3;
            /* couleur plus foncée au survol pour un effet interactif */
        }

        /* Style des cartes pour l'affichage des données */
        .card {
            background-color: #333;
            /* fond des cartes */
            border: none;
            /* pas de bordure pour les cartes pour un look plus moderne */
            color: #eaeaea;
            /* couleur de texte claire pour le contraste */
        }

        /* Titres dans les cartes */
        .card-title {
            font-size: 1.5rem;
            /* taille de la police pour les titres dans les cartes */
        }

        /* Texte dans les cartes */
        .card-text {
            font-size: 1rem;
            /* taille de la police pour le contenu textuel dans les cartes */
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Restaurant Database</h1>
        <!-- Formulaire pour permettre à l'utilisateur de choisir une entité à afficher -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="entitySelect" class="form-label">Select an entity to display:</label>
                <select class="form-select" id="entitySelect" name="entity" required>
                    <option value="" disabled selected>Select your option</option>
                    <option value="clients">Clients</option>
                    <option value="plats">Plats</option>
                    <option value="categories">Categories</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Display</button>
        </form>
        <div id="results" class="mt-4">
            <?php
            // Gestion des requêtes POST pour récupérer les données choisies par l'utilisateur
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $entity = $_POST['entity'];  // Capture de l'entité sélectionnée par l'utilisateur

                // Paramètres de connexion à la base de données
                $hostname = 'localhost';
                $dbname = 'restaurant';
                $username = 'admin';  // Remplacez par votre nom d'utilisateur de la base de données
                $password = 'Afpa1234';  // Remplacez par votre mot de passe de la base de données

                try {
                    // Établissement de la connexion avec la base de données via PDO
                    $dsn = "mysql:host=$hostname;dbname=$dbname;charset=utf8mb4";
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Activation des exceptions pour les erreurs
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"  // Assure l'encodage UTF-8
                    ];

                    $dbh = new PDO($dsn, $username, $password, $options);

                    // Sélection et affichage des données basées sur l'entité choisie
                    if ($entity === 'clients') {
                        $stmt = $dbh->prepare('SELECT * FROM client');
                        $stmt->execute();
                        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($clients as $client) {
                            /* Ici, chaque élément HTML est généré par des appels à echo, et les valeurs PHP sont intégrées en concaténant des chaînes. 
                            Cette méthode peut rendre le code un peu lourd visuellement et difficile à suivre en raison de l'utilisation fréquente de la concaténation.*/
                            echo '<div class="card mb-3">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . htmlspecialchars($client['prenom'] . ' ' . $client['nom']) . '</h5>';
                            echo '<p class="card-text">Email: ' . htmlspecialchars($client['email']) . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } elseif ($entity === 'plats') {
                        $stmt = $dbh->prepare('SELECT plat.nom_plat, plat.prix, categorie.nom_categorie FROM plat JOIN categorie ON plat.id_categorie = categorie.id_categorie');
                        $stmt->execute();
                        $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($plats as $plat) { 
                        /* Cette syntaxe ouvre et ferme les balises PHP autour d'un bloc de HTML pur. 
                         Les données PHP sont intégrées directement dans le HTML à l'aide de la balise courte <?= ?>, qui est équivalente à <?php echo ?>. 
                         Cela rend le code plus lisible et facile à éditer, en particulier pour ceux qui travaillent fréquemment avec HTML. */
                         ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($plat['nom_plat']) ?></h5>
                                    <p class="card-text">Category: <?= htmlspecialchars($plat['nom_categorie']) ?></p>
                                    <p class="card-text">Price: <?= htmlspecialchars($plat['prix']) ?> €</p>
                                </div>
                            </div>
                        <?php
                        }
                    } elseif ($entity === 'categories') {
                        $stmt = $dbh->prepare('SELECT * FROM categorie');
                        $stmt->execute();
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($categories as $categorie) { ?>
                        <!-- Une approche intermédiaire, ce bloc mélange des balises PHP ouvertes/fermées et des commandes echo pour injecter des valeurs PHP.
                         Cette méthode combine les avantages de la clarté HTML et de la flexibilité du PHP pour le contenu dynamique. -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo (htmlspecialchars($categorie['nom_categorie'])) ?></h5>
                                </div>
                            </div>
                        <?php
                        }
                    }
                } catch (PDOException $e) {
                    // Affichage d'un message en cas d'erreur de connexion
                    echo 'Erreur de connexion à la base de données: ' . $e->getMessage();
                }
            }
            ?>
        </div>
    </div>
    <!-- Script Bootstrap pour le support des composants interactifs -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>