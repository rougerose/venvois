<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function formulaires_export_envois_commandes_saisies_dist() {
	$saisies = array(
		array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'info_export',
				'texte' => _T('envois_commande:formulaire_export_explication')
			)
		)
	);
	return $saisies;
}



function formulaires_export_envois_commandes_charger_dist() {
	$valeurs = array();
	
	return $valeurs;
}



function formulaires_export_envois_commandes_verifier_dist() {
	$erreurs = array();
	
	$todo = (_request('todo')) ? json_decode(_request('todo')) : array();
	
	if (!count($todo)) {
		$erreurs['message_erreur'] = 'erreur';
	}

	return $erreurs;
}



function formulaires_export_envois_commandes_traiter_dist() {
	refuser_traiter_formulaire_ajax();
	$res = array();
	
	$todo = json_decode(_request('todo'));
	$export_auteur = array();
	$export = array();
	
	foreach ($todo as $id_envois_commande) {
		$envois_commande = sql_fetsel('*', 'spip_envois_commandes', 'id_envois_commande='.$id_envois_commande);
		
		// Exporter les données relatives à l'auteur (destinataire de la commande)
		if ($id_auteur = sql_getfetsel('id_auteur', 'spip_auteurs_liens', 'id_objet='.$id_envois_commande.' AND objet='.sql_quote('envois_commande'))) {
			// organisation
			$organisation_nom = sql_getfetsel('nom', 'spip_organisations', 'id_auteur='.$id_auteur);
			$export_auteur['organisation'] = ($organisation_nom) ? $organisation_nom : '';
			
			// contact
			$contact = sql_fetsel('civilite, nom, prenom', 'spip_contacts', 'id_auteur='.$id_auteur);
			if ($contact) {
				$export_auteur['civilite'] = $contact['civilite'];
				$export_auteur['nom'] = $contact['nom'];
				$export_auteur['prenom'] = $contact['prenom'];
			} else {
				$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.$id_auteur);
				$export_auteur['civilite'] = '';
				$export_auteur['nom'] = nom($auteur['nom']);
				$export_auteur['prenom'] = prenom($auteur['nom']);
			}
			
			// adresse
			$adresse_cles = array_flip(array('voie', 'complement', 'boite_postale', 'code_postal', 'ville', 'pays'));
			$adresse = sql_fetsel('*', 'spip_adresses AS adresses INNER JOIN spip_adresses_liens AS L1 ON (L1.id_adresse = adresses.id_adresse)', 'L1.id_objet='.$id_auteur.' AND L1.objet='.sql_quote('auteur'));
			foreach ($adresse_cles as $cle => $v) {
				$export_auteur[$cle] = ($adresse[$cle]) ? $adresse[$cle] : '';
			}
			
			$export[] = $export_auteur;
			
			// Statut du détail de commande et éventuellement de la commande elle-même.
			$id_commandes_detail = sql_getfetsel('id_objet', 'spip_envois_commandes_liens', 'objet='.sql_quote('commandes_detail').' AND id_envois_commande='.$id_envois_commande);

			$detail = sql_fetsel('*', 'spip_commandes_details', 'id_commandes_detail='.$id_commandes_detail);

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
				
				// Vérifier s'il est possible de modifier le statut de
				// la commande et de l'article
				$instituer_commande = true;
				$instituer_detail = true;
				
				$envois_suivants = sql_allfetsel(
					'*',
					'spip_envois_commandes',
					'id_commande='.$id_commande.' AND statut='.sql_quote('attente').' AND id_envois_commande!='.$id_envois_commande);
				
				// si la commande comporte d'autres articles
				if ($envois_suivants) {
					// produit ou rubrique
					if ($detail['objet'] == 'produit' or $detail['objet'] == 'rubrique') {
						$instituer_detail = true;
						$instituer_commande = false;
					}
					
					// abonnement
					if ($detail['objet'] == 'abonnements_offre') {
						
						foreach ($envois_suivants as $k => $envoi_suivant) {
							$id_detail_envoi_suivant = sql_getfetsel('id_objet', 'spip_envois_commandes_liens', 'id_envois_commande='.$envoi_suivant['id_envois_commande'].' AND objet='.sql_quote('commandes_detail'));
							
							if ($id_detail_envoi_suivant == $id_commandes_detail) {
								// l'article suivant est lié au même abonnement
								// que l'article en cours
								$instituer_detail = false;
								$instituer_commande = false;
							} else {
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
				}
				
				if ($instituer_commande) {
					autoriser_exception('instituer', 'commande', $id_commande);
					objet_modifier('commande', $id_commande, $set);
					autoriser_exception('instituer', 'commande', $id_commande, false);
				}
			}
		}
	}
	
	if (count($export)) {
		$exporter_csv = charger_fonction('exporter_csv', 'inc');
		$nom_fichier = 'envois-commandes_'.date('Ymd-His');
		$url_fichier = $exporter_csv($nom_fichier, $export, ',', null, false);
		
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
	
	return $res;
}
