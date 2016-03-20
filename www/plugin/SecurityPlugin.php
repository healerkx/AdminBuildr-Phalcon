<?php

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

class SecurityPlugin extends Plugin
{
    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return boolean
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $acl = new Phalcon\Acl\Adapter\Memory();


        $acl->setDefaultAction(Phalcon\Acl::DENY);

        $roles = array(
            'users' => new Phalcon\Acl\Role('Users'),
            'guests' => new Phalcon\Acl\Role('Guests')
        );
        foreach ($roles as $role) {
            $acl->addRole($role);
        }

        var_dump($acl->getRoles());exit;

        $controllerName = $dispatcher->getControllerName();
        $actionName = $dispatcher->getActionName();

        $session = $this->session;

        if ($this->canAccess($session, $controllerName, $actionName)) {
            return true;
        } else {

            $url = "$controllerName/$actionName";
            // TODO: Log access forbidden log
            // TODO: $dispatcher->forward('Access Forbidden');
        }
    }

    public function canAccess($session, $controllerName, $actionName)
    {


        return true;
    }
}