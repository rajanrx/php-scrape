<?php

namespace Tests\Unit;

use Concise\Core\TestCase;
use Scraper\Scrape\ConfigurationManager;
use Scraper\Structure\Configuration;
use Scraper\Structure\RegexField;
use Scraper\Structure\TextField;

class ConfigurationManagerTest extends TestCase
{
    protected static $jsonFile;

    protected static $dir;

    /** @var  ConfigurationManager */
    protected $configurationManager;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$dir = realpath(__DIR__ . '/../Data');
        self::$jsonFile = self::$dir . "/configuration.json";
        if (file_exists(self::$jsonFile)) {
            unlink(self::$jsonFile);
        }
    }

    public function setUp()
    {
        parent::setUp();

        $this->configurationManager =
            ConfigurationManager::getInstance(self::$jsonFile);
        $this->assertInstanceOf(
            ConfigurationManager::class,
            $this->configurationManager
        );
    }

    public function testNewFileIsGeneratedIfNotExists()
    {
        $this->assertTrue(file_exists(self::$jsonFile));
    }

    public function testConfigurationGeneratedCanBeSavedAsJson()
    {
        $configuration = $this->configurationManager->getConfiguration();
        $this->assertInstanceOf(Configuration::class, $configuration);
        $data = $this->getJsonData();
        $this->assertEquals(0, count($data->fields));
        $configuration->setTargetXPath('//div[@class="explore-content"]');
        $configuration->setRowXPath('//*[contains(@class,"repo-list")]/li');
        $configuration->setFields(
            [
                new TextField(
                    [
                        'name'  => 'repo_name',
                        'xpath' => './/div[1]/h3/a',
                    ]
                ),
                new TextField(
                    [
                        'name'     => 'repo_url',
                        'xpath'    => './/div[1]/h3/a',
                        'property' => 'href',
                    ]
                ),
                new TextField(
                    [
                        'name'       => 'description',
                        'xpath'      => './/div[@class="py-1"]/p',
                        'canBeEmpty' => true,
                    ]
                ),
                new RegexField(
                    [
                        'name'  => 'stars_today',
                        'xpath' => './/div[4]/span[@class="float-right"]',
                        'regex' => '/(\d*)\s[stars]/',
                    ]
                ),
            ]
        );
        $this->configurationManager->save();
        $this->assertEquals(
            file_get_contents(self::$dir . '/git-repo.json'),
            file_get_contents(self::$dir . '/configuration.json')
        );
    }

    /**
     * @depends testConfigurationGeneratedCanBeSavedAsJson
     */
    public function testConfigurationCanBeLoadedFromJson()
    {
        $configuration = $this->configurationManager->getConfiguration();
        $fields = $configuration->getFields();
        $this->assertEquals(4, count($fields));
        $this->assertInstanceOf(TextField::class, $fields[0]);
        $this->assertInstanceOf(TextField::class, $fields[1]);
        $this->assertInstanceOf(TextField::class, $fields[2]);
        $this->assertTrue($fields[3] instanceof  RegexField);
        $this->assertEquals('repo_name', $fields[0]->name);
        $this->assertEquals('.//div[1]/h3/a', $fields[0]->xpath);
        $this->assertEquals(null, $fields[0]->cssPath);
        $this->assertEquals(null, $fields[0]->property);
        $this->assertEquals(false, $fields[0]->canBeEmpty);
        $this->assertEquals('/(\d*)\s[stars]/', $fields[3]->regex);
    }

    protected function getJsonData()
    {
        $jsonData = file_get_contents(self::$jsonFile);

        return json_decode($jsonData);
    }
}
