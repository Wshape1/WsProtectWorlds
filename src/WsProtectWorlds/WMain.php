<?php

namespace WsProtectWorlds;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\level\Level;
use pocketmine\block\block;

class WMain extends PluginBase implements Listener{
public function onEnable(){
$this->getServer()->getPluginManager()->registerEvents($this, $this);
if(!is_file($this->getDataFolder())){
@mkdir($this->getDataFolder(),0777,true);
$this->saveResource("Config.yml");
$this->Config=new Config($this->getDataFolder()."Config.yml",2);
}
}

public function BreakAndPlace($event){
$player=$event->getPlayer();
$name=$player->getName();
$level=$player->getLevel()->getFolderName();
$msg=$this->Config->get("Message");
$prefix=$this->Config->get("Prefix");
$type=$this->Config->get("SendType");
if(in_array($level,$this->Config->get("Worlds")) and !in_array($name,$this->Config->get("Admins"))){
$event->setCancelled(true);
if($type == 0){
$player->sendMessage($prefix.$msg);
}
if($type == 1){
$player->sendTip($prefix.$msg);
}
if($type == 2){
$player->sendPopup($prefix.$msg);
}
}
}
public function BreakBlock(BlockBreakEvent $event){
$this->BreakAndPlace($event);
}
public function PlaceBlock(BlockPlaceEvent $event){
$this->BreakAndPlace($event);
}
public function onCommand(CommandSender $sender, Command $command, $label, array $args){
	$prefix=$this->Config->get("Prefix");
	$name=$sender->getName();
	$worlds=$this->Config->get("Worlds");
	$admins=$this->Config->get("Admins");
	$cadmins=$this->Config->get("CmdAdmins");
	switch($command->getName()){
		case "wpw":
		if(!isset($args[0])){
			$sender->sendMessage($prefix."\n§b/wpw wadd <世界名字> --添加一个世界被保护\n§b/wpw wdel <世界名字> --移除一个被保护世界\n§b/wpw aadd <名字> --添加一个世界管理员\n§b/wpw adel <名字> --移除一个世界管理员\n§b/wpw list --查看被保护的世界,管理员");
}
                    if(isset($args[0])){
					switch($args[0]){
						case "wadd":
						if(in_array($name,$cadmins)){
						if(isset($args[1])){
						if(!in_array($args[1],$worlds)){
								$sender->sendMessage($prefix."§b成功添加{$args[1]}到世界保护");
								$worlds[]=$args[1];
								$this->Config->set("Worlds",$worlds);
								$this->Config->save();
							}else{
								$sender->sendMessage($prefix."§c世界{$args[1]}已存在");
							}
	}else{
		$sender->sendMessage($prefix."§c使用有误 §b用法/wpw wadd <世界名字>");
	}
						}else{
								$sender->sendMessage($prefix."§c你没有权限使用");
							}
							return true;
	              
					case "wdel":
					if(in_array($name,$cadmins)){
						if(isset($args[1])){
							if(in_array($args[1],$worlds)){
								$sender->sendMessage($prefix."§b成功从世界保护列表删除{$args[1]}");
								$tou=array_search($args[1], $worlds);
								$tou=array_splice($worlds, $tou, 1); 
								$this->Config->set("Worlds",$worlds);
								$this->Config->save();
							}else{
								$sender->sendMessage($prefix."§c世界{$args[1]}没有被保护");
							}
	}else{
		$sender->sendMessage($prefix."§c使用有误 §b用法/wpw wdel <世界名字>");
	}
					}else{
								$sender->sendMessage($prefix."§c你没有权限使用");
							}
			return true;
					case "aadd":
					if(in_array($name,$cadmins)){
					if(isset($args[1])){
							if(!in_array($args[1],$admins)){
								$admins[]=$args[1];
								$sender->sendMessage($prefix."§b成功添加{$args[1]}到世界管理员");
								$this->Config->set("Admins",$admins);
								$this->Config->save();
							}else{
								$sender->sendMessage($prefix."§c管理员{$args[1]}已存在");
							}
	}else{
		$sender->sendMessage($prefix."§c使用有误 §b用法/wpw aadd <名字>");
	}
}else{
								$sender->sendMessage($prefix."§c你没有权限使用");
							}
							return true;
	                case "adel":
					if(in_array($name,$cadmins)){
					if(isset($args[1])){
							if(in_array($args[1],$admins)){
								$sender->sendMessage($prefix."§b成功从世界管理员列表删除{$args[1]}");
								$tou=array_search($args[1], $admins);
								$tou=array_splice($admins, $tou, 1); 
								$this->Config->set("Admins",$admins);
								$this->Config->save();
							}else{
								$sender->sendMessage($prefix."§c管理员{$args[1]}不存在");
							}
	}else{
		$sender->sendMessage($prefix."§c使用有误 §b用法/wpw adel <名字>");
	}
					}else{
						$sender->sendMessage($prefix."§c你没有权限使用");
					}
					return true;
	                case "list":
						if($sender->isOp()){
							$worlds_=implode(",",$worlds);
							$admins_=implode(",",$admins);
							$sender->sendMessage($prefix."世界:\n§b".$worlds_."\n".$prefix."管理员:\n§b".$admins_);
						}else{
							$sender->sendMessage($prefix."§c你没有权限使用");
						}
					return true;

						
						case "cadd":
						if($sender instanceof Player){
							$sender->sendMessage($prefix."§c你没有权限使用");
						}else{
						if(isset($args[1])){
							if(!in_array($args[1],$cadmins)){
							$cadmins[]=$args[1];
							$sender->sendMessage($prefix."§b成功设置{$args[1]}为命令管理员");
								$this->Config->set("CmdAdmins",$cadmins);
							$this->Config->save();
							}else{
								$sender->sendMessage($prefix."§c命令管理员{$args[1]}已存在");
							}
						}else{
						$sender->sendMessage($prefix."§c使用有误 §b用法/wpw cadd <名字>");
						}
						}
						return true;
						case "cdel":
						if($sender instanceof Player){
						$sender->sendMessage($prefix."§c你没有权限使用");
					}else{
						if(isset($args[1])){
							if(in_array($args[1],$cadmins)){
							$sender->sendMessage($prefix."§b成功从命令管理员删除{$args[1]}");
							    $tou=array_search($args[1], $cadmins);
								$tou=array_splice($cadmins, $tou, 1);
								$this->Config->set("CmdAdmins",$cadmins);
								$this->Config->save();
							}else{
								$sender->sendMessage($prefix."§c{$args[1]}不在命令管理员列表");
							}
						}else{
						$sender->sendMessage($prefix."§c使用有误 §b用法/wpw cdel <名字>");
						}
					}
					return true;
					}
}
	}
}
}



