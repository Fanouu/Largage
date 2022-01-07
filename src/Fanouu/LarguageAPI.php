<?php

namespace Fanouu;

use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;

use Fanouu\Larguage;
use Fanouu\Task\LarguageStartedTask;

class LarguageAPI{
  
  private $plugin;
  
  public function __construct(Larguage $plugin){
    $this->plugin = $plugin;
    $this->larg_data = new Config($this->plugin->getDataFolder() . "data/larguage.json", Config::JSON);
    $this->cfg = new Config($this->plugin->getDataFolder() . "settings.yml", Config::YAML);
  }
    
  public function getRestantTime($key){
      $timer = intval($this->larg_data->get("Larguage_nexTime") - time());
      $heures = intval(abs($timer / 3600));
      $minutes = intval(abs($timer / 60) % 60);
      $secondes = intval(abs($timer  - $minutes * 60));
      if($key == "h") return $heures;
      if($key == "m") return $minutes;
      if($key == "s") return $secondes;
    }
    
    public function getRestantTimeForClaim($key){
      $timer = intval($this->larg_data->get("LimiteLarguageForClaim") - time());
          $heures = intval(abs($timer / 3600));
          $minutes = intval(abs($timer / 60) % 60);
          $secondes = intval(abs($timer  - $minutes * 60));
      if($key == "h") return $heures;
      if($key == "m") return $minutes;
      if($key == "s") return $secondes;
  }

  public function get($key){
    return $this->larg_data->get($key);
  }

  public function set($key, $keys){
    $this->larg_data->set($key, $keys);
    $this->larg_data->save();
  }
}