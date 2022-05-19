<?php

namespace App\Services;


use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Interactions\Internal\WebDriverSendKeysAction;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverExpectedCondition as EC;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverKeys;


class FacebookWrapper
{
    private RemoteWebDriver $driver;

    public function __construct()
    {

    }

    public function setup(): void
    {

        $desiredCapabilities = DesiredCapabilities::chrome();
        $options = new ChromeOptions();
        $options->addArguments(["--disable-notifications"]);
        $desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $this->driver = RemoteWebDriver::create("http://localhost:4444", $desiredCapabilities);
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function login(string $email, string $password): bool
    {
        /**
            * @var $emailElement RemoteWebElement
            * @var $passwordElement RemoteWebElement
            * @var $buttonElement RemoteWebElement
         */
        $this->driver->get("https://www.facebook.com/");
        $webdriver = new WebDriverWait($this->driver, 30);

        $emailElement = $webdriver->until(EC::presenceOfElementLocated(By::cssSelector("input[name='email']")));
        $emailElement->sendKeys($email);

        $passwordElement = $webdriver->until(EC::presenceOfElementLocated(By::cssSelector("input[name='pass']")));
        $passwordElement->sendKeys($password);

        $buttonElement = $webdriver->until(EC::presenceOfElementLocated(By::cssSelector("button[type='submit']")));
        $buttonElement->click();

        try {
            // Validate it logged
            $webdriver->until(EC::presenceOfElementLocated(By::className("rq0escxv")));
            return true;
        }
        catch (TimeoutException|NoSuchElementException|Exception)
        {
        }
        return false;
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function fillFields(Product $product): void
    {
        /**
         * @var $imageInput RemoteWebElement
         * @var $titleInput RemoteWebElement
         * @var $priceInput RemoteWebElement
         * @var $categorySelector RemoteWebElement
         * @var $conditionSelector RemoteWebElement
         * @var $conditionElement RemoteWebElement
         * @var $availabilitySelector RemoteWebElement
         * @var $availabilityElement RemoteWebElement
         * @var $productTagsElement RemoteWebElement
         * @var $skuElement RemoteWebElement
         * @var $locationElement RemoteWebElement
         * @var $hideFromFriendsElement RemoteWebElement
         */
        $waiter = new WebDriverWait($this->driver, 8);

        $imageInput = $waiter->until(EC::presenceOfElementLocated(By::xpath("//input[contains(@accept, 'image/*')]")));
        $imageInput->sendKeys(join("\n", $product->imagesPath));

        $titleInput = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Title']/following-sibling::input")));
        $titleInput->sendKeys($product->title);

        $priceInput = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Price']/following-sibling::input")));
        $priceInput->sendKeys($product->price);

        $categorySelector = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Category']/following-sibling::div")));
        $categorySelector->click();
        $categoryElement = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='". $product->category ."']")));
        $categoryElement->click();

        $conditionSelector = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Condition']/following-sibling::div")));
        $conditionSelector->click();
        $conditionElement = $waiter->until(EC::presenceOfElementLocated(By::xpath("//div/span[text()='". $product->condition ."']")));
        $conditionElement->click();

        $descriptionTextArea = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Description']/following-sibling::textarea")));
        $descriptionTextArea->sendKeys($product->description);

        $availabilitySelector = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Availability']/following-sibling::div")));
        $availabilitySelector->click();
        $availabilityElement = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='" . $product->availability . "']")));
        $availabilityElement->click();

        $productTagsElement = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Product tags']/following::textarea")));
        $productTagsElement->click();
        foreach ($product->productTags as $tag)
        {
            $productTagsElement->sendKeys($tag)->sendKeys(WebDriverKeys::ENTER);
        }

        $skuElement = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='SKU']/following-sibling::input")));
        $skuElement->sendKeys($product->sku);

        $locationElement = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Location']/following-sibling::input")));
        if (!empty($product->location))
        {
            $locationElement->sendKeys(WebDriverKeys::CONTROL . "A");
            $locationElement->sendKeys(WebDriverKeys::DELETE);
            $locationElement->clear();
            $locationElement->sendKeys($product->location);

        }

        $hideFromFriendsElement = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Hide from friends']")));
        $hideFromFriendsElement->click();
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function publishProduct(Product $product): void
    {
        $waiter = new WebDriverWait($this->driver, 60);
        $this->driver->get("https://www.facebook.com/marketplace/create/item");
        $this->fillFields($product);

        $nextBtn = $this->driver->findElement(By::xpath("//span[text()='Next']"));
        $nextBtn->click();

        $publishBtn = $waiter->until(EC::presenceOfElementLocated(By::xpath("//span[text()='Publish']")));
        $publishBtn->click();
    }

    public function quit(): void
    {
        $this->driver->quit();
    }
}
