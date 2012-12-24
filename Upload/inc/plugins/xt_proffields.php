<?php
/**
* This plugin depends on XThreads.
* Most method used here uses XThreads method.
*
* XThreads official release thread:
*
*		http://mybbhacks.zingaburga.com/showthread.php?tid=288
*		Coded by: Yumi/ZiNgA BuRgA
*		@ http://mybbhacks.zingaburga.com
*/

if(!defined('IN_MYBB')) die();

function xt_proffields_info(){
	return array(
		'name'			=> 'Additional Settings For Profile Fields',
		'description'	=> '<strong style="color: red"><em><a href="http://mybbhacks.zingaburga.com/showthread.php?tid=288">XThreads</a> is required for this plugin</em></strong><br />Add some additional settings for Profile Fields.',
		'website'		=> 'http://mybbhacks.zingaburga.com',
		'author'		=> 'XThreads Mania',
		'authorsite'	=> 'http://mybbhacks.zingaburga.com',
		'version'		=> '1.17',
		'compatibility' => '1*',
		'guid'        	=> ''
	);
}

function xt_proffields_insfields(){
	return  array(
		1=>array('field'=>'xt_proffields_reg','def'=>'tinyint(1) unsigned NOT NULL default 0','type'=>'tinyint','inp'=>'yn'),
		2=>array('field'=>'xt_proffields_html','def'=>'tinyint(1) unsigned NOT NULL default 0','type'=>'tinyint','inp'=>'yn'),
		3=>array('field'=>'xt_proffields_mycode','def'=>'tinyint(1) unsigned NOT NULL default 0','type'=>'tinyint','inp'=>'yn'),
		4=>array('field'=>'xt_proffields_imgcode','def'=>'tinyint(1) unsigned NOT NULL default 0','type'=>'tinyint','inp'=>'yn'),
		5=>array('field'=>'xt_proffields_videocode','def'=>'tinyint(1) unsigned NOT NULL default 0','type'=>'tinyint','inp'=>'yn'),
		6=>array('field'=>'xt_proffields_smilies','def'=>'tinyint(1) unsigned NOT NULL default 0','type'=>'tinyint','inp'=>'yn'),
		7=>array('field'=>'xt_proffields_badwords','def'=>'tinyint(1) unsigned NOT NULL default 0','type'=>'tinyint','inp'=>'yn'),
		8=>array('field'=>'xt_proffields_regex','def'=>'varchar(255) NOT NULL default \'\'','type'=>'varchar','inp'=>'text'),
		9=>array('field'=>'xt_proffields_cinp','def'=>'text NOT NULL','type'=>'text','inp'=>'textarea'),
		10=>array('field'=>'xt_proffields_fml','def'=>'text NOT NULL','type'=>'text','inp'=>'textarea'),
		11=>array('field'=>'xt_proffields_brv','def'=>'text NOT NULL','type'=>'text','inp'=>'textarea'),
		12=>array('field'=>'xt_proffields_df','def'=>'text NOT NULL','type'=>'text','inp'=>'textarea'),
		13=>array('field'=>'xt_proffields_viewable','def'=>'varchar(255) NOT NULL default \'\'','type'=>'varchar','inp'=>'ug'),
		14=>array('field'=>'xt_proffields_editable','def'=>'varchar(255) NOT NULL default \'\'','type'=>'varchar','inp'=>'ug')
	);
}

