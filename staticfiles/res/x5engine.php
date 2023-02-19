<?php

/**
 * This file contains all the classes used by the PHP code created by WebSite X5
 *
 * @category  X5engine
 * @package   X5engine
 * @copyright 2013 - Incomedia Srl
 * @license   Copyright by Incomedia Srl http://incomedia.eu
 * @version   WebSite X5 START 13.0.0
 * @link      http://websitex5.com
 */

@session_start();


/**
 * Blog class
 * @access public
 */
class L10n
{
	private $l10n = array();

	/**
	 * Create a new localization object
	 * @param array $l10n The localizations array
	 */
	public function __construct($l10n)
	{
		$this->l10n = $l10n;
	}

	/**
	 * Get a localization string.
	 * The string is taken from the ones specified at step 1 of WSX5
	 *
	 * @method l10n
	 * 
	 * @param {string} $id      The localization key
	 * @param {string} $default The default string
	 * 
	 * @return {string}         The localization
	 */
	public function get($id, $default = "")
	{
	    if (!isset($this->l10n[$id]))
	        return $default;

	    return $this->l10n[$id];
	}
}




/**
 * Create the required instanced basing on the configuration setup by the user
 */
class Configuration
{

    static private $analytics = false;
    static private $blog = false;
    static private $cart = false;
    static private $controlPanel = false;
    static private $privateArea = false;
    static private $l10n = false;

    static public function getAnalytics()
    {
        global $imSettings;

        if (!isset($imSettings['analytics']) || $imSettings['analytics']['type'] != 'wsx5analytics') {
            return null;
        }

        if (self::$analytics == false) {
            $prefix = $imSettings['analytics']['database']['table'];
            $dbconf = getDbData($imSettings['analytics']['database']['id']);
            self::$analytics =  new Analytics($dbconf['host'], $dbconf['user'], $dbconf['password'], $dbconf['database'], $prefix);
        }

        return self::$analytics;
    }

    static public function getBlog()
    {
        if (self::$blog == false) {
            self::$blog = new imBlog();
        }
        return self::$blog;
    }

    static public function getCart()
    {
        global $imSettings;

        if (self::$cart == false) {
            self::$cart = new ImCart();
        }

        return self::$cart;
    }

    static public function getControlPanel()
    {
        global $imSettings;

        $icon = "";
        if (isset($imSettings['admin']['icon'])) {
            $icon = $imSettings['admin']['icon'];
        }
        else if (isset($imSettings['general']['icon'])) {
            $icon = $imSettings['general']['icon'];
        }
        // Try to transform the url to a relative one
        $icon = str_replace($imSettings['general']['url'], "", $icon);
        // Prepend the logo icon with the correct path to root if it's not absolute
        if (strlen($icon) && substr($icon, 0, 7) != "http://" && substr($icon, 0, 8) != "https://") {
            $icon = "../" . trim($icon, "/");
        }

        if (self::$controlPanel == false) {
            self::$controlPanel = new ControlPanel(
                isset($imSettings['general']['sitename']) ? $imSettings['general']['sitename'] : "",
                $imSettings['general']['url'],
                $icon,
                isset($imSettings['admin']['theme']) ? $imSettings['admin']['theme'] : "orange"
            );
        }

        return self::$controlPanel;
    }

    static public function getPrivateArea()
    {
        global $imSettings;

        if (!self::$privateArea) {
            self::$privateArea = new imPrivateArea();
            if (isset($imSettings['access']['dbid'])) {
                $db = getDbData($imSettings['access']['dbid']);
                self::$privateArea->setDbData($db['host'], $db['user'], $db['password'], $db['database'], $imSettings['access']['dbtable']);
            }
        }
        return self::$privateArea;
    }

    /**
     * Load a dynamic object starting from its ID
     * @param  String $id        The dynamic object id
     * @return DynamicObject     The dynamic object
     */
    static public function getDynamicObject($id)
    {
        global $imSettings;

        $data = false;

        if (isset($imSettings['dynamicobjects']['pages'][$id])) {
            $data = $imSettings['dynamicobjects']['pages'][$id];
        } else if (isset($imSettings['dynamicobjects']['template'][$id])) {
            $data = $imSettings['dynamicobjects']['template'][$id];
        }

        if (!is_array($data)) {
            return null;
        }

        $dynObj = new DynamicObject($id);
        $dynObj->setDefaultText(str_replace(array("\n", "\r"), array("<br />", ""), $data['defaultContent']));

        if (isset($data['dbid'])) {
            $db = getDbData($data['dbid']);
            $dynObj->loadFromDb($db['host'], $db['user'], $db['password'], $db['database'], $data['dbtable']);
        } else if (isset($data['subfolder'])) {
            $dynObj->loadFromFile(pathCombine(array($imSettings['general']['public_folder'], $data['subfolder'])));
        }

        return $dynObj;
    }


    /**
     * Get the localization object
     *
     * @return L10n
     */
    static public function getLocalizations()
    {
        global $l10n;

        if (self::$l10n === false) {
            self::$l10n = new L10n($l10n);
        }

        return self::$l10n;
    }

    /**
     * Get the configuration array
     */
    static public function getSettings()
    {
        global $imSettings;

        return $imSettings;
    }
}


/**
 * x5Captcha handling class
 * @access public
 */
class X5Captcha {

    private $nameList;
    private $charList;

    /**
     * Build a new captcha class
     * @param {Array} $nameList
     * @param {Array} $charList
     */
    function __construct($nameList, $charList) {
        $this->nameList = $nameList;
        $this->charList = $charList;
    }

    /**
     * Show the captcha chars
     */
    function show($sCode)
    {
        $text = "<!DOCTYPE HTML>
            <html>
          <head>
          <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
          <meta http-equiv=\"pragma\" content=\"no-cache\">
          <meta http-equiv=\"cache-control\" content=\"no-cache, must-revalidate\">
          <meta http-equiv=\"expires\" content=\"0\">
          <meta http-equiv=\"last-modified\" content=\"\">
          </head>
          <body style=\"margin: 0; padding: 0; border-collapse: collapse;\">";

        for ($i = 0; $i < strlen($sCode); $i++) {
            $text .= "<img style=\"margin:0; padding:0; border: 0; border-collapse: collapse; width: 24px; height: 24px; position: absolute; top: 0; left: " . (24 * $i) . "px;\" src=\"imcpa_".$this->nameList[substr($sCode, $i, 1)].".gif\" width=\"24\" height=\"24\">";
        }

        $text .= "</body></html>";

        return $text;
    }

    /**
     * Check the sent data
     * @param {String} code The correct code
     * @param {String} ans  The user's answer
     */
    function check($code, $ans)
    {
        if ($ans == "") {
            return '-1';
        }
        for ($i = 0; $i < strlen($code); $i++) {
            if ($this->charList[substr(strtoupper($code), $i, 1)] != substr(strtoupper($ans), $i, 1)) {
                return '-1';
            }
        }
        return '0';
    }
}


/**
 * reCaptcha handling class
 * @access public
 */
class ReCaptcha {

    private $secretKey;

    /**
     * Build a new captcha class
     * @param {String} $secretKey
     */
    function __construct($secretKey) {
        $this->secretKey = $secretKey;
    }

    /**
     * Check the response
     * @param $response The response to be checked
     */
    function check($response)
    {
        // Create the POST data
        $post = "secret=" . urlencode($this->secretKey) . "&response=" . urlencode($response);
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $post .= "&remoteip=" . urldecode($_SERVER['HTTP_X_FORWARDED_FOR']);
        }
        else if (isset($_SERVER['REMOTE_ADDR'])) {
            $post .= "&remoteip=" . urldecode($_SERVER['REMOTE_ADDR']);   
        }

        // Use curl instead of file_get_contents (which can be blocked)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}





















/**
 * Blog class
 * @access public
 */
class ImGuestbook
{
	/**
	 * Get all the comments from all the guestbooks in the current website
	 *
	 * @param String $from       The start date (can be empty)
	 * @param String $to         The end date (can be empty)
	 * @param Boolean $approved  True to get only the approved comments, false to the only the ones waiting for validation
	 * 
	 * @return Array
	 */
	static public function getAllComments($from = "", $to = "", $approved = true)
	{
		global $imSettings;

		$commentsArray = array();
		foreach ($imSettings['guestbooks'] as $gb) {
			$comments = new ImTopic($gb['id'], "../", "index.php?id=" . $gb['id']);
	            $comments->loadXML($gb['folder']);
	        foreach ($comments->getComments($from, $to, $approved) as $comment) {
	        	$comment['title'] = "";
	            $commentsArray[] = $comment;
	        }
		}
		usort($commentsArray, array("ImTopic", "compareCommentsArray"));
		return $commentsArray;
	}
}



/**
 * @summary
 * Provides a set of useful methods for managing the private area, the users and the accesses.
 * To use it, you must include __x5engine.php__ in your code.
 *
 * @description Create a new ImDb Object
 *
 * @class
 * @constructor
 */
class imPrivateArea
{
    public $admin_email;

