<?php

// Copyright © 2004 Stéphane Salès
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
 * Envoi d'un nouveau mot de passe
 *
 * @package    Galette
 *
 * @author     Stéphane Salès
 * @copyright  2004 Stéphane Salès
 * @copyright  2007-2008 Johan Cwiklinski
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version    $Id$
 */

require_once('includes/galette.inc.php');
include(WEB_ROOT."classes/texts.class.php");

	// initialize warnings
	$error_detected = array();
	$warning_detected = array();


	function isEmail($login) {
		if( empty($login) ) {
			$GLOBALS["error_detected"] = _T("empty login");
		} else {
			$req = "SELECT email_adh
				FROM ".PREFIX_DB."adherents
				WHERE login_adh=".txt_sqls($login);
			$result = &$GLOBALS["DB"]->Execute($req);

			if ($result->EOF) {
				$GLOBALS["error_detected"] = _T("this login doesn't exist");
				dblog("Nonexistent login sent via the lost password form. Login:"." \"" . $login ."\"");
			}else{
				$email=$result->fields[0];
				if( empty($email) ) {
					$GLOBALS["error_detected"] = _T("This account doesn't have a valid email address. Please contact an administrator.");
					dblog("Someone asked to recover his password but had no email. Login:"." \"" . $login . "\"");
				}else
				return $email;
			}
		}
	}

	// Validation
	if (isset($_POST['valid']) && $_POST['valid'] == "1") {
		$login_adh=$_POST['login'];
		//if field contain the character @ we consider that is an email
		if ( strpos($login_adh,'@') !== FALSE ) {
			$query = "SELECT login_adh from ".PREFIX_DB."adherents where email_adh=".txt_sqls($login_adh);
			$result = &$DB->Execute($query);
			$login_adh = $result->fields[0];
		}
		$email_adh=isEmail($login_adh);

		//send the password
		if(	$email_adh!="" )
		{
			$query = "SELECT id_adh from ".PREFIX_DB."adherents where login_adh=".txt_sqls($login_adh);
			$result = &$DB->Execute($query);
			if ($result->EOF) {
				$warning_detected = _T("There is  no password for user :")." \"" . $login_adh . "\"";
				//TODO need to clean die here
      } else {
				$id_adh = $result->fields[0];
      }
			//make temp password
			$tmp_passwd = makeRandomPassword(7);
			$hash = md5($tmp_passwd);
			//delete old tmp_passwd
			$query = "DELETE FROM ".PREFIX_DB."tmppasswds";
			$query .= " WHERE id_adh = $id_adh ";
			if (!$DB->Execute($query))
				$warning_detected = _T("delete failed");
			//insert temp passwd in database
			$query = "INSERT INTO ".PREFIX_DB."tmppasswds";
			$query .= " (id_adh, tmp_passwd, date_crea_tmp_passwd)";
			$query .= " VALUES($id_adh, '$hash', ".$DB->DBTimeStamp(time()).")";
			if (!$DB->Execute($query))
				$warning_detected = _T("There was a database error when inserting data");
				//$warning_detected = $DB->ErrorMsg();
			// Get email text in database
			$texts = new texts();
			$mtxt = $texts->getTexts("pwd",PREF_LANG);
			// Replace Tokens
			$mtxt[tbody] = str_replace("{CHG_PWD_URI}", "http://".$_SERVER["SERVER_NAME"].dirname($_SERVER["REQUEST_URI"])."/change_passwd.php?hash=$hash",$mtxt[tbody]);
			$mtxt[tbody] = str_replace("{LOGIN}", custom_html_entity_decode($login_adh, ENT_QUOTES), $mtxt[tbody]);
			$mtxt[tbody] = str_replace("{PASSWORD}", custom_html_entity_decode($tmp_passwd, ENT_QUOTES),$mtxt[tbody]);
      	$mail_result = custom_mail($email_adh,$mtxt[tsubject],$mtxt[tbody]);
			if( $mail_result == 1) {
				dblog("Password sent. Login:"." \"" . $login_adh . "\"");
				$warning_detected = _T("Password sent. Login:")." \"" . $login_adh . "\"";
				//$password_sent = true;
			} else {
        switch ($mail_result) {
          case 2 :
            dblog("Email sent is disabled in the preferences");
            $warning_detected = _T("Email sent is disabled in the preferences. Ask galette admin");
            break;
          case 3 :
            dblog("A problem happened while sending password for account:"." \"" . $login_adh . "\"");
            $warning_detected = _T("A problem happened while sending password for account:")." \"" . $login_adh . "\"";
            break;
          case 4 :
            dblog("The mail server filled in the preferences cannot be reached");
            $warning_detected = _T("The mail server filled in the preferences cannot be reached. Ask Galette admin");
            break;
					case 5 :
						dblog("**IMPORTANT** There was a probably breaking attempt when sending mail to :"." \"" . $email_adh . "\"");
						$error_detected[] = _T("**IMPORTANT** There was a probably breaking attempt when sending mail to :")." \"" . $email_adh . "\"";
						break;
          default :
            dblog("A problem happened while sending password for account:"." \"" . $login_adh . "\"");
            $warning_detected = _T("A problem happened while sending password for account:")." \"" . $login_adh . "\"";
            break;
        }
			}
		}
	}

$tpl->assign('page_title', _T("Password recovery"));
	$tpl->assign("error_detected",$error_detected);
	$tpl->assign("warning_detected",$warning_detected);

  // display page
	$tpl->display("lostpasswd.tpl");
?>