<?php
/**
 * CRedisCache class file
 *
 * use and modify it as you wish
 * 
 * @author Gustavo Salom� <gustavonips@gmail.com>
 * @license http://www.opensource.org/licenses/gpl-3.0.html
 */

/**
 * CRedisCache uses Predis client as redis php client{@link https://github.com/nrk/predis predis}.
 */
class Cache_CRedisCache extends Cache_CCache
{
	/**
	 * @var Redis the Redis instance
	 */
	protected $_cache=null;
	/**
	 * @var array list of servers 
	 */
	protected $_servers=array();
    /**
	 * @var string location of the predis client 
	 */
	/** public $predis_class_name="Cache_Predis_PredisClient"; **/
    /**
	 * @var string list of servers 
	 */
	public $servers=array('host'=>'127.0.0.1','port'=>6379);

	/**
	 * Initializes this application component.
	 * This method is required by the {@link IApplicationComponent} interface.
	 * It creates the redis instance and adds redis servers.
	 * @throws CException if redis extension is not loaded
	 */
	public function init() {
		parent::init();
        $this->getRedis();
	}

	/**
	 * @return mixed the redis instance (or redisd if {@link useRedisd} is true) used by this component.
	 */
	public function getRedis()
	{
		if($this->_cache!==null)
			return $this->_cache;
		else{
            //require_once Yii::getPathOfAlias($this->predisPath).".php";
            //Yii::log('Opening Redis connection',CLogger::LEVEL_TRACE);
			return $this->_cache=new Cache_PRedis_Client($this->servers);
        }
	}

	/**
	 * Retrieves a value from cache with a specified key.
	 * @param string $id a key identifying the cached value
	 * @return mixed the value stored in cache, false if the value is not in the cache, expired or the dependency has changed.
	 */
	public function hget($prefix, $id)
	{
		Yii::trace('Serving "'.$id.'" from cache','system.caching.'.get_class($this));
	    $value = $this->_cache->hget($this->generateUniqueKey($prefix), $id);
		if($value !== false) {
			$data=unserialize($value);
			
			if(!is_array($data)) {
				return false;
			}
			if(!($data[1] instanceof ICacheDependency) || !$data[1]->getHasChanged()) {
				Yii::trace('Serving "'.$id.'" from cache','system.caching.'.get_class($this));
				return $data[0];
			}
		}
		
		return false;
	}
    
	public function hset($prefix, $id, $value, $dependency=null)
	{
		//Yii::trace('Saving "'.$id.'" to cache','system.caching.'.get_class($this));
		
		if($dependency!==null) {
			$dependency->evaluateDependency();
		}
		
		$data=array($value, $dependency);
		
		return $this->_cache->hset($this->generateUniqueKey($prefix), $id, serialize($data));
	}
	
	public function hdel($prefix, $id)
	{
		//Yii::trace('Saving "'.$id.'" to cache','system.caching.'.get_class($this));
		return $this->_cache->hdel($this->generateUniqueKey($prefix), $id);
	}
    
	public function keys($id)
	{
	    $data = $this->_cache->keys($this->generateUniqueKey($id));
		if($data !== false)
		{
			return $data;
		}
		return array();
	}
	
	public function lpush($id, $value)
	{
		//Yii::trace('Saving "'.$id.'" to cache','system.caching.'.get_class($this));
		return $this->_cache->lpush($this->generateUniqueKey($id), $value);
	}
	
	public function rpush($id, $value)
	{
		//Yii::trace('Saving "'.$id.'" to cache','system.caching.'.get_class($this));
		return $this->_cache->rpush($this->generateUniqueKey($id), $value);
	}
	
	public function lpop($id)
	{
		//Yii::trace('Serving "'.$id.'" from cache','system.caching.'.get_class($this));
		return $this->_cache->lpop($this->generateUniqueKey($id));
	}
	
	public function rpop($id)
	{
		//Yii::trace('Serving "'.$id.'" from cache','system.caching.'.get_class($this));
		return $this->_cache->rpop($this->generateUniqueKey($id));
	}
	
	public function llen($id)
	{
		//Yii::trace('Serving "'.$id.'" from cache','system.caching.'.get_class($this));
		return $this->_cache->llen($this->generateUniqueKey($id));
	}

	/**
	 * Retrieves a value from cache with a specified key.
	 * This is the implementation of the method declared in the parent class.
	 * @param string $key a unique key identifying the cached value
	 * @return string the value stored in cache, false if the value is not in the cache or expired.
	 */
	protected function getValue($key)
	{
		return $this->_cache->get($key);
	}

	/**
	 * Retrieves multiple values from cache with the specified keys.
	 * @param array $keys a list of keys identifying the cached values
	 * @return array a list of cached values indexed by the keys
	 * @since 1.0.8
	 */
	protected function getValues($keys)
	{
		return $this->_cache->mget($keys);
	}

	/**
	 * Stores a value identified by a key in cache.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string $key the key identifying the value to be cached
	 * @param string $value the value to be cached
	 * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function setValue($key,$value,$expire)
	{
		if($expire>0) {
			return $this->_cache->setex($key,$expire,$value);
		} else {
			return $this->_cache->set($key,$value);
        }
	}
    
	protected function addValue($key, $value, $expire) {
		if($expire>0){
            if($this->_cache->setnx($key, $expire, $value)) {
                return $this->_cache->expire($key, $expire);
            }
            return false;
		}else {
			return $this->_cache->setnx($key, $value);
        }
	}
    
	protected function deleteValue($key) {
		return $this->_cache->del($key);
	}
	
	/**
	 * Deletes all values from cache.
	 * This is the implementation of the method declared in the parent class.
	 * @return boolean whether the flush operation was successful.
	 * @since 1.1.5
	 */
	protected function flushValues() {
		return $this->_cache->flush();
	}
	
    /**
     * call unusual method
     * */
    public function __call($method,$args){
        return call_user_func_array(array($this->_cache,$method),$args);
    }
    
	protected function generateUniqueKey($key) {
		return "{$this->keyPrefix}.{$key}";
	}
    
	public function offsetExists($id) {
		return $this->_cache->exists($id);
	}
}

