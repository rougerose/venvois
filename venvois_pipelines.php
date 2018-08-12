<?php
/**
 * Utilisations de pipelines par Envois
 *
 * @plugin     Envois
 * @copyright  2018
 * @author     Christophe Le Drean
 * @licence    GNU/GPL
 * @package    SPIP\Venvois\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function venvois_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les envois_commandes
	if (!$e['edition'] and in_array($e['type'], array('envois_commande'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}



	if ($texte) {
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}


/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function venvois_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/envois_commandes', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('envois_commande:info_envois_commandes_auteur')
		), array('ajax' => true));
	}
	return $flux;
}




/**
 * Optimiser la base de données
 *
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function venvois_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('envois_commande'=>'*', 'envois_ponctuel'=>'*'), '*');

	sql_delete('spip_envois_commandes', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

/**
 * @pipeline formulaire_fond
 * @param  array  $flux
 * @return array
 */
function venvois_formulaire_fond($flux) {
	// Envois ponctuels : Inclure le javascript dans le formulaire editer_liens.
	// Ce code js permet de prendre en compte le nombre d'exemplaires à envoyer
	// à chaque auteur sélectionné.
	if ($flux['args']['form'] == 'editer_liens' and $flux['args']['contexte']['objet'] == 'envois_ponctuel') {
		$js = recuperer_fond('prive/squelettes/inclure/js_envois_ponctuel_editer_liens', $flux['args']['contexte']);
			$marque = '<!--extra-->';
			
			if (($html = strpos($flux['data'], $marque)) !== false) {
				$flux['data'] = substr_replace($flux['data'], $js, $html + strlen($marque), 0);
			}
	}
	return $flux;
}


/**
 * @pipeline formulaire_charger
 * @param  array  $flux
 * @return array
 */
function venvois_formulaire_charger($flux) {
	// Envois ponctuels : charger la liste des auteurs_destinataires,
	// qui est une liste auteurs_lies/associer de SPIP légèrement modifiée.
	if (
		$flux['args']['form'] == 'editer_liens'
		and in_array('envois_ponctuels', $flux['args']['args'])
		and in_array('auteurs', $flux['args']['args'])
	) {
		$flux['data']['_vue_liee'] = 'auteurs_destinataires_lies';
		$flux['data']['_vue_ajout'] = 'auteurs_destinataires_associer';
	}
	return $flux;
}
