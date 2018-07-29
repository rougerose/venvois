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
	$flux['data'] += objet_optimiser_liens(array('envois_commande'=>'*'), '*');

	sql_delete('spip_envois_commandes', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}


function venvois_post_edition($flux) {
	$f = $flux;
	return $flux;
}
