<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/


if (!defined('IN_ANWSION'))
{
	die;
}

define('IN_MOBILE', true);

class main extends AWS_CONTROLLER
{
    public function get_access_rule()
    {
        $rule_action['rule_type'] = 'white';
        $rule_action['actions'] = array();

        return $rule_action;
    }

    public function setup()
    {
        if ($_GET['ignore_ua_check'] == 'FALSE')
        {
            HTTP::set_cookie('_ignore_ua_check', 'FALSE');
        }


//        if (!$this->user_id AND !$this->user_info['permission']['visit_site'] AND $_GET['act'] != 'login' AND $_GET['act'] != 'register')
//        {
//            HTTP::redirect(base64_encode($_SERVER['REQUEST_URI']));
//        }

        switch ($_GET['act'])
        {
            default:
                if (!$this->user_id)
                {
                    HTTP::redirect('/m/login/url-' . base64_encode($_SERVER['REQUEST_URI']));
                }
                break;

            case 'index':
            case 'explore':
            case 'login':
            case 'question':
            case 'register':
            case 'topic':
            case 'search':
            case 'people':
            case 'article':
            case 'find_password':
            case 'find_password_success':
            case 'find_password_modify':
                // Public page..
                break;
        }

        TPL::import_clean();

        TPL::import_css(array(
            'mobile/css/mobile.css'
        ));

        TPL::import_js(array(
            'js/jquery.2.js',
            'js/jquery.form.js',
            'mobile/js/framework.js',
            'mobile/js/aws-mobile.js',
            'mobile/js/app.js',
            'mobile/js/aw-mobile-template.js'
        ));

        if (in_weixin())
        {
            $noncestr = mt_rand(1000000000, 9999999999);

            TPL::assign('weixin_noncestr', $noncestr);

            $jsapi_ticket = $this->model('openid_weixin_weixin')->get_jsapi_ticket($this->model('openid_weixin_weixin')->get_access_token(get_setting('weixin_app_id'), get_setting('weixin_app_secret')));

            $url = ($_SERVER['HTTPS'] AND !in_array(strtolower($_SERVER['HTTPS']), array('off', 'no'))) ? 'https' : 'http';

            $url .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            TPL::assign('weixin_signature', $this->model('openid_weixin_weixin')->generate_jsapi_ticket_signature(
                $jsapi_ticket,
                $noncestr,
                TIMESTAMP,
                $url
            ));
        }
    }

    public function home_action()
    {



        if (!$this->user_id)
        {
            HTTP::redirect(get_js_url('/ml/home/'));
        }

        TPL::output('ml/home');
    }
}