function xt_proffields_activate(){
	global $db;
	xt_proffields_deactivate();
	foreach(xt_proffields_insfields() as $field => $desc){
		if(!$db->field_exists($desc['field'],'profilefields')) $db->add_column('profilefields',$desc['field'],$desc['def']);
	}
	xt_proffields_cache();
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
	$db->insert_query('templates',array(
		'title'		=> 'xt_proffields_reg_fields',
		'template'	=> $db->escape_string('<br />
<fieldset class="trow2">
	<legend><strong>{$lang->xt_proffields_no_req}</strong></legend>
	<table cellspacing="0" cellpadding="{$theme[\'tablespace\']}">
		{$xt_proffields_reg_fields_field}
	</table>
</fieldset>'),
		'sid'		=> -1
	));
	$db->insert_query('templates',array(
		'title'		=> 'xt_proffields_reg_fields_field',
		'template'	=> $db->escape_string('<tr>
	<td>
		{$ufid[\'name\']}
		<br />
		<span class="smalltext">{$ufid[\'description\']}</span>
	</td>
</tr>
<tr>
	<td>{$code}</td>
</tr>'),
		'sid'		=> -1
	));
	require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('member_register','#\{\$requiredfields\}#','{$requiredfields}{$xt_proffields_reg_fields}');
}

function xt_proffields_deactivate(){
	global $db;
	foreach(xt_proffields_insfields() as $field => $desc){
		if($db->field_exists($desc['field'],'profilefields')) $db->drop_column('profilefields',$desc['field']);
	}
	if(is_object($GLOBALS['cache']->handler)){
		$GLOBALS['cache']->handler->delete('xt_proffields');
	}
	$db->delete_query('datacache','title="xt_proffields"');
	if(file_exists(MYBB_ROOT.'cache/xt_proffields_evalcache.php')) @unlink(MYBB_ROOT.'cache/xt_proffields_evalcache.php');
	$db->delete_query('templates','title IN("xt_proffields_reg_fields","xt_proffields_reg_fields_field")');
	$xtpfsg = $db->fetch_field($db->simple_select('settinggroups','gid','name="xt_proffields"'),'gid');
	if($xtpfsg){
		$db->delete_query('settings','gid='.$xtpfsg);
		$db->delete_query('settinggroups','gid='.$xtpfsg);
		rebuild_settings();
	}
	require_once MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('member_register','#\{\$xt_proffields_reg_fields\}#','',0);
}

$plugins->add_hook('admin_config_profile_fields_add','xt_proffields_field');
$plugins->add_hook('admin_config_profile_fields_edit','xt_proffields_field');
function xt_proffields_field(){
	global $mybb;
	foreach(xt_proffields_insfields() as $field => $desc){
		switch ($desc['type']){
			case 'tinyint':
				$mybb->input[$desc['field']] = $mybb->input[$desc['field']] ? $mybb->input[$desc['field']] : 0;
			break;
			case 'varchar':
			case 'text':
				$mybb->input[$desc['field']] = $mybb->input[$desc['field']] ? $mybb->input[$desc['field']] : '';
			break;
		}
	}
	$GLOBALS['plugins']->add_hook('admin_formcontainer_end','xt_proffields_form');
}

$plugins->add_hook('admin_config_profile_fields_add','xt_proffields_admin_proffields_add');
function xt_proffields_admin_proffields_add(){
	control_object($GLOBALS['db'], '
		function insert_query($table, $array){
			static $done=false;
			if(!$done && $table == "profilefields"){
				$done = true;
				xt_proffields_add($array);
			}
			return parent::insert_query($table, $array);
		}
	');
}

function xt_proffields_add(&$new_profile_field){
	xt_proffields_submit($new_profile_field);
}

$plugins->add_hook('admin_config_profile_fields_edit','xt_proffields_admin_proffields_edit');
function xt_proffields_admin_proffields_edit(){
	control_object($GLOBALS['db'], '
		function update_query($table, $array, $where="", $limit="", $no_quote=false){
			static $done=false;
			if(!$done && $table == "profilefields"){
				$done = true;
				xt_proffields_edit($array);
			}
			return parent::update_query($table, $array, $where, $limit, $no_quote);
		}
	');
}

function xt_proffields_edit(&$profile_field){
	xt_proffields_submit($profile_field);
}

function xt_proffields_submit(&$fields){
	global $mybb, $db;
	$textbased = in_array($mybb->input['fieldtype'],array('text','textarea'));
	$sval = in_array($mybb->input['fieldtype'],array('text','select','radio'));
	if($textbased && $mybb->input['xt_proffields_mycode']){
		$fields_xt_proffields_imgcode = intval($mybb->input['xt_proffields_imgcode']);
		$fields_xt_proffields_videocode = intval($mybb->input['xt_proffields_videocode']);
	}else{
		$fields_xt_proffields_imgcode = 0;
		$fields_xt_proffields_videocode = 0;
	}
	$fields['xt_proffields_reg'] = $mybb->input['required'] || $mybb->input['editable'] == 0 ? 0 : intval($mybb->input['xt_proffields_reg']);
	$fields['xt_proffields_html'] = $textbased ? intval($mybb->input['xt_proffields_html']) : 0;
	$fields['xt_proffields_mycode'] = $textbased ? intval($mybb->input['xt_proffields_mycode']) : 0;
	$fields['xt_proffields_imgcode'] = $fields_xt_proffields_imgcode;
	$fields['xt_proffields_videocode'] = $fields_xt_proffields_videocode;
	$fields['xt_proffields_smilies'] = $textbased ? intval($mybb->input['xt_proffields_smilies']) : 0;
	$fields['xt_proffields_badwords'] = $textbased ? intval($mybb->input['xt_proffields_badwords']) : 0;
	$fields['xt_proffields_regex'] = $sval ? $db->escape_string(str_replace("\0",'',$mybb->input['xt_proffields_regex'])) : '';
	foreach(xt_proffields_insfields() as $xtpffields => $xtpffield){
		if($xtpffield['inp'] == 'textarea'){
			$fields[$xtpffield['field']] = $db->escape_string($mybb->input[$xtpffield['field']]);
		}
	}
	if(is_array($mybb->input['xt_proffields_viewable'])){
		$ugchecked = array();
		foreach($mybb->input['xt_proffields_viewable'] as $gid){
			$ugchecked[] = intval($gid);
		}
		$fields['xt_proffields_viewable'] = implode(',',$ugchecked);
	}else{
		$fields['xt_proffields_viewable'] = '';
	}
	if($mybb->input['editable'] == 1 && $mybb->input['required'] == 0){
		if(is_array($mybb->input['xt_proffields_editable'])){
			$ugchecked = array();
			foreach($mybb->input['xt_proffields_editable'] as $gid){
				$ugchecked[] = intval($gid);
			}
			$fields['xt_proffields_editable'] = implode(',',$ugchecked);
		}else{
			$fields['xt_proffields_editable'] = '';
		}
	}else{
		$fields['xt_proffields_editable'] = '';
	}
}

function xt_proffields_form(){
	global $form_container, $lang;
	if($form_container->_title == $lang->add_new_profile_field || $form_container->_title == $lang->edit_profile_field){
		global $mybb, $form;
		$lang->load('xt_proffields');
		foreach(xt_proffields_insfields() as $field => $desc){
			$desc_desc = $desc['field'].'_desc';
			switch ($desc['inp']){
				case 'yn':
					$form_container->output_row($lang->$desc['field'],$lang->$desc_desc,$form->generate_yes_no_radio($desc['field'],$mybb->input[$desc['field']],true,array('id'=>$desc['field'].'_yes','class'=>$desc['field']),array('id'=>$desc['field'].'_no','class'=>$desc['field'])),'row_'.$desc['field'],array(),array('id'=>'row_'.$desc['field']));
				break;
				case 'text':
					$form_container->output_row($lang->$desc['field'],$lang->$desc_desc,$form->generate_text_box($desc['field'],$mybb->input[$desc['field']],array('id'=>$desc['field'])),$desc['field'],array(),array('id'=>'row_'.$desc['field']));
				break;
				case 'textarea':
					$form_container->output_row($lang->$desc['field'],$lang->$desc_desc,$form->generate_text_area($desc['field'],$mybb->input[$desc['field']],array('id'=>$desc['field'])),$desc['field'],array(),array('id'=>'row_'.$desc['field']));
				break;
				case 'ug':
					if($mybb->request_method != 'post') $mybb->input[$desc['field']] = explode(',',$mybb->input[$desc['field']]);
					$form_container->output_row($lang->$desc['field'],$lang->$desc_desc,$form->generate_group_select($desc['field'].'[]',$mybb->input[$desc['field']],array('multiple'=>true,'size'=>5)));
				break;
			}
		}
		echo '<script type="text/javascript">
			Event.observe(window, "load", function() {
				new Peeker($("fieldtype"),$("row_xt_proffields_html"),/text/,false);
				new Peeker($("fieldtype"),$("row_xt_proffields_mycode"),/text/,false);
				new Peeker($("fieldtype"),$("row_xt_proffields_imgcode"),/text/,false);
				new Peeker($$(".xt_proffields_mycode"),$("row_xt_proffields_imgcode"),/1/,true);
				new Peeker($("fieldtype"),$("row_xt_proffields_videocode"),/text/,false);
				new Peeker($$(".xt_proffields_mycode"),$("row_xt_proffields_videocode"),/1/,true);
				new Peeker($("fieldtype"),$("row_xt_proffields_smilies"),/text/,false);
				new Peeker($("fieldtype"),$("row_xt_proffields_badwords"),/text/,false);
				new Peeker($("fieldtype"),$("row_xt_proffields_regex"),/^(text|select|radio)$/,false);
				new Peeker($("fieldtype"),$("row_xt_proffields_fml"),/^(checkbox|radio|select)$/,false);
			});
		</script>';
	}
}

$plugins->add_hook('admin_config_profile_fields_add_commit','xt_proffields_commit');
$plugins->add_hook('admin_config_profile_fields_edit_commit','xt_proffields_commit');
$plugins->add_hook('admin_config_profile_fields_delete_commit','xt_proffields_commit');
function xt_proffields_commit(){
	xt_proffields_cache();
}

$plugins->add_hook('admin_tools_cache_start','xt_proffields_rebuild');
$plugins->add_hook('admin_tools_cache_rebuild','xt_proffields_rebuild');
function xt_proffields_rebuild(){
	control_object($GLOBALS['cache'], '
		function update_xt_proffields(){
			xt_proffields_cache();
		}
	');
}

function xt_proffields_cache(){
	require_once MYBB_ROOT.'inc/xthreads/xt_phptpl_lib.php';
	$fields = array('VALUE' => null, 'RAWVALUE' => null);
	$fieldsinp = array('VALUE' => null, 'RAWVALUE' => null, 'INPUT' => null);
	$query = $GLOBALS['db']->simple_select('profilefields','*','',array('order_by'=>'disporder asc'));
	$pfieldscache = array();
	$evalcache = '';
	$sntz_fields = array('xt_proffields_html','xt_proffields_mycode','xt_proffields_imgcode','xt_proffields_videocode','xt_proffields_smilies','xt_proffields_badwords');
	$isset_fields = array('length','maxlength','required','editable','hidden','postnum','xt_proffields_reg','xt_proffields_regex');
	while($pfields = $GLOBALS['db']->fetch_array($query)){
		$evalcache .= '
function xt_proffields_fid'.$pfields['fid'].'($field,$vars=array()){
	switch($field){';
		foreach(array('xt_proffields_cinp','xt_proffields_brv','xt_proffields_df') as $field){
			if(isset($pfields[$field])){
				switch($field){
					case 'xt_proffields_brv':
						xthreads_sanitize_eval($pfields[$field]);
					break;
					case 'xt_proffields_cinp':
						xthreads_sanitize_eval($pfields[$field],$fieldsinp);
					break;
					case 'xt_proffields_df':
						xthreads_sanitize_eval($pfields[$field],$fields);
					break;					
				}
				if($pfields[$field] !== ''){
					$evalcache .= '
		case \''.$field.'\':
			return "'.$pfields[$field].'";';
				}
				if($pfields[$field]) $pfields[$field] = 1;
			}else{
				$pfields[$field] = false;
			}
		}
		$evalcache .= '
	} return \'\';
}
';
		if($pfields['xt_proffields_viewable']){
			$pfields['xt_proffields_viewable'] = array_unique(explode(',',$pfields['xt_proffields_viewable']));
		}
		if($pfields['xt_proffields_editable']){
			$pfields['xt_proffields_editable'] = array_unique(explode(',',$pfields['xt_proffields_editable']));
		}
		if($pfields['xt_proffields_fml']){
			$options = explode("\n",$pfields['xt_proffields_fml']);
			if(is_array($options)){
				$options_array = array();
				foreach($options as $key => $val){
					$option = explode('{|}',$val);
					$options_array[$option[0]] = $option[1];
				}
				$pfields['xt_proffields_fml'] = $options_array;
			}
		}
		$sntzsep = $pfields['xt_proffields_snt'] = '';
		foreach($sntz_fields as $sntz){
			$pfields['xt_proffields_snt'] .= $sntzsep.$pfields[$sntz];
			$sntzsep = '|';
			unset($pfields[$sntz]);
		}
		foreach($isset_fields as $isset){
			if(empty($pfields[$isset])) unset($pfields[$isset]);
		}
		$pfieldscache[$pfields['fid']] = $pfields;
	}
	$GLOBALS['cache']->update('xt_proffields',$pfieldscache);
	$fp = fopen(MYBB_ROOT.'cache/xt_proffields_evalcache.php','w');
	fwrite($fp, '<?php
/***
	Cache for xt_proffields
	This cache method uses XThreads cache method
***/
'.$evalcache.'
?>');
	fclose($fp);
}

$plugins->add_hook('global_start','xt_proffields_tcache');
function xt_proffields_tcache(){
	if(THIS_SCRIPT == 'member.php'){
		global $templatelist;
		if(isset($templatelist)) $templatelist .= ',xt_proffields_reg_fields,xt_proffields_reg_fields_field';
	}
}

$plugins->add_hook('member_profile_end','xt_proffields_profile');
function xt_proffields_profile(){
	global $userfields,$mybb;
	xt_proffields_load($userfields);
	if($mybb->settings['xt_proffields_memprofile'] != 0){
		global $xtpfc;
		if(!$xtpfc) $xtpfc = $GLOBALS['cache']->read('xt_proffields');
		if($xtpfc){
			global $templates,$memprofile,$theme,$lang,$profilefields;
			$customfields = $profilefields = '';
			$bgcolor = 'trow1';
			foreach($xtpfc as $uf => $customfield){
				if(xthreads_user_in_groups($customfield['xt_proffields_viewable']) && $customfield['hidden'] == 0){
					$field = 'fid'.$customfield['fid'];
					$customfieldval = xt_proffields_disp($customfield,$userfields[$field]);
					$customfield['name'] = htmlspecialchars_uni($customfield['name']);
					if($mybb->settings['xt_proffields_ucp'] != 0){
						if(xt_proffields_user_in_groups($customfield['xt_proffields_editable'],$memprofile) && $customfieldval){
							eval('$customfields .= "'.$templates->get('member_profile_customfields_field').'";');
						}
					}else{
						eval('$customfields .= "'.$templates->get('member_profile_customfields_field').'";');
					}
					$bgcolor = alt_trow();
				}
			}
			if($customfields) eval('$profilefields = "'.$templates->get('member_profile_customfields').'";');
		}
	}
}

$plugins->add_hook('memberlist_user','xt_proffields_memberlist');
function xt_proffields_memberlist(&$user){
	xt_proffields_load($user);
}

$plugins->add_hook('showthread_start','xt_proffields_showthread');
$plugins->add_hook('newreply_start','xt_proffields_showthread');
$plugins->add_hook('newreply_do_newreply_end','xt_proffields_showthread');
function xt_proffields_showthread(){
	control_object($GLOBALS['templates'], '
		function get($title, $eslashes=1, $htmlcomments=1){
			if($title == \'postbit_author_user\'){
				xt_proffields_showthread_postbit();
			}
			return parent::get($title, $eslashes, $htmlcomments);
		}
	');
}

function xt_proffields_showthread_postbit(){
	xt_proffields_load($GLOBALS['post'],1);
}

$plugins->add_hook('datahandler_user_validate','xt_proffields_validate');
function xt_proffields_validate(&$pf){
	global $xtpfc;
	if(!$xtpfc) $xtpfc = $GLOBALS['cache']->read('xt_proffields');
	if($xtpfc){
		global $lang;
		if(!$lang->xt_proffields_error) $lang->load('xt_proffields');
		foreach($xtpfc as $pfields => $pfval){
			$upf = $GLOBALS['mybb']->input['profile_fields']['fid'.$pfval['fid']];
			$upfname = htmlspecialchars_uni($pfval['name']);
			if($upf && $pfval['xt_proffields_regex'] && !preg_match('~'.str_replace('~','\\~',$pfval['xt_proffields_regex']).'~si',$upf)){
				$lang->xt_proffields_error = $lang->sprintf($lang->xt_proffields_error,$upfname);
				$pf->set_error($lang->xt_proffields_error);
			}
		}
	}
}

$plugins->add_hook('member_register_start','xt_proffields_regstart');
function xt_proffields_regstart(){
	global $xtpfc;
	if(!$xtpfc) $xtpfc = $GLOBALS['cache']->read('xt_proffields');
	if($xtpfc){
		global $templates,$mybb,$xt_proffields_reg_fields,$theme,$lang,$errors,$xtpf_inp,$xtpf_data;
		$xt_proffields_reg_fields_field = '';
		foreach($xtpfc as $uf => $ufid){
			if($ufid['xt_proffields_reg']){
				$code = '';
				$code = xt_proffields_inp($ufid,$user,$errors,$vars);
				if(!$ufid['xt_proffields_cinp']){
					eval('$xt_proffields_reg_fields_field .= "'.$templates->get('xt_proffields_reg_fields_field').'";');
				}else{
					$xtpf_inp['fid'.$ufid['fid']] = xt_proffields_cinp($ufid,$vars);
				}
			}
		}
		if($xt_proffields_reg_fields_field){
			if(!$lang->xt_proffields_no_req) $lang->load('xt_proffields');
			eval('$xt_proffields_reg_fields = "'.$templates->get('xt_proffields_reg_fields').'";');
		}
	}
}

$plugins->add_hook('member_register_end','xt_proffields_regend');
function xt_proffields_regend(){
	global $xtpfc;
	if(!$xtpfc) $xtpfc = $GLOBALS['cache']->read('xt_proffields');
	if($xtpfc){
		global $lang;
		if(!$lang->xt_proffields_error_js) $lang->load('xt_proffields');
		$xt_proffields_validator = '';
		foreach($xtpfc as $uf => $ufid){
			if($ufid['required'] && $ufid['xt_proffields_regex']){
				$reg = strtr($ufid['xt_proffields_regex'],array('\\'=>'\\\\','\''=>'\\\''));
				$xt_proffields_validator .= "\tregValidator.register('fid".$uf."','regexp',{match_field:'fid".$uf."',regexp:'".$reg."', failure_message:'{$lang->xt_proffields_error_js}'});\n";
			}
		}
		if($xt_proffields_validator) $GLOBALS['validator_extra'] .= $xt_proffields_validator;
	}
}

$plugins->add_hook('usercp_profile_end','xt_proffields_ucpend');
function xt_proffields_ucpend(){
	if($GLOBALS['mybb']->settings['xt_proffields_ucp'] != 0){
		global $xtpfc;
		if(!$xtpfc) $xtpfc = $GLOBALS['cache']->read('xt_proffields');
		if($xtpfc){
			global $templates,$mybb,$customfields,$lang,$theme,$user,$errors,$xtpf_inp,$xtpf_data;
			$altbg = 'trow1';
			$xtpf_inp = array();
			$customfields = '';
			foreach($xtpfc as $uf => $profilefield){
				if($profilefield['editable'] == 1){
					$code = '';
					if($profilefield['postnum'] && $profilefield['postnum'] > $user['postnum']){
						continue;
					}
					if(!$profilefield['required']){
						if(xthreads_user_in_groups($profilefield['xt_proffields_editable'])){
							$code = xt_proffields_inp($profilefield,$user,$errors,$vars);
							if(!$profilefield['xt_proffields_cinp']){
								eval('$customfields .= "'.$templates->get('usercp_profile_customfield').'";');
							}else{
								$xtpf_inp['fid'.$profilefield['fid']] = xt_proffields_cinp($profilefield,$vars);
							}
						}
					}
					$altbg = alt_trow();
				}
			}
			if($customfields) eval('$customfields = "'.$templates->get('usercp_profile_profilefields').'";');
		}
	}
}

function xt_proffields_inp(&$pa,&$user,&$errors,&$vars=array()){
	global $mybb,$xtpf_data;
	$pa['type'] = htmlspecialchars_uni($pa['type']);
	$pa['name'] = htmlspecialchars_uni($pa['name']);
	$pa['description'] = htmlspecialchars_uni($pa['description']);
	$thing = explode("\n",$pa['type'],'2');
	$type = $thing[0];
	$options = $thing[1];
	$field = 'fid'.$pa['fid'];
	$field_id = ' id="xtpf_'.$field.'"';
	$xtpf_data = $pa;
	$select = '';
	if($errors){
		$userfield = $mybb->input['profile_fields'][$field];
	}else{
		$userfield = $user[$field];
	}
	switch($type){
		case 'multiselect';
			if($errors){
				$useropts = $userfield;
			}else{
				$useropts = explode("\n",$userfield);
			}
			if(is_array($useropts)){
				foreach($useropts as $key => $val){
					$val = htmlspecialchars_uni($val);
					$seloptions[$val] = $val;
				}
			}
			$expoptions = explode("\n",$options);
			if(is_array($expoptions)){
				foreach($expoptions as $key => $val){
					$val = trim($val);
					$val = str_replace("\n","\\n",$val);			
					$sel = '';
					if($val == $seloptions[$val]) $sel = ' selected="selected"';
					$select .= '<option value="'.$val.'"'.$sel.'>'.$val.'</option>'."\n";
				}
				if(!$pa['length']) $pa['length'] = 3;
				$vars['INPUT'] = $code = '<select'.$field_id.' name="profile_fields['.$field.'][]" size="'.$pa['length'].'" multiple="multiple">'.$select.'</select>';
			}
		break;
		case 'select';
			$expoptions = explode("\n",$options);
			if(is_array($expoptions)){
				foreach($expoptions as $key => $val){
					$value = $sel = '';
					$val = trim($val);
					$val = str_replace("\n","\\n",$val);
					if($pa['xt_proffields_fml'][$val]){
						$value = $pa['xt_proffields_fml'][$val];
					}else{
						$value = $val;
					}
					if($val == htmlspecialchars_uni($userfield)) $sel = ' selected="selected"';
					$select .= '<option value="'.$val.'"'.$sel.'>'.$value.'</option>';
				}
				if(!$pa['length']) $pa['length'] = 1;
				$vars['INPUT'] = $code = '<select'.$field_id.' name="profile_fields['.$field.']" size="'.$pa['length'].'">'.$select.'</select>';
			}
		break;
		case 'radio';
			$expoptions = explode("\n",$options);
			if(is_array($expoptions)){
				$vars['VALUE$'] = array();
				foreach($expoptions as $key => $val){
					$checked = $value = $fieldkey = '';
					if($pa['xt_proffields_fml'][$val]){
						$value = $pa['xt_proffields_fml'][$val];
					}else{
						$value = $val;
					}
					if($val == $userfield) $checked = ' checked="checked"';
					$fieldkey = $key+1;
					$each_code .= '<table border="0"><tr><td style="vertical-align: middle"><input id="'.$field.'_'.$fieldkey.'" type="radio" class="radio" name="profile_fields['.$field.']" value="'.$val.'"'.$checked.' /></td><td style="vertical-align: middle"><label for="'.$field.'_'.$fieldkey.'"><span class="smalltext">'.$value.'</span></label></td></tr></table>';
					$vars['VALUE$'][$fieldkey] = '<table border="0"><tr><td style="vertical-align: middle"><input id="'.$field.'_'.$fieldkey.'" type="radio" class="radio" name="profile_fields['.$field.']" value="'.$val.'"'.$checked.' /></td><td style="vertical-align: middle"><label for="'.$field.'_'.$fieldkey.'">'.$value.'</label></td></tr></table>';
				}
				$vars['INPUT'] = $code = $each_code;
			}
		break;
		case 'checkbox';
			if($errors){
				$useropts = $userfield;
			}else{
				$useropts = explode("\n",$userfield);
			}
			if(is_array($useropts)){
				foreach($useropts as $key => $val){
					$seloptions[$val] = $val;
				}
			}
			$expoptions = explode("\n",$options);
			if(is_array($expoptions)){
				$vars['VALUE$'] = array();
				foreach($expoptions as $key => $val){
					$checked = $value = $fieldkey = '';
					if($pa['xt_proffields_fml'][$val]){
						$value = $pa['xt_proffields_fml'][$val];
					}else{
						$value = $val;
					}
					if($val == $seloptions[$val]) $checked = ' checked="checked"';
					$fieldkey = $key+1;
					$each_code .= '<table border="0"><tr><td style="vertical-align: middle"><input id="'.$field.'_'.$fieldkey.'" type="checkbox" class="checkbox" name="profile_fields['.$field.'][]" value="'.$val.'"'.$checked.' /></td><td style="vertical-align: middle"><label for="'.$field.'_'.$fieldkey.'">'.$value.'</label></td></tr></table>';
					$vars['VALUE$'][$fieldkey] = '<table border="0"><tr><td style="vertical-align: middle"><input id="'.$field.'_'.$fieldkey.'" type="checkbox" class="checkbox" name="profile_fields['.$field.'][]" value="'.$val.'"'.$checked.' /></td><td style="vertical-align: middle"><label for="'.$field.'_'.$fieldkey.'">'.$value.'</label></td></tr></table>';
				}
				$vars['INPUT'] = $code = $each_code;
			}
		break;
		case 'textarea';
			$value = htmlspecialchars_uni($userfield);
			$vars = array('VALUE'=>$value);
			$vars['INPUT'] = $code = '<textarea'.$field_id.' name="profile_fields['.$field.']" rows="6" cols="30" style="width: 95%">'.$value.'</textarea>';
		break;
		default:
			$value = htmlspecialchars_uni($userfield);
			$vars = array('VALUE'=>$value);
			$maxlength = '';
			if($pa['maxlength'] > 0) $maxlength = ' maxlength="'.$pa['maxlength'].'"';
			$vars['INPUT'] = $code = '<input'.$field_id.' type="text" name="profile_fields['.$field.']" class="textbox" size="'.$pa['length'].'"'.$maxlength.' value="'.$value.'" />';
		break;
	}
	return $code;
}

function xt_proffields_cinp(&$pa,&$vars=array()){
	require_once MYBB_ROOT.'cache/xt_proffields_evalcache.php';
	$evalfunc = 'xt_proffields_fid'.$pa['fid'];
	if(function_exists($evalfunc)){
		$msg = $evalfunc('xt_proffields_cinp',$vars);
		return $msg;
	}
}

function xt_proffields_parse(&$pa,&$v){
	global $parser;
	if(!is_object($parser)){
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	$sntz = explode('|',$pa['xt_proffields_snt']);
	$parser_options = array(
		'allow_html' => $sntz[0],
		'allow_mycode' => $sntz[1],
		'allow_imgcode' => $sntz[2],
		'allow_videocode' => $sntz[3],
		'allow_smilies' => $sntz[4],
		'filter_badwords' => $sntz[5]
	);
	return $parser->parse_message($v,$parser_options);
}
function xt_proffields_disp(&$pa,&$v){
	$evalfunc = 'xt_proffields_fid'.$pa['fid'];
	if(!function_exists($evalfunc)) return;
	if(xthreads_user_in_groups($pa['xt_proffields_viewable'])){
		global $xtpf_data;
		$xtpf_data = $pa;
		if(trim($v) === '' || $v === null){
			$msg = $evalfunc('xt_proffields_brv');
		}else{
			$exp = explode("\n",$pa['type'],'2');
			$type = $exp[0];
			switch($type){
				case 'text':
				case 'textarea':
					$vars = array('VALUE' => xt_proffields_parse($pa,$v));
					if($pa['xt_proffields_regex']){
						if(preg_match('~'.str_replace('~','\\~',$pa['xt_proffields_regex']).'~si',$v,$match)){
							$vars['VALUE$'] = array();
							foreach($match as $i => &$val) {
								$vars['VALUE$'][$i] = xt_proffields_parse($pa,$val);
							}
						}
					}
				break;
				case 'checkbox':
					$values = explode("\n",$v);
					$vars['VALUE$'] = array();
					foreach($values as $key => $val){
						$value = '';
						if($pa['xt_proffields_fml'][$val]){
							$value = $pa['xt_proffields_fml'][$val];
						}else{
							$value = $val;
						}
						$fieldkey = $key+1;
						$vars['VALUE$'][$fieldkey] = $value;
					}
				break;
				case 'select':
				case 'radio':
					if($pa['xt_proffields_fml'][$v]){
						$value = trim($pa['xt_proffields_fml'][$v]);
						if($value === '' && $pa['xt_proffields_brv']) $value = $evalfunc('xt_proffields_brv');
					}else{
						$value = $v;
					}
					$vars = array('VALUE'=>$value);
				break;
				default:
					$vars = array('VALUE'=>xt_proffields_parse($pa,$v));
				break;
			}
			$msg = $evalfunc('xt_proffields_df',$vars);
		}
		return $msg;
	}
}

function xt_proffields_load(&$ref,$html=0){
	global $xtpf,$xtpfc;
	if(!$xtpfc) $xtpfc = $GLOBALS['cache']->read('xt_proffields');
	require_once MYBB_ROOT.'cache/xt_proffields_evalcache.php';
	$xtpf = $ref;
	if($xtpf){
		foreach($xtpf as $uf => &$vf){
			if(substr($uf,0,3) == 'fid' && ctype_digit(substr($uf,3))){
				$ufid = substr($uf,3);
				$val = $html ? htmlspecialchars_decode($vf) : $vf;
				$xtpf[$uf] = xt_proffields_disp($xtpfc[$ufid],$val);
			}
		}
	}
}

// This function is copied from XThreads's xthreads_user_in_groups(&$gids) function.
function xt_proffields_user_in_groups(&$gids,&$user){
	if(empty($gids)) return true;
	static $ingroups = null;
	if(!isset($ingroups))
		$ingroups = xthreads_get_user_usergroups($user);
	foreach($gids as $gid)
		if(isset($ingroups[$gid]))
			return true;
	return false;
}
?>