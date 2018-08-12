<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function formulaires_export_envois_abonnements_charger_dist($redirect = '') {
	$valeurs = array();
	$valeurs['numero_reference'] = _request('numero_reference');
	$valeurs['ids'] = _request('ids');
	
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
		
	} elseif (_request('exporter_selection')) {
		$etape_export = true;
		$ids = (_request('ids')) ? json_decode(_request('ids')) : array();
		
	} elseif (_request('exporter_tout')) {
		$etape_export = true;
		set_request('ids', '');
		
		$abonnements = sql_allfetsel('id_abonnement', 'spip_abonnements', 'statut='.sql_quote('actif').' AND numero_debut <='.sql_quote($numero_reference).' AND numero_fin >='.sql_quote($numero_reference), '', 'numero_debut');
		
		foreach ($abonnements as $abonnement) {
			$ids[] = $abonnement['id_abonnement'];
		}
	}
	
	if ($etape_export and !count($ids)) {
		return $res['message_erreur'] = _T('envois_commande:formulaire_export_message_erreur_aucune_selection');
	} elseif ($etape_export) {
		$export_auteur = charger_fonction('auteur', 'exporter');
		$export = array();
		
		foreach ($ids as $id) {
			$id_abonnement = intval($id);
			$id_auteur = sql_getfetsel('id_auteur', 'spip_abonnements', 'id_abonnement='.$id_abonnement);
			$export[] = $export_auteur($id_auteur);
		}
		
		if (count($export)) {
			$exporter_csv = charger_fonction('exporter_csv', 'inc');
			$nom_fichier = 'envois-commandes_'.date('Ymd-His');
			$modele_entetes = charger_fonction('auteur_entetes', 'exporter');
			$entetes = $modele_entetes();
			$url_fichier = $exporter_csv($nom_fichier, $export, ',', $entetes, false);
			
			if ($url_fichier) {
				$rep = sous_repertoire(_DIR_VAR, 'export_envois');
				$contenu = '';
				$fichier = lire_fichier($url_fichier, $contenu);
				$chemin = _DIR_VAR.'export_envois/'.$nom_fichier.'.csv';
				if ($fichier and ecrire_fichier($chemin, $contenu)) {
					$res['message_ok'] = _T('envois_commande:formulaire_export_message_ok', array('url' => $chemin));
				} else {
					$res['message_erreur'] = _T('envois_commande:formulaire_export_message_erreur');
				}
			}
		}
	}
	
	if ($redirect) {
		$res['redirect'] = parametre_url($redirect, 'numero_reference', $numero_reference);
	}
	
	$res['editable'] = true;
	return $res;
}
