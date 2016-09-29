<?php

namespace Modules\OptinAndShare\Pages;

use Lightning\Model\User;
use Lightning\Tools\Configuration;
use Lightning\Tools\Database;
use Lightning\Tools\Form;
use Lightning\Tools\Messenger;
use Lightning\Tools\Request;
use Lightning\Tools\Security\Random;
use Lightning\Tools\Template;
use Lightning\View\Facebook\SDK;
use Lightning\View\Page;

class Optin extends Page {
    const TABLE = 'optin_and_share';
    protected $page = ['optin', 'OptinAndShare'];
    protected $settings = [];

    public function __construct() {
        parent::__construct();
        $route = Request::getLocation();
        $this->settings = Configuration::get('modules.OptinAndShare.settings.' . $route, []);
        $this->updateSettings($this->settings);
    }

    public function hasAccess() {
        return true;
    }

    public function get() {
        Form::requiresToken();
        if (!empty($this->settings['templates']['optin'])) {
            $this->page = $this->settings['templates']['optin'];
        }
    }

    public function getShare() {
        $token = Request::get('t', Request::TYPE_BASE64);

        // Load the token to make sure it's valid.
        if (!Database::getInstance()->check(static::TABLE, ['token' => $token])) {
            Messenger::error('Invalid Token');
            $this->redirect();
        }
        SDK::init();

        Template::getInstance()->set('token', $token);

        $this->page = !empty($this->settings['templates']['share']) ? $this->settings['templates']['share'] : ['share', 'OptinAndShare'];
    }

    public function getThanks() {
        $this->page = !empty($this->settings['templates']['thanks']) ? $this->settings['templates']['thanks'] : ['thanks', 'OptinAndShare'];
    }

    public function postRegister() {
        $email = Request::post('email', Request::TYPE_EMAIL);
        if (empty($email)) {
            throw new \Exception('Invalid Email');
        }

        $user = User::addUser($email);
        if (!empty($this->settings['list_id'])) {
            $user->subscribe($this->settings['list_id']);
        }

        // Create a regular token and insert into the database.
        $db = Database::getInstance();
        do {
            $token = Random::get(64, Random::BASE64);
        } while ($db->check(static::TABLE, ['token' => $token]));

        $db->insert(static::TABLE, [
            'token' => $token,
            'user_id' => $user->id,
            'shared' => 0,
        ]);

        $this->redirect(['action' => 'share', 't' => $token]);
    }

    public function postShared() {
        $token = Request::post('t', Request::TYPE_BASE64);

        $db = Database::getInstance();

        // Load the token.
        $t = $db->selectRow(static::TABLE, ['token' => $token]);

        if (empty($t)) {
            Messenger::error('Invalid Token');
            $this->redirect();
        } else {
            // Update the token.
            $db->update(static::TABLE, ['shared' => 1], ['optin_id' => $t['optin_id']]);

            $this->redirect(['action' => 'thanks']);
        }
    }
}
