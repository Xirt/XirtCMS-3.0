<?php

/**
 * Class for managing authentication sesssions
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2018
 * @package     XirtCMS
 */
class XCMS_Authentication {

    /**
     * Internal reference to CI
     * @var Object
     */
    private static $CI;


    /**
     * Reference used to save / retrieve user ID for authentication session
     * @var String
     */
    const REF_ID = "USER_ID";


    /**
     * Reference used to save / retrieve username for authentication session
     * @var String
     */
    const REF_NAME = "USER_NAME";


    /**
     * Reference used to save / retrieve session hash for authentication session
     * @var String
     */
    const REF_HASH = "USER_HASH";


    /**
     * Regular Expression to validate password strength
     * @var String
     */
    const PW_REGEX = "/(?=^.{6,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s)[0-9a-zA-Z!@#$%^&*()]*$/";


    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and helpers
     */
    function __construct() {

        self::$CI =& get_instance();
        self::$CI->load->helper("cookie");
        self::$CI->load->library("session");
        self::$CI->load->library("xcms_config");

    }


    /**
     * Returns the ID of the currently authenticated user
     *
     * @param   boolean     $skipAuthCheck  Toggle skipping of the authentication check
     * @return  mixed                       Returns the user ID or null if not available
     */
    public static function getUserId(bool $skipAuthCheck = false) {

        if (!$skipAuthCheck && !XCMS_Authentication::check()) {
            return null;
        }

        return self::_getUserId();

    }


   /**
     * Returns the username of the currently authenticated user
     *
     * @param   boolean     $skipAuthCheck  Toggle skipping of the authentication check
     * @return  mixed                       Returns the username or null if not available
     */
    public static function getUsername(bool $skipAuthCheck = false) {

        if (!$skipAuthCheck && !XCMS_Authentication::check()) {
            return null;
        }

        return self::_getUsername();

    }


    /**
     * Returns the UserModel for the currently authenticated user
     *
	 * @deprecated
     * @param   boolean     $skipAuthCheck  Toggle skipping of the authentication check
     * @return  mixed                       Returns the UserModel or null if not available
     */
    public static function getUserModel(bool $skipAuthCheck = false) {

        if (!$skipAuthCheck && !XCMS_Authentication::check()) {
            return null;
        }

        return self::_getUserModel();

    }


	/**
     * Returns the authorization level for the given user (or current user in case of no input)
     *
	 * @deprecated
	 * @param   object	$user 				The user for which to retrieve the authorization level
     * @return  int                       	Returns the authorization level or level '1' if not authorized
     */
    public static function getAuthorizationLevel($user = null) {

		if ($user || $user = UserHelper::getCurrentUser(true)) {
			return UserHelper::getUsergroup($user->get("usergroup_id"))->get("authorization_level");
		}

		return 1;

	}

    /**
     * Returns the ID of the currently authenticated user
     *
     * @return  mixed                       Returns the user ID or null if not available
     */
    private static function _getUserId() {

        if (!($userId = self::$CI->session->userdata(self::REF_ID))) {
            $userId = get_cookie(self::REF_ID);
        }

        return ($userId === null) ? null : intval($userId);

    }


   /**
     * Returns the username of the currently authenticated user
     *
     * @param   boolean     $skipCheck      Toggle skipping of the authentication check
     * @return  mixed                       Returns the username or null if not available
     */
    private static function _getUsername() {

        if (!($userName = self::$CI->session->userdata(self::REF_NAME))) {
            $userName = get_cookie(self::REF_NAME);
        }

        return ($userName === null) ? null : $userName;

    }


    /**
     * Returns the UserModel for the currently authenticated user
     *
     * @return  UserModel                   Returns the UserModel for current user
     */
    private static function _getUserModel() {

        self::$CI->load->helper("user");
        return UserHelper::getUser(self::_getUserId(true));

    }


   /**
    * Returns the encoded hash of the currently authenticated user
    *
    * @return   mixed                       Returns the current encoded hash or null if not available
    */
    private static function _getHash() {

        if (!($userHash = self::$CI->session->userdata(self::REF_HASH))) {
            $userHash = get_cookie(self::REF_HASH);
        }

        return ($userHash === null) ? null : $userHash;

    }


