<?php

namespace StoredLibrary;
use StoredLibrary\Connection as Db;

class Util {

    private static $instance;
    private $SessionUtil;
    private $coresTimeLime = array('timeline-yellow', 'timeline-blue', 'timeline-green', 'timeline-purple', 'timeline-red', 'timeline-grey');
    private $valuesDeParaDisp = array('LuzSala' => 'a luz da sala',
                                      'LuzQuarto1' => 'a luz do quarto1',
                                      'LuzQuarto2' => 'a luz do quarto2',
                                      'LuzBanheiro' => 'a luz do banheiro',
                                      'LuzCozinha' => 'a luz da cozinha',
                                      'LuzCorredor' => 'a luz do corredor',
                                      'LuzVaranda' => 'a luz da varanda',
                                      'PortaoGaragem' => 'o portao da garagem',
                                      'LuzGaragem' => 'a luz da garagem',
                                      'BombaPiscina' => 'a bomba da piscina',
                                      'Piscina' => 'as luzes da piscina');

    public static function getInstance() {
        if(!isset($instance)) {
            return self::$instance = new self();
        } else {
            return self::$instance;
        }
    }

    public function __construct() {
        if(!isset($this->SessionUtil)) {
            $this->SessionUtil = new Session();
        }
    }

    public function verifyUserPass($login, $password) {
        $db = Db::getInstance()->select()
           ->from('user', array('userId, userLogin'))
           ->where('userLogin = ?', $login)
           ->andWhere('userPass = ?',$this->encrypt($password));
        return $db->fetchAll();
    }

    public function dadosUser($login) {
        $db = Db::getInstance()->select()
            ->from('user')
            ->where('userLogin = ?', $login);
        return $db->fetchObject();
    }

    public function saveArduino($data, $user) {
        $arrayValues = array('userArduinoIp' => $data->ipArduino, 'userArduinoPort' => $data->portaArduino, 'userSerialXBee' => $data->serialXBee);
        $where       = "userLogin = '$user'";
        if($this->SessionUtil->isRegisteredParam('dataArduino')) {
            $this->SessionUtil->updateParam('dataArduino', $data->ipArduino.':'.$data->portaArduino);
            $this->SessionUtil->renew();
        }
        Db::getInstance()->update('user', $arrayValues, $where);
        return '1';
    }

    public function saveUserInfo($data, $user) {
        $arrayValues = array('userName' => $data->name, 'userLastName' => $data->lastname, 'userMail' => $data->email);
        $where       = "userLogin = '$user'";
        Db::getInstance()->update('user', $arrayValues, $where);
        return '1';
    }

    public function savePassword($data, $user) {
        $arrayValues = array('userPass' => $this->encrypt($data->password));
        $where       = "userLogin = '$user'";
        Db::getInstance()->update('user', $arrayValues, $where);
        return '1';
    }

    public function loadIpArduino($login) {
        if(!$this->SessionUtil->isRegisteredParam('dataArduino')) {
            $db = Db::getInstance()->select()
                ->from('user', array('userArduinoIp', 'userArduinoPort'))
                ->where('userLogin = ?', $login);
            $values = $db->fetchRow();
            $this->SessionUtil->register('dataArduino', $values['userArduinoIp'].':'.$values['userArduinoPort']);
            return $this->SessionUtil->getSession()['dataArduino'];
        } else {
            return $this->SessionUtil->getSession()['dataArduino'];
        }
    }

    public function createEvent($objEvent, $userId) {
        $objInsert = array('timeExecution' => $objEvent->timeExec,
                           'usesCheckPresence' => empty($objEvent->presenca) ? 0 : 1,
                           'averageConsumption' => $objEvent->consumoMedioValue,
                           'usesAverageConsumption' => empty($objEvent->consumomedio) ? 0 : 1,
                           'actionExecute' => empty($objEvent->acao) ? 0 : 1,
                           'descriptionEvent' => htmlspecialchars($objEvent->description, ENT_QUOTES),
                           'dispEvent' => $objEvent->disp,
                           'userId' => $userId,
                           'dateCreate' => 'now()');
        return Db::getInstance()->insert('userevent', $objInsert, true);
    }

    public function removeEvent($idEvent, $userId) {
        return Db::getInstance()->delete('userevent', "idEvent=".$idEvent." and userId=".$userId);
    }

    public function dadosTimeLine($userId) {
        $db = Db::getInstance()->select()
            ->from('userevent')
            ->where('userId', $userId);
        return $db->fetchAll();
    }

    public function generateHTMLTimelime($timeline) {
        $pageTimeLine = "";
        shuffle($this->coresTimeLime);
        $iterator = 0;
        foreach($timeline as $values) {
            $acao = ($values['actionExecute'] == 1 ? 'Ligar' : 'Desligar');
            if($values['dispEvent'] == 'PortaoGaragem' && $values['actionExecute'] == 1)
                $acao = 'Abrir';
            else {
                if($values['actionExecute'] == 0 && $values['dispEvent'] = 'PortaoGaragem') {
                    $acao = 'Fechar';
                }
            }
            $pageTimeLine .= '<li class="'.$this->coresTimeLime[$iterator].'" id="idTimeLime'.$values['idEvent'].'">';
                $pageTimeLine .= '<div class="timeline-time">';
                    $pageTimeLine .= '<span class="date">Criado em '. date('d/m/Y H:m:s', strtotime($values['dateCreate'])).' </span>';
                    $pageTimeLine .= '<span class="time">'.date('H:m', strtotime($values['timeExecution'])).'</span>';
                $pageTimeLine .= '</div>';
                $pageTimeLine .= '<div class="timeline-icon"><i class="icon-lightbulb"></i></div>';
                $pageTimeLine .= '<div class="timeline-body">';
                    $pageTimeLine .= '<h2>'.$acao.' '.$this->valuesDeParaDisp[$values['dispEvent']].'</h2>';
                    $pageTimeLine .= '<div class="timeline-content">';
                        $pageTimeLine .= empty($values['descriptionEvent']) ? "" : $values['descriptionEvent'].' <br/>';
                        if($values['usesAverageConsumption']) {
                            $pageTimeLine .= '<span><i class="icon-bar-chart"></i> Utiliza checagem por consumo médio: '.$values['averageConsumption'].'</span><br/>';
                        }
                        if($values['usesCheckPresence']) {
                            $pageTimeLine .= '<span><i class="icon-user"></i> Checa a presença de usuários</span>';
                        }
                    $pageTimeLine .= '</div>';
                    $pageTimeLine .= '<div class="timeline-footer"><a href="javascript: void(0)" onclick="Framework.removeEvent('.$values['idEvent'].');" class="nav-link pull-right">Excluir <i class="icon-trash"></i></a></div>';
                $pageTimeLine .= '</div>';
            $pageTimeLine .= '</li>';
            $iterator++;
            if($iterator >= sizeof($this->coresTimeLime)) {
                $iterator = 0;
            }
        }
        return $pageTimeLine;
    }

    public function loadURLImage($imageName) {
        return (empty($imageName) ? IMAGE_DIR.'no-image.png' : IMAGE_DIR.$imageName);
    }

    public static function encrypt($text, $salt = 'TEAM4')
    {
        return trim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    $salt,
                    $text,
                    MCRYPT_MODE_ECB,
                    mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)
                )
            )
        );
    }


    public static function decrypt($text, $salt = 'TEAM4')
    {
        return trim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                $salt,
                base64_decode($text),
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)
            )
        );
    }

}
