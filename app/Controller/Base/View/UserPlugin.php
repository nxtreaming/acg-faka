<?php
declare(strict_types=1);

namespace App\Controller\Base\View;


use App\Model\Business;
use App\Model\Config;
use App\Util\Client;
use Kernel\Exception\ViewException;
use Kernel\Util\View;

/**
 * Class UserPlugin
 * @package App\Controller\Base\View
 */
abstract class UserPlugin extends \App\Controller\Base\User
{
    /**
     * @param string $title
     * @param string $template
     * @param array $data
     * @param bool $controller
     * @return string
     * @throws \Kernel\Exception\ViewException
     */
    public function render(string $title, string $template, array $data = [], bool $controller = false): string
    {
        try {
            $data['title'] = $title;
            $cfg = Config::list();
            foreach ($cfg as $k => $v) {
                $data["config"][$k] = $v;
            }
            $domain = Client::getDomain();
            $business = Business::query()->where("subdomain", $domain)->first() ?? Business::query()->where("topdomain", $domain)->first();
            if ($business) {
                $data['config']['shop_name'] = $business->shop_name;
                $data['config']['title'] = $business->title;
                $data['config']['notice'] = $business->notice;
                $data['config']['service_url'] = $business->service_url != "" ? $business->service_url : "https://wpa.qq.com/msgrd?v=1&uin={$business->service_qq}";
            }
            $user = $this->getUser();
            if ($user) {
                $data['user'] = $user;
                $data['group'] = $this->getUserGroup()->toArray();
            }
            return View::render($template, $data, BASE_PATH . "/app/Plugin/" . ($controller ? \Kernel\Util\Plugin::$currentControllerPluginName : \Kernel\Util\Plugin::$currentPluginName) . "/View");
        } catch (\SmartyException $e) {
            throw new ViewException($e->getMessage());
        }
    }
}