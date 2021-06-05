<?php

/** 
*     _    _ _                  ____ 
*    / \  | (_) ___ __ _ _ __  / ___|
*   / _ \ | | |/ __/ _` | '_ \| |    
*  / ___ \| | | (_| (_| | | | | |___ 
* /_/   \_\_|_|\___\__,_|_| |_|\____|
*                                 
*                                  
*  -I'm getting stronger if I'm not dying-
*
* @version 1.0
* @author AlicanCopur
* @copyright HashCube Network © | 2015 - 2021
* @license Açık yazılım lisansı altındadır. Tüm hakları saklıdır. 
*/                                   

namespace AlicanCopur;

use pocketmine\{
	plugin\PluginBase,
	command\Command,
	command\CommandSender,
        level\Level
};

class WorldConverter extends PluginBase {
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		$this->startConversion();
		return true;
	}
	private function startConversion(): void{
		//TODO: better way instead of 'system()'
		system("cd ".$this->getServer()->getDataPath());
		$levels = scandir($this->getServer()->getDataPath()."worlds");
		foreach($levels as $level){
		    if($level == "." || $level == "..") continue;
			$this->convert($level);
		}
		$this->getLogger()->info("=================================");
		$this->getLogger()->info("Conversion successfully finished!");
		$this->getLogger()->info("=================================");
	}
	public function convert($level): void{
		$this->getLogger()->info("Level " . $level . " is converting...");
		$world = $this->getServer()->getLevelByName($level);
                if($world instanceof Level) $world->unload();
		$this->anvilToRegion($level);
		$this->regionToPMAnvil($level);
		$dir = $this->getServer()->getDataPath()."worlds/".$level."/region/";
		$files = scandir($dir);
		foreach($files as $file){
		    if($file == "." || $file == "..") continue;
			$info = pathinfo($file);
    		$format = $info['extension'];
    		if($format == "mca" || $format == "mcr")
    			unlink($dir.$file);
		}
		$this->getServer()->loadLevel($level);
		$world = $this->getServer()->getLevelByName($level);
		$world->unload(); //If we don't unload and then load, entities and tiles may be make bug.
          	$this->getServer()->loadLevel($level);
	}
	//TODO: better way for run jar files
	private function anvilToRegion($level): void{
		system("java -jar AnvilToRegion.jar worlds/".$level);
	}
	private function regionToPMAnvil($level): void{
		system("java -jar AnvilConverter.jar worlds ".$level." pmanvil");
	}
}
