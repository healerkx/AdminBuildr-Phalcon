<?php

/**
 * Created by PhpStorm.
 * User: Healer
 * Date: 2014/11/18
 * Time: 15:12
 */

/**
 * Class Config
 *
 *
 */
class CConfig
{
	/**
	 * @var null
	 * 用于存放common路径下面的全局配置
	 */
	private static $commonConfigs = null;

	private static $applicationConfigs = null;

	private static $devConfigs = null;


    public static function loadCommonConfig($configFile)
	{
		if (!self::$commonConfigs)
		{
			self::$commonConfigs = include "$configFile";
		}
	}

	public static function loadApplicationConfig($configFile)
	{
		if (!self::$applicationConfigs)
		{
			self::$applicationConfigs = include "$configFile";
		}
	}

	public static function loadDevConfig($configFile)
	{
		if (!self::$devConfigs)
		{
			self::$devConfigs = include "$configFile";
		}
	}

	/**
	 * @param $key
	 * @param bool $default
	 * @return array|string|int
	 *
	 * 用法:
	 * $host = Config::get('master.host');
	 * $port = Config::get('slave.port', 3306);
	 */
	public static function get($key, $default = false)
	{
		$keys = explode('.', $key);

		// 优先级0 (先获取Dev Private的配置)
		if (self::$devConfigs)
		{
			// 存在则读取
			$ret = self::getValue($keys, self::$devConfigs);
			if (isset($ret))
			{
				return $ret;
			}
		}

		// 优先级1 (先获取Application Module的配置)
		$ret = self::getValue($keys, self::$applicationConfigs);
		if (!isset($ret))
		{
			// 优先级2 (Common Module的配置[全局配置])
			$ret = self::getValue($keys, self::$commonConfigs);
		}

		if (!isset($ret) && $default)
		{
			// 优先级3 (使用默认值)
			return $default;
		}
		return $ret;
	}

	protected static function getValue($keys, $config)
	{
		$m = $config;
		foreach ($keys as $key)
		{
			$m = $m[$key];
			if ($m === false || $m === null )
			{
				break;
			}
		}

		return $m;
	}
}