<?php

namespace Application\Bundle\DefaultBundle\Tests\Menu;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test cases for MenuBuilder
 *
 * @author Stepan Tanasiychuk <ceo@stfalcon.com>
 */
class MenuBuilderTest extends WebTestCase
{

    /**
     * Test main menu on the default bundle pages
     */
    public function testMainMenu()
    {
        $client = $this->createClient();

        /** check homepage **/
        $crawler = $client->request('GET', $this->getUrl('homepage'));
        // check if a response is successful
        $this->assertTrue($client->getResponse()->isSuccessful());
        // check the current item of the menu
        $this->assertEquals(1, $crawler->filter('nav>ul>li.current:contains("Наши работы")')->count());
        $this->assertEquals(0, $crawler->filter('nav>ul>li.current:contains("Команда")')->count());


        /** check blog default page **/
        $crawler = $client->request('GET', $this->getUrl('blog'));
        // check if a response is successful
        $this->assertTrue($client->getResponse()->isSuccessful());
        // check the current item of the menu
        $this->assertEquals(0, $crawler->filter('nav>ul>li.current:contains("Главная")')->count());
        $this->assertEquals(1, $crawler->filter('nav>ul>li.current:contains("Блог")')->count());


        /** check blog default page **/
        $crawler = $client->request('GET', $this->getUrl('contacts'));
        // check if a response is successful
        $this->assertTrue($client->getResponse()->isSuccessful());
        // check the current item of the menu
        $this->assertEquals(0, $crawler->filter('nav>ul>li.current:contains("Блог")')->count());
        $this->assertEquals(1, $crawler->filter('nav>ul>li.current:contains("Контакты")')->count());
    }
}