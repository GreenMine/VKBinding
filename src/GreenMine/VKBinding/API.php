<?php


namespace GreenMine\VKBinding;


use pocketmine\Player;
use GreenMine\VKBinding\Loader;

class API
{

    private $table_name;
    private $TABLE;

    private $player;
    private $dbinfo;

    public function __construct(\mysqli $connect, $table_name)
    {
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }
        $this->connect = $connect;
        $this->table_name = $table_name;
    }

     public function exec(): void {
        $this->TABLE = $this->table_name."(id MEDIUMINT NOT NULL AUTO_INCREMENT, Player VARCHAR(255), vkid INT(100) DEFAULT 0, VKFirstName VARCHAR(255), VKLastName VARCHAR(255), State INT(10), sub tinyint(1) DEFAULT 0, PRIMARY KEY(id)) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";//PLAYER, COINS, VKID ,VKFName, VKLName
        $this->connect->set_charset('utf8');
        $this->connect->query("CREATE TABLE IF NOT EXISTS ".$this->TABLE);
    }

    public function setPlayer($player): API {
        $this->player = $player;
        $this->dbinfo = $this->getInfo();
        return $this;
    }

    public function createUser($vkid =0, $fname = '', $lname = '', $state = 0, $sub = 0) {
        if($this->connect->query("SELECT * FROM ".$this->table_name." WHERE Player = '$this->player'")->num_rows == 0){
            $this->connect->query("INSERT INTO ".$this->table_name."(Player, vkid, VKFirstName ,VKLastName, State, sub) VALUES ('$this->player', '$vkid', '$fname', '$lname', '$state', '$sub')");
            return [$this->player, $vkid, $fname, $lname, $state];
        }
        return true;
    }

    public function getInfo(): array {
        $new = $this->createUser();
        if($new !== true)
            return $new;
        return mysqli_fetch_row($this->connect->query("SELECT * FROM ".$this->table_name." WHERE Player = '$this->player'"));
    }

    public function getVKName(): array {
        return [$this->dbinfo[3], $this->dbinfo[4]];
    }

    public function getVKID(): int {
        return $this->dbinfo[2];
    }

    public function getState(): int {
        return $this->dbinfo[5];
    }

    public function haveActiveBind(): bool {
        return $this->getState() == 1 ? true : false;
    }

    public function isSubscribe(): bool {
        return $this->dbinfo[6] == 1 ? true : false;
    }

    public function setState($state) : bool {
        $this->connect->query("UPDATE ".$this->table_name." SET State = '$state' WHERE Player = '$this->player'");
        return true;
    }
}
