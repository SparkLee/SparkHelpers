<?php
require_once(__DIR__.'/../helpers.php');

class HelpersTest extends PHPUnit_Framework_TestCase {
	/**
     * 测试函数：helpers.php->spk_is_valide_x()
     *
     * @author Spark Lee <liweijsj@163.com>
     * @since  2019/02/18 20:30
     * @dataProvider providerSpkIsValideX
     */
	public function testSpkIsValideX($xtype, $xcontent, $res) {
		$this->assertEquals($res, spk_is_valide_x($xtype, $xcontent));
	}
	// 数据提供器（Data Provider）
	// 参考1：http://www.phpunit.cn/manual/5.7/zh_cn/writing-tests-for-phpunit.html
	// 参考2：https://phpunit.readthedocs.io/en/8.0/writing-tests-for-phpunit.html#data-providers
	public function providerSpkIsValideX() {
		return [
			['phone', '18973832617', true],      // 合法手机号
			['phone', '100', false],             // 非法手机号
			['email', 'liweijsj@163.com', true], // 合法邮箱
			['email', 'hello', false],           // 非法邮箱
		];
	}
}

