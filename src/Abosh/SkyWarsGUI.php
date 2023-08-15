<?php
namespace Abosh;

use muqsit\invmenu\InvMenuHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use muqsit\invmenu\InvMenu;

class SkyWarsGUI extends PluginBase{

	public function onEnable()
	{
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
	}



	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		if($command->getName() === "swgui"){
			if(!$sender instanceof Player) return false;
			$menu = InvMenu::create(InvMenu::TYPE_CHEST);
			$menu->readonly();
			$menu->setName("§aSkyWars");
			$inventory = $menu->getInventory();
			$item = Item::get(368, 0, 1);
			$plugin = $this->getServer()->getPluginManager()->getPlugin("SKYWARS-PRO");
			$arenas = $plugin->arenas;
			foreach ($arenas as $key => $value){
				$nbt = $item->getNamedTag();
				$nbt->setString("skywarsArena", $key);
				$item->setCustomName("§e".$key);
				$item->setLore(["§e".$value->getSlot(true)." / ".$value->getSlot()]);
				$index = array_search($value, array_values($arenas));
				$inventory->setItem((int) $index, $item);
			}
			$menu->send($sender);
			$menu->setListener(function (Player $player, Item $itemClicked, Item $itemPutIn, SlotChangeAction $inventoryAction){
				$nbt = $itemClicked->getNamedTag();
				if($nbt->hasTag("skywarsArena")){
					$this->getServer()->dispatchCommand($player, "sw join ".$nbt->getString("skywarsArena"));
					$player->removeWindow($inventoryAction->getInventory());
				}
			});
		}
		return true;
	}
}