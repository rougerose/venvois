<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Entete de colonnes du fichier CSV 
 * @return array
 */
function exporter_auteur_entetes_dist() {
	return array(
		_T('vprofils:formulaire_organisation_label_court'), //organisation
		_T('vprofils:formulaire_service_label'), // service
		_T('vprofils:formulaire_civilite_label'), // civilite
		_T('vprofils:formulaire_nom_label'), // nom
		_T('vprofils:formulaire_prenom_label'), // prenom
		_T('vprofils:formulaire_voie_label'), // adresse
		_T('vprofils:formulaire_complement_label'), // complément d'adresse
		_T('vprofils:formulaire_boite_postale_label'), // boite_postale
		_T('vprofils:formulaire_code_postal_label'), // code postal
		_T('vprofils:formulaire_ville_label'), // ville
		_T('vprofils:formulaire_region_label'), // région ou état
		_T('vprofils:formulaire_pays_label'), // pays
		_T('vprofils:formulaire_code_facteur_label') // code facteur
	);
}
