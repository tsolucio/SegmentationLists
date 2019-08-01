/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function loadSubsList(type, id) {
	var element = type+'_cv_list';
	var value = document.getElementById(element).value;

	var filter = document.getElementById(element)[document.getElementById(element).selectedIndex].value;
	if (filter=='None') {
		return false;
	}
	if (value != '') {
		document.getElementById('status').style.display='inline';
		jQuery.ajax({
			method: 'GET',
			url: 'index.php?module=SubsList&action=SubsListAjax&file=LoadList&ajax=true&return_action=DetailView&return_id='+id+'&list_type='+type+'&cvid='+value
		}).done(function (response) {
			document.getElementById('status').style.display='none';
			jQuery('#RLContents').html(response);
		});
	}
}

function AutoSync(module, id, autoSync, filter) {
	if (!(this instanceof AutoSync)) {
		return new AutoSync(module, id, autoSync, filter);
	}

	var
		$ = jQuery,
		self = this,
		$indicator = $('#indicator_SubsList_' + module),
		$autoSync = $('#autosync-' + module),
		$filter = $('#' + module + '_cv_list');

	function updateUI() {
		if ($autoSync.prop('checked')) {
			$filter.prop('disabled', true);
			$('#tbl_SubsList_' + module + ' .crmbutton').css('background', '#ccc').prop('disabled', true);
		} else {
			$filter.prop('disabled', false);
			$filter.val('None');
			$('#tbl_SubsList_' + module + ' .crmbutton').css('background', '').prop('disabled', false);
		}
	}

	self.update = function () {
		var
			autoSync = $autoSync.prop('checked'),
			filter = $filter.val();

		if (autoSync && (filter == 'None' || filter==null)) {
			$autoSync.prop('checked', false);
			alert('Select a List first');
		}

		$indicator.show();
		$.ajax({
			url: 'index.php',
			data: {
				module: 'SubsList',
				action: 'SubsListAjax',
				file: 'saveAutoSync',
				relmodule: module,
				record: id,
				autosync: autoSync ? 1 : 0,
				filter: autoSync ? filter : 0
			}
		}).done(function () {
			$indicator.hide();
			updateUI();
		});
	};

	//$autoSync.prop('checked', autoSync ? true : false);
	//$filter.val(filter);
	//updateUI();

	return this;
}

function setupAutoSync(relatedmodule) {
	var info = document.getElementById('autoSync'+relatedmodule+'Info').innerHTML;
	info = JSON.parse(info);
	AutoSync(relatedmodule, info.slid, info.autoSync, info.filter).update();
}