    private $session_type;
    private $session_email;
    private $session_real_name;
    private $session_first_name;
    private $session_last_name;
    private $session_uid;
    private $session_gids;
    private $session_page;
    private $cookie_name;
    private $salt;

    function __construct()
    {
        $imSettings = Configuration::getSettings();

        $this->session_type       = "im_access_utype";
        $this->session_email      = "im_access_email";
        $this->session_real_name  = "im_access_real_name";
        $this->session_first_name = "im_access_first_name";
        $this->session_last_name  = "im_access_last_name";
        $this->session_page       = "im_access_request_page";
        $this->session_uid        = "im_access_uid";
        $this->session_gids       = "im_access_gids";
        $this->cookie_name        = "im_access_cookie_uid";
        $this->salt               = $imSettings['general']['salt'];
    }

    /**
     * Login a user with username and password
     *
     * @param {string} $email Email (or username on fallback)
     * @param {string} $pwd   Password
     *
     * @return {int} An error code:
     *                  -5 if the user email is not validated,
     *                  -2 if the username or password are invalid,
     *                  -1 if there's a db error,
     *                  0 if the process exits correctly
     */
    public function login($email, $pwd)
    {
        $imSettings = Configuration::getSettings();

        if (!strlen($email) || !strlen($pwd))
            return -2;

        // Check if the user exists in the hardcoded file
        if (isset($imSettings['access']['users'][$email]) && $imSettings['access']['users'][$email]['password'] == $pwd) {
            $this->_setSession(
                "1",
                $imSettings['access']['users'][$email]['id'],
                $imSettings['access']['users'][$email]['groups'],
                $email,
                $imSettings['access']['users'][$email]['firstname'],
                $imSettings['access']['users'][$email]['lastname']
            );
            return 0;
        }
        return -2;
    }

    /**
     * Logout a user
     *
     * @return {Void}
     */
    public function logout()
    {
        $_SESSION[$this->session_type]  = "";
        $_SESSION[$this->session_email] = "";
        $_SESSION[$this->session_uid]   = "";
        $_SESSION[$this->session_page]  = "";
        $_SESSION[$this->session_gids]  = array();
        $_SESSION['HTTP_USER_AGENT']    = "";
        @setcookie($this->cookie_name, "", time() - 3600, "/");
        $_COOKIE[$this->cookie_name]    = "";
    }

    /**
     * Save the current page as the referer
     *
     * @param {String} $page The page to store
     *
     * @return {Void}
     */
    public function savePage($page = "")
    {
        $imSettings = Configuration::getSettings();
        $url = strlen($page) ? $page : $_SERVER['REQUEST_URI'];
        $_SESSION[$this->session_page] = $this->_encode($url, $this->salt);
    }

    /**
     * Clear the current saved page
     */
    public function clearSavedPage()
    {
        $_SESSION[$this->session_page] = "";
    }

    /**
     * Return the referer page name (the one which caused the user to land on the login page).
     *
     * @method getSavedPage
     *
     * @return {mixed} The name of the page or false if no referer is available.
     */
    public function getSavedPage()
    {
        $imSettings = Configuration::getSettings();
        if (isset($_SESSION[$this->session_page]) && $_SESSION[$this->session_page] != "")
            return $this->_decode($_SESSION[$this->session_page], $this->salt);
        return false;
    }

    /**
     * Use whoIsLogged instead
     *
     * @deprecated
     *
     * @return {mixed}
     */
    public function who_is_logged()
    {
        return $this->whoIsLogged();
    }

    /**
     * Get an array of data about the logged user
     *
     * @return {mixed} An array containing the data of the current logged user or false if no user is logged.
     */
    public function whoIsLogged()
    {
        $imSettings = Configuration::getSettings();
        if (isset($_SESSION[$this->session_email]) && $_SESSION[$this->session_email] != "" && isset($_SESSION[$this->session_email])) {
            $email = $this->_decode($_SESSION[$this->session_email], $this->salt);
            $firstname = trim($this->_decode($_SESSION[$this->session_first_name], $this->salt));
            $lastname = trim($this->_decode($_SESSION[$this->session_last_name], $this->salt));
            $uid = isset($_SESSION[$this->session_uid]) && $_SESSION[$this->session_uid] != "" ? $this->_decode($_SESSION[$this->session_uid], $this->salt) : "";
            $groups = isset($_SESSION[$this->session_gids]) ? $_SESSION[$this->session_gids] : "";
            return array(
                "email"     => $email,
                "uid"       => $uid,
                "firstname" => $firstname,
                "lastname"  => $lastname,
                "groups"    => $groups,
                // Compatibility mode for the old code snippets
                "realname"  => strlen($firstname) || strlen($lastname) ? trim($firstname . " " . $lastname) : $email,
                "username"  => $email
            );
        }
        return false;
    }

