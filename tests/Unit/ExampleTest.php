<?php

namespace Tests\Unit;

use App\Services\Availability;
use App\Services\Category;
use App\Services\Condition;
use App\Services\FacebookWrapper;
use App\Services\Product;
use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use http\Message;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_login() : void
    {
        $wrapper = new FacebookWrapper();
        $wrapper->setup();
        $result = $wrapper->login("ezeaquev@hotmail.com", "ninaloki2000");
        if ($result)
        {
            $product = new Product;
            $product->imagesPath = ["C:/Users/faste/Pictures/Imagen2.jpg", "C:/Users/faste/Pictures/Imagen4.png"];
            $product->title = "Test";
            $product->description = "Description for product";
            $product->price = 4.15;
            $product->category = Category::tools;
            $product->condition = Condition::new;
            $product->availability = Availability::single;
            $product->productTags = ["Venta", "test", "qsyo"];
            $product->sku = "ASDASD";
            $product->hideFromFriends = true;
            $wrapper->publishProduct($product);
        }
        self::assertTrue($result, "Credenciales incorrectas");
    }
}
