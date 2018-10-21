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
	$contact = sql_fetsel('civilite, nom, prenom', 'spip_contacts', 'id_auteur='.$id_auteur);
	$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.$id_auteur);
	
	if ($contact) {
		$export_contact['civilite'] = ($contact['civilite']) ? _T('vprofils:info_civilite_'.$contact['civilite']) : ''; 
		$export_contact['nom'] = $contact['nom'];
		$export_contact['prenom'] = $contact['prenom'];
		$export_supplement['email'] = ($auteur['email']) ? $auteur['email'] : '';
	} else {
		$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.$id_auteur);
		$export_contact['civilite'] = '';
		$export_contact['nom'] = nom($auteur['nom']);
		$export_contact['prenom'] = prenom($auteur['nom']);
		$export_supplement['email'] = ($auteur['email']) ? $auteur['email'] : '';
	}
	
	// adresse
	include_spip('inc/filtres');
	$adresse_cles = array_flip(array('organisation', 'service', 'voie', 'complement', 'boite_postale', 'code_postal', 'ville', 'region', 'pays', 'code_facteur'));
	
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
		$export_contact['civilite'] = '';
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
	
	return $export;
}
