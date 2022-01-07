<?php

namespace Fanouu\Task;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\scheduler\Task;
use Fanouu\Larguage;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use Solaria\Utils\Utils;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\item\Item;

class LarguageStartedTask extends Task{

    private $plugin;
    
    public static $claimedby = [];
    public static $times;

    public function __construct(Larguage $plugin){
        $this->plugin = $plugin;
        $this->larg_data = new Config($this->plugin->getDataFolder() . "data/larguage.json", Config::JSON);
        $this->cfg = new Config($this->plugin->getDataFolder() . "settings.yml", Config::YAML);
        $this->economyapi = Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI");
    }

    public function onRun(int $currentTick){
        
        $timeLarguage = $this->plugin->getLarguageAPI()->get("LimiteLarguageForClaim");
        if(!$timeLarguage || $timeLarguage == 0){
            self::$times = $this->cfg->get("LarguageDespawnTime")*60;
            $this->plugin->getLarguageAPI()->set("LimiteLarguageForClaim", time() + self::$times);
           
        }
        
        $minutes = $this->plugin->getLarguageAPI()->getRestantTimeForClaim("m");
        $sec = $this->plugin->getLarguageAPI()->getRestantTimeForClaim("s");
        
        if($minutes == 0){
          Server::getInstance()->broadcastMessage($this->cfg->get("LarguageDespawn"));
          $this->plugin->getScheduler()->cancelTask($this->getTaskId());
            
        }

        if($minutes === $this->getTimers() and($sec == 50)){
            Server::getInstance()->broadcastMessage(str_replace("{times}", $this->getTimers(), $this->cfg->get("LarguageDespawnAlerts")));
        }

    }
   public function getTimers(){
        $timers = $this->cfg->get("LarguageDespawnAlertsTime");
        foreach($timers as $time => $times){
            return (int)$times;
        }
    }
}