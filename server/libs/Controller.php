<?php
require_once 'libs/Validator.php';
require_once 'models/Session.php';

abstract class Controller {

    /**
     * Instance-related stuff
    */
    abstract public function handler();
    abstract public function validations();

    public function getHandler() {
        return function () {
            try {
                $this->validate();
            } catch (ValidationException $exception) {
                Response::respondError($exception->getMessage());
                return;
            }

            $this->handler();
        };
    }
    
    public function validate() {
        $validator = new Validator();
        
        $validator->validate($this->validations());
    }

    public static function request($key) {
        $app = self::getAppInstance();

        return $app->request()->post($key);
    }
    
    public static function getLoggedUser() {
        $session = Session::getInstance();

        if ($session->isStaffLogged()) {
            return Staff::getUser((int)self::request('csrf_userid'));
        } else {
            return User::getUser((int)self::request('csrf_userid'));
        }
    }

    public static function isUserLogged() {
        $session = Session::getInstance();

        return $session->checkAuthentication(array(
            'userId' => Controller::request('csrf_userid'),
            'token' => Controller::request('csrf_token')
        ));
    }

    public static function isStaffLogged($level = 1) {
        return Controller::isUserLogged() && (Controller::getLoggedUser()->level >= $level);
    }

    public static function getAppInstance() {
        return \Slim\Slim::getInstance();
    }
}