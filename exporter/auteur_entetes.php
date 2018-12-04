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
		_T('export:entete_envoi'), // ENVOI
		_T('export:entete_organisation'), // ORGA
		_T('export:entete_service'), // SERVICE
		_T('export:entete_prenom'), // PRENOM
		_T('export:entete_nom'), // NOM
		_T('export:entete_batiment'), // BATAPP
		_T('export:entete_adresse'), // ADRESSE
		_T('export:entete_boite_postale'), // BP
		_T('export:entete_code_postal'), // CP
		_T('export:entete_commune'), // COMMUNE
		_T('export:entete_etat'), // REGION ETAT
		_T('export:entete_code_facteur'), // QL
		_T('export:entete_pays'), // PAYS
		_T('export:entete_email'), // EMAIL
	);
}
