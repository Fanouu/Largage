<?php


namespace Fanouu\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;
use Fanouu\Larguage;
use Fanouu\Task\LarguageTask;
use pocketmine\level\Position;

class LargageCommand extends Command {

  private $plugin;

    public function __construct(Larguage $plugin) {
        $this->plugin = $plugin;
        parent::__construct("largage", $plugin->Config()->get("LarguageCommandDescription"));
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {

        //if ($sender instanceof Player){
            
        if(isset($args[0])){
            switch($args[0]){
                case "start":
                    if($sender->hasPermission("largage.start.cmd")){
                        LarguageTask::getInstance()->start();
                    }else{
                        $sender->sendMessage($this->plugin->Config()->get("LarguageNoPerm"));
                    }
            }
        }else{
            $rdm = $this->randomTP($sender);
            $rdm = $rdm[array_rand($rdm)];
            $sender->teleport($rdm);
        }

       // }else{

            //$sender->sendMessage($this->plugin->Config()->get("LarguageNoConsole"));

        //}

    }
    
    public function randomTP($player): array{
        $position = [];
        $cfg = new Config(Larguage::getInstance()->getDataFolder() . "settings.yml", Config::YAML);
            foreach($cfg->get("LarguageTeleportPos") as $tele => $teleport){
                $tp = explode(":", $teleport);
                $pos = new Position((int)$tp[1], (int)$tp[2], (int)$tp[3], $player->getServer()->getLevelByName($tp[0]));
                $position[] = $pos;
            }
        return $position;
    }
}