<?php

namespace Fanouu\Entity;

use Fanouu\Larguage;
use Fanouu\Task\LarguageStartedTask;
use Fanouu\Entity\LarguageBoiteEntity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\utils\Config;

class LarguageEntity extends Human {

    public function __construct(Level $level, CompoundTag $nbt) {
        $path = Larguage::getInstance()->getDataFolder() . "LarguageTextures/Larguage.png";
        $data = Larguage::getInstance()->PNGtoBYTES($path);
        $cape = "";
        $path = Larguage::getInstance()->getDataFolder() . "LarguageTextures/Larguage.json";
        $geometry = file_get_contents($path);

        $skin = new Skin($this->getName(), $data, $cape, "geometry.Larguage", $geometry);

        $this->setSkin($skin);
        parent::__construct($level, $nbt);
    }

    public function attack(EntityDamageEvent $source): void {
       $this->boite();
    }

    public function onUpdate(int $currentTick): bool {
        $this->motion->y = -0.1;
        if($this->isOnGround()) {
            $this->boite();

        }
        return parent::onUpdate($currentTick);
    }

    public function boite() {
        
        $cfg = new Config(Larguage::getInstance()->getDataFolder() . "settings.yml", Config::YAML);
        $this->flagForDespawn();
        $nbt = self::createBaseNBT($this->asVector3()->add(0, 1));
        $largageBoite = self::createEntity("LarguageBoiteEntity", $this->level, $nbt);
        $largageBoite->setNameTagAlwaysVisible(true);
        $largageBoite->spawnToAll();
        LarguageBoiteEntity::$hp[$largageBoite->getId() . "_id"] = $cfg->get("LarguagePv");
        Larguage::getInstance()->getScheduler()->scheduleRepeatingTask(new LarguageStartedTask(Larguage::getInstance()), 20);
        
        Larguage::getInstance()->getLarguageAPI()->set("LimiteLarguageForClaim", 0);

    }

    public function applyGravity(): void {
        //   parent::applyGravity();
    }
    
    public function getName(): string {
        return "LargageEntity";
    }

}