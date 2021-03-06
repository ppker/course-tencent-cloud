<?php

namespace App\Http\Home\Controllers;

use App\Models\User as UserModel;
use App\Services\Auth\Home as HomeAuth;
use App\Traits\Response as ResponseTrait;
use App\Traits\Security as SecurityTrait;
use Phalcon\Mvc\Dispatcher;

class LayerController extends \Phalcon\Mvc\Controller
{

    /**
     * @var UserModel
     */
    protected $authUser;

    use ResponseTrait;
    use SecurityTrait;

    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        if ($this->isNotSafeRequest()) {
            $this->checkHttpReferer();
            $this->checkCsrfToken();
        }

        $this->checkRateLimit();

        return true;
    }

    public function initialize()
    {
        $this->authUser = $this->getAuthUser();

        $this->view->setVar('auth_user', $this->authUser);
    }

    protected function getAuthUser()
    {
        /**
         * @var HomeAuth $auth
         */
        $auth = $this->getDI()->get('auth');

        return $auth->getCurrentUser();
    }

}
