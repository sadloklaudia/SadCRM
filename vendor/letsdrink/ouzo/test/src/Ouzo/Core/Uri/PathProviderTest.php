<?php
use Ouzo\Uri\PathProvider;

class PathProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnRedirectUrlIfExists()
    {
        // given
        $provider = new PathProvider();
        $_SERVER['REDIRECT_URL'] = '/redirect/url';

        // when
        $path = $provider->getPath();

        // then
        $this->assertEquals('/redirect/url', $path);
    }

    /**
     * @test
     */
    public function shouldReturnRedirectUrlWithRedirectQueryStringIfExists()
    {
        // given
        $provider = new PathProvider();
        $_SERVER['REDIRECT_URL'] = '/redirect/url';
        $_SERVER['REDIRECT_QUERY_STRING'] = 'id=1&name=john';

        // when
        $path = $provider->getPath();

        // then
        $this->assertEquals('/redirect/url?id=1&name=john', $path);
    }

    /**
     * @test
     */
    public function shouldReturnRequestUriIfRedirectUrlNotExist()
    {
        // given
        $provider = new PathProvider();
        $_SERVER['REQUEST_URI'] = '/request/uri';

        // when
        $path = $provider->getPath();

        // then
        $this->assertEquals('/request/uri', $path);
    }
}
