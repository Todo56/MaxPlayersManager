<?php
namespace MaxPlayersManager;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Todoe56\GreekRanks\Instances\GreekPlayer;
use Todoe56\GreekRanks\Main;

class Loader extends PluginBase implements Listener {

    public Config $config;
    public static Loader $instance;

    public function onEnable()
    {
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml");

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("MaxPlayersManager", new SetMaxPlayersCommand());
        self::$instance = $this;
    }

    public function onQuit(PlayerQuitEvent $event){
        if(!Main::getInstance()->ranksManager->getPlayer($event->getPlayer())->send) $event->setQuitMessage("");
    }

    /**
     * @param PlayerJoinEvent $event
     * @priority Highest
     */
    public function onJoin(PlayerJoinEvent $event){
        if(!Main::getInstance()->ranksManager->getPlayer($event->getPlayer())->send) $event->setJoinMessage("");
    }

    public function proceedWithChecks(Player $player, GreekPlayer $greekPlayer){
        if($player->isOp()) return;
        if(count($this->getServer()->getOnlinePlayers()) < (int) $this->config->get("maxPlayers")) return;

        $mainRank = $greekPlayer->getMainRank()->getName();
        $rank = $greekPlayer->getRank() ? $greekPlayer->getRank()->getName() : "none";

        if((!in_array($mainRank, $this->config->get("allowedMainRanks")) && !in_array($rank, $this->config->get("allowedRanks"))) && count($this->getServer()->getOnlinePlayers()) >= (int) $this->config->get("maxPlayers")){
            $greekPlayer->send = false;
            $player->kick("Server is full.", false);
        }
    }

    public static function getInstance(): Loader {
        return self::$instance;
    }
}