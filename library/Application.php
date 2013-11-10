<?php

namespace StoredLibrary;
use \StoredLibrary;

class Application
{
    private static $_instance;
    private $Login;

    public static function getInstance() {
        if(!isset($_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        if(!isset($this->Login)) {
            $this->Login = Login::getInstance();
        }
    }

    public function run()
    {
        try {
            if (isset($_POST['action'])) {
                $value = json_decode($_POST['dataset']);
                switch($_POST['action']) {
                    case 'logar':
                        echo $this->Login->start(array('username' => $value->username, 'password' => $value->password));
                        break;
                    case 'logout':
                        echo $this->Login->logout();
                        break;
                    case 'ipArduinoSave':
                        echo Util::getInstance()->saveArduino($value, $this->Login->sessionUserName());
                        break;
                    case 'saveUserInfo':
                        echo Util::getInstance()->saveUserInfo($value, $this->Login->sessionUserName());
                        break;
                    case 'savePassword':
                        echo Util::getInstance()->savePassword($value, $this->Login->sessionUserName());
                    case 'saveEvent':
                        echo Util::getInstance()->createEvent($value, $this->Login->sessionUserId());
                        break;
                    case 'removeEvent':
                        echo Util::getInstance()->removeEvent($value, $this->Login->sessionUserId());
                        break;
                }
            } elseif(isset($_GET['action'])) {
                switch($_GET['action']) {
                    case 'loadWindow':
                        $dataSet  = json_decode($_GET['dataset']);
                        switch($dataSet){
                            case 'pageUser':
                                $DadosUser = Util::getInstance()->dadosUser($this->Login->sessionUserName());
                                $configsPageUser = array(
                                    'NAME' => $DadosUser->userName,
                                    'LASTNAME' => $DadosUser->userLastName,
                                    'LOGIN' => $DadosUser->userLogin,
                                    'MAIL' => $DadosUser->userMail,
                                    'PHOTO' => Util::getInstance()->loadURLImage($DadosUser->userPhoto),
                                    'ARDUINOIP' => $DadosUser->userArduinoIp,
                                    'ARDUINOPORT' => $DadosUser->userArduinoPort,
                                    'XBEESERIAL' => $DadosUser->userSerialXBee
                                );
                                echo Template::display(TEMPLATE_DIR.$dataSet.'.tpl.html', $configsPageUser);
                                break;
                            case "pageDashboard":
                            case "pageTemperature":
                            case "pageSobre":
                            case "pageDispSala":
                            case "pageDispCozinha":
                            case "pageDispBanheiro":
                            case "pageDispQuartos":
                            case "pageDispCorredor":
                            case "pageDispVaranda":
                            case "pageDispGaragem":
                            case "pageDispPiscina":
                                echo Template::display(TEMPLATE_DIR.$dataSet.'.tpl.html');
                                break;
                            case "pageTimeline":
                                $dadosTimeLime = Util::getInstance()->dadosTimeLine($this->Login->sessionUserId());
                                if(empty($dadosTimeLime)) {
                                    $pageTimeLine = "";
                                } else {
                                    $pageTimeLine = Util::getInstance()->generateHTMLTimelime($dadosTimeLime);
                                }
                                echo Template::display(TEMPLATE_DIR.$dataSet.'.tpl.html', array('TIMELINEEVENTS' => $pageTimeLine));
                                break;
                            default: break;
                        }
                        break;
                    case 'loadIpArduino':
                        $endeIP = Util::getInstance()->loadIpArduino($this->Login->sessionUserName());
                        if(!strpos($endeIP,'http://')) {
                            $endeIP = 'http://'.$endeIP;
                        }
                        echo $endeIP;
                        break;
                }
            }else {
                if($this->Login->sessionExists()) {
                    $configsTpl = array(
                        'HEADER' => Template::display(TEMPLATE_DIR.'pageHeader.tpl.html',
                            array('TITLE_PAGE' => TITLE_PAGE,
                                'CSSPATH' => CSSPATH)),
                        'USERSESSION' => strtoupper($this->Login->sessionUserName()),
                        'FOOTERINTRO' => Template::display(TEMPLATE_DIR.'pageFooterIntro.tpl.html'),
                        'PAGENAMEAPP' => PAGENAMEAPP,
                        'FOOTER' => Template::display(TEMPLATE_DIR.'pageFooter.tpl.html', array('JSPATH'  => JSPATH)),
                    );
                    echo Template::display(TEMPLATE_DIR.'pageIntro.tpl.html', $configsTpl);
                } else {
                    $configsTpl = array(
                        'HEADER' => Template::display(TEMPLATE_DIR.'pageHeader.tpl.html',
                            array('TITLE_PAGE' => TITLE_PAGE,
                                'CSSPATH' => CSSPATH)),
                        'LOGO_PATH' => IMAGEPATH.'logo.png',
                        'FOOTER' => Template::display(TEMPLATE_DIR.'pageFooter.tpl.html', array('JSPATH'  => JSPATH))
                    );
                    echo Template::display(TEMPLATE_DIR.'pageLogin.tpl.html', $configsTpl);
                }
            }
        }catch (\Exception $e) {
            throw $e;
        }
    }
}