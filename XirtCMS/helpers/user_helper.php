<?php

/**
 * Static utility class for XirtCMS users
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class UserHelper {

    /**
     * Sends the given password to the given user
     *
     * @param   UserModel   $user           The recipient (user) for this notification
     * @param   String      $password       The new password for the given user
     */
    public static function commmunicatePassword($user, String $password) {

        $CI = get_instance();
        $CI->load->library("email");
        $email = $CI->email->initialize(
            array("mailtype" => "html"
        ));

        // Set e-mail headers
        $email->from(XCMS_Config::get("EMAIL_SENDER_EMAIL"), XCMS_Config::get("EMAIL_SENDER_NAME"))
            ->subject("Your new password")
            ->to($user->get("email"));

        // Set e-mail content and send
        $email->message($CI->load->view('emails/reset_password.tpl', array(
            "name"     => $user->get("real_name"),
            "username" => $user->get("username"),
            "password" => $password,
            "target"   => base_url()
        ), true))->send();

    }


    /**
     * Attempts to retrieve the UserModel for the current user
     *
     * @param   boolean     $authCheck  	Toggle skipping of the authentication check
     * @return  mixed                       Returns the UserModel for the reqested user or null on failure
     */
    public static function getCurrentUser($authCheck = false) {

        $CI = get_instance();
        $CI->load->model("UserModel", false);

		if ($id = XCMS_Authentication::getUserId()) {
			return UserHelper::getUser($id);
		}

		return $authCheck ? null : new UserModel();

    }


    /**
     * Attempts to retrieve the requested UserModel (a default UserModel if no ID is given)
     *
     * @param   int         $id             The ID of the requested user (or empty for default user)
     * @return  mixed                       Returns the UserModel for reqested user or null on failure
     */
    public static function getUser(int $id = null) {

        $CI = get_instance();
        $CI->load->model("UserModel", false);

        $user = new UserModel();
        if (is_null($id) || $user->load($id)) {
            return $user;
        }

        return null;

    }


    /**
     * Returns the authorization level for the given or current user
     *
     * @param   object    	$user           The UserModel for which to retrieve the authorization level
     * @return  mixed                       Returns the requested authorization level
     */
    public static function getAuthorizationLevel($user = null) {

		$user = $user ? $user : UserHelper::getCurrentUser();
		if (($id = $user->get("usergroup_id")) && $usergroup = UserHelper::getUserGroup($id)) {
			return $usergroup->get("authorization_level");
		}

		return 1;

    }

    /**
     * Attempts to retrieve the requested UsergroupModel
     *
	 * @param	id 			$id				The id of the requested usergroup
     * @return  mixed                 		The requested UsergroupModel or null on failure
     */
    public static function getUserGroup(int $id) {

        $usergroups = UserHelper::_getUserGroups();
        if (array_key_exists($id, $usergroups)) {
			return $usergroups[$id];
		}

		return null;

    }


    /**
     * Returns list with all known UsergroupModel
     *
     * @return  Array                       All valid usergroups
     */
    private static function _getUserGroups() {

        // Prerequisites
        $CI =& get_instance();
        $CI->load->model("UsergroupsModel", false);

		// Attempt using cache (performance optimization)
        if ($usergroups = XCMS_Cache::get("usergroups")) {
            return $usergroups;
        }

		$usergroups = array();
        foreach ((new UsergroupsModel())->load()->toArray() as $usergroup) {
            $usergroups[$usergroup->get("id")] = $usergroup;
        }

        XCMS_Cache::set("usergroups", $usergroups);
        return $usergroups;

    }

}
?>