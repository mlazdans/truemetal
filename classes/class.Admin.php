<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

require_once('../classes/class.Permission.php');
class Admin
{
	var $permissions = array();

	function Admin($module_name)
	{
		global $_USER;

		$perm = new Permission;
		$this->permissions = $perm->user_permissions($_USER['user_login'], $module_name);
	} // Admin

	function CheckPermissions($permissions = array())
	{
		if(is_array($permissions)) {
			foreach($permissions as $perm)
				if(in_array($perm, $this->permissions))
					return true;
		} else
			if(in_array($permissions, $this->permissions))
				return true;

		$this->error_msg = ACCESS_DENIED;
		return false;
	} // CheckPermissions

}
