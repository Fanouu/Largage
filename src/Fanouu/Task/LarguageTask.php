<?php

namespace Fanouu\Task;

use pocketmine\Server;
use pocketmine\scheduler\Task;
use Fanouu\Larguage;
use pocketmine\utils\Config;
use Solaria\Utils\Utils;
use Fanouu\LarguageAPI;
use Fanouu\Entity\LarguageEntity;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\level\Position;

class LarguageTask extends Task{

    private $plugin;
    public static $times = null;
    private static $me;

    public function __construct(Larguage $plugin){
        $this->plugin = $plugin;
        $this->larg_data = new Config($this->plugin->getDataFolder() . "data/larguage.json", Config::JSON);
        $this->cfg = new Config($this->plugin->getDataFolder() . "settings.yml", Config::YAML);
        self::$me = $this;
    }

    public function onRun(int $currentTick){
        $timer = intval($this->larg_data->get("Larguage_nexTime") - time());
          $heures = $this->plugin->getLarguageAPI()->getRestantTime("h");
        
        $minutes = $this->plugin->getLarguageAPI()->getRestantTime("m");
        $sec = $this->plugin->getLarguageAPI()->getRestantTime("s");
        

        $timeLarguage = $this->plugin->getLarguageAPI()->get("Larguage_nexTime");
        if(!$timeLarguage or($timeLarguage <= 0)){
            $unity = $this->cfg->get("LarguageTimeUnity");
            if($unity == "seconds"){
                self::$times = $this->cfg->get("LarguageTime");
            }
            if($unity == "minutes"){
                self::$times = $this->cfg->get("LarguageTime")*60;
            }
            if($unity == "hours"){
                self::$times = $this->cfg->get("LarguageTime")*60*60;
            }
            $this->plugin->getLarguageAPI()->set("Larguage_nexTime", time() + self::$times);
            
            //$this->plugin->getKothAPI()->set("koth_nexTime", time() + 30);
           
        }
        if($heures == 0){
        if($minutes === $this->getTimers() and($sec == 50)){
            Server::getInstance()->broadcastMessage(str_replace("{times}", $this->getTimers(), $this->cfg->get("LarguageSpawnAlerts")));
        }

        if($minutes == 0 && $this->larg_data->get("LarguageEnable") === true){
            $this->start();
        }
        }
    }
    
    public function start(){
      $this->plugin->getLarguageAPI()->set("LarguageEnable", false);
      $this->plugin->getLarguageAPI()->set("Larguage_nexTime", 0);
      Server::getInstance()->broadcastMessage($this->cfg->get("LarguageStarted"));
      $LarguagePos = $this->cfg->get("LarguagePos");
      foreach($LarguagePos as $coord => $pos){
        $pos1 = explode(":", $pos);
        $nbt = Entity::createBaseNBT(new Vector3((int)$pos1[1] + 0.5, (int)$pos1[2] + 50, (int)$pos1[3] + 0.5));
        $larguageEntity = Entity::createEntity("LarguageEntity", $this->plugin->getServer()->getLevelByName($pos1[0]), $nbt);
        $larguageEntity->setNameTagAlwaysVisible(false);
        $larguageEntity->spawnToAll();
      }
    }
    
    public static function getInstance(): LarguageTask{
        return self::$me;
    }
    
    public function getTimers(){
        $timers = $this->cfg->get("LarguageSpawnAlertsTime");
        foreach($timers as $time => $times){
            return (int)$times;
        }
    }
}