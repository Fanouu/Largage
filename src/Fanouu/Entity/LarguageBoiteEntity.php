<?php

namespace Fanouu\Entity;

use Fanouu\Larguage;
use Fanouu\Task\LarguageStartedTask;
use pocketmine\entity\Human;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Server;
use pocketmine\utils\Config;

class LarguageBoiteEntity extends Human {
    
    public static $hp = [];

    public function __construct(Level $level, CompoundTag $nbt) {
        $path = Larguage::getInstance()->getDataFolder() . "LarguageTextures/LarguageBoite.png";
        $data = Larguage::getInstance()->PNGtoBYTES($path);
        $cape = "";
        $path = Larguage::getInstance()->getDataFolder() . "LarguageTextures/LarguageBoite.json";
        $geometry = file_get_contents($path);

        $skin = new Skin($this->getName(), $data, $cape, "geometry.LarguageBoite", $geometry);

        $this->setSkin($skin);
        parent::__construct($level, $nbt);
    }

    public function attack(EntityDamageEvent $event): void {
        $cfg = new Config(Larguage::getInstance()->getDataFolder() . "settings.yml", Config::YAML);
        if(!isset(self::$hp[$event->getEntity()->getId() . "_id"])){
            self::$hp[$event->getEntity()->getId() . "_id"] = $cfg->get("LarguagePv");
        }
        if($event instanceof EntityDamageByEntityEvent){
          $player = $event->getDamager();
          if($player instanceof Player){
            $itemIndex = $player->getInventory()->getHeldItemIndex();
             $item = $player->getInventory()->getItem($itemIndex);
             
             if($item->getId() == 511){
                $this->flagForDespawn();
             }
             $hp = self::$hp[$event->getEntity()->getId() . "_id"];
             $eid = $event->getEntity()->getId() . "_id";
             self::$hp[$eid] = $hp - 1;
             $player->sendPopup(str_replace("{hp}", self::$hp[$eid], $cfg->get("LarguageHpRestant")));
             if(self::$hp[$eid] <= 0){
               $this->flagForDespawn();
               Server::getInstance()->broadcastMessage(str_replace("{player}", $player->getName(), $cfg->get("LarguageClaim")));
                 if($cfg->get("LarguageRandomDrop") === false){
                   $this->addDrop($player, $event->getEntity());
                 }else{
                     $rdm = $this->addDrop($player, $event->getEntity());
                     $rdm = $rdm[array_rand($rdm)];
                     $player->getLevel()->dropItem($event->getEntity(), $rdm);
                 }
             }
          }
        }
    }

    public function onUpdate(int $currentTick): bool {
        $this->motion->y = -0.8;
        $minutes = Larguage::getInstance()->getLarguageAPI()->getRestantTimeForClaim("m");
        if($minutes == 0){
            $this->flagForDespawn();
        }
        return parent::onUpdate($currentTick);
    }

    public function applyGravity(): void {
        //   parent::applyGravity();
    }
    
    public function getName(): string {
        return "LarguageBoiteEntity";
    }
    
    public function addDrop($player, $entity){
        $cfg = new Config(Larguage::getInstance()->getDataFolder() . "settings.yml", Config::YAML);
        $items = [];
        if($cfg->get("LarguageRandomDrop") === true){
            foreach($cfg->get("LarguageDrop") as $drop => $drops){
                $dropes = explode(",", $drops);
                $item = Item::get((int)$dropes[0], (int)$dropes[1], (int)$dropes[2]);
                if(isset($dropes[3]) && isset($dropes[4])){
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment((int)$dropes[3]), (int)$dropes[4]));
                }
                $items[] = $item;
            }
            return $items;
        }else if($cfg->get("LarguageRandomDrop") === false){
            foreach($cfg->get("LarguageDrop") as $drop => $drops){
                $dropes = explode(",", $drops);
                $item = Item::get((int)$dropes[0], (int)$dropes[1], (int)$dropes[2]);
                if(isset($drops[3]) && isset($drops[4])){
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment((int)$dropes[3]), (int)$dropes[4]));
                }
                
                $player->getLevel()->dropItem($entity, $item);
            }
        }
    }

}