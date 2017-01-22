<?php


namespace App\Model;

use Dibi\Connection;
use Nette\Security as NS;
use Nette;

class DatabaseAuthenticator extends Nette\Object implements NS\IAuthenticator
{
    /** @var Connection */
    private $database;

    const USERNAME_TAKEN = 101;

    function __construct(Connection $database)
    {
        $this->database = $database;
    }

    function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $row = $this->database->select('*')->from('users')
        ->where(['username'=>$username])->fetch();


        if (!$row) {
            throw new NS\AuthenticationException('User not found.');
        }

        if (!NS\Passwords::verify($password, $row->password)) {
            throw new NS\AuthenticationException('Invalid password.');
        }

        return new NS\Identity($row->id, $row->role, ['username' => $row->username]);
    }

    public function add($username, $password, $role="guest")
    {
        $this->database->query("INSERT INTO users", [
            'username'=>$username,
            'password'=>NS\Passwords::hash($password),
            'role'=>$role

        ]);
    }
}