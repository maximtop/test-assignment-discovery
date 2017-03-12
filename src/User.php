<?php

namespace BalanceUpdater;

class User
{
    private $id;
    private $name;
    private $balance;

    public function __construct($id, $name, $balance)
    {
        $this->id = $id;
        $this->name = $name;
        $this->balance = $balance;
    }

    public function changeBalance($amount)
    {
        $this->balance += $amount;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBalance()
    {
        return $this->balance;
    }
}
