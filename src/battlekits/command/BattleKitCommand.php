<?php
namespace battlekits\command;

use battlekits\BattleKits;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;

class BattleKitCommand extends Command implements PluginIdentifiableCommand{
    private $main;
    public function __construct(BattleKits $main){
        parent::__construct("battlekits", "Get your kits.", "/kit [name]", ["bk", "kit"]);
        $this->main = $main;
    }
    public function execute(CommandSender $sender, $label, array $args){
        if(isset($args[0])){
            if($sender instanceof Player){
                $this->getPlugin()->getKitPaymentController()->grantKit($args[0], $sender);
            }
            else{
                $sender->sendMessage("Please run command in game.");
            }
        }
        else{
            if($sender->hasPermission("battlekits.listkits")){
                $count = 0;
                foreach($this->getPlugin()->getKitStore()->getKits() as $name => $kit){
                    if($kit->isFree() || $this->getPlugin()->isLinkedToEconomy()){
                        if($sender instanceof Player){
                            if($kit->isActiveIn($sender->getLevel())){
                                $sender->sendMessage(sprintf($this->getPlugin()->getConfig()->get('list-format'), $name, $this->getPlugin()->getConfig()->get('econ-prefix'), ($kit->isFree() ? "0" : $kit->getCost())));
                                $count++;
                            }
                        }
                        else{
                            $sender->sendMessage("$name: " . $this->getPlugin()->getConfig()->get('econ-prefix') . ($kit->isFree() ? "0" : $kit->getCost()));
                            $count++;
                        }
                    }
                }
                $sender->sendMessage("Listed $count of " . count($this->getPlugin()->getKitStore()->getKits()));
            }
        }
    }
    public function getPlugin(){
        return $this->main;
    }
}