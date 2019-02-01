<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function action_exporter_envois_ponctuel($arg = null) {
	// appel direct depuis une url avec arg = "id-objet-objet"
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($objet, $id_objet) = explode("-", $arg);

	$id_objet = intval($id_objet);

	if ($id_objet and $objet and $objet = 'envois_ponctuel') {
		// Les auteurs à exporter
		$auteurs = sql_allfetsel('objet, id_objet, quantite', 'spip_envois_ponctuels_liens', 'id_envois_ponctuel='.$id_objet);

		// Chaque auteur peut recevoir plusieurs exemplaires :
		// dupliquer les auteurs concernés dans le tableau
		$ids_auteurs = array();

		foreach ($auteurs as $auteur) {
			while(intval($auteur['quantite']--)) {
				$ids_auteurs[] = $auteur['id_objet'];
			}
		}

		if (count($ids_auteurs)) {
			$exporter_auteur = charger_fonction('auteur', 'exporter');
			$export = array();

			// $export = sql_select('id_auteur, nom, email, statut', 'spip_auteurs', sql_in('id_auteur', $ids_auteurs));

			// $export = sql_allfetsel('id_auteur, nom, email, statut', 'spip_auteurs', sql_in('id_auteur', $ids_auteurs));

			foreach ($ids_auteurs as $id_auteur) {
				//$export[] = $exporter_auteur($id_auteur);
				$export[] = array('descriptif' => '') + $exporter_auteur($id_auteur);
			}
		}
		if (count($export)) {
			$exporter_csv = charger_fonction('exporter_csv', 'inc');
			$modele_entetes = charger_fonction('auteur_entetes', 'exporter');
			$entetes = $modele_entetes();
			$nom_fichier = 'envois_ponctuels_'.date('Ymd-His');
			$exporter_csv($nom_fichier, $export, ',', $entetes);
		}
	}
}
