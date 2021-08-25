<?php
namespace MaxPlayersManager;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;

class SetMaxPlayersCommand extends Command {

    public function __construct()
    {
        parent::__construct("setmaxplayers", "Set the maximum players allowed in the server.", "/setmaxplayers 100", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender->isOp()){
            $sender->sendMessage("§cYou do not have permission to use this command.");
            return;
        }

        if(!isset($args[0])){
            $sender->sendMessage("§cMention a valid number.");
            return;
        }

        $newMaxPlayers = (int) $args[0];

        Loader::getInstance()->config->set("maxPlayers", $newMaxPlayers);
        Loader::getInstance()->config->save();
        $sender->sendMessage("§2New player limit set to $newMaxPlayers successfully.");
    }
}