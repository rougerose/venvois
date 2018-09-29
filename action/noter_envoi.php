<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function action_noter_envoi($id_commande, $objet, $id_objet) {
	if (
		$id_commande 
		AND $objet 
		AND $id_objet 
		AND $detail = sql_fetsel(
			'*', 
			'spip_commandes_details', 
			'id_commande='.intval($id_commande).' AND objet='.sql_quote($objet).' AND id_objet='.intval($id_objet)
		)
	) {
		$id_auteur = sql_getfetsel('id_auteur', 'spip_commandes', 'id_commande='.intval($id_commande));
		
		include_spip('inc/autoriser');
		include_spip('action/editer_objet');
		include_spip('action/editer_liens');
		
		// abonnement
		if (
			$objet == 'abonnements_offre'
			AND $abonnement = sql_fetsel(
				'*', 
				'spip_abonnements', 
				'id_commande='.intval($id_commande).' AND id_abonnements_offre='.intval($id_objet).' AND statut='.sql_quote('actif')
			)
		) {
			include_spip('inc/vnumeros');
			$numeros_debut_fin = array($abonnement['numero_debut'], $abonnement['numero_fin']);
			$liste_numeros = vnumeros_lister_disponibles($numeros_debut_fin);
		
			autoriser_exception('creer', 'envois_commande', '');
			foreach ($liste_numeros as $numero => $rubrique) {
				$set = array(
					'id_commande' => intval($id_commande),
					'descriptif' => generer_info_entite($rubrique['id_rubrique'], 'rubrique', 'titre'),
					'statut' => 'attente'
				);
				
				$id_envois_commande = objet_inserer('envois_commande', null, $set);
				objet_associer(
					array('envois_commande' => $id_envois_commande), 
					array('commandes_detail' => $detail['id_commandes_detail'])
				);
				objet_associer(
					array('envois_commande' => $id_envois_commande), 
					array('auteur' => $id_auteur)
				);
			}
			autoriser_exception('creer', 'envois_commande', '', false);
		}
		
		// rubrique (achat d'exemplaire)
		// ou produit (cadeau lié à la souscription d'abonnement)
		if ($objet == 'rubrique' or $objet == 'produit') {
			$nb = $detail['quantite'];
			
			autoriser_exception('creer', 'envois_commande', '');
			while ($nb-->0){
				$set = array(
					'id_commande' => intval($id_commande),
					'descriptif' => generer_info_entite(intval($id_objet), $objet, 'titre'),
					'statut' => 'attente'
				);
				$id_envois_commande = objet_inserer('envois_commande', null, $set);
				//objet_associer(array('auteur' => $id_auteur), array('envois_commande' => $id_envois_commande));
				objet_associer(
					array('envois_commande' => $id_envois_commande), 
					array('commandes_detail' => $detail['id_commandes_detail'])
				);
				objet_associer(
					array('envois_commande' => $id_envois_commande),
					array('auteur' => $id_auteur)
				);
			}
			autoriser_exception('creer', 'envois_commande', '', false);
		}
	}
}