    /**
     * Check if the logged user can access to a specific page.
     * The page is provided using its page id.
     *
     * @param {int} $page The page id. You can retrieve the page id from the file x5settings.php.
     *
     * @return {int} 0 if the current user can access the page,
     *                 -2 if the XSS security checks are not met
     *                 -3 if the user is not logged
     *                 -4 if the user is still not validated
     *                 -8 if the user cannot access the page
     */
    public function checkAccess($page)
    {
        $imSettings = Configuration::getSettings();

        //
        // The session can live only in the same browser
        //

        if (!isset($_SESSION[$this->session_type]) || $_SESSION[$this->session_type] == "" || !isset($_SESSION[$this->session_uid]) || $_SESSION[$this->session_uid] == "")
            return -3;

        if (!isset($_SESSION['HTTP_USER_AGENT']) || $_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'] . $this->salt))
            return -2;

        $uid = $this->_decode($_SESSION[$this->session_uid], $this->salt);
        if (!@in_array($uid, $imSettings['access']['pages'][$page]) && !@in_array($uid, $imSettings['access']['admins']))
            return -8; // The active user cannot access to this page
        return 0;
    }

    /**
     * Get the current user's landing page.
     *
     * @return {mixed} The filename of the user's landing page or false if the
     * user is not logged or has no landing page.
     */
    public function getLandingPage()
    {
        $imSettings = Configuration::getSettings();
        if (!isset($_SESSION[$this->session_type]) || !isset($_SESSION[$this->session_email]) || $_SESSION[$this->session_email] === '' || !isset($_SESSION[$this->session_uid]) || $_SESSION[$this->session_uid] === '')
            return false;


        // WSX5-845: The registration page can be activated or deactivated
        // The following may return boolean value 'false' if the user has no
        // active landing page
        $session_email = $this->_decode($_SESSION[$this->session_email], $this->salt);
        if (isset($imSettings['access']['users'][$session_email])) {
            return $imSettings['access']['users'][$session_email]['page'];
        }
        return false;
    }

    /**
     * Convert a status code to a text message
     *
     * @param {int} $code The error code
     *
     * @return {string} The text message related to the provided error code
     */
    public function messageFromStatusCode($code)
    {
        $imSettings = Configuration::getSettings();

        switch ($code) {

            // Error
            case -9 :
            $reason = l10n("form_password", "The password entered must comply with the following rules:");
            $reason = $reason . "<br>" . str_replace("{0}", $imSettings['password_policy']['minimum_characters'], l10n("form_password_length", "- at least {0} characters"));
            if($imSettings['password_policy']['include_uppercase']){
                $reason = $reason . "<br>" . l10n("form_password_upper", "- at least one capital letter (A-Z)");
            }
            if($imSettings['password_policy']['include_numeric']){
                $reason = $reason . "<br>" . l10n("form_password_numeric", "- at least one number (0-9)");
            }
            if($imSettings['password_policy']['include_special']){
                $reason = $reason . "<br>" . l10n("form_password_special", "- at least one special character (!@#$%&*()<>?)");   
            }
            return $reason;
            case -8 : return l10n("private_area_account_not_allowed", "Your account is not allowed to access the selected page");
            case -7 : return l10n("private_area_lostpassword_error", "We cannot find your data.");
            case -6 : return l10n("private_area_user_already_exists", "The user already exists.");
            case -5 : return l10n("private_area_not_validated", "Your account is not yet validated.");
            case -4 : return l10n("private_area_waiting", "Your account is not yet active.");
            //case -3 : return l10n("private_area_not_allowed", "A login is required to access this page.");
            case -2 : return l10n("private_area_login_error", "Wrong username or password.");
            case -1 : return l10n("private_area_generic_error", "Generic error.");

            // Success
            case 1  : return l10n('private_area_login_success', "Login succesful.");
            case 2  : return l10n('private_area_validation_sent', 'We sent you a validation email.');
            case 3  : return l10n('private_area_registration_success', 'You are now registered.');
            case 4  : return l10n('private_area_lostpassword_success', 'We sent you an email with your password.');

            default : return "";
        }
    }

    /**
     * Redirect in a session safe mode. IIS requires this.
     *
     * @ignore
     *
     * @param  {string} $to The redirect URL.
     *
     * @return {void}
     */
    public function sessionSafeRedirect($to)
    {
        exit('<!DOCTYPE html><html lang="it" dir="ltr"><head><title>Loading...</title><meta http-equiv="refresh" content="1; url=' . $to . '"></head><body><p style="text-align: center;">Loading...</p></body></html>');
    }

    /**
     * Get the data about a user.
     *
     * @param  {string} $id The username
     *
     * @return {mixed} The user's data (As associative array) or null if the user is not found.
     * The associative array contains the following keys: id, ts, ip, username, password, realname, email, key, validated, groups, hash
     */
    public function getUserByUsername($username)
    {
        $imSettings = Configuration::getSettings();

        // Search in the file
        if (isset($imSettings['access']['users'][$username])) {
            $user = $imSettings['access']['users'][$username];
            return array(
                "id"        => $user['id'],
                "ts"        => "",
                "ip"        => "",
                "email"     => $user['email'],
                // Compatibility mode for old code snippets
                "username"  => $user['email'],
                "password"  => $user['password'],
                "firstname" => $user['firstname'],
                "lastname"  => $user['lastname'],
                // Compatibility mode for old code snippets
                "realname"  => isset($user['realname']) ? $user['realname'] : trim($user['firstname'] . " " . $user['lastname']),
                "key"       => "",
                "validated" => true,
                "groups"    => $user['groups'],
                "hash"      => $this->_getUserHash($username, $user['password'])
            );
        }
        return null;
    }


    /**
     * Encode the string
     *
     * @ignore
     *
     * @param  string $string The string to encode
     * @param  $key The encryption key
     *
     * @return string    The encoded string
     */
    private function _encode($s, $k)
    {
        if (strlen($s) == 0) {
            return "";
        }

        $r = array();
        for($i = 0; $i < strlen($s); $i++) {
            $r[] = ord($s[$i]) + ord($k[$i % strlen($k)]);
        }

        // Try to encode it using base64
        if (function_exists("base64_encode") && function_exists("base64_decode")) {
            return base64_encode(implode('.', $r));
        }

        return implode('.', $r);
    }

    /**
     * Decode the string
     *
     * @ignore
     *
     * @param  string $s The string to decode
     * @param  string $k The encryption key
     *
     * @return string    The decoded string
     */
    private function _decode($s, $k)
    {
        if (strlen($s) == 0) {
            return "";
        }

        // Try to decode it using base64
        if (function_exists("base64_encode") && function_exists("base64_decode")) {
            $s = base64_decode($s);
        }

        $s = explode(".", $s);
        $r = array();
        for($i = 0; $i < count($s); $i++) {
            $r[$i] = chr($s[$i] - ord($k[$i % strlen($k)]));
        }
        return implode('', $r);
    }

    /**
     * Setup a simple session that does not allow to login but rather contains
     * some useful data about the user.
     *
     * This is used mainly to provide some informations about the user
     * once registration but BEFORE validation and login.
     * This data can be retrieve using $this->whoIsLogged();
     *
     * @ignore
     *
     * @param String $email
     * @param String $firstname
     * @param String $lastname
     */
    private function _setSimpleSession($email, $firstname, $lastname)
    {
        $_SESSION[$this->session_email]      = $this->_encode($email, $this->salt);
        $_SESSION[$this->session_first_name] = $this->_encode($firstname, $this->salt);
        $_SESSION[$this->session_last_name]  = $this->_encode($lastname, $this->salt);
    }

    /**
     * Set the session after the login
     *
     * @ignore
     *
     * @param {string} $type "0" or "1"
     * @param {string} $uid
     * @param {array}  $gids
     * @param {string} $email
     * @param {string} $firstname
     * @param {string} $lastname
     *
     * @return {Void}
     */
    private function _setSession($type, $uid, $gids, $email, $firstname, $lastname)
    {
        @session_regenerate_id();
        $this->_setSimpleSession($email, $firstname, $lastname);
        $_SESSION[$this->session_type]       = $this->_encode($type, $this->salt);
        $_SESSION[$this->session_uid]        = $this->_encode($uid, $this->salt);
        $_SESSION[$this->session_gids]       = $gids;
        $_SESSION['HTTP_USER_AGENT']         = md5($_SERVER['HTTP_USER_AGENT'] . $this->salt);
        @setcookie($this->cookie_name, $this->_encode($uid, $this->salt), 0, "/"); // Expires when the browser is closed
    }

}


/**
 * Contains the methods used by the search engine
 * @access public
 */
class imSearch {

    var $scope;
    var $page;
    var $results_per_page;

    function __construct()
    {
        $this->setScope();
        $this->results_per_page = 10;
    }

    /**
     * Loads the pages defined in search.inc.php  to the search scope
     * @access public
     */
    function setScope()
    {
        global $imSettings;
        $scope = $imSettings['search']['general']['defaultScope'];

        // Logged users can search in their private pages
        $pa = new imPrivateArea();
        if ($user = $pa->who_is_logged()) {
            foreach ($imSettings['search']['general']['extendedScope'] as $key => $value) {
                if (in_array($user['uid'], $imSettings['access']['pages'][$key]))
                    $scope[] = $value;
            }
        }

        $this->scope = $scope;
    }

