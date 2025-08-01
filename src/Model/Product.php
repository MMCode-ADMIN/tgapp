<?php

namespace WebCrawler\Model;

use DateTimeImmutable;

class Product
{
    private string $url;
    private ?string $title;
    private Money $money;
    private ?string $availability;
    private DateTimeImmutable $scrapedAt;

    /**
     * @param string $url
     * @param string|null $title
     * @param Money|null $money
     * @param string|null $availability
     */
    public function __construct(string $url, ?string $title, ?Money $money, ?string $availability)
    {
        $this->url = $url;
        $this->title = $title;
        $this->money = $money;
        $this->availability = $availability;
        $this->scrapedAt = new DateTimeImmutable();
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): Product {
        $this->url = $url;

        return $this;
    }

    /**
     * @param string|null $title
     * @return $this
     */
    public function setTitle(?string $title): Product {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string|null $availability
     * @return $this
     */
    public function setAvailability(?string $availability): Product {
        $this->availability = $availability;

        return $this;
    }

    /**
     * @param Money $money
     * @return $this
     */
    public function setMoney(Money $money): Product {
        $this->money = $money;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getAvailability(): ?string {
        return $this->availability;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getScrapedAt(): DateTimeImmutable {
        return $this->scrapedAt;
    }

    /**
     * @return Money
     */
    public function getMoney(): Money {
        return $this->money;
    }
}
