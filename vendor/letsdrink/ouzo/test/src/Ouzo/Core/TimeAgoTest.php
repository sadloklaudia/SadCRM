<?php
use Ouzo\TimeAgo;
use Ouzo\Utilities\Clock;

class TimeAgoTest extends PHPUnit_Framework_TestCase
{
    public function dates()
    {
        return array(
            array('2012-02-20 12:00', '2012-02-20 11:59', 'just now'),
            array('2012-02-20 12:00', '2012-02-20 11:55', '5 min. ago'),
            array('2012-02-20 12:00', '2012-02-20 11:00', 'today at 11:00'),
            array('2012-02-20 12:00', '2012-02-19 12:00', 'yesterday at 12:00'),
            array('2012-02-20 12:00', '2012-01-20 12:00', 'Jan 20'),
            array('2012-02-20 12:00', '2012-01-19 12:00', 'Jan 19'),
            array('2012-02-20 12:00', '2012-01-20 11:59', 'Jan 20'),
            array('2012-02-20 12:00', '2012-01-20 11:55', 'Jan 20'),
            array('2012-02-20 12:00', '2011-01-20 12:00', '2011-01-20'),
            array('2012-02-20 12:00', '2011-01-19 12:00', '2011-01-19'),
            array('2012-02-20 12:00', '2011-01-20 11:59', '2011-01-20'),
            array('2012-02-20 12:00', '2011-01-20 11:55', '2011-01-20')
        );
    }

    /**
     * @test
     * @dataProvider dates
     */
    public function shouldReturnYesterdayAt($currentDate, $date, $expectedText)
    {
        //given
        Clock::freeze($currentDate);

        //when
        $timeAgo = TimeAgo::create($date)->asString();

        //then
        $this->assertEquals($expectedText, $timeAgo);
    }
}