    /**
     * Do the pages search
     * @access public
     * @param queries The search query (array)
     */
    function searchPages($queries)
    {
        
        global $imSettings;
        $html = "";
        $found_content = array();
        $found_count = array();

        if (is_array($this->scope)) {
            foreach ($this->scope as $filename) {
                $count = 0;
                $weight = 0;
                $file_content = @implode("\n", file($filename));
                // Replace the nonbreaking space with a white space
                // to avoid that is converted to a 196+160 UTF8 char
                $file_content = str_replace("&nbsp;", " ", $file_content);
                if (function_exists("html_entity_decode"))
                    $file_content = html_entity_decode($file_content, ENT_COMPAT, 'UTF-8');

                // Remove the page menu
                while (stristr($file_content, "<div id=\"imPgMn\"") !== false) {
                    $style_start = imstripos($file_content, "<div id=\"imPgMn\"");
                    $style_end = imstripos($file_content, "</div", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove the breadcrumbs
                while (stristr($file_content, "<div id=\"imBreadcrumb\"") !== false) {
                    $style_start = imstripos($file_content, "<div id=\"imBreadcrumb\"");
                    $style_end = imstripos($file_content, "</div", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove CSS
                while (stristr($file_content, "<style") !== false) {
                    $style_start = imstripos($file_content, "<style");
                    $style_end = imstripos($file_content, "</style", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove JS
                while (stristr($file_content, "<script") !== false) {
                    $style_start = imstripos($file_content, "<script");
                    $style_end = imstripos($file_content, "</script", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove noscript tag
                while (stristr($file_content, "<noscript") !== false) {
                    $style_start = imstripos($file_content, "<noscript");
                    $style_end = imstripos($file_content, "</noscript", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove the links for accessibility
                while (stristr($file_content, "<span class=\"imHidden\"") !== false) {
                    $style_start = imstripos($file_content, "<span class=\"imHidden\"");
                    $style_end = imstripos($file_content, "</span", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Remove PHP
                while (stristr($file_content, "<?php") !== false) {
                    $style_start = imstripos($file_content, "<?php");
                    $style_end = imstripos($file_content, "?>", $style_start) !== false ? imstripos($file_content, "?>", $style_start) + 2 : strlen($file_content);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }
                $file_title = "";

                // Get the title of the page
                preg_match('/\<title\>([^\<]*)\<\/title\>/', $file_content, $matches);
                if (count($matches) > 1)
                    $file_title = $matches[1];
                else {
                    preg_match('/\<h2\>([^\<]*)\<\/h2\>/', $file_content, $matches);
                    if (count($matches) > 1)
                        $file_title = $matches[1];
                }

                if ($file_title != "") {
                    foreach ($queries as $query) {
                        $title = imstrtolower($file_title);
                        while (($title = stristr($title, $query)) !== false) {
                            $weight += 5;
                            $count++;
                            $title = substr($title, strlen($query));
                        }
                    }
                }

                // Get the keywords
                preg_match('/\<meta name\=\"keywords\" content\=\"([^\"]*)\" \/>/', $file_content, $matches);
                if (count($matches) > 1) {
                    $keywords = $matches[1];
                    foreach ($queries as $query) {
                        $tkeywords = imstrtolower($keywords);
                        while (($tkeywords = stristr($tkeywords, $query)) !== false) {
                            $weight += 4;
                            $count++;
                            $tkeywords = substr($tkeywords, strlen($query));
                        }
                    }
                }

                // Get the description
                preg_match('/\<meta name\=\"description\" content\=\"([^\"]*)\" \/>/', $file_content, $matches);
                if (count($matches) > 1) {
                    $keywords = $matches[1];
                    foreach ($queries as $query) {
                        $tkeywords = imstrtolower($keywords);
                        while (($tkeywords = stristr($tkeywords, $query)) !== false) {
                            $weight += 3;
                            $count++;
                            $tkeywords = substr($tkeywords, strlen($query));
                        }
                    }
                }

                $page_pos = strpos($file_content, "<div id=\"imContent\">") + strlen("<div id=\"imContent\">");
                $page_end = strpos($file_content, "<div id=\"imBtMn\">");
                if ($page_end == false)
                    $page_end = strpos($file_content, "</body>");
                $file_content = strip_tags(substr($file_content, $page_pos, $page_end-$page_pos));
                $t_file_content = imstrtolower($file_content);

                foreach ($queries as $query) {
                    $file = $t_file_content;
                    while (($file = stristr($file, $query)) !== false) {
                        $count++;
                        $weight++;
                        $file = substr($file, strlen($query));
                    }
                }

                if ($count > 0) {
                    $found_count[$filename] = $count;
                    $found_weight[$filename] = $weight;
                    $found_content[$filename] = $file_content;
                    if ($file_title == "")
                        $found_title[$filename] = $filename;
                    else
                        $found_title[$filename] = $file_title;
                }
            }
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_weight as $name => $weight) {
                $count = $found_count[$name];
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $title = strip_tags($found_title[$name]);
                    $file = $found_content[$name];
                    $file = strip_tags($file);
                    $ap = 0;
                    $filelen = strlen($file);
                    $text = "";
                    for ($j=0; $j<($count > 6 ? 6 : $count); $j++) {
                        $minpos = $filelen;
                        $word = "";
                        foreach ($queries as $query) {
                            if ($ap < $filelen && ($pos = strpos(strtoupper($file), strtoupper($query), $ap)) !== false) {
                                if ($pos < $minpos) {
                                    $minpos = $pos;
                                    $word = $query;
                                }
                            }
                        }
                        $prev = explode(" ", substr($file, $ap, $minpos-$ap));
                        if (count($prev) > ($ap > 0 ? 9 : 8))
                            $prev = ($ap > 0 ? implode(" ", array_slice($prev, 0, 8)) : "") . " ... " . implode(" ", array_slice($prev, -8));
                        else
                            $prev = implode(" ", $prev);
                        if (strlen($word)) {
                            $text .= $prev . "<strong>" . substr($file, $minpos, strlen($word)) . "</strong>";
                            $ap = $minpos + strlen($word);
                        }
                    }
                    $next = explode(" ", substr($file, $ap));
                    if (count($next) > 9)
                        $text .= implode(" ", array_slice($next, 0, 8)) . "...";
                    else
                        $text .= implode(" ", $next);
                    $text = str_replace("|", "", $text);
                    $text = str_replace("<br />", " ", $text);
                    $text = str_replace("<br>", " ", $text);
                    $text = str_replace("\n", " ", $text);
                    $text = str_replace("\t", " ", $text);
                    $text = trim($text);
                    $html .= "<div class=\"imSearchPageResult\"><h3><a class=\"imCssLink\" href=\"" . $name . "\">" . strip_tags($title, "<b><strong>") . "</a></h3>" . strip_tags($text, "<b><strong>") . "<div class=\"imSearchLink\"><a class=\"imCssLink\" href=\"" . $name . "\">" . $imSettings['general']['url'] . "/" . $name . "</a></div></div>\n";
                }
            }
            $html = preg_replace_callback('/\\s+/', create_function('$matches', 'return implode(\' \', $matches);'), $html);
            $html .= "<div class=\"imSLabel\">&nbsp;</div>\n";
        }

        return array("content" => $html, "count" => count($found_content));
    }

    function searchBlog($queries)
    {
        
        global $imSettings;
        $html = "";
        $found_content = array();
        $found_count = array();

        if (isset($imSettings['blog']) && is_array($imSettings['blog']['posts'])) {
            foreach ($imSettings['blog']['posts'] as $key => $value) {
                // WSXELE-799: Skip the post that are published in the future
                if ($value['utc_time'] > time()) {
                    continue;
                }
                $count = 0;
                $weight = 0;
                $filename = 'blog/index.php?id=' . $key;
                $file_content = $value['body'];
                // Rimuovo le briciole dal contenuto
                while (stristr($file_content, "<div id=\"imBreadcrumb\"") !== false) {
                    $style_start = imstripos($file_content, "<div id=\"imBreadcrumb\"");
                    $style_end = imstripos($file_content, "</div", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }

                // Rimuovo gli stili dal contenuto
                while (stristr($file_content, "<style") !== false) {
                    $style_start = imstripos($file_content, "<style");
                    $style_end = imstripos($file_content, "</style", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }
                // Rimuovo i JS dal contenuto
                while (stristr($file_content, "<script") !== false) {
                    $style_start = imstripos($file_content, "<script");
                    $style_end = imstripos($file_content, "</script", $style_start);
                    $style = substr($file_content, $style_start, $style_end - $style_start);
                    $file_content = str_replace($style, "", $file_content);
                }
                $file_title = "";

                // Conto il numero di match nel titolo
                foreach ($queries as $query) {
                    $t_count = @preg_match_all('/' . preg_quote($query, '/') . '/', imstrtolower($value['title']), $matches);
                    if ($t_count !== false) {
                        $weight += ($t_count * 4);
                        $count += $t_count;
                    }
                }

                // Conto il numero di match nei tag
                foreach ($queries as $query) {
                    if (in_array($query, $value['tag'])) {
                        $count++;
                        $weight += 4;
                    }
                }

                $title = "Blog &gt;&gt; " . $value['title'];

                // Cerco nel contenuto
                foreach ($queries as $query) {
                    $file = imstrtolower($file_content);
                    while (($file = stristr($file, $query)) !== false) {
                        $count++;
                        $weight++;
                        $file = substr($file, strlen($query));
                    }
                }

                if ($count > 0) {
                    $found_count[$filename] = $count;
                    $found_weight[$filename] = $weight;
                    $found_content[$filename] = $file_content;
                    $found_breadcrumbs[$filename] = "<div class=\"imBreadcrumb\" style=\"display: block; padding-bottom: 3px;\">" . l10n('blog_published_by') . "<strong> " . $value['author'] . " </strong>" . l10n('blog_in') . " <a href=\"blog/index.php?category=" . urlencode(str_replace(' ', '_', $value['category'])) . "\" target=\"_blank\" rel=\"nofollow\">" . $value['category'] . "</a> &middot; " . $value['timestamp'] . "</div>";
                    if ($title == "")
                        $found_title[$filename] = $filename;
                    else
                        $found_title[$filename] = $title;
                }
            }
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_weight as $name => $weight) {
                $count = $found_count[$name];
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $title = strip_tags($found_title[$name]);
                    $file = $found_content[$name];
                    $file = strip_tags($file);
                    $ap = 0;
                    $filelen = strlen($file);
                    $text = "";
                    for ($j=0;$j<($count > 6 ? 6 : $count);$j++) {
                        $minpos = $filelen;
                        $word = "";
                        foreach ($queries as $query) {
                            if ($ap < $filelen && ($pos = strpos(strtoupper($file), strtoupper($query), $ap)) !== false) {
                                if ($pos < $minpos) {
                                    $minpos = $pos;
                                    $word = $query;
                                }
                            }
                        }
                        $prev = explode(" ", substr($file, $ap, $minpos-$ap));
                        if(count($prev) > ($ap > 0 ? 9 : 8))
                            $prev = ($ap > 0 ? implode(" ", array_slice($prev, 0, 8)) : "") . " ... " . implode(" ", array_slice($prev, -8));
                        else
                            $prev = implode(" ", $prev);
                        $text .= $prev . "<strong>" . substr($file, $minpos, strlen($word)) . "</strong> ";
                        $ap = $minpos + strlen($word);
                    }
                    $next = explode(" ", substr($file, $ap));
                    if(count($next) > 9)
                        $text .= implode(" ", array_slice($next, 0, 8)) . "...";
                    else
                        $text .= implode(" ", $next);
                    $text = str_replace("|", "", $text);
                    $html .= "<div class=\"imSearchBlogResult\"><h3><a class=\"imCssLink\" href=\"" . $name . "\">" . strip_tags($title, "<b><strong>") . "</a></h3>" . strip_tags($found_breadcrumbs[$name], "<b><strong>") . "\n" . strip_tags($text, "<b><strong>") . "<div class=\"imSearchLink\"><a class=\"imCssLink\" href=\"" . $name . "\">" . $imSettings['general']['url'] . "/" . $name . "</a></div></div>\n";
                }
            }
            echo "  <div class=\"imSLabel\">&nbsp;</div>\n";
        }

        $html = preg_replace_callback('/\\s+/', create_function('$matches', 'return implode(\' \', $matches);'), $html);
        return array("content" => $html, "count" => count($found_content));
    }

    // Di questa funzione manca la paginazione!
    function searchProducts($queries)
    {
        
        global $imSettings;
        $html = "";
        $found_products = array();
        $found_count = array();

        foreach ($imSettings['search']['products'] as $id => $product) {
            $count = 0;
            $weight = 0;
            $t_title = strip_tags(imstrtolower($product['name']));
            $t_description = strip_tags(imstrtolower($product['description']));

            // Conto il numero di match nel titolo
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_title, $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 4);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella descrizione
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_description, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            if ($count > 0) {
                $found_products[$id] = $product;
                $found_weight[$id] = $weight;
                $found_count[$id] = $count;
            }
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_products as $id => $product) {
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $count = $found_count[$id];
                    $html .= "<div class=\"imSearchProductResult\">";
                    // Top row
                    $html .= "<div class=\"imProductImage\">";
                    $html .= $product['image'];
                    $html .= "</div>";
                    $html .= "<div class=\"imProductDescription\">";
                    $html .= "<div class=\"imProductTitle\">";
                    $html .= "<h3>" . $product['name'] . "</h3>";
                    $html .= "<span>" . $product['price'] . "<img src=\"cart/images/cart-add.png\" onclick=\"x5engine.cart.ui.addToCart('" . $id . "', 1);\" style=\"cursor: pointer;\" /></span>";
                    $html .= "</div>";
                    $html .= "<p>" . strip_tags($product['description']) . "</p>";
                    $html .= "</div>";
                    // Close the container
                    $html .= "</div>";
                }
            }
        }

        return array("content" => $html, "count" => count($found_products));
    }

    // Di questa funzione manca la paginazione!
    function searchImages($queries)
    {
        
        global $imSettings;
        $id = 0;
        $html = "";
        $found_images = array();
        $found_count = array();

        foreach ($imSettings['search']['images'] as $image) {
            $count = 0;
            $weight = 0;
            $t_title = strip_tags(imstrtolower($image['title']));
            $t_description = strip_tags(imstrtolower($image['description']));

            // Conto il numero di match nel titolo
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_title, $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 4);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella location
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', imstrtolower($image['location']), $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 2);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella descrizione
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_description, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            if ($count > 0) {
                $found_images[$id] = $image;
                $found_weight[$id] = $weight;
                $found_count[$id] = $count;
            }

            $id++;
        }

        if (count($found_count)) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_images as $id => $image) {
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $count = $found_count[$id];
                    $html .= "<div class=\"imSearchImageResult\">";
                    $html .= "<div class=\"imSearchImageResultContent\"><a href=\"" . $image['page'] . "\"><img src=\"" . $image['src'] . "\" /></a></div>";
                    $html .= "<div class=\"imSearchImageResultContent\">";
                    $html .= "<h3>" . $image['title'];
                    if ($image['location'] != "")
                        $html .= "&nbsp;(" . $image['location'] . ")";
                    $html .= "</h3>";
                    $html .= strip_tags($image['description']);
                    $html .= "</div>";
                    $html .= "</div>";
                }
            }
        }

        return array("content" => $html, "count" => count($found_images));
    }

    // Di questa funzione manca la paginazione!
    function searchVideos($queries)
    {
        
        global $imSettings;
        $id = 0;
        $found_count = array();
        $found_videos = array();
        $html = "";
        $month = 7776000;

        foreach ($imSettings['search']['videos'] as $video) {
            $count = 0;
            $weight = 0;
            $t_title = strip_tags(imstrtolower($video['title']));
            $t_description = strip_tags(imstrtolower($video['description']));

            // Conto il numero di match nei tag
            foreach ($queries as $query) {
                $t_count = preg_match_all('/\\s*' . preg_quote($query, '/') . '\\s*/', imstrtolower($video['tags']), $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 10);
                    $count += $t_count;
                }
            }

            // I video più recenti hanno maggiore peso in proporzione
            $time = strtotime($video['date']);
            $ago = strtotime("-3 months");
            if ($time - $ago > 0)
                $weight += 5 * max(0, ($time - $ago)/$month);

            // Conto il numero di match nel titolo
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', $t_title, $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 4);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella categoria
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query, '/') . '/', imstrtolower($video['category']), $matches);
                if ($t_count !== false) {
                    $weight += ($t_count * 2);
                    $count += $t_count;
                }
            }

            // Conto il numero di match nella descrizione
            foreach ($queries as $query) {
                $t_count = preg_match_all('/' . preg_quote($query) . '/', $t_description, $matches);
                if ($t_count !== false) {
                    $weight++;
                    $count += $t_count;
                }
            }

            if ($count > 0) {
                $found_videos[$id] = $video;
                $found_weight[$id] = $weight;
                $found_count[$id] = $count;
            }

            $id++;
        }

