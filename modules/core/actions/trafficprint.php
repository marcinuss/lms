<?php

/*
 * LMS version 1.7-cvs
 *
 *  (C) Copyright 2001-2005 LMS Developers
 *
 *  Please, see the doc/AUTHORS for more information about authors!
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 *  $Id$
 */

$type = isset($_GET['type']) ? $_GET['type'] : '';

switch($type)
{
	case 'customertraffic': /******************************************/

		$month = $_POST['month'] ? $_POST['month'] : date('n');
		$year = $_POST['year'] ? $_POST['year'] : date('Y');
		$customer = $_POST['customer'] ? $_POST['customer'] : $_GET['customer'];

		$layout['pagetitle'] = trans('Stats of Customer $0 in month $1', $LMS->GetCustomerName($customer), strftime('%B %Y', mktime(0,0,0,$month,1,$year)));
	
		$from = mktime(0,0,0,$month,1,$year);
		$to = mktime(0,0,0,$month+1,1,$year);

    		if($list = $DB->GetAll('SELECT download, upload, dt
	                	    FROM stats
				    LEFT JOIN nodes ON (nodeid = nodes.id)
				    WHERE ownerid = ? AND dt >= ? AND dt < ?',
				    array($customer, $from, $to)))
		{
			for($i=1; $i<=date('t',$from); $i++)
				$stats[$i]['date'] = mktime(0,0,0,$month,$i,$year); 
				
			foreach($list as $row)
			{
				$day = date('j', $row['dt']);
				
				$stats[$day]['download'] += $row['download'];
				$stats[$day]['upload'] += $row['upload'];
			}
			
			for($i=1; $i<=date('t',$from); $i++)
			{
				$stats[$i]['upavg'] = $stats[$i]['upload']*8/1024/86400; //kbps
				$stats[$i]['downavg'] = $stats[$i]['download']*8/1024/86400; //kbps
				
				$listdata['upload'] += $stats[$i]['upload'];
				$listdata['download'] += $stats[$i]['download'];
				$listdata['upavg'] += $stats[$i]['upavg'];
				$listdata['downavg'] += $stats[$i]['downavg'];
				
				list($stats[$i]['upload'], $stats[$i]['uploadunit']) = setunits($stats[$i]['upload']);
				list($stats[$i]['download'], $stats[$i]['downloadunit']) = setunits($stats[$i]['download']);
			}

			$listdata['upavg'] = $listdata['upavg']/date('t', $from);
			$listdata['downavg'] = $listdata['downavg']/date('t', $from);
			list($listdata['upload'], $listdata['uploadunit']) = setunits($listdata['upload']);
			list($listdata['download'], $listdata['downloadunit']) = setunits($listdata['download']);
		}

		$SMARTY->assign('stats', $stats);
		$SMARTY->assign('listdata', $listdata);
		$SMARTY->display('printcustomertraffic.html');
	break;

	default:
		$layout['pagetitle'] = trans('Printing');
		
		$yearstart = date('Y',$DB->GetOne('SELECT MIN(dt) FROM stats'));
		$yearend = date('Y',$DB->GetOne('SELECT MAX(dt) FROM stats'));
		for($i=$yearstart; $i<$yearend+1; $i++)
			$statyears[] = $i;
		for($i=1; $i<13; $i++)
			$months[$i] = strftime('%B', mktime(0,0,0,$i,1));
		
		$SMARTY->assign('currmonth', date('n'));
		$SMARTY->assign('curryear', date('Y'));
		$SMARTY->assign('statyears', $statyears);
		$SMARTY->assign('months', $months);
		$SMARTY->assign('customers', $LMS->GetCustomerNames());
		$SMARTY->assign('printmenu', 'traffic');
		$SMARTY->display('printindex.html');
	break;
}

?>