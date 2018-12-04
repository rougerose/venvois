<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function exporter_auteur_dist($id_auteur = 0) {
	$id_auteur = intval($id_auteur);
	$export = array();
	$export_organisation = array();
	$export_contact = array();
	$export_adresse = array();
	$export_supplement = array();
	
	if (!$id_auteur) {
		return '';
	}
	
	// contact
	$contact = sql_fetsel('nom, prenom', 'spip_contacts', 'id_auteur='.$id_auteur);
	$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.$id_auteur);
	
	if ($contact) {
		$export_contact['prenom'] = $contact['prenom'];
		$export_contact['nom'] = $contact['nom'];
		$export_supplement['email'] = ($auteur['email']) ? $auteur['email'] : '';
	} else {
		$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.$id_auteur);
		$export_contact['prenom'] = prenom($auteur['nom']);
		$export_contact['nom'] = nom($auteur['nom']);
		$export_supplement['email'] = ($auteur['email']) ? $auteur['email'] : '';
	}
	
	// adresse
	include_spip('inc/filtres');
	$adresse_cles = array_flip(array('organisation', 'service', 'complement', 'voie', 'boite_postale', 'code_postal', 'ville', 'region', 'code_facteur', 'pays'));
	
	$adresse = sql_fetsel('*', 'spip_adresses AS adresses INNER JOIN spip_adresses_liens AS L1 ON (L1.id_adresse = adresses.id_adresse)', 'L1.id_objet='.$id_auteur.' AND L1.objet='.sql_quote('auteur'));
	
	$export_organisation['organisation'] = $adresse['organisation'];
	$export_organisation['service'] = $adresse['service'];
	unset($adresse_cles['organisation'], $adresse_cles['service']);
	
	if (
		$export_contact['nom']
		and $export_contact['prenom'] == ''
		and $export_organisation['organisation'] == ''
	) {
		$export_organisation['organisation'] = $export_contact['nom'];
		$export_contact['nom'] = '';
	}
	
	foreach ($adresse_cles as $cle => $v) {
		if ($cle == 'pays' and $code_pays = $adresse[$cle]) {
			$nom_pays = sql_getfetsel('nom', 'spip_pays', 'code='.sql_quote($code_pays));
			$pays = extraire_multi($nom_pays, 'fr', array('echappe_span' => true));
			$export_adresse[$cle] = ($pays) ? $pays : '';
			continue;
		}
		$export_adresse[$cle] = ($adresse[$cle]) ? $adresse[$cle] : '';
	}
	
	$export = array_merge($export_organisation, $export_contact, $export_adresse, $export_supplement);
	
	
	// Formater les données
	$export['nom'] = (strlen($export['nom'])) ? mb_strtoupper($export['nom']) : '';
	
	$export['prenom'] = (strlen($export['prenom'])) ? ucwords($export['prenom']) : '';
	
	$export['organisation'] = (strlen($export['organisation'])) ? mb_strtoupper($export['organisation']) : '';
	
	$export['service'] = (strlen($export['service'])) ?  mb_strtoupper($export['service']) : '';

	$export['complement'] = (strlen($export['complement'])) ? mb_strtoupper($export['complement']) : '';
	
	$export['voie'] = (strlen($export['voie'])) ? mb_strtoupper($export['voie']) : '';
	
	$export['boite_postale'] = (strlen($export['boite_postale'])) ? mb_strtoupper($export['boite_postale']) : '';
	
	$export['ville'] = (strlen($export['ville'])) ? mb_strtoupper($export['ville']) : '';
	
	$export['region'] = (strlen($export['region'])) ? mb_strtoupper($export['region']) : '';
	
	if (strlen($export['pays'])) {
		$export['pays'] = ($export['pays'] == 'France') ? '' : mb_strtoupper($export['pays']);
	}
	
	return $export;
}
