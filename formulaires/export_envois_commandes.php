<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function formulaires_export_envois_commandes_saisies_dist($exporter = '', $redirect = '') {
	$saisies = array();
	
	$saisies = array(
		array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'test',
				'defaut' => 0
			)
		)
	);
	
	if ($exporter) {
		$saisies_export = array();
		foreach ($exporter as $id_envois_commande) {
			$saisies_export[] = array(
				'saisie' => 'hidden',
				'options' => array(
					'nom' => 'export',
					'defaut' => $id_envois_commande
				)
			);
		}
		$saisies = array_merge($saisies, $saisies_export);
	}
	
	// TODO: Ajouter une explication sur les données qui seront exportées 
	// et l'action en attente -> envoyer.
	
	// if ($_exporter and is_array($_exporter) and count($_exporter)) {
	// 	$saisies_export = array();
	// 	foreach ($_exporter as $id_envois_commande) {
	// 		$saisies_export[] = array(
	// 			'saisie' => 'hidden',
	// 			'options' => array(
	// 				'nom' => 'id_envois_commande'.$id_envois_commande,
	// 				'defaut' => $id_envois_commande
	// 			)
	// 		);
	// 	}
	// 	$saisies = array_merge($saisies, $saisies_export);
	// }

	return $saisies;
}


function formulaires_export_envois_commandes_charger_dist($redirect = '') {
	$valeurs = array();
	
	//$valeurs['exporter'] = request('_exporter');
	
	// if ($_exporter and is_array($_exporter) and count($_exporter)) {
	// 	foreach ($_exporter as $id_envois_commande) {
	// 		$valeurs['_exporter'] = "<input type='hidden' name='_exporter[]' value='$id_envois_commande' />";
	// 	}
	// }
	
	return $valeurs;
}

function formulaires_export_envois_commandes_verifier_dist($redirect = '') {
	$erreurs = array();
	
	
	// if (!$_exporter) {
	// 	$erreurs['message_erreur'] = _T('message erreur');
	// }
	return $erreurs;
}

function formulaires_export_envois_commandes_traiter_dist($redirect = '') {
	refuser_traiter_formulaire_ajax();
	$res = array();
	if ($redirect) {
		//$res['redirect'] = $redirect;
		//$res['redirect'] = parametre_url($redirect, '_exporter[]', null);
	}
	$res['message_ok'] = 'Export fait';
	return $res;
}
