<?php

declare(strict_types=1);

namespace Amirxd\ClearLag;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Amirxd\ClearLag\Manage\ClearManager;
use Amirxd\ClearLag\Utils\Settings;
use pocketmine\utils\Config;

class UltraClearLag extends PluginBase {
    use SingletonTrait;
    
    private ClearManager $clearManager;
    private Settings $settingManager;
    private Config $settingConfig;
    
    public function onLoad(): void {
        self::setInstance($this);
    }
    
    public function onEnable(): void {
        $this->getLogger()->info("§aEnabling UltraClearLag...");
        
        $this->iniConfigs();
        $this->iniManagers();
        $this->registerCommands();
        $this->startTasks();
        
        $this->getLogger()->info("§aUltraClearLag Enabled!");
    }
    
    private function iniConfigs(): void {
        @mkdir($this->getDataFolder());
        $this->saveResource("settings.yml");
        $this->settingConfig = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
    }

    private function iniManagers(): void {
        $this->clearManager = new ClearManager($this);
        $this->settingManager = new Settings($this->settingConfig);
    }

    private function registerCommands(): void {
        
    }
    
    private function startTasks(): void {
        $this->clearManager->startClearTasks();
    }
    
    public function getClearManager(): ClearManager {
        return $this->clearManager;
    }
    
    public function getSettingManager(): Settings {
        return $this->settingManager;
    }
    
    public function onDisable(): void {
        $this->getLogger()->info("§cUltraClearLag Disabled!");
    }
}