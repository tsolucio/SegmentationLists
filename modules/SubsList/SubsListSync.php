<?php
/*************************************************************************************************
 * Copyright 2011-2013 TSolucio  --  This file is a part of vtMktDashboard.
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************
*  Module       : SubsList
*  Version      : 1.9
*  Author       : TSolucio
*************************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'modules/CustomView/CustomView.php';
global $adb;
$mod_strings = array();
$slid = empty($_REQUEST['src_record']) ? '' : vtlib_purify($_REQUEST['src_record']);
$current_user = Users::getActiveAdminUser();
$params = array();
$query = 'select * from vtiger_subslist mc join vtiger_crmentity crm_mc on crm_mc.crmid=mc.subslistid where crm_mc.deleted=0';
if (!empty($slid)) {
	$query .= ' and mc.subslistid=?';
	$params[] = $slid;
}
$res = $adb->pquery($query, $params);
while ($row = $adb->getNextRow($res, false)) {
	if ($row['contacts_autosync']) {
		loadList($row['subslistid'], 'Contacts', 'contactid', $row['contacts_filter']);
	}
	if ($row['accounts_autosync']) {
		loadList($row['subslistid'], 'Accounts', 'accountid', $row['accounts_filter']);
	}
	if ($row['leads_autosync']) {
		loadList($row['subslistid'], 'Leads', 'leadid', $row['leads_filter']);
	}
}

function loadList($id, $list_type, $list_id_field, $cvid) {
	global $adb, $current_user;

	$adb->pquery('delete from vtiger_crmentityrel where crmid=?', array($id));

	$queryGenerator = new QueryGenerator($list_type, $current_user);
	if (!empty($cvid)) {
		$queryGenerator->initForCustomViewById($cvid);
	} else {
		$queryGenerator->initForDefaultCustomView();
	}
	$listquery = $queryGenerator->getQuery();

	$rs = $adb->query($listquery);

	while ($row=$adb->fetch_array($rs)) {
		$adb->pquery("INSERT INTO vtiger_crmentityrel (crmid,module,relcrmid,relmodule) VALUES(?,'SubsList',?,'$list_type')", array((int)$id, (int)$row[$list_id_field]));
	}
}

if (isset($_REQUEST['gotodv']) && $_REQUEST['gotodv']==1 && !empty($slid)) {
	header('Location: index.php?module=SubsList&action=DetailView&record=' . urlencode($slid));
}
?>
