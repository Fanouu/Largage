<?php

namespace Fanouu;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Fanouu\Entity\LarguageEntity;
use Fanouu\Entity\LarguageBoiteEntity;
use pocketmine\entity\Entity;
use Fanouu\LarguageAPI;
use Fanouu\Task\LarguageTask;
use pocketmine\entity\Human;
use Fanouu\Command\LargageCommand;

class Larguage extends PluginBase{
    
    private static $larguage;
    private static $largAPI;
    
    public function onEnable(){
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "data");
        $this->saveResource("settings.yml");
        $this->getLogger()->info("--------------------------");
        $this->getLogger()->info("Plugin Larguage loaded");
        $this->getLogger()->info("Larguage Spawn Position:");
        foreach($this->Config()->get("LarguagePos") as $coord => $pos){
            $this->getLogger()->info($pos);
        }
        $this->getLogger()->info("--------------------------");
               
        $this->saveResource("LarguageTextures/Larguage.json");
        $this->saveResource("LarguageTextures/Larguage.png");
        $this->saveResource("LarguageTextures/LarguageBoite.json");
        $this->saveResource("LarguageTextures/LarguageBoite.png");
        Entity::registerEntity(LarguageEntity::class, true, ["LarguageEntity"]);
        Entity::registerEntity(LarguageBoiteEntity::class, true, ["LarguageBoiteEntity"]);
        self::$larguage = $this;
        self::$largAPI = new LarguageAPI($this);
        $this->getLarguageAPI()->set("Larguage_nexTime", 0);
        $this->getLarguageAPI()->set("LarguageEnable", false);
        $this->getScheduler()->scheduleRepeatingTask(new LarguageTask($this), 20);
        $this->deleteAllEntity();
        
        $this->getServer()->getCommandMap()->registerAll("largage", [
            new LargageCommand($this),
        ]);
   }
   public function deleteAllEntity(){
        foreach($this->getServer()->getLevels() as $level){
            foreach($level->getEntities() as $entity){
                   
                    $this->getLogger()->info($entity->getName() . "was deleted");
                    $entity->flagForDespawn();
            }
         }
               
        
    }
    
    public function Config(){
        return new Config($this->getDataFolder() . "settings.yml", Config::YAML);
    }
               
    public function PNGtoBYTES($path) : string{
        $img = @imagecreatefrompng($path);
        $bytes = "";
        for ($y = 0; $y < (int) @getimagesize($path)[1]; $y++) {
            for ($x = 0; $x < (int) @getimagesize($path)[0]; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $bytes .= chr(($rgba >> 16) & 0xff) . chr(($rgba >> 8) & 0xff) . chr($rgba & 0xff) . chr(((~(($rgba >> 24))) << 1) & 0xff);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }
    
    public static function getInstance(): Larguage{
        return self::$larguage;
    }
    
    public function getLarguageAPI(): LarguageAPI {
        return self::$largAPI;
    }
}