<?php
declare(strict_types=1);

/**
 * SessionController (Main Session Controller)
 *
 * This contains the primary code for the session
 * Created by Neil McWilliam
 * 
 */

namespace Invo\Controllers;

use Invo\Forms\RegisterForm;
use Invo\Models\Users;
use Phalcon\Db\RawValue;

/**
 * SessionController
 *
 * Allows to register new users
 */
class RegisterController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Sign Up/Sign In');

        parent::initialize();
    }

    /**
     * Action to register a new user
     */
    public function indexAction(): void
    {
        $form = new RegisterForm();

        if ($this->request->isPost()) {
            $password = $this->request->getPost('password');
            $repeatPassword = $this->request->getPost('repeatPassword');

            if ($password !== $repeatPassword) {
                $this->flash->error('Passwords are different');

                return;
            }

            $user = new Users();
            $user->username = $this->request->getPost('username', 'alphanum');
            $user->password = sha1($password);
            $user->name = $this->request->getPost('name', ['string', 'striptags']);
            $user->email = $this->request->getPost('email', 'email');
            $user->created_at = new RawValue('now()');
            $user->active = '1';

            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }
            } else {
                $this->tag->setDefault('email', '');
                $this->tag->setDefault('password', '');

                $this->flash->success('Thanks for signing up, please log in to start crawling your website.');

                $this->dispatcher->forward([
                    'controller' => 'session',
                    'action'     => 'index',
                ]);

                return;
            }
        }

        $this->view->form = $form;
    }
}
