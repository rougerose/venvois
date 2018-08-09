<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function exporter_auteur_dist($id_auteur = 0) {
	$id_auteur = intval($id_auteur);
	$export = array();
	
	if (!$id_auteur) {
		return '';
	}
	
	// contact
	$contact = sql_fetsel('civilite, nom, prenom, organisation, service', 'spip_contacts', 'id_auteur='.$id_auteur);
	
	if ($contact) {
		$export['organisation'] = $contact['organisation'];
		$export['service'] = $contact['service'];
		$export['civilite'] = $contact['civilite'];
		$export['nom'] = $contact['nom'];
		$export['prenom'] = $contact['prenom'];
	} else {
		$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.$id_auteur);
		$export['organisation'] = '';
		$export['service'] = '';
		$export['civilite'] = '';
		$export['nom'] = nom($auteur['nom']);
		$export['prenom'] = prenom($auteur['nom']);
	}
	
	// adresse
	$adresse_cles = array_flip(array('voie', 'complement', 'boite_postale', 'code_postal', 'ville', 'pays'));
	
	$adresse = sql_fetsel('*', 'spip_adresses AS adresses INNER JOIN spip_adresses_liens AS L1 ON (L1.id_adresse = adresses.id_adresse)', 'L1.id_objet='.$id_auteur.' AND L1.objet='.sql_quote('auteur'));
	
	foreach ($adresse_cles as $cle => $v) {
		$export[$cle] = ($adresse[$cle]) ? $adresse[$cle] : '';
	}
	
	return $export;
}
