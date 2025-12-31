<?php

declare(strict_types=1);

namespace Amirxd\ClearLag\Utils;

use pocketmine\utils\Config;

class Settings {

    private Config $config;
    private array $data;

    public function __construct(Config $config) {
        $this->config = $config;
        $this->data = $config->getAll();
    }

    public function getClearItemsTimer(): int {
        return (int) ($this->data["clear-item-timer"] ?? 300);
    }
    
    public function getWarningTime(): int {
        return (int) ($this->data["warning-time"] ?? 30);
    }
    
    public function getClearableEntities(): array {
        return $this->data["clearable-entities"] ?? ["item", "xp_orb"];
    }
    
    public function getExemptWorlds(): array {
        return $this->data["exempt-worlds"] ?? [];
    }
    
    public function save(): void {
        $this->config->setAll($this->data);
        $this->config->save();
    }
}