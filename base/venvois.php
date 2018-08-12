<?php
/**
 * Déclarations relatives à la base de données
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
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function venvois_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['envois_commandes'] = 'envois_commandes';
	$interfaces['table_des_tables']['envois_ponctuels'] = 'envois_ponctuels';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function venvois_declarer_tables_objets_sql($tables) {

	$tables['spip_envois_commandes'] = array(
		'type' => 'envois_commande',
		'principale' => 'oui',
		'table_objet_surnoms' => array('envoiscommande'), // table_objet('envois_commande') => 'envois_commandes' 
		'field'=> array(
			'id_envois_commande' => 'bigint(21) NOT NULL',
			'id_commande'        => 'bigint(21) NOT NULL DEFAULT 0',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'date_envoi'         => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'descriptif'         => 'text NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_envois_commande',
			'KEY statut'         => 'statut',
		),
		'titre' => 'descriptif AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('date_envoi', 'descriptif'),
		'champs_versionnes' => array('descriptif'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_envois_commandes_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'attente'     => 'envois_commande:texte_statut_attente',
			'envoye'   => 'envois_commande:texte_statut_envoye',
			'abandon'   => 'envois_commande:texte_statut_abandon',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut_images' => array(
			'prepa'    => 'puce-preparer-8.png',
			'attente'    => 'puce-proposer-8.png',
			'envoye'    => 'puce-publier-8.png',
			'abandon'  => 'puce-refuser-8.png',
			'poubelle' => 'puce-supprimer-8.png'
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'envoye',
				'previsu'   => 'envoye,prop,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'envois_commande:texte_changer_statut_envois_commande',
	);
	
	$tables['spip_envois_ponctuels'] = array(
		'type' => 'envois_ponctuel',
		'principale' => 'oui',
		'table_objet_surnoms' => array('envoisponctuel'), // table_objet('envois_ponctuel') => 'envois_ponctuels' 
		'field'=> array(
			'id_envois_ponctuel' => 'bigint(21) NOT NULL',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'descriptif'         => 'text NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_envois_ponctuel',
		),
		'titre' => 'titre AS titre, "" AS lang',
		'date' => 'date',
		'champs_editables'  => array('titre', 'descriptif'),
		'champs_versionnes' => array('titre', 'descriptif'),
		'rechercher_champs' => array("titre" => 1, "descriptif" => 2),
		'tables_jointures'  => array('spip_envois_ponctuels_liens'),
	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function venvois_declarer_tables_auxiliaires($tables) {

	$tables['spip_envois_commandes_liens'] = array(
		'field' => array(
			'id_envois_commande' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_envois_commande,id_objet,objet',
			'KEY id_envois_commande' => 'id_envois_commande',
		)
	);
	
	$tables['spip_envois_ponctuels_liens'] = array(
		'field' => array(
			'id_envois_ponctuel' => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'quantite'                 => 'tinyint(2) DEFAULT "0" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_envois_ponctuel,id_objet,objet',
			'KEY id_envois_ponctuel' => 'id_envois_ponctuel',
		)
	);

	return $tables;
}
