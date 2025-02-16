<?php
/**
 * @copyright Copyright (c) 2021 深圳市酷瓜软件有限公司
 * @license https://opensource.org/licenses/GPL-2.0
 * @link https://www.koogua.com
 */

namespace Bootstrap;

use App\Exceptions\BadRequest as BadRequestException;
use App\Exceptions\Forbidden as ForbiddenException;
use App\Exceptions\NotFound as NotFoundException;
use App\Exceptions\ServiceUnavailable as ServiceUnavailableException;
use App\Exceptions\Unauthorized as UnauthorizedException;
use App\Library\Logger as AppLogger;
use Phalcon\Config;
use Phalcon\Di\Injectable;
use Throwable;

class HttpErrorHandler extends Injectable
{

    public function __construct()
    {
        set_exception_handler([$this, 'handleException']);

        set_error_handler([$this, 'handleError']);

        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * @param Throwable $e
     */
    public function handleException($e)
    {
        $this->setStatusCode($e);

        if ($this->response->getStatusCode() == 500) {
            $this->report($e);
        }

        if ($this->request->isApi()) {
            $this->apiError($e);
        } elseif ($this->request->isAjax()) {
            $this->ajaxError($e);
        } else {
            $this->pageError($e);
        }
    }

    public function handleError($errNo, $errStr, $errFile, $errLine)
    {
        if (in_array($errNo, [E_WARNING, E_NOTICE, E_DEPRECATED, E_USER_WARNING, E_USER_NOTICE, E_USER_DEPRECATED])) {
            return true;
        }

        $logger = $this->getLogger();

        $logger->error("Error [{$errNo}]: {$errStr} in {$errFile} on line {$errLine}");

        return false;
    }

    public function handleShutdown()
    {
        $error = error_get_last();

        if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {

            $logger = $this->getLogger();

            $logger->error("Fatal Error [{$error['type']}]: {$error['message']} in {$error['file']} on line {$error['line']}");
        }
    }

    /**
     * @param Throwable $e
     */
    protected function setStatusCode($e)
    {
        if ($e instanceof BadRequestException) {
            $this->response->setStatusCode(400);
        } elseif ($e instanceof UnauthorizedException) {
            $this->response->setStatusCode(401);
        } elseif ($e instanceof ForbiddenException) {
            $this->response->setStatusCode(403);
        } elseif ($e instanceof NotFoundException) {
            $this->response->setStatusCode(404);
        } elseif ($e instanceof ServiceUnavailableException) {
            $this->response->setStatusCode(503);
        } else {
            $this->response->setStatusCode(500);
        }
    }

    /**
     * @param Throwable $e
     */
    protected function report($e)
    {
        $content = sprintf('%s(%d): %s', $e->getFile(), $e->getLine(), $e->getMessage());

        $logger = $this->getLogger();

        $logger->error($content);

        $config = $this->getConfig();

        if ($config->path('env') == 'dev' || $config->path('log.trace')) {

            $content = sprintf('Trace Content: %s', $e->getTraceAsString());

            $logger->error($content);
        }
    }

    /**
     * @param Throwable $e
     */
    protected function apiError($e)
    {
        $content = $this->translate($e->getMessage());

        $this->response->setJsonContent($content);

        $this->response->send();
    }

    /**
     * @param Throwable $e
     */
    protected function ajaxError($e)
    {
        $content = $this->translate($e->getMessage());

        $this->response->setJsonContent($content);

        $this->response->send();
    }

    /**
     * @param Throwable $e
     */
    protected function pageError($e)
    {
        $content = $this->translate($e->getMessage());

        $this->flashSession->error($content['msg']);

        $code = $this->response->getStatusCode();

        $for = "home.error.{$code}";

        $this->response->redirect(['for' => $for])->send();
    }

    protected function translate($code)
    {
        $errors = require config_path('errors.php');

        return [
            'code' => $code,
            'msg' => $errors[$code] ?? $code,
        ];
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->getDI()->getShared('config');
    }

    protected function getLogger()
    {
        $logger = new AppLogger();

        return $logger->getInstance('http');
    }

}
