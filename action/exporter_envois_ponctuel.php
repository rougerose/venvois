<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function action_exporter_envois_ponctuel($id_objet = null, $objet = null) {
	// appel direct depuis une url avec arg = "id-objet-objet"
	if (is_null($id_objet) or is_null($objet)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		list($id_objet, $objet) = explode("-", $arg);
	}
	
	if ($id_objet and $objet and $objet = 'envois_ponctuel') {
		// Les auteurs à exporter
		$auteurs = sql_allfetsel('objet, id_objet, quantite', 'spip_envois_ponctuels_liens', 'id_envois_ponctuel='.intval($id_objet));
		
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
			
			foreach ($ids_auteurs as $id_auteur) {
				$export[$id_auteur] = $exporter_auteur($id_auteur);
				$export[$id_auteur] = array_merge(array('descriptif' => ''), $export[$id_auteur]);
			}
		}
		if (count($export)) {
			$exporter_csv = charger_fonction('exporter_csv', 'inc');
			$modele_entetes = charger_fonction('auteur_entetes', 'exporter');
			$entetes = $modele_entetes();
			$nom_fichier = 'envois-ponctuels_'.date('Ymd-His');
			
			$exporter_csv($nom_fichier, $export, ',', $entetes, $envoyer = true);
			
		}
	}
}
