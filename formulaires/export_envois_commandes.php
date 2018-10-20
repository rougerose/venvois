<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}



function formulaires_export_envois_commandes_charger_dist($redirect = '', $statut = '', $tri = '') {
	$valeurs = array();	
	$valeurs['statut'] = $statut;
	$valeurs['tri_liste_envois_commandes'] = $tri;
	$valeurs['exporter_envois_commandes'] = _request('exporter_envois_commandes');
	return $valeurs;
}



function formulaires_export_envois_commandes_verifier_dist($redirect = '', $statut = '', $tri = '') {
	$erreurs = array();
	
	$ids_envois = _request('exporter_envois_commandes');
	
	if (!$ids_envois) {
		$erreurs['message_erreur'] = _T('envois_commande:formulaire_export_message_erreur_aucune_selection');
	}
	
	return $erreurs;
}


function formulaires_export_envois_commandes_traiter_dist($redirect = '', $statut = '', $tri = '') {
	$res = array();
	
	// if ($redirect) {
	// 	refuser_traiter_formulaire_ajax();
	// }
	
	$ids_envois = $export = array();
	$ids_envois = _request('exporter_envois_commandes');

	$exporter_auteur = charger_fonction('auteur', 'exporter');
	
	foreach ($ids_envois as $id_envois_commande) {
		$envois_commande = sql_fetsel('*', 'spip_envois_commandes', 'id_envois_commande='.intval($id_envois_commande));
		if ($envois_commande) {
			export_envois_commandes_traiter_commande($envois_commande);
			$id_auteur = $envois_commande['id_auteur'];
		}
		
		if ($id_auteur) {
			$export[$id_envois_commande] = $exporter_auteur($id_auteur);
			$export[$id_envois_commande]['descriptif'] = $envois_commande['descriptif'];
		}
	}
	
	if (count($export)) {
		include_spip('inc/documents');
		$repertoire_exports = sous_repertoire(_DIR_VAR, 'export_envois');
		$url_source_csv = exporter_commandes($export);
		$nom_fichier_csv = basename($url_source_csv);
		$url_csv = deplacer_fichier_upload($url_source_csv, $repertoire_exports.$nom_fichier_csv, true);
		$res['message_ok'] = "Le fichier d'export est disponible : <a href='$url_csv'>$nom_fichier_csv</a>";
	}
	$res['editable'] = true;
	
	return $res;
}


function export_envois_commandes_traiter_commande($envois_commande) {
	$id_envois_commande = intval($envois_commande['id_envois_commande']);
	
	$id_commandes_detail = sql_getfetsel('id_objet', 'spip_envois_commandes_liens', 'objet='.sql_quote('commandes_detail').' AND id_envois_commande='.$id_envois_commande);
	
	$detail = sql_fetsel('*', 'spip_commandes_details', 'id_commandes_detail='.intval($id_commandes_detail));
	
	if ($detail and $envois_commande['statut'] == 'attente') {
		$id_commande = intval($detail['id_commande']);
		
		include_spip('inc/autoriser');
		include_spip('action/editer_objet');
		
		$set = array('statut' => 'envoye');
		
		// Modifier le statut de id_envois_commande : envoyé
		autoriser_exception('instituer', 'envois_commande', $id_envois_commande);
		$set_envoi = array('statut' => 'envoye', 'date_envoi' => date('Y-m-d H:i:s'));
		objet_modifier('envois_commande', $id_envois_commande, $set_envoi);
		autoriser_exception('instituer', 'envois_commande', $id_envois_commande, false);
		
		// Modifier le statut de la commande et de l'article ?
		$instituer_commande = true;
		$instituer_detail = true;
		
		$envois_suivants = sql_allfetsel(
			'*',
			'spip_envois_commandes',
			'id_commande='.$id_commande.' AND statut='.sql_quote('attente').' AND id_envois_commande!='.$id_envois_commande);
		
		// si la commande comporte d'autres articles
		if ($envois_suivants) {
			
			// produit ou rubrique ?
			if ($detail['objet'] == 'produit' or $detail['objet'] == 'rubrique') {
				$instituer_detail = true;
				$instituer_commande = false;
			}
			
			// abonnement ?
			if ($detail['objet'] == 'abonnements_offre') {
				
				foreach ($envois_suivants as $k => $envoi_suivant) {
					$id_detail_envoi_suivant = sql_getfetsel('id_objet', 'spip_envois_commandes_liens', 'id_envois_commande='.$envoi_suivant['id_envois_commande'].' AND objet='.sql_quote('commandes_detail'));
					
					if ($id_detail_envoi_suivant == $id_commandes_detail) {
						// L'article suivant est lié au même abonnement
						// que l'article en cours, on ne change aucun
						// statut.
						$instituer_detail = false;
						$instituer_commande = false;
					} else {
						// Changer uniquement le statut de l'article.
						$instituer_detail = true;
						$instituer_commande = false;
					}
				}
			}
		}
		
		if ($instituer_detail) {
			autoriser_exception('instituer', 'commandes_detail', $id_commandes_detail);
			objet_modifier('commandes_detail', $id_commandes_detail, $set);
			autoriser_exception('instituer', 'commandes_detail', $id_commandes_detail, false);
			// TODO: envoyer un notification pour chaque article expédié ?
		}
		
		if ($instituer_commande) {
			autoriser_exception('instituer', 'commande', $id_commande);
			objet_modifier('commande', $id_commande, $set);
			autoriser_exception('instituer', 'commande', $id_commande, false);
			
			$notifications = charger_fonction('notifications', 'inc');
			// pour le client
			$notifications('commande_client_envoye', $id_commande);
			// pour Vacarme
			$notifications('commande_vendeur_envoye', $id_commande);
		}
	}
}


function exporter_commandes($commandes) {
	$modele_entetes = charger_fonction('commande_entetes', 'exporter');
	$entetes = $modele_entetes();
	
	$titre_fichier = 'envois-commande_'.date('Ymd-His');
	
	$exporter_csv = charger_fonction('exporter_csv', 'inc');
	$url_fichier = $exporter_csv($titre_fichier, $commandes, ',', $entetes, false);
	return $url_fichier;
}
