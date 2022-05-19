<?php

namespace App\Services;

class Product
{
    public string $title;
    public float $price;
    public string $category;
    public string $condition;
    public string $description;
    public string $availability = "";
    public array $productTags = array();
    public string $sku = "";
    public string $location = "";
    public array $imagesPath = array();
    public bool $hideFromFriends = false;
}
