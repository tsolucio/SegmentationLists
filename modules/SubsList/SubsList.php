<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';

class SubsList extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_subslist';
	public $table_index= 'subslistid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'utility', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'filterList');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_subslistcf', 'subslistid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_subslist', 'vtiger_subslistcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_subslist'   => 'subslistid',
		'vtiger_subslistcf' => 'subslistid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'subslistno' => array('subslist' => 'subslistno'),
		'subslistname' => array('subslist' => 'subslistname'),
		'subslisttype' => array('subslist' => 'subslisttype'),
		'subsliststatus' => array('subslist' => 'subsliststatus'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'subslistno' => 'subslistno',
		'subslistname' => 'subslistname',
		'subslisttype' => 'subslisttype',
		'subsliststatus' => 'subsliststatus',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'subslistname';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'subslistno' => array('subslist' => 'subslistno'),
		'subslistname' => array('subslist' => 'subslistname'),
		'subslisttype' => array('subslist' => 'subslisttype'),
		'subsliststatus' => array('subslist' => 'subsliststatus'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'subslistno' => 'subslistno',
		'subslistname' => 'subslistname',
		'subslisttype' => 'subslisttype',
		'subsliststatus' => 'subsliststatus',
	);

	// For Popup window record selection
	public $popup_fields = array('subslistname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'subslistname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'subslistname';

	// Required Information for enabling Import feature
	public $required_fields = array('subslistname'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'subslistname';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'subslistname');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'SEGL-', '0000001');
			$module = Vtiger_Module::getInstance($modulename);
			$modContacts = Vtiger_Module::getInstance('Contacts');
			$modContacts->setRelatedList($module, $modulename, array('SELECT'));
			$modAccs = Vtiger_Module::getInstance('Accounts');
			$modAccs->setRelatedList($module, $modulename, array('SELECT'));
			$modLeads = Vtiger_Module::getInstance('Leads');
			$modLeads->setRelatedList($module, $modulename, array('SELECT'));
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// public function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	/**
	 * Default (generic) function to handle the related list for the module.
	 * NOTE: Vtiger_Module::setRelatedList sets reference to this function in vtiger_relatedlists table
	 * if function name is not explicitly specified.
	 */
	/* overriden version of get_related_list function */
	public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $currentModule, $singlepane_view, $adb;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		$button = '';

		require_once 'modules/CustomView/CustomView.php';

		$res = $adb->pquery('select * from vtiger_subslist where subslistid=?', array($id));
		$rssl = $adb->getNextRow($res, false);
		switch ($related_module) {
			case 'Contacts':
				$autoSync = $rssl['contacts_autosync'];
				$filter = $rssl['contacts_filter'];
				break;
			case 'Accounts':
				$autoSync = $rssl['accounts_autosync'];
				$filter = $rssl['accounts_filter'];
				break;
			case 'Leads':
				$autoSync = $rssl['leads_autosync'];
				$filter = $rssl['leads_filter'];
				break;
			default:
				$autoSync = false;
				$filter = 0;
				break;
		}

		$checked = $autoSync ? 'checked' : '';
		$disabled = $autoSync ? 'disabled' : '';
		$disabledcss = $autoSync ? 'background:#ccc;' : '';
		$lhtml = "<input type='checkbox' id='autosync-{$related_module}' onclick='setupAutoSync(\"{$related_module}\");' $checked>"
		.getTranslatedString('AutoSync').'&nbsp;&nbsp;';
		$lhtml .= "<select id='".$related_module."_cv_list' class='small' $disabled><option value='None'>-- ".getTranslatedString('Select One')." --</option>";
		$oCustomView = new CustomView($related_module);
		//$viewid = $oCustomView->getViewId($related_module);
		$customviewcombo_html = $oCustomView->getCustomViewCombo($filter, true);
		$lhtml .= $customviewcombo_html;
		$lhtml .= '</select>&nbsp;&nbsp';

		$button .= $lhtml."<input title='".getTranslatedString('LBL_LOAD_LIST', 'SubsList')."' class='crmbutton small edit' style='$disabledcss' $disabled value='"
			.getTranslatedString('LBL_LOAD_LIST', 'SubsList')."' type='button' name='button' onclick='loadSubsList(\"$related_module\",\"$id\")'>";
		$button .= '&nbsp;&nbsp;&nbsp;&nbsp';
		$button .= "<span id='autoSync{$related_module}Info' style=\"display:none\">".'{"slid":"'.$id.'","autoSync":"'.$autoSync.'","filter":"'.$filter.'"}</span>';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			$wfs = '';
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$wfs = new VTWorkflowManager($adb);
				$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
				if (!$racbr || $racbr->hasRelatedListPermissionTo('select', $related_module)) {
					$button .= "<input title='" . getTranslatedString('LBL_SELECT') . ' ' . getTranslatedString($related_module, $related_module).
						"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
						"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',".
						"'width=640,height=602,resizable=0,scrollbars=0');\" value='" . getTranslatedString('LBL_SELECT') . ' '.
						getTranslatedString($related_module, $related_module) . "' style='$disabledcss' $disabled>&nbsp;";
				}
			}
// 			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
// 				if ($wfs == '') {
// 					$wfs = new VTWorkflowManager($adb);
// 					$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
// 				}
// 				if (!$racbr || $racbr->hasRelatedListPermissionTo('create',$related_module)) {
// 					$button .= "<input type='hidden' name='createmode' value='link' />" .
// 						"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString('SINGLE_'.$related_module) . "' class='crmbutton small create'" .
// 						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
// 						" value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString('SINGLE_'.$related_module, $related_module) . "'>&nbsp;";
// 				}
// 			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true') {
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
		}

		$query = 'SELECT vtiger_crmentity.* ';

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name";

		$more_relation = '';
		// Select Custom Field Table Columns if present
		if (isset($other->customFieldTable)) {
			$query .= ', '.$other->customFieldTable[0].'.*';
			$more_relation .= " INNER JOIN ".$other->customFieldTable[0]." ON ".$other->customFieldTable[0].'.'.$other->customFieldTable[1]
				." = $other->table_name.$other->table_index";
		}
		if (!empty($other->related_tables)) {
			foreach ($other->related_tables as $tname => $relmap) {
				$query .= ", $tname.*";

				// Setup the default JOIN conditions if not specified
				if (empty($relmap[1])) {
					$relmap[1] = $other->table_name;
				}
				if (empty($relmap[2])) {
					$relmap[2] = $relmap[0];
				}
				$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
			}
		}
		$query .= ', '.$other->table_name.'.*';
		$query .= " FROM $other->table_name";
		$query .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $other->table_name.$other->table_index";
		$query .= ' INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid)';
		$query .= $more_relation;
		$query .= ' LEFT  JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid';
		$query .= ' LEFT  JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid';
		$query .= " WHERE vtiger_crmentity.deleted = 0 AND (vtiger_crmentityrel.crmid = $id OR vtiger_crmentityrel.relcrmid = $id)";

		if (GlobalVariable::getVariable('Debug_RelatedList_Query', '0') == '1') {
			echo '<br>'.$query.'<br>';
		}

		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array('header'=>array(),'entries'=>array(),'navigation'=>array('',''));
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
?>