    /**
     * Checks whether the current visiter is authenticated or not
     *
     * @return  mixed                       Return true if visiter is authenticated, false otherwhise
     */
    public static function check() {

        $userData = array(
            "user_ip"      => $_SERVER["REMOTE_ADDR"],
            self::REF_ID   => self::_getUserId(),
            self::REF_NAME => self::_getUsername(),
            "secret_key"   => XCMS_Config::get("AUTH_SECRET")
        );

        $candidate = hash(XCMS_Config::get("AUTH_HASH_TYPE"), implode($userData));
        return ($candidate == self::_getHash());

    }


    /**
    * Attempts to authenticate the current visitor
    *
    * @param    UserModel   $user           The user to authenticate
    * @param    String      $password       The password of the user to authenticate
    * @param    boolean     $cookies        Toggles the use of cookies (defaults to false)
    * @return   int                         1 on success, error code otherwise
    */
    public static function create(UserModel $user, String $password, bool $cookies = false) {

        self::destroy();

        if (!($userId = self::_verify($user, $password)) || $userId < 1) {
            return $userId;
        }

        $sessionId   = $user->get("id");
        $sessionUser = self::hash($user->get("username"));
        $sessionHash = hash(XCMS_Config::get("AUTH_HASH_TYPE"), implode(array(
            "user_ip"       => $_SERVER["REMOTE_ADDR"],
            self::REF_ID    => $sessionId,
            self::REF_NAME  => $sessionUser,
            "secret_key"    => XCMS_Config::get("AUTH_SECRET")
        )));

        if ($cookies) {

            set_cookie(self::REF_ID  , $sessionId  , 31536000);
            set_cookie(self::REF_NAME, $sessionUser, 31536000);
            set_cookie(self::REF_HASH, $sessionHash, 31536000);

        }

        self::$CI->session->set_userdata(array(
            self::REF_ID   => $sessionId,
            self::REF_NAME => $sessionUser,
            self::REF_HASH => $sessionHash
        ));

        return true;

    }


    /**
     * Destroys authentication session / cookies
     *
     * @return  boolean                     Always true
     */
    public static function destroy() {

        delete_cookie(self::REF_ID);
        delete_cookie(self::REF_NAME);
        delete_cookie(self::REF_HASH);

        self::$CI->session->unset_userdata(self::REF_ID);
        self::$CI->session->unset_userdata(self::REF_NAME);
        self::$CI->session->unset_userdata(self::REF_HASH);

        return true;

    }


    /**
     * Verifies a username / password combination
     *
     * @param   String      $user           String containing the username
     * @param   String      $password       String containing the password
     * @return  int                         User ID on success, error code otherwise
     */
    public static function _verify(String $user, String $password) {

        /**************************
         * METHOD 1 :: DB Details *
         **************************/
        if (self::hash($password, $user->get("salt")) == $user->get("password")) {
            return true;
        }

        return 0;
    }


    /**
     * Generates a random hash based on the given password and salt
     *
     * @param   String      $password       The password to use for the hash
     * @param   String      $salt           The salt to use for the hash (optional)
     * @return  String                      The generated hash
     */
    public static function hash(String $password, String $salt = null) {

        $salt = $salt ? $salt : self::generateSalt();

        if (!CRYPT_BLOWFISH) {

            // Return hash using MD5 (not preferred)
            log_message("warning", "Blowfish encryption not available (using MD5 instead).");
            return crypt($password . $salt);

        }

        // Return hash using Blowfish (preferred)
        return crypt($password, "$2a$08$" . $salt . "$");

    }


    /**
     * Returns a random alphanumeric password
     *
     * @param   int         $length         The length of the password (optional, defaults 8)
     * @return  String                      The created password
     */
    public static function generatePassword(int $length = 8) {

        $charset = array();
        $charset[] = range("a", "z");
        $charset[] = range("A", "Z");
        $charset[] = range("0", "9");

        do {

            $list = array();

            for ($i = 0; $i < $length; $i++) {

                $group = $charset[array_rand($charset)];
                $list[] = $group[array_rand($group)];

            }

            $password = implode($list);

        } while (!preg_match(self::PW_REGEX, $password));

        return $password;

    }


    /**
     * Returns a random salt for secure password storage
     *
     * @return  String                      The generated salt
     */
    public static function generateSalt() {
        return substr(md5(uniqid(rand(), true)), 0, 21);
    }

}
?>