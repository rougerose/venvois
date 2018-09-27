<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function formulaires_export_envois_abonnements_charger_dist($redirect = '') {
	$valeurs = array(
		'numero_reference' => _request('numero_reference'),
		'ids' => _request('ids'),
		'filtrer' => _request('filtrer'),
		'echeance' => _request('echeance'),
		'hors_echeance' => _request('hors_echeance')
	);
	
	return $valeurs;
}

function formulaires_export_envois_abonnements_verifier_dist($redirect = '') {
	$erreurs = array();
	$ids = _request('ids');
	if (_request('afficher_abonnes')) {
		set_request('ids', '');
	} elseif (_request('exporter_selection') and !$ids) {
		$erreurs['message_erreur'] = _T('envois_commande:formulaire_export_message_erreur_aucune_selection');
	}
	
	return $erreurs;
}

function formulaires_export_envois_abonnements_traiter_dist($redirect = '') {
	if ($redirect) {
		refuser_traiter_formulaire_ajax();
	}
	$res = array();
	$ids = array();
		
	$numero_reference = _request('numero_reference');
	
	$etape_export = false;
	
	if (_request('afficher_abonnes')) {
		if ($redirect) {
			$redirection = parametre_url($redirect, 'filtrer', '');
			$redirection = parametre_url($redirection, 'echeance', '');
			$redirection = parametre_url($redirection, 'hors_echeance', '');
			$redirection = parametre_url($redirection, 'numero_reference', $numero_reference);
			$res['redirect'] = $redirection;
		}

	} elseif (_request('exporter_selection')) {
		$etape_export = true;
		$ids = (_request('ids')) ? json_decode(_request('ids')) : array();
		
	} elseif (_request('exporter_tout')) {
		$etape_export = true;
		set_request('ids', '');
		
		$where = array(
			'numero_debut <='.sql_quote($numero_reference),
			'numero_fin >='.sql_quote($numero_reference),
			sql_in('statut', array('actif', 'resilie'))
		);
		
		$filtrer = _request('filtrer');
		$echeance = _request('echeance');
		$hors_echeance = _request('hors_echeance');
		
		if ($filtrer) {
			if ($echeance) {
				$where[] = 'numero_fin ='.sql_quote($numero_reference);
			}
			
			if ($hors_echeance) {
				$where[] = 'numero_fin !='.sql_quote($numero_reference);
			}
		}
		
		$ids = sql_allfetsel('id_abonnement, id_auteur', 'spip_abonnements', $where);
	}
	
	if ($etape_export and !count($ids)) {
		return $res['message_erreur'] = _T('envois_commande:formulaire_export_message_erreur_aucune_selection');
	} elseif($etape_export and count($ids)) {
		exporter_abonnements($ids, $numero_reference);
	}
	
	if ($redirect) {
		$res['redirect'] = parametre_url($redirect, 'numero_reference', $numero_reference);
	}
	
	$res['editable'] = true;
	return $res;
}


function exporter_abonnements($abonnements, $numero_reference) {
	$export = array();
	
	$export_auteur = charger_fonction('auteur', 'exporter');
	
	foreach ($abonnements as $ids) {
		$id_auteur = intval($ids['id_auteur']);
		$export[] = $export_auteur($id_auteur);
	}
	
	if (count($export)) {
		$modele_entetes = charger_fonction('auteur_entetes', 'exporter');
		$entetes = $modele_entetes();
		
		$titre_fichier = 'envois-abonnements_'.$numero_reference.'_'.date('Ymd-His');
		
		$exporter_csv = charger_fonction('exporter_csv', 'inc');
		$exporter_csv($titre_fichier, $export, ',', $entetes, true);
		// exit();
	}
}