        if ($found_count) {
            arsort($found_weight);
            $i = 0;
            foreach ($found_videos as $id => $video) {
                $i++;
                if (($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
                    $count = $found_count[$id];
                    $html .= "<div class=\"imSearchVideoResult\">";
                    $html .= "<div class=\"imSearchVideoResultContent\"><a href=\"" . $video['page'] . "\"><img src=\"" . $video['src'] . "\" /></a></div>";
                    $html .= "<div class=\"imSearchVideoResultContent\">";
                    $html .= "<h3>" . $video['title'];
                    if (!$video['familyfriendly'])
                        $html .= "&nbsp;<span style=\"color: red; text-decoration: none;\">[18+]</span>";
                    $html .= "</h3>";
                    $html .= strip_tags($video['description']);
                    if ($video['duration'] > 0) {
                        if (function_exists('date_default_timezone_set'))
                            date_default_timezone_set('UTC');
                        $html .= "<span class=\"imSearchVideoDuration\">" . l10n('search_duration') . ": " . date("H:i:s", $video['duration']) . "</span>";
                    }
                    $html .= "</div>";
                    $html .= "</div>";
                }
            }
        }

        return array("content" => $html, "count" => count($found_videos));
    }

    /**
     * Start the site search
     * 
     * @param array  $keys The search keys as string (string)
     * @param string $page Page to show (integer)
     * @param string $type The content type to show
     *
     * @return void
     */
    function search($keys, $page, $type)
    {
        global $imSettings;

        $html = "";
        $content = "";
        $emptyResultsHtml = "<div style=\"margin-top: 15px; text-align: center; font-weight: bold;\">" . l10n('search_empty') . "</div>\n";

        $html .= "<div class=\"imPageSearchField\"><form method=\"get\" action=\"imsearch.php\">";
        $html .= "<input style=\"width: 200px; padding: 3px; vertical-align: middle;\" class=\"search_field\" value=\"" . htmlspecialchars($keys, ENT_COMPAT, 'UTF-8') . "\" type=\"text\" name=\"search\" />";
        $html .= "<input style=\"margin-left: 6px; padding: 3px 3px; vertical-align: middle; cursor: pointer;\" type=\"submit\" value=\"" . l10n('search_search') . "\">";
        $html .= "</form></div>\n";

        // Exit if no search query was given
        if (trim($keys) == "" || $keys == null) {
            $html .= $emptyResultsHtml;
            return $html;
        }

        $search = trim(imstrtolower($keys));
        $this->page = $page;

        $queries = explode(" ", $search);

        // Search everywhere to populate the results numbers shown in the sidebar menu
        // Pages
        $pages = $this->searchPages($queries);
        // Fallback on the selection if there are no pages
        if ($pages['count'] == 0 && $type == "pages")
            $type = "blog";

        // Blog
        if (isset($imSettings['blog']) && is_array($imSettings['blog']['posts']) && count($imSettings['blog']['posts']) > 0)
            $blog = $this->searchBlog($queries);
        else
            $blog = array("count" => 0);
        // Fallback on the selection if there is no blog
        if ($blog['count'] == 0 && $type == "blog")
            $type = "products";

        // Products
        if (is_array($imSettings['search']['products']) && count($imSettings['search']['products']) > 0)
            $products = $this->searchProducts($queries);
        else
            $products = array("count" => 0);
        // Fallback on the selection if there are no products
        if ($products['count'] == 0 && $type == "products")
            $type = "images";

        // Images
        if (is_array($imSettings['search']['images']) && count($imSettings['search']['images']) > 0)
            $images = $this->searchImages($queries);
        else
            $images = array("count" => 0);
        // Fallback on the selection if there are no images
        if ($images['count'] == 0 && $type == "images")
            $type = "videos";

        // Videos
        if (is_array($imSettings['search']['videos']) && count($imSettings['search']['videos']) > 0)
            $videos = $this->searchVideos($queries);
        else
            $videos = array("count" => 0);
        // Fallback on the selection if there are no videos
        if ($videos['count'] == 0 && $type == "videos")
            $type = "pages";            

        // Show only the requested content type
        switch ($type) {
            case "pages":
                if ($pages['count'] > 0)
                    $content .= "<div>" . $pages['content'] . "</div>\n";
                $results_count = $pages['count'];
                break;
            case "blog":
                if ($blog['count'] > 0)
                    $content .= "<div>" . $blog['content'] . "</div>\n";
                $results_count = $blog['count'];
                break;
            case "products":
                if ($products['count'] > 0)
                    $content .= "<div>" . $products['content'] . "</div>\n";
                $results_count = $products['count'];
                break;
            case "images":
                if ($images['count'] > 0)
                    $content .= "<div>" . $images['content'] . "</div>\n";
                $results_count = $images['count'];
                break;
            case "videos":
                if ($videos['count'] > 0)
                    $content .= "<div>" . $videos['content'] . "</div>\n";
                $results_count = $videos['count'];
                break;
        }

        // Exit if there are no results
        if (!$results_count) {
            $html .= $emptyResultsHtml;
            return $html;
        }

        $sidebar = "<ul>\n";
        if ($pages['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=pages\">" . l10n('search_pages') . " (" . $pages['count'] . ")</a></span></li>\n";
        if ($blog['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=blog\">" . l10n('search_blog') . " (" . $blog['count'] . ")</a></span></li>\n";
        if ($products['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=products\">" . l10n('search_products') . " (" . $products['count'] . ")</a></span></li>\n";
        if ($images['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=images\">" . l10n('search_images') . " (" . $images['count'] . ")</a></span></li>\n";
        if ($videos['count'] > 0)
            $sidebar .= "\t<li><span class=\"imScMnTxt\"><a href=\"imsearch.php?search=" . urlencode($keys) . "&type=videos\">" . l10n('search_videos') . " (" . $videos['count'] . ")</a></span></li>\n";
        $sidebar .= "</ul>\n";

        $html .= "<div id=\"imSearchResults\">\n";
        $html .= "\t<div id=\"imSearchSideBar\">" . $sidebar . "</div>\n";
        $html .= "\t<div id=\"imSearchContent\">" . $content . "</div>\n";
        $html .= "</div>\n";

        // Pagination
        if ($results_count > $this->results_per_page) {
            $html .= "<div style=\"text-align: center; clear: both;\">";
            // Back
            if ($page > 0) {
                $html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . ($page - 1) . "&type=" . $type . "\">&lt;&lt;</a>&nbsp;";
            }

            // Central pages
            $start = max($page - 5, 0);
            $end = min($page + 10 - $start, ceil($results_count/$this->results_per_page));

            for ($i = $start; $i < $end; $i++) {
                if ($i != $this->page)
                    $html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . $i . "&type=" . $type . "\">" . ($i + 1) . "</a>&nbsp;";
                else
                    $html .= ($i + 1) . "&nbsp;";
            }

            // Next
            if ($results_count > ($page + 1) * $this->results_per_page) {
                $html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . ($page + 1) . "&type=" . $type . "\">&gt;&gt;</a>";
            }
            $html .= "</div>";
        }

        return $html;
    }
}


/**
 * Contains the methods used to style and send emails
 * @access public
 */
class ImSendEmail
{

    var $header;
    var $footer;
    var $bodyBackground;
    var $bodyBackgroundEven;
    var $bodyBackgroundOdd;
    var $bodyBackgroundBorder;
    var $bodyTextColorOdd;
    var $bodySeparatorBorderColor;
    var $emailBackground;
    var $emailContentStyle;
    var $emailContentFontFamily;
    var $emailType = "html";
    var $pathToRoot = "../";
    var $use_smtp = false;
    var $use_smtp_auth = false;
    var $smtp_host;
    var $smtp_port;
    var $smtp_username;
    var $smtp_password;
    var $smtp_encryption = 'none';

    /**
     * Apply the CSS style to the HTML code
     * @param  string $html The HTML code
     * @return string       The styled HTML code
     */
    function styleHTML($html)
    {
        $html = str_replace("[email:contentStyle]", $this->emailContentStyle, $html);
        $html = str_replace("[email:contentFontFamily]", $this->emailContentFontFamily, $html);
        $html = str_replace("[email:bodyBackground]", $this->bodyBackground, $html);
        $html = str_replace("[email:bodyBackgroundBorder]", $this->bodyBackgroundBorder, $html);
        $html = str_replace("[email:bodyBackgroundOdd]", $this->bodyBackgroundOdd, $html);
        $html = str_replace("[email:bodyBackgroundEven]", $this->bodyBackgroundEven, $html);
        $html = str_replace("[email:bodyTextColorOdd]", $this->bodyTextColorOdd, $html);
        $html = str_replace("[email:bodySeparatorBorderColor]", $this->bodySeparatorBorderColor, $html);
        $html = str_replace("[email:emailBackground]", $this->emailBackground, $html);
        return $html;
    }

    /**
     * Send an email
     *
     * @param string $from        Self explanatory
     * @param string $to          Self explanatory
     * @param string $subject     Self explanatory
     * @param string $text        Self explanatory
     * @param string $html        Self explanatory
     * @param array  $attachments Self explanatory
     *
     * @return boolean
     */
    function send($from = "", $to = "", $subject = "", $text = "", $html = "", $attachments = array())
    {
        /*
        |--------------
        |  PHPMailer
        |--------------
         */
        if ($this->emailType == 'phpmailer') {
            require_once("PHPMailerAutoload.php");
            $email = new PHPMailer;
            // SMTP support
            if ($this->use_smtp) {
                $email->isSMTP();
                $email->Host = $this->smtp_host;
                $email->Port = $this->smtp_port;
                if ($this->smtp_encryption != 'none') {
                    $email->SMTPSecure = $this->smtp_encryption;
                }
                $email->SMTPAuth = $this->use_smtp_auth;
                if ($this->use_smtp_auth) {
                    $email->Username = $this->smtp_username;
                    $email->Password = $this->smtp_password;
                }
            }
            // Meta
            $email->CharSet = 'UTF-8'; // WSXELE-1067: Force UTF-8
            $email->Subject = $subject;
            $email->From = addressFromEmail($from);
            $email->FromName = nameFromEmail($from);
            // WSXELE-1120: Split the email addresses if necessary
            $to = str_replace(";", ",", $to); // Make sure it works for both "," and ";" separators
            foreach (explode(",", $to) as $addr) {
                // WSXELE-1157: Provide support for the format John Doe <johndoe@email.com>
                $email->addAddress(addressFromEmail($addr), nameFromEmail($addr));
            }
            // Content
            $email->isHTML(true);
            $email->Body = $this->header . $this->styleHTML($html) . $this->footer;
            $email->AltBody = $text;
            // Attachments
            foreach ($attachments as $file) {
                if (isset($file['name']) && isset($file['content']) && isset($file['mime'])) {
                    $email->addStringAttachment($file['content'], $file['name'], 'base64', $file['mime'], 'attachment');
                }
            }
            if (!$email->send()) {
                $this->registerLog($email->ErrorInfo);
                return false;
            }
            return true;
        }

        /*
        |--------------
        |  WSX5 class
        |--------------
         */

        $email = new imEMail($from, $to, $subject, "utf-8");
        $email->setText($text);
        $email->setHTML($this->header . $this->styleHTML($html) . $this->footer);
        $email->setStandardType($this->emailType);
        foreach ($attachments as $a) {
            if (isset($a['name']) && isset($a['content']) && isset($a['mime'])) {
                $email->attachFile($a['name'], $a['content'], $a['mime']);
            }
        }
        if (!$email->send()) {
            $this->registerLog("Cannot send email with internal script");
            return false;
        }
        return true;
    }

    /**
     * Restore some special chars escaped previously in WSX5
     *
     * @param string $str The string to be restored
     *
     * @return string
     */
    function restoreSpecialChars($str)
    {
        $str = str_replace("{1}", "'", $str);
        $str = str_replace("{2}", "\"", $str);
        $str = str_replace("{3}", "\\", $str);
        $str = str_replace("{4}", "<", $str);
        $str = str_replace("{5}", ">", $str);
        return $str;
    }

    /**
     * Decode the Unicode escaped chars like %u1239
     *
     * @param string $str The string to be decoded
     *
     * @return string
     */
    function decodeUnicodeString($str)
    {
        $res = '';

        $i = 0;
        $max = strlen($str) - 6;
        while ($i <= $max) {
            $character = $str[$i];
            if ($character == '%' && $str[$i + 1] == 'u') {
                $value = hexdec(substr($str, $i + 2, 4));
                $i += 6;

                if ($value < 0x0080) // 1 byte: 0xxxxxxx
                    $character = chr($value);
                else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
                    $character = chr((($value & 0x07c0) >> 6) | 0xc0) . chr(($value & 0x3f) | 0x80);
                else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
                $character = chr((($value & 0xf000) >> 12) | 0xe0) . chr((($value & 0x0fc0) >> 6) | 0x80) . chr(($value & 0x3f) | 0x80);
            } else
                $i++;

            $res .= $character;
        }
        return $res . substr($str, $i);
    }

    /**
     * Get the email log path (relative to the sites' root)
     * @return String
     */
    function getLogPath()
    {
        global $imSettings;
        return pathCombine(array($imSettings['general']['public_folder'], "email_log.txt"));
    }

    /**
     * Register a message in the email log
     * @param  String $message The message to be saved in the log
     * @return void
     */
    function registerLog($message)
    {
        if (function_exists("file_get_contents") && function_exists("file_put_contents")) {
            $data = "";
            $file = pathCombine(array($this->pathToRoot, $this->getLogPath()));
            if (file_exists($file)) {
                $data = @file_get_contents($file);
            }
            $data = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL . $data;
            @file_put_contents($file, $data);
        }
    }
}


/**
 * Provide the smallest and simplest PHP templating engine ever
 */
class Template {

	private $vars = array();
	private $view_template_file;

	public function __construct($view_template_file)
	{
		$this->view_template_file = $view_template_file;
	}

	public function __get($name)
	{
		return $this->vars[$name];
	}

	public function __set($name, $value)
	{
		if($name == 'view_template_file') {
			throw new Exception("Cannot bind variable named 'view_template_file'");
		}
		$this->vars[$name] = $value;
	}

	public function render()
	{
		extract($this->vars);
		ob_start();
		include($this->view_template_file);
		return ob_get_clean();
	}
}

/**
 * Server Test Class
 * @access public
 */
class imTest {

    /**
     * Test the current WSX5 configuration
     *
     * @return Array An array containing the test results as array("name" => string, "success" => bool, "message" => string)
     */
    static public function testWsx5Configuration()
    {
        global $imSettings;

        $testedFolders = array();
        $test = new imTest();
        $results = array();

        $results[] = array(
            "name"    => l10n('admin_test_version') . ": " . PHP_VERSION,
            "message" => l10n('admin_test_version_suggestion'),
            "success" => $test->php_version_test()
        );

        $results[] = array(
            "name"    => l10n('admin_test_session'),
            "message" => l10n('admin_test_session_suggestion'),
            "success" => $test->session_test()
        );

        @chdir("../.");

        // Generic public folder
        if (isset($imSettings['general']['public_folder'])) {
            $testedFolders[] = $imSettings['general']['public_folder'];
            $results[] = array(
                "name"    => l10n('admin_test_folder') . ($imSettings['general']['public_folder'] != "" ? " (" . $imSettings['general']['public_folder'] . ")": " (site root folder)"),
                "message" => l10n("admin_test_folder_suggestion"),
                "success" => $test->writable_folder_test($imSettings['general']['public_folder'])
            );
        }

        // Blog public folder
        if (isset($imSettings['blog']) && $imSettings['blog']['comments_source'] == 'wsx5' && $imSettings['blog']['sendmode'] == 'file' && !in_array($imSettings['blog']['folder'], $testedFolders)) {
            $testedFolders[] = $imSettings['blog']['folder'];
            $results[] = array(
                "name"    => l10n('admin_test_folder') . ($imSettings['blog']['folder'] != "" ? " (" . $imSettings['blog']['folder'] . ")": " (site root folder)"),
                "message" => l10n("admin_test_folder_suggestion"),
                "success" => $test->writable_folder_test($imSettings['blog']['folder'])
            );
        }



        @chdir("admin");





/**
 * XML Handling class
 * @access public
 */
class imXML 
{
    var $tree = array();
    var $force_to_array = array();
    var $error = null;
    var $parser;
    var $inside = false;

    function __construct($encoding = 'UTF-8')
    {
        $this->parser = xml_parser_create($encoding);
        xml_set_object($this->parser, $this); // $this was passed as reference &$this
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
        xml_set_element_handler($this->parser, "startEl", "stopEl");
        xml_set_character_data_handler($this->parser, "charData");
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    }

    function parse_file($file)
    {
        $fp = @fopen($file, "r");
        if (!$fp)
            return false;
        while ($data = fread($fp, 4096)) {
            if (!xml_parse($this->parser, $data, feof($fp))) {
                return false;
            }
        }
        fclose($fp);
        return $this->tree[0]["content"];
    }

    function parse_string($str)
    {
        if (!xml_parse($this->parser, $str))
            return false;
        if (isset($this->tree[0]["content"]))
            return $this->tree[0]["content"];
        return false;
    }

    function startEl($parser, $name, $attrs)
    {
        array_unshift($this->tree, array("name" => $name));
        $this->inside = false;
    }

    function stopEl($parser, $name)
    {
        if ($name != $this->tree[0]["name"])
            return false;
        if (count($this->tree) > 1) {
            $elem = array_shift($this->tree);
            if (isset($this->tree[0]["content"][$elem["name"]])) {
                if (is_array($this->tree[0]["content"][$elem["name"]]) && isset($this->tree[0]["content"][$elem["name"]][0])) {
                    array_push($this->tree[0]["content"][$elem["name"]], $elem["content"]);
                } else {
                    $this->tree[0]["content"][$elem["name"]] = array($this->tree[0]["content"][$elem["name"]],$elem["content"]);
                }
            } else {
                if (in_array($elem["name"], $this->force_to_array)) {
                    $this->tree[0]["content"][$elem["name"]] = array($elem["content"]);
                } else {
                    if (!isset($elem["content"])) $elem["content"] = "";
                    $this->tree[0]["content"][$elem["name"]] = $elem["content"];
                }
            }
        }
        $this->inside = false;
    }

    function charData($parser, $data)
    {
        if (!preg_match("/\\S/", $data))
                return false;
        if ($this->inside) {
            $this->tree[0]["content"] .= $data;
        } else {
            $this->tree[0]["content"] = $data;
        }
        $this->inside_data = true; 
    }
}


/**
 * Prints an error that warns the user that JavaScript is not enabled, then redirects to the previous page after 5 seconds.
 *
 * @param {boolean} $docType True to use the meta redirect with a complete document. False to use a javascript code.
 *
 * @return {Void}
 */
function imPrintJsError($docType = true)
{
    return imPrintError(l10n('form_js_error'), $docType);
}

/**
 * Prints a custom error message then redirects to the previous page after 5 seconds.
 *
 * @param {string} $message The message to show
 * @param {boolean} $docType True to use the meta redirect with a complete document. False to use a javascript code.
 *
 * @return {Void}
 */
function imPrintError($message, $docType = true)
{
    if ($docType) {
        $html = "<DOCTYPE><html><head><meta charset=\"UTF-8\"> <meta http-equiv=\"Refresh\" content=\"5;URL=" . $_SERVER['HTTP_REFERER'] . "\"></head><body>";
        $html .= $message;
        $html .= "</body></html>";
    } else {
        $html = "<meta charset=\"UTF-8\"> <meta http-equiv=\"Refresh\" content=\"5;URL=" . $_SERVER['HTTP_REFERER'] . "\">";
        $html .= $message;
    }
    return $html;
}

/**
 * Check if the current user can access to the provided page id.
 * If not, the user is redirected to the login page.
 *
 * @method imCheckAccess
 *
 * @param {string} $page The id of the page to check
 * @param {string} $pathToRoot The path to reach the root from the current folder
 *
 * @return {Void}
 */
function imCheckAccess($page, $pathToRoot = "")
{
    $pa = Configuration::getPrivateArea();
    $stat = $pa->checkAccess($page);
    if ($stat !== 0) {
        $pa->savePage();
        header("Location: " . $pathToRoot . "imlogin.php?loginstatus=" . $stat );
        exit;
    }
}


/**
 * Shuffle an associative array
 *
 * @method shuffleAssoc
 *
 * @param {array} $list The array to shuffle
 *
 * @return {array}       The shuffled array
 */
function shuffleAssoc($list)
{
    if (!is_array($list))
        return $list;
    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key)
        $random[$key] = $list[$key];
    return $random;
}

/**
 * If you want to support PHP 4 code, use this function instead of stripos.
 *
 * @method imstripos
 *
 * @param {string}  $haystack Where to search
 * @param {string}  $needle   What to replace
 * @param {integer} $offset   Start searching from here
 *
 * @return {integer}          The position of the searched string
 */
function imstripos($haystack, $needle , $offset = 0)
{
    if (function_exists('stripos')) // Is PHP5+
        return stripos($haystack, $needle, $offset);

    // PHP4 fallback
    return strpos(strtolower($haystack), strtolower($needle), $offset);
}

/**
 * Get a localization string.
 * The string is taken from the ones specified at step 1 of WSX5
 *
 * @method l10n
 *
 * @param {string} $id      The localization key
 * @param {string} $default The default string
 *
 * @return {string}         The localization
 */
function l10n($id, $default = "")
{
    global $l10n;

    if (!isset($l10n[$id]))
        return $default;

    return $l10n[$id];
}

/**
 * Combine a series of paths
 *
 * @method pathCombine
 *
 * @param  {array}  $paths The array with the elements of the path
 *
 * @return {string} The path created combining the elements of the array
 */
function pathCombine($paths = array())
{
    $s = array();
    foreach ($paths as $path) {
        if (strlen($path)) {
            $s[] = trim($path, "/\\ ");
        }
    }
    return implode("/", $s);
}

/**
 * Check if the argument is an email address
 * @param  String  $email
 * @return boolean
 */
function isEmail($email)
{
    return strpos($email, "@") > 0 && preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $email);
}

/**
 * Try to convert a string to lowercase using multibyte encoding
 *
 * @param  string $str
 *
 * @return string
 */
function imstrtolower($str)
{
    return (function_exists("mb_convert_case") ? mb_convert_case($str, MB_CASE_LOWER, "UTF-8") : strtolower($str));
}

if (!function_exists('htmlspecialchars_decode')) {
    /**
     * Fallback for htmlspecialchars_decode in PHP4
     * @ignore
     * @param  string  $text
     * @param  integer $quote_style
     * @return string
     */
    function htmlspecialchars_decode($text, $quote_style = ENT_COMPAT)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
    }
}

if (!function_exists('json_encode')) {
    /**
     * Fallback for json_encode before PHP 5.2
     * @ignore
     */
    function json_encode($data)
    {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . addslashes($data) . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }
}

/**
 * Check for the valid data about spam and js
 *
 * @param  string $expectedValue The expected value for the JS check field
 *
 * @return bool
 */
function checkJsAndSpam($expectedValue = "")
{
    // Spam!
    if ($_POST['prt'] != "") {
        return false;
    }

    // Javascript disabled
    if (!isset($_POST['imJsCheck'])) {
        echo imPrintJsError(false);
        return false;
    }

    if (strlen($_POST['imJsCheck']) && $_POST['imJsCheck'] != $expectedValue) {
        echo imPrintJsError(false);
        return false;
    }

    return true;
}

/**
 * Search if at least one element of $needle is in $haystack.
 * @param  Array   $needle   Non-associative array
 * @param  Array   $haystack Non-associative array
 * @param  boolean $all      Set to true to ensure that all the elements in $needle are in $haystack
 * @return boolean
 */
function in_array_field($needle, $haystack, $all = false)
{
    if ($all) {
        foreach ($needle as $key)
            if (!in_array($key, $haystack))
                return false;
        return true;
    } else {
        foreach ($needle as $key)
            if (in_array($key, $haystack))
                return true;
        return false;
    }
}

/**
 * Filter the var from unwanted input chars.
 * Basically remove the quotes added by magic_quotes
 * @param  mixed $var The var to filter
 * @return mixed      The filtered var
 */
function imFilterInput($var) {
    // Remove the magic quotes
    if (get_magic_quotes_gpc()) {
        // String
        if (is_string($var))
            $var = stripslashes($var);
        // Array
        else if (is_array($var)) {
            for ($i = 0; $i < count($var); $i++)
                $var[$i] = imFilterInput($var[$i]);
        }
    }
    return $var;
}

/**
 * Get the most recent date in the provided array of PHP times that do not define a future time.
 * The date is provided in the RSS format Sun, 15 Jun 2008 21:15:07 GMT
 * @param  array    $timeArr
 * @return string
 */
function getLastAvailableDate($timeArr) {
    if (count($timeArr) > 0) {
        sort($timeArr, SORT_DESC);
        $utcTime = time() + date("Z", time());
        foreach ($timeArr as $time) {
            if ($time <= $utcTime) {
                return date("r", $time);
            }
        }
    }
    return date("r", time());
}

/**
 * Update the query string adding or updating a variable with a specified value
 *
 * @param  string $name  The variable to be replaced
 * @param  string $value The value to set
 *
 * @return string        The updated query string
 */
function updateQueryStringVar($name, $value) {
    if (!isset($_SERVER['QUERY_STRING'])) return "";
    $qs = array();
    parse_str($_SERVER['QUERY_STRING'], $qs);
    $qs[$name] = $value;
    return http_build_query($qs);
}

/**
 * Get the email address from a string formatted like "John Doe <johndoe@email.com>"
 *
 * @param  String $email The email string
 *
 * @return String        The email address
 */
function addressFromEmail($email) {
    $start = strpos($email, "<");
    $end = strpos($email, ">");
    if ($start > 0 && $end !== false) {
        return trim(substr($email, $start + 1, $end - $start - 1));
    }
    return $email;
}

/**
 * Get the user name from a string formatted like "John Doe <johndoe@email.com>"
 *
 * @param  String $email The email string
 *
 * @return String        The user name
 */
function nameFromEmail($email) {
    $end = strpos($email, "<");
    if ($end > 0) {
        return trim(substr($email, 0, $end));
    }
    return $email;
}


$imSettings = Array();
$l10n = Array();
$phpError = false;
$ImMailer = new ImSendEmail();

@include_once "imemail.inc.php";        // Email class - Static
@include_once "x5settings.php";         // Basic settings - Dynamically created by WebSite X5
@include_once "access.inc.php";         // Private area data - Dynamically created by WebSite X5
@include_once "l10n.php";               // Localizations - Dynamically created by WebSite X5
@include_once "search.inc.php" ;        // Search engine data - Dynamically created by WebSite X5



// End of file