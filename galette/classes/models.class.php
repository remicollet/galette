<?php

// Copyright © 2007 John Perr
// Copyright © 2007-2008 Johan Cwiklinski
//
// This file is part of Galette (http://galette.tuxfamily.org).
//
// Galette is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Galette is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Galette. If not, see <http://www.gnu.org/licenses/>.

/**
 * models.class.php, 31 octobre 2007
 *
 * @package Galette
 * 
 * @name Models
 * @author     John Perr
 * @copyright  2007 John Perr
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version    $Id$
 * @since      Disponible depuis la Release 0.7
 */

/** TODO
* - The above constant should be defined at higher level
* - all errors messages should be handled by pear::log
*/
set_include_path(get_include_path() . PATH_SEPARATOR . WEB_ROOT . "includes/pear/" . PATH_SEPARATOR . WEB_ROOT . "includes/pear/PEAR/" . PATH_SEPARATOR . WEB_ROOT . "includes/pear/MDB2");

require_once("MDB2.php");


/**
 * Text class for galette
 *
 * @name Models
 * @package Galette
 *
 */

class Models {
	private $modlist;
	private $modname;
	private $error = array();
	private $is_error = false;
	const MODELS = "models";
	const ADH = "adherents";
	const FIELDS = "field_types";

	function __construct(){}
	function __destruct(){}

	/**
	* chekError
	* @param array: Database result array
	* @return boolean: True if database error raised
	*/
	private function chekError(){
		// Vérification des erreurs
		global $mdb;
		if ($mdb->inError()) {
		   $this->is_error = true;
			array_push ($this->error,$mdb->getErrorMessage());
			array_push ($this->error,$mdb->getErrorDetails());
		}
		return $this->is_error;
	}

	/**
	* getError
	* @return array: Database result array
	*/
	public function getError(){
		return $this->error;
	}

	/** Import XML files generated by PanCake
	* @param string: Reference of file to read
	* @return boolean: status of read operation
	*/
	public function readXMLModels($modelfile){
		global $mdb;
		if (file_exists($modelfile)) {
			$xmlModel = simplexml_load_file($modelfile);
			$this->modname = $xmlModel->name;
	   // First delete models if it already exists in database
			$requete = 'DELETE FROM '.$mdb->quoteIdentifier(PREFIX_DB.self::MODELS);
			$requete .= ' WHERE '.$mdb->quoteIdentifier('mod_name').'='.$mdb->quote($this->modname);
			$result = $mdb->execute($requete);
			// Vérification des erreurs
			self::chekError();
			if(!$this->is_error){
		// Then Load Fields in database (one for each model)
				$requete = 'INSERT INTO '.$mdb->quoteIdentifier(PREFIX_DB.self::MODELS);
				$requete .= ' ('.$mdb->quoteIdentifier('mod_name').','.$mdb->quoteIdentifier('mod_xml').')';
				$requete .= ' VALUES ('.$mdb->quote($this->modname).','.$mdb->quote($xmlModel->asXML()).')';
				$result = $mdb->execute($requete);
				// Vérification des erreurs
				self::chekError();
			}
		} else {
			array_push ($this->error,_T("Error opening model file ".$modelfile));
		}
		return $this->is_error;
	}

	/**
	* Get list of models available in database
	* @param string: model name or pattern
	* @return arrayt: Model id and name
	*/
	public function getModels($modnm = "%"){
		global $mdb;

		// Get models corresponding to pattern
		$requete = "SELECT  ".$mdb->quoteIdentifier('mod_id').",".$mdb->quoteIdentifier('mod_name');
		$requete .= " FROM ".$mdb->quoteIdentifier(PREFIX_DB.self::MODELS);
		$requete .= " WHERE ".$mdb->quoteIdentifier('mod_name')."=".$mdb->quote($modnm);
		$result = $mdb->query($requete);

		// Vérification des erreurs
		self::chekError();
		if(!$this->is_error && $result->numRows()>0){
			$this->$modlist = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		}
		return $this->$modlist;
	}

	/**
	* Load a uniq model
	* @param string: model name
	* @return SimpleXML object: Model description
	*/
	public function load($modid){
		global $mdb;

		// Get models corresponding to pattern
		$requete = "SELECT ".$mdb->quoteIdentifier('mod_xml')." FROM ".$mdb->quoteIdentifier(PREFIX_DB.self::MODELS);
		$requete .= " WHERE ".$mdb->quoteIdentifier('mod_id')."=".$mdb->quote($modid);
		$result = $mdb->query($requete);

		// Vérification des erreurs
		self::chekError();
		if(!$this->is_error && $result->numRows()>0){
			$xmlstring = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
			return simplexml_load_string($xmlstring['mod_xml']);
		} else {
			return simplexml_load_string('<model><name>'._T('ERROR LOADING MODEL').'</name></model>');
		}
	}
	/**
	* Export filed names for model constructor PanCake
	* @param string: Path of file to write
	* @return boolean: status of write operation
	*/
	public function writeFields($file="DocFields.txt"){
		global $mdb;
		// All fields from table adherents
		$requete = "DESC ".$mdb->quoteIdentifier(PREFIX_DB.self::ADH);
		$result = $mdb->query($requete);
		// Vérification des erreurs
		self::chekError();
		if(!$this->is_error && $result->numRows()>0){
			$fields_adh = $result->fetchAll(MDB2_FETCHMODE_ASSOC);
	// List fields from adherents table
			$buffer = "# "._T("Field list for Pancake document model editor")."\n";
			$buffer .="# "._T("Generated by Galette ").GALETTE_VERSION."\n";
			$buffer .="# \$Date : ".date('d M Y')."\$\n\n";
			foreach ($fields_adh as $f) {
				$buffer .= $f['field']."\t".$f['type']."\n";
			}
		}
	// Add dynamic fields related to adherents
		$requete = "SELECT ".$mdb->quoteIdentifier('field_name').",";
		$requete .= $mdb->quoteIdentifier('field_size');
		$requete .= " FROM ".$mdb->quoteIdentifier(PREFIX_DB.self::FIELDS);
		$requete .= " WHERE ".$mdb->quoteIdentifier('field_form')."=".$mdb->quote('adh');
		$result = $mdb->query($requete);
		// Vérification des erreurs
		self::chekError();
		if(!$this->is_error && $result->numRows()>0){
			$fields_adh = $result->fetchAll(MDB2_FETCHMODE_ASSOC);
			foreach ($fields_adh as $f) {
				$buffer .= $f['field_name']."\t".$f['field_size']."\n";
			}
			header('Content-type: application/text');
			header('Content-Length: '.strlen($buffer));
			header('Content-disposition: attachement; filename="'.$file.'"');
			echo $buffer;
		}
		return $this->is_error;
	}
}
?>