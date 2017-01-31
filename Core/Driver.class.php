<?php
/**
 * 驱动基类
 * @author Yurun <admin@yurunsoft.com>
 */
abstract class Driver
{
	/**
	 * 当前驱动名称
	 * @var type 
	 */
	public static $driverName = '';
	/**
	 * 所有驱动和实例
	 * @var type 
	 */
	public static $instances = array();
	/**
	 * 初始化框架驱动
	 */
	public static function init()
	{
		static::__initBefore();
		self::$instances[static::$driverName] = array();
		static::__initAfter();
		// 加载框架内置配置
		if(isset(Yurun::$config['CORE_' . strtoupper(static::$driverName)]))
		{
			foreach(Yurun::$config['CORE_' . strtoupper(static::$driverName)] as $name => $option)
			{
				self::create($option,$name);
			}
		}
		// 绑定项目加载事件
		if(Yurun::$isAppLoaded)
		{
			static::onAppLoad();
		}
		else
		{
			Event::register('YURUN_APP_LOAD_COMPLETE', static::$driverName . '::onAppLoad');
		}
	}
	protected static function __initBefore()
	{
		
	}
	protected static function __initAfter()
	{
		
	}
	/**
	 * 项目加载
	 */
	public static function onAppLoad()
	{
		static::__onAppLoadBefore();
		// 加载项目驱动
		$configs = Config::get('@.APP_' . strtoupper(static::$driverName),array());
		foreach($configs as $name => $option)
		{
			if(isset($option['autoload']) && $option['autoload'])
			{
				self::create($option,$name);
			}
		}
		static::__onAppLoadAfter();
	}
	protected static function __onAppLoadBefore()
	{
		
	}
	protected static function __onAppLoadAfter()
	{
		
	}
	/**
	 * 创建驱动实例
	 *
	 * @param string $name        	
	 * @param string $alias        	
	 * @param array $args        	
	 * @return mixed
	 */
	public static function create($option = array(), $alias = null)
	{
		if(isset(self::$instances[static::$driverName][$alias]))
		{
			return self::$instances[static::$driverName][$alias];
		}
		else
		{
			$object = static::__createBefore($option, $alias);
			if(null === $object)
			{
				$className = static::$driverName . $option['type'];
				$ref = new ReflectionClass($className);
				$object = $ref->newInstance(isset($option['option']) ? $option['option'] : array());
			}
			static::__createAfter($option, $alias, $object);
			if(null !== $alias)
			{
				self::$instances[static::$driverName][$alias] = $object;
			}
			return $object;
		}
	}
	protected static function __createBefore(&$option,$alias)
	{
		
	}
	protected static function __createAfter(&$option,$alias,&$object)
	{
		
	}
	/**
	 * 获得驱动实例，不存在返回null
	 *
	 * @param type $name        	
	 * @return mixed
	 */
	public static function getInstance($alias = null)
	{
		if(null === $alias)
		{
			$alias = self::defaultAlias();
		}
		if (isset(self::$instances[static::$driverName][$alias]))
		{
			return self::$instances[static::$driverName][$alias];
		}
		else
		{
			$option = Config::get('@.APP_' . strtoupper(static::$driverName) . '.' . $alias);
			if(false === $option)
			{
				return null;
			}
			else
			{
				return self::create($option,$alias);
			}
		}
	}
	
	/**
	 * 删除驱动实例
	 * @param type $alias
	 */
	public static function removeInstance($alias = null)
	{
		if(null === $alias)
		{
			$alias = self::defaultAlias();
		}
		if (isset(self::$instances[static::$driverName][$alias]))
		{
			unset(self::$instances[static::$driverName][$alias]);
		}
	}
	
	/**
	 * 清空驱动实例
	 */
	public static function clearInstance()
	{
		self::$instances[static::$driverName] = array();
	}
	
	/**
	 * 驱动实例是否存在
	 * @param type $alias
	 * @return type
	 */
	public static function instanceExists($alias = null)
	{
		return isset(self::$instances[static::$driverName][null === $alias ? self::defaultAlias() : $alias]);
	}
	
	/**
	 * 获取驱动实例数量
	 *
	 * @return int
	 */
	public static function instanceCount($alias)
	{
		return count(self::$configs[$alias]);
	}
	
	/**
	 * 获取驱动默认实例名称
	 * @return type
	 */
	public static function defaultAlias()
	{
		return Config::get('@.DEFAULT_' . strtoupper(static::$driverName));
	}
}