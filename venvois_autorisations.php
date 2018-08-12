<?php
/**
 * Définit les autorisations du plugin Envois
 *
 * @plugin     Envois
 * @copyright  2018
 * @author     Christophe Le Drean
 * @licence    GNU/GPL
 * @package    SPIP\Venvois\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function venvois_autoriser() {
}



// 
// Objet envois_commandes
// ----------------------
// 

/**
 * Autorisation de voir un élément de menu (envoiscommandes)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoiscommandes_menu_dist($faire, $type, $id, $qui, $opt) {
	return true;
}


/**
 * Autorisation de créer (envoiscommande)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoiscommande_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

/**
 * Autorisation de voir (envoiscommande)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoiscommande_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

/**
 * Autorisation de modifier (envoiscommande)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoiscommande_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}

/**
 * Autorisation de supprimer (envoiscommande)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoiscommande_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}



/**
 * Autorisation de lier/délier l'élément (envoiscommandes)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_associerenvoiscommandes_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}


function autoriser_envoiscommande_instituer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}


// 
// Objet envois_ponctuels
// ----------------------
// 

/**
 * Autorisation de voir un élément de menu (envoisponctuels)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoisponctuels_menu_dist($faire, $type, $id, $qui, $opt) {
	return true;
}



/**
 * Autorisation de créer (envoisponctuel)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoisponctuel_creer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}



/**
 * Autorisation de voir (envoisponctuel)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoisponctuel_voir_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}



/**
 * Autorisation de modifier (envoisponctuel)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoisponctuel_modifier_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}



/**
 * Autorisation de supprimer (envoisponctuel)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_envoisponctuel_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}



/**
 * Autorisation de lier/délier l'élément (envoisponctuels)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_associerenvoisponctuels_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}


function autoriser_envoisponctuel_iconifier_dist($faire, $type, $id, $qui, $opt) {
	return false;
}
