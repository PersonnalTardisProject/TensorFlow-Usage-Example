<!DOCTYPE html>
<html>
<head>
	<title>Côte des armes</title>
	<link rel="stylesheet" href="style.css">
	<meta charset="utf-8">
</head>
<body>
	<h1 class='title'>Prediction du prix moyen des armes</h1>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<!-- Label et menu déroulant pour la sélection du marque -->
		<div class="form-opt">
			<label class='weapon-lbl' for="marque">Marque:</label>
			<select class='weapon-opt' id="marque" name="marque">
				<?php
					// Récupération des données du fichier CSV
					$data = array_map('str_getcsv', file('../data/example_predictions.csv'));

					// Récupération de l'entête et détermination de la colonne correspondant à la marque
					$header = array_shift($data);
					$marque_col = array_search('Marque', $header);

					// Extraction des différentes marques disponibles
					$marques = array();
					foreach ($data as $row) {
						$marque = $row[$marque_col];
						if (!in_array($marque, $marques)) {
							$marques[] = $marque;
						}
					}

					// Tri des marques par ordre alphabétique et création des options du menu déroulant
					sort($marques);
					foreach ($marques as $marque) {
						echo "<option value=\"$marque\">$marque</option>";
					}
				?>
			</select>
		</div>
		<!-- Label et menu déroulant pour la sélection du modele -->
		<div class="form-opt">
			<label class='weapon-lbl' for="modele">Modèle:</label>
			<select class='weapon-opt' id="modele" name="modele">
				<?php
					// Récupération des données du fichier CSV
					$data = array_map('str_getcsv', file('../data/example_predictions.csv'));

					// Récupération de l'entête et détermination de la colonne correspondant au modèle
					$header = array_shift($data);
					$model_col = array_search('Modele', $header);

					// Extraction des différents modèles disponibles
					$modeles = array();
					foreach ($data as $row) {
						$modele = $row[$model_col];
						if (!in_array($modele, $modeles)) {
							$modeles[] = $modele;
						}
					}

					// Tri des modèles par ordre alphabétique et création des options du menu déroulant
					sort($modeles);
					foreach ($modeles as $modele) {
						echo "<option value=\"$modele\">$modele</option>";
					}
				?>
			</select>
		</div>
		<!-- Label et menu déroulant pour la sélection du canon -->
		<div class="form-opt">
			<label class='weapon-lbl' for="canon">Canon:</label>
			<select class='weapon-opt' id="canon" name="canon">
				<?php
					// Récupération des données du fichier CSV
					$data = array_map('str_getcsv', file('../data/example_predictions.csv'));

					// Récupération de l'entête et détermination de la colonne correspondant au canon
					$header = array_shift($data);
					$canon_col = array_search('Canon', $header);

					// Extraction des différents canons disponibles
					$canons = array();
					foreach ($data as $row) {
						$canon = $row[$canon_col];
						if (!in_array($canon, $canons)) {
							$canons[] = $canon;
						}
					}

					// Tri des canons par ordre alphabétique et création des options du menu déroulant
					sort($canons);
					foreach ($canons as $canon) {
						echo "<option value=\"$canon\">$canon</option>";
					}
				?>
			</select>
		</div>
		<!-- Bouton de validation du formulaire -->
		<input type="submit" name="Weapon" value="Valider">
	</form>
	<?php
		// Chargement des données du fichier CSV dans un tableau
		$donnees = array_map('str_getcsv', file('../data/example_predictions.csv'));

		// Vérification de la présence des données du formulaire
		if(isset($_POST['Weapon'])){
			
			// Récupération des données du formulaire
			$modele = $_POST['modele'];
			$marque = $_POST['marque'];
			$canon = $_POST['canon'];
			
			// Création d'un tableau vide pour stocker les résultats filtrés
			$donnees_filtrees = array();
			
			// Boucle pour filtrer les résultats en fonction des caractéristiques choisies
			foreach ($donnees as $row) {
				if ($row[0] == $marque && $row[1] == $canon && $row[2] == $modele) {
					array_push($donnees_filtrees, $row);
				}
			}
			
			// Fonction pour formater les nombres avec des virgules et des points
			function fnumber_format($number, $decimals='', $sep1='', $sep2='') {
				// Correction de l'arrondi pour les nombres ayant 5 comme dernière décimale
				if (($number * pow(10 , $decimals + 1) % 10 ) == 5)
					$number -= pow(10 , -($decimals+1));
		
				return number_format($number, $decimals, $sep1, $sep2);
			}

			if (count($donnees_filtrees) == 0) { // Si aucun résultat n'est trouvé pour les caractéristiques choisies, on affiche un message d'erreur.
				echo "<h4 style=\"color:red;\">Aucun résultat trouvé pour les caractéristiques choisies.</h4>";
				
				// On utilise un script Python pour obtenir une estimation du prix.
				$output = shell_exec("./search.py \"$marque\" \"$modele\" \"$canon\"");
			
				// On affiche un résumé des caractéristiques choisies.
				echo "<h3>Résumé des caractéristiques choisies:</h3>";
				echo "<h4>Modèle:</h4><p class='res-txt'> $modele</p>";
				echo "<h4>Marque:</h4><p class='res-txt'> $marque</p>";
				echo "<h4>Canon:</h4><p class='res-txt'> $canon</p>";
			
				// On affiche le résultat de l'estimation du prix.
				echo "<h3>Résultats déterminé avec IA:</h3>";
				echo "<h4>Prix moyen: </h4><p>" . fnumber_format($output, 2, '.', ',') . "€</p>";

			} else { // Si des résultats sont trouvés, on affiche un résumé des caractéristiques choisies.
				echo "<h3>Résumé des caractéristiques choisies:</h3>";
				echo "<h4>Modèle:</h4><p class='res-txt'> $modele</p>";
				echo "<h4>Marque:</h4><p class='res-txt'> $marque</p>";
				echo "<h4>Canon:</h4><p class='res-txt'> $canon</p>";
			
				// On récupère les prix actuels et moyens des produits correspondant aux caractéristiques choisies.
				$prix_actuel = array_column($donnees_filtrees, 3);
				$prix_moyen = array_column($donnees_filtrees, 4);
			
				// On affiche les résultats.
				echo "<h3>Résultats:</h3>";
				echo "<h4>Prix moyen: </h4><p>" . fnumber_format($prix_moyen[0], 2, '.', ',') . "€</p>";
				echo "<h4>Prix actuel: </h4>";
				echo "<p>- min: " . fnumber_format(min($prix_actuel), 2, '.', ',') . "€</p>";
				echo "<p>- max: " . fnumber_format(max($prix_actuel), 2, '.', ',') . "€</p>";
			}
			
			// On vide les données postées pour éviter qu'elles ne soient traitées à nouveau lors d'un rechargement de la page.
			unset($_POST['Weapon']);			
		}
	?>
</body>
</html>