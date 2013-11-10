<?php

namespace StoredLibrary;
use StoredLibrary\Util;
use StoredLibrary\Template as Template;

class Login
{
    private static $_instance;
    private $_Session;

    public static function getInstance() {
        if(!isset($_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct(){
        if(!isset($this->_Session)) {
            $this->_Session = new Session(10);
        }
    }

    public function start ($data) {
        $values = Util::getInstance()->verifyUserPass($data['username'], $data['password']);
        if(!empty($values)) {
            $this->_Session->register('userLogin', array('userId' => Util::encrypt($values[0]['userId']), 'userName' => Util::encrypt($data['username'])));
            $this->_Session->renew();
            $configsTpl = array(
                'HEADER' => '',
                'USERSESSION' => strtoupper($this->sessionUserName()),
                'FOOTERINTRO' => Template::display(TEMPLATE_DIR.'pageFooterIntro.tpl.html'),
                'PAGENAMEAPP' => PAGENAMEAPP,
                'FOOTER' => Template::display(TEMPLATE_DIR.'pageFooter.tpl.html', array('JSPATH'  => JSPATH)),
            );
            return json_encode(array('error' => 0, 'msg' => Template::display(TEMPLATE_DIR.'pageIntro.tpl.html', $configsTpl)));
        } else {
            return json_encode(array('error' => 1, 'msg' => 'Usuário ou senha inválido.'));
        }
    }

    public function sessionExists() {
        if(isset($this->_Session)) {
            if($this->_Session->isRegisteredParam('userLogin')) {
                $this->_Session->renew();
                return true;
            }
        }
        return false;
    }

    public function sessionUserName() {
        if(isset($this->_Session)) {
            if($this->_Session->isRegisteredParam('userLogin')) {
                return Util::decrypt($this->_Session->getSession()['userLogin']['userName']);
            }
        }
    }

    public function sessionUserId() {
        if(isset($this->_Session)) {
            if($this->_Session->isRegisteredParam('userLogin')) {
                return Util::decrypt($this->_Session->getSession()['userLogin']['userId']);
            }
        }
    }


    public function logout () {
        if(isset($this->_Session)) {
            if($this->_Session->isRegisteredParam('userLogin')) {
                $this->_Session->end();
            }
        }
    }


}