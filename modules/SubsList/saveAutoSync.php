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

$relModule = vtlib_purify($_REQUEST['relmodule']);
$id = vtlib_purify($_REQUEST['record']);
$autoSync = vtlib_purify($_REQUEST['autosync']);
$filter = vtlib_purify($_REQUEST['filter']);

$result = $adb->pquery('select 1 from vtiger_ws_entity where name=?', array($relModule)); // security check
if ($result && $adb->num_rows($result)==1) {
	$prefix = strtolower($relModule);
	$adb->pquery(
		"update vtiger_subslist set {$prefix}_autosync=?, {$prefix}_filter=? where subslistid=?",
		array($autoSync, $filter, $id)
	);
}
