# VKBinding
**VKBinding - это API для привзяки аккаунтов Вконтакте и использования их в других плагинах.**

## Как установить!?
 - Скачайте этот репозиторий и вставьте его в папку `plugins` своего сервера
 - Если вам нужен `.phar` архив вставьте скачанный архив на сервис [Phar](https://phar.scer.io/)
 
 ## Использование
 Для использования вам нужно импортировать класс `GreenMine\VKBinding\Loader` в ваш плагин
 ```php
use GreenMine\VKBinding\Loader;
 ```
 
 ### Иницилизация класса
 Далее надо инициализировать класс, для этого в функции в которой вы хотите использовать API, напишите следующее
```php
$api = Loader::getInstance();
``` 
 
### Методы класса
  `$api::setPlayer()` устанавливает ник для базы данных
```php
/** @var String $player */
$api->setPlayer('GreenMine94');
```
  `$api::getState()` получаем текущий уровень привязки
```php
/** @var int $state */
echo $api->getState();
```
Уровни привязки:
 - 0 - аккаунт не привязан
 - 1 - ожидает потдвержение на сервере Minecraft
 - 2 - аккаунт привязан
 
`$api::getVKName()` получаем Имя и Фамилию привязанного аккаунта(массив)
 ```php
 /** @var Array $vkdata */
 var_dump($api->getVKName());
```
`$api::getVKID()` получаем id vk привязанного пользователя
 ```php
 /** @var int $vkid */
 var_dump($api->getVKName());
```
`$api::isSubscribe()` проверяем подписан ли пользователь на сообщество
 ```php
 /** @var int $vkid */
 var_dump($api->isSubscribe());
```
 ### Примеры
 Приветсвутем зашедшего пользователя, если у него привязан аккаунт
 ```php
public function join(PlayerJoinEvent $event) : void {
    $player = $event->getPlayer();
    $api = Loader::getInstance();
    $api->setPlayer($player->getName());
    if($api->getState() == 2) {
        $player->sendMessage('§aЗдравствуйте, §e'. implode(' ', $api->getVKName()) . '!');
    }
}
```