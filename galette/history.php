<?php

// Copyright © 2003 Frédéric Jaqcuot
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
 * Historique
 *
 * @package    Galette
 *
 * @author     Frédéric Jaqcuot
 * @copyright  2003 Frédéric Jaqcuot
 * @copyright  2007-2008 Johan Cwiklinski
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version    $Id$
 */

require_once('includes/galette.inc.php');

if( !$login->isLogged() )
{
	header('location: index.php');
	die();
}
if( !$login->isAdmin())
{
	header('location: voir_adherent.php');
	die();
}

if (isset($_GET['reset']) && $_GET['reset'] == 1){
	$hist->clean();
}


if( isset($_GET['page']) && is_numeric($_GET['page']) ) $hist->page = $_GET['page'];

if( isset($_GET['nbshow']) && is_numeric($_GET['nbshow'])) $hist->show = $_GET['nbshow'];

if(isset($_GET['tri'])){
	if($_GET['tri'] == $hist->tri){//ordre inverse
		$hist->invertorder();
	}else{//ordre normal
		$hist->tri = $_GET['tri'];
		$hist->setDirection(History::ORDER_ASC);
	}
}

$logs = array();

$logs = $hist->getHistory();
$_SESSION['galette']['history'] = serialize($hist);

$tpl->assign("logs",$logs);
$tpl->assign("nb_lines",count($logs));
$tpl->assign("nb_pages", $hist->pages);
$tpl->assign("page", $hist->page);
$tpl->assign("numrows", $hist->show);
$tpl->assign('history', $hist);
$tpl->assign('nbshow_options', array(
		10 => "10",
		20 => "20",
		50 => "50",
		100 => "100",
		0 => _T("All")));
$content = $tpl->fetch("history.tpl");
$tpl->assign("content", $content);
$tpl->display("page.tpl");
?>