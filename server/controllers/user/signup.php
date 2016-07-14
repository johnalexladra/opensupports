<?php

class SignUpController extends Controller {
    const PATH = '/signup';

    public function validations() {
        return [
            'permission' => 'any',
            'requestData' => []
        ];
    }

    public function handler() {
        $email =  Controller::request('email');
        $password =  Controller::request('password');

        $userId = $this->createNewUserAndRetrieveId($email, $password);

        EmailSender::validRegister($email);

        Response::respondSuccess(array(
            'userId' => $userId,
            'userEmail' => $email
        ));
    }

    public function createNewUserAndRetrieveId($email, $password) {
        $userInstance = new User();
        $userInstance->setProperties(array(
            'email' => $email,
            'password' => Hashing::hashPassword($password)
        ));

        return $userInstance->store();
    }
}
