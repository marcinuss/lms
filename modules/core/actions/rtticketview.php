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

if(! $LMS->TicketExists($_GET['id']))
{
	$SESSION->redirect('?m=rtqueuelist');
}

if(! $LMS->GetUserRightsRT($AUTH->id, 0, $_GET['id']))
{
	$SMARTY->display('noaccess.html');
	$SESSION->close();
	die;
}

if(isset($_GET['delmsgid']) && $LMS->FirstMessage($_GET['id']) != $_GET['delmsgid'])
	$LMS->MessageDel($_GET['delmsgid']);

$ticket = $LMS->GetTicketContents($_GET['id']);
$layout['pagetitle'] = trans('Ticket Review: No. $0',sprintf("%06d",$ticket['ticketid']));

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('ticket', $ticket);
$SMARTY->display('rtticketview.html');
?>