<?php

namespace App\Affiliate\Entity;

class Affiliate
{
    private int $id;
    private string $token;
    private int $visitors = 0;
    private float $balance = 0;
    private float $withdrawn = 0;
    private int $userId;

    private array $affiliated = [];
    private int $signups = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }


    public function addVisitor(){
        $this->visitors++;
    }

    public function addSignup(float $amount){
        $this->signups++;
        $this->setWithdrawn($this->getWithdrawn() + $amount);
    }

    public function addWithdrawn(float $amount){
        $this->setWithdrawn($this->getWithdrawn() + $amount);
    }


    public function addBalance(float $amount){
        $this->setBalance($this->getBalance() + $amount);
    }


    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return array
     */
    public function getAffiliated(): array
    {
        return $this->affiliated;
    }

    /**
     * @param array $affiliated
     */
    public function setAffiliated(array $affiliated): void
    {
        $this->affiliated = $affiliated;
    }

    /**
     * @return float
     */
    public function getWithdrawn(): float
    {
        return $this->withdrawn;
    }

    /**
     * @param float $withdrawn
     */
    public function setWithdrawn(float $withdrawn): void
    {
        $this->withdrawn = $withdrawn;
    }

    /**
     * @return int
     */
    public function getSignups(): int
    {
        return $this->signups;
    }

    /**
     * @param int $signups
     */
    public function setSignups(int $signups): void
    {
        $this->signups = $signups;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    /**
     * @return int
     */
    public function getVisitors(): int
    {
        return $this->visitors;
    }

    /**
     * @param int $visitors
     */
    public function setVisitors(int $visitors): void
    {
        $this->visitors = $visitors;
    }

    public function getConversionRate()
    {
        $visitors = $this->getVisitors();
        $signups = $this->getSignups();
        return (0 < $visitors ? round($signups / $visitors * 100, 2) : 0) ."%";
    }

}