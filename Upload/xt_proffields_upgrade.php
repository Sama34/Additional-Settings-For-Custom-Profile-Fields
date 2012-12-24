<?php
/**
* This is an upgrade file for xt_proffields plugin (from v1.0 to v1.1)
*/

if(!defined('IN_MYBB')) die();

function xt_proffields_upgrade_info(){
	return array(
		'name'			=> 'Upgrade For Additional Settings For Profile Fields Plugin',
		'description'	=> '<strong style="color: red"><em>Activate this upgrade, then deactivate. You can delete this file after deactivate it.</em></strong>',
		'website'		=> 'http://mybbhacks.zingaburga.com',
		'author'		=> 'XThreads Mania',
		'authorsite'	=> 'http://mybbhacks.zingaburga.com',
		'version'		=> '1.0',
		'compatibility' => '1*',
		'guid'        	=> ''
	);
}

function xt_proffields_upgrade_activate(){
	global $db;
	$plist = $GLOBALS['cache']->read('plugins');
	if(!$plist['active']['xt_proffields']){
		global $mybb;
		$sep = $mybb->version_code >= 1500 ? '-':'/';
		flash_message('This plugin required Additional Settings For Profile Fields Plugin to be installed or actived','error');
		admin_redirect('index.php?module=config'.$sep.'plugins');
	}
	#========= v1.1 ==================#
		# Add Editable by Usergroups permission
		if(!$db->field_exists('xt_proffields_editable','profilefields')){
			$db->add_column('profilefields','xt_proffields_editable','varchar(255) NOT NULL default \'\'');
		}
	#========= v1.11 ==================#
		# Create settings group and move the settings there
		$xtpfsg = $db->fetch_field($db->simple_select('settinggroups','gid','name="xt_proffields"'),'gid');
		if(!$xtpfsg){
			$db->insert_query('settinggroups',array(
				'name'			=> 'xt_proffields',
				'title'			=> 'Additional Settings For Custom Profile Fields',
				'description'	=> 'Settings for Additional Settings For Custom Profile Fields plugin',
				'disporder'		=> '99',
				'isdefault'		=> 'no'
			));
			$gid = $db->insert_id();
			$db->insert_query('settings',array(
				'name'			=> 'xt_proffields_ucp',
				'title'			=> 'Editable by Usergroups',
				'description'	=> 'Enable the Editable by Usergroups?',
				'optionscode'	=> 'yesno',
				'value'			=> '0',
				'disporder'		=> '1',
				'gid'			=> intval($gid)
			));
			$db->insert_query('settings',array(
				'name'			=> 'xt_proffields_memprofile',
				'title'			=> 'Custom Profile Fields Block',
				'description'	=> 'Enable the Custom Profile Fields Block?',
				'optionscode'	=> 'yesno',
				'value'			=> '0',
				'disporder'		=> '2',
				'gid'			=> intval($gid)
			));
			rebuild_settings();
		}
	#========= v1.12 ==================#
		# Custom Input
		if(!$db->field_exists('xt_proffields_cinp','profilefields')){
			$db->add_column('profilefields','xt_proffields_cinp','text not null');
		}
	#========= v1.13 ==================#
		# Formatting Map List
		if(!$db->field_exists('xt_proffields_fml','profilefields')){
			$db->add_column('profilefields','xt_proffields_fml','text not null');
		}
	#========= v1.14 ==================#
		# Remove it from PM
		# Remove unused cache item
	#========= v1.15 ==================#
		# Compatibility with XThreads 1.50
	#========= v1.16 ==================#
		# Add {INPUT} element for displaying the default input
	#========= v1.17 ==================#
		# Use Formatting Map List for select input
	require_once MYBB_ROOT.'inc/plugins/xt_proffields.php';
	xt_proffields_cache();
}

function xt_proffields_upgrade_deactivate(){}
?>