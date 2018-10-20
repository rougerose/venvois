<?php 

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Action pour supprimer un·e envois_ponctuel
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
**/
function action_supprimer_envois_ponctuel_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);
	
	if ($id_envois_ponctuel = intval($arg)
		and autoriser('supprimer', 'envoisponctuel', $id_envois_ponctuel))
	{
		include_spip('action/editer_liens');
		objet_dissocier(array('envois_ponctuel' => $id_envois_ponctuel), array('*' => '*'));

		sql_delete('spip_envois_ponctuels', 'id_envois_ponctuel='.intval($id_envois_ponctuel));

	}
}
