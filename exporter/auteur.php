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
	
	if (!$id_auteur) {
		return '';
	}
	
	// contact
	$contact = sql_fetsel('civilite, nom, prenom', 'spip_contacts', 'id_auteur='.$id_auteur);
	
	if ($contact) {
		$export_contact['civilite'] = ($contact['civilite']) ? _T('vprofils:info_civilite_'.$contact['civilite']) : ''; 
		$export_contact['nom'] = $contact['nom'];
		$export_contact['prenom'] = $contact['prenom'];
	} else {
		$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.$id_auteur);
		$export_contact['civilite'] = '';
		$export_contact['nom'] = nom($auteur['nom']);
		$export_contact['prenom'] = prenom($auteur['nom']);
	}
	
	// adresse
	$adresse_cles = array_flip(array('organisation', 'service', 'voie', 'complement', 'boite_postale', 'code_postal', 'ville', 'region', 'pays', 'code_facteur'));
	
	$adresse = sql_fetsel('*', 'spip_adresses AS adresses INNER JOIN spip_adresses_liens AS L1 ON (L1.id_adresse = adresses.id_adresse)', 'L1.id_objet='.$id_auteur.' AND L1.objet='.sql_quote('auteur'));
	
	$export_organisation['organisation'] = $adresse['organisation'];
	$export_organisation['service'] = $adresse['service'];
	unset($adresse_cles['organisation'], $adresse_cles['service']);
	
	foreach ($adresse_cles as $cle => $v) {
		$export_adresse[$cle] = ($adresse[$cle]) ? $adresse[$cle] : '';
	}
	
	$export = array_merge($export_organisation, $export_contact, $export_adresse);
	
	return $export;
}
