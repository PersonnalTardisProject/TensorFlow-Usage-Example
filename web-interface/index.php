<!DOCTYPE html>
<html>
<head>
	<title>Côte des armes</title>
	<link rel="stylesheet" href="style.css">
	<meta charset="utf-8">
	<!-- <script src="script.js"></script> -->
</head>
<body>
	<h1>Prediction du prix moyen des armes</h1>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<label for="marque">Marque:</label>
		<select id="marque" name="marque">
			<?php
				$data = array_map('str_getcsv', file('../data/example_predictions.csv'));
				$header = array_shift($data);
				$marque_col = array_search('Marque', $header);

				$marques = array();
				foreach ($data as $row) {
					$marque = $row[$marque_col];
					if (!in_array($marque, $marques)) {
						$marques[] = $marque;
					}
				}

				sort($marques);
				// echo "<option value=0>marque</option>";
				foreach ($marques as $marque) {
					echo "<option value=\"$marque\">$marque</option>";
				}
			?>
		</select>
		<label for="modele">Modèle:</label>
		<select id="modele" name="modele">
			<?php
				$data = array_map('str_getcsv', file('../data/example_predictions.csv'));
				$header = array_shift($data);
				$model_col = array_search('Modele', $header);

				$modeles = array();
				foreach ($data as $row) {
					$modele = $row[$model_col];
					if (!in_array($modele, $modeles)) {
						$modeles[] = $modele;
					}
				}

				sort($modeles);
				// echo "<option value=0>modele</option>";
				foreach ($modeles as $modele) {
					echo "<option value=\"$modele\">$modele</option>";
				}
			?>
		</select>
		<label for="canon">Canon:</label>
		<select id="canon" name="canon">
			<?php
				$data = array_map('str_getcsv', file('../data/example_predictions.csv'));
				$header = array_shift($data);
				$canon_col = array_search('Canon', $header);

				$canons = array();
				foreach ($data as $row) {
					$canon = $row[$canon_col];
					if (!in_array($canon, $canons)) {
						$canons[] = $canon;
					}
				}

				sort($canons);
				// echo "<option value=0>canon</option>";
				foreach ($canons as $canon) {
					echo "<option value=\"$canon\">$canon</option>";
				}
			?>
		</select>
		<input type="submit" name="Weapon" value="Valider">
	</form>
	<?php
		// Charger les données d'exemple depuis le fichier CSV
		$donnees = array_map('str_getcsv', file('../data/example_predictions.csv'));

		// Si l'utilisateur a soumis le formulaire
		if(isset($_POST['Weapon'])){
			
			// Récupérer les valeurs choisies par l'utilisateur
			$modele = $_POST['modele'];
			$marque = $_POST['marque'];
			$canon = $_POST['canon'];
			
			// Filtrer les données pour ne garder que celles qui correspondent aux valeurs choisies
			$donnees_filtrees = array();
			
			foreach ($donnees as $row) {
				if ($row[0] == $marque && $row[1] == $canon && $row[2] == $modele) {
					array_push($donnees_filtrees, $row);
				}
			}
			
			// Afficher les résultats
			function fnumber_format($number, $decimals='', $sep1='', $sep2='') {

				if (($number * pow(10 , $decimals + 1) % 10 ) == 5)
					$number -= pow(10 , -($decimals+1));
		
				return number_format($number, $decimals, $sep1, $sep2);
			}

			if (count($donnees_filtrees) == 0) {
				echo "<h2 style=\"color:red;\">Aucun résultat trouvé pour les caractéristiques choisies.</h2>";
				$output = shell_exec("./search.py \"$marque\" \"$modele\" \"$canon\"");

				echo "<h2>Résumé des caractéristiques choisies:</h2>";
				echo "<p>Modèle: $modele</p>";
				echo "<p>Marque: $marque</p>";
				echo "<p>Canon: $canon</p>";

				echo "<h2>Résultats déterminé avec IA:</h2>";
				echo "<h3 style=\"padding:0 0 0 20px;\">Prix moyen: </h3><p style=\"padding:0 0 0 40px;\">" . fnumber_format($output, 2, '.', ',') . "€</p>";
			} else {
				// Extraire les prix de chaque ligne
				$prix_actuel = array_column($donnees_filtrees, 3);
				$prix_moyen = array_column($donnees_filtrees, 4);

				echo "<h2>Résumé des caractéristiques choisies:</h2>";
				echo "<p>Modèle: $modele</p>";
				echo "<p>Marque: $marque</p>";
				echo "<p>Canon: $canon</p>";
				echo "<h2>Résultats:</h2>";
				echo "<h3 style=\"padding:0 0 0 20px;\">Prix moyen: </h3><p style=\"padding:0 0 0 40px;\">" . fnumber_format($prix_moyen[0], 2, '.', ',') . "€</p>";
				echo "<h3 style=\"padding:0 0 0 20px;\">Prix actuel: </h3>";
				echo "<p style=\"padding:0 0 0 40px;\">- min: " . fnumber_format(min($prix_actuel), 2, '.', ',') . "€</p>";
				echo "<p style=\"padding:0 0 0 40px;\">- max: " . fnumber_format(max($prix_actuel), 2, '.', ',') . "€</p>";
			}

			unset($_POST['Weapon']);
		}
	?>
</body>
</html>