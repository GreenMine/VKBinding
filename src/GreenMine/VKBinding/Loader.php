<?php

declare(strict_types=1);

namespace GreenMine\VKBinding;

    use pocketmine\event\player\PlayerJoinEvent;
    use pocketmine\event\Listener;
    use pocketmine\plugin\PluginBase;
    use pocketmine\command\CommandSender;
    use pocketmine\command\Command;
    use pocketmine\Player;
    use pocketmine\utils\Config;

    class Loader extends PluginBase implements Listener
    {

        private static $instance;

        public function onEnable(): void
        {
            //CONNECT CONFIG
            $this->saveResource('config.json');
            $this->cfg = new Config($this->getDataFolder() . 'config.json', Config::JSON);
            $this->cfg->enableJsonOption(JSON_UNESCAPED_UNICODE);
            //LOAD CONFIG
            //LOAD CONFIG
            $data = $this->cfg->get('dbInfo');
            //CONNECT API
            $connect = new \mysqli($data['host'], $data['username'], $data['password'], $data['dbname']);
            $this->api = new API($connect, $data['tablename']);
            $this->api->exec();
            self::$instance = $this->api;
            //REGISTER EVENTS
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
            $this->getLogger()->info('Load VKBinding');
        }

	public static function getInstance(): API {
		return self::$instance;
	}

        public function onJoin(PlayerJoinEvent $event)
        {
            $player = $event->getPlayer();
            $name = $player->getName();
            $this->api->setPlayer($name);
            if($this->api->haveActiveBind()) {
                $player->sendMessage('§aК вашему аккаунта хотят привязать аккаунт вконтакте'. PHP_EOL .'Напишите §e/acceptbind §aчтобы принять его, и §c/refusebind §aчтобы отклонить');
            }
        }

        public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
        {
            $this->api->setPlayer($sender->getName());
            switch ($command->getName()) {
                case 'acceptbind':
                        if($this->api->haveActiveBind()) {
                        $this->api->setState(2);
                        $sender->sendMessage('§aВы успешно привязали аккаунт');
                    }else {
                        $sender->sendMessage('§cУ вас нет активных привязок');
                    }
                    return true;
                case 'refusebind':
                    if($this->api->haveActiveBind()) {
                        $this->api->setState(0);
                        $sender->sendMessage('§cВы успешно отклонили привязку аккаунта');
                    }else {
                        $sender->sendMessage('§cУ вас нет активных привязок');
                    }
                    return true;
                case 'vkinfo':
                    if($this->api->getState() == 2) {
                        $sender->sendMessage('§2О вашем аккаунте:'.PHP_EOL.'ИФ: §e'. implode(' ', $this->api->getVKName()) . PHP_EOL . 'Ссылка на ваш VK: vk.com/id'. $this->api->getVKID());
                    }
                default:
                    return false;
            }
        }
        public function onDisable(): void
        {
            $this->getLogger()->info('Unload VKBinding');
        }
    }
