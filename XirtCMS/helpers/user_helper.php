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
    public static function commmunicatePassword($user, $password) {

        $CI &= get_instance();
        $CI->load->library("email");
        $email = $CI->email->initialize(
            array("mailtype" => "html"
        ));

        // Set e-mail headers
        $email->from(XCMS_Config::get("EMAIL_SENDER_NAME"), XCMS_Config::get("EMAIL_SENDER_EMAIL"))
            ->subject("Your new password")
            ->to($user->get("email"));

        // Set e-mail content and send
        $email->message($this->load->view('emails/reset_password.tpl', array(
            "name"     => $user->get("real_name"),
            "username" => $user->get("username"),
            "password" => $password,
            "target"   => base_url()
        ), true))->send();

    }

}
?>