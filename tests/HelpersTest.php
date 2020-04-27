<?php

class HelpersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider_spk_is_valide_x
     */
    public function test_spk_is_valide_x($xtype, $xcontent, $expected)
    {
        $this->assertEquals($expected, spk_is_valide_x($xtype, $xcontent));
    }

    /**
     * 数据提供器（Data Provider）
     *
     * @see http://www.phpunit.cn/manual/5.7/zh_cn/writing-tests-for-phpunit.html
     * @see https://phpunit.readthedocs.io/en/8.0/writing-tests-for-phpunit.html#data-providers
     *
     * @return array
     */
    public function provider_spk_is_valide_x()
    {
        return [
            ['phone', '18973832617', true],      // 合法手机号
            ['phone', '100', false],             // 非法手机号
            ['email', 'liweijsj@163.com', true], // 合法邮箱
            ['email', 'hello', false],           // 非法邮箱
        ];
    }

    /**
     * @dataProvider provider_spk_human_seconds
     */
    public function test_spk_human_seconds($seconds, $format, $expected)
    {
        $this->assertEquals($expected, spk_human_seconds($seconds, $format));
    }

    public function provider_spk_human_seconds()
    {
        return [
            [59, 1, '59秒'],
            [59, 2, '59秒前'],
            [123, 1, '2分3秒'],
            [123, 2, '2分钟前'],
            [3600, 1, '1时'],
            [3600, 2, '1小时前'],
            [3600 * 24 * 2, 1, '2天'],
            [3600 * 24 * 2, 2, '2天前'],
            [3600 * 24 * 30 * 13, 1, '1年25天'],
            [3600 * 24 * 30 * 13, 2, '1年前'],
            [3600 * 24 * 30 * 13 + 66, 1, '1年25天1分6秒'],
        ];
    }

    public function test_spk_format_ts()
    {
        $this->assertEquals(date('Y-m-d H:i:s'), spk_format_ts(time()));
        $this->assertEquals('2020-04-23 23:38:28', spk_format_ts(1587656308));
    }

    /**
     * @dataProvider provider_spk_human_filesize
     */
    public function test_spk_human_filesize($bytes, $decimals, $expected)
    {
        $this->assertEquals($expected, spk_human_filesize($bytes, $decimals));
    }

    public function provider_spk_human_filesize()
    {
        return [
            [988, 0, '988B'],
            [98288, 1, '96.0K'],
            [1283, 2, '1.25K'],
            [8298288, 2, '7.91M'],
            [9008298288, 2, '8.39G'],
        ];
    }

    /**
     * @dataProvider provider_spk_default
     */
    public function test_spk_default($var, $default, $expected)
    {
        $this->assertEquals($expected, spk_default($var, $default));
    }

    public function provider_spk_default()
    {
        return [
            [123, '', 123],
            ['中国', '-', '中国'],
            [null, '', ''],
            [0, '零', '零'],
            ['', '空', '空'],
            ['', 0, 0],
        ];
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage 校验失败
     * @throws Throwable
     */
    public function test_spk_throw_if_with_message()
    {
        spk_throw_if(true, '\\Exception', '校验失败');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage 验签失败
     * @expectedExceptionCode 1001
     * @throws Throwable
     */
    public function test_spk_throw_if_with_message_code()
    {
        spk_throw_if('xxx' != 'yyy', '\\InvalidArgumentException', '验签失败', 1001);
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage 1怎么能小于0呢
     * @expectedExceptionCode 111
     * @throws Throwable
     */
    public function test_spk_throw_unless_with_message_code()
    {
        spk_throw_unless(1 < 0, '\\ErrorException', '1怎么能小于0呢', 111);
    }

    public function test_spk_snake_to_camel()
    {
        $this->assertEquals('getLastName', spk_snake_to_camel('getLastName'));
        $this->assertEquals('getLastName', spk_snake_to_camel('get_last_name'));
    }
}

