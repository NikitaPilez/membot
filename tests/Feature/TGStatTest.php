<?php

namespace Tests\Feature;

use App\Helpers\TGStat;
use Tests\TestCase;

class TGStatTest extends TestCase
{
    public function test_get_human_views_more_thousand()
    {
        $string = '""
    \n
                        2.8k
    ""';

        $number = TGStat::getHumanViews($string);
        $this->assertEquals(2800, $number);
    }

    public function test_get_human_views_more_thousand_and_integer()
    {
        $string = '""
    \n
                        2.0k
    ""';

        $number = TGStat::getHumanViews($string);
        $this->assertEquals(2000, $number);
    }

    public function test_get_human_views_more_thousand_and_integer_without_dot()
    {
        $string = '""
    \n
                        2k
    ""';

        $number = TGStat::getHumanViews($string);
        $this->assertEquals(2000, $number);
    }

    public function test_get_human_views_less_thousand()
    {
        $string = '""
    \n
                        223
    ""';

        $number = TGStat::getHumanViews($string);
        $this->assertEquals(223, $number);
    }
}
