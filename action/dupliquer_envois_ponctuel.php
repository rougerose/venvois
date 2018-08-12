<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function action_dupliquer_envois_ponctuel($id_objet = null, $objet = null) {
	// appel direct depuis une url avec arg = "id-objet"
	if (is_null($id_objet) or is_null($objet)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		list($id_objet, $objet) = explode("-", $arg);
	}
	
	if ($id_objet and $objet) {
		// dupliquer
		if ($id_objet_copie = intval(objet_dupliquer($objet, $id_objet))) {
			// On redirige sur la page de l'objet dupliqué
			redirige_par_entete(
				str_replace('&amp;', '&', generer_url_entite($id_objet_copie, $objet))
			);
		}
	}
}


function objet_dupliquer($objet, $id_objet) {
	include_spip('base/objets');
	include_spip('action/editer_objet');
	
	$id_objet_copie = false;
	$cle_objet = id_table_objet($objet);
	$id_objet = intval($id_objet);
	
	// dupliquer uniquement les champs suivants
	$champs = 'titre, descriptif';
	
	$infos_a_dupliquer = sql_fetsel($champs, table_objet_sql($objet), "$cle_objet = $id_objet");
	unset($infos_a_dupliquer[$cle_objet]);
	
	// modifier le titre original
	$infos_a_dupliquer['titre'] .= ' copie';
	
	$id_objet_duplicata = objet_inserer($objet, 0, $infos_a_dupliquer);
	
	if (intval($id_objet_duplicata)) {
		
		// dupliquer les auteurs liés
		$auteurs = sql_allfetsel('id_objet, objet, quantite', 'spip_envois_ponctuels_liens', 'id_envois_ponctuel='.$id_objet);
		
		if ($auteurs) {
			foreach ($auteurs as $auteur) {
				objet_associer(array($objet => $id_objet_duplicata), array($auteur['objet'] => $auteur['id_objet']), array('quantite' => $auteur['quantite']));
			}
		}
	}

	return $id_objet_duplicata;
}
