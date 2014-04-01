<?php
/****************************************************************************
 *   Copyright (C) 2011 by Evgeny V. Kokovikhin                             *
 *                                                                          *
 *   This program is free software; you can redistribute it and/or modify   *
 *   it under the terms of the GNU Lesser General Public License as         *
 *   published by the Free Software Foundation; either version 3 of the     *
 *   License, or (at your option) any later version.                        *
 *                                                                          *
 ****************************************************************************/
	
	/**
	 *
	**/
	class PinbedPeclMemcached extends PeclMemcached
	{
		/**
		 * @return PinbedPeclMemcached 
		**/
		public static function create(
			$host = Memcached::DEFAULT_HOST,
			$port = Memcached::DEFAULT_PORT,
			$connectTimeout = PeclMemcached::DEFAULT_TIMEOUT
		)
		{
			return new self($host, $port, $connectTimeout);
		}
		
		public function append($key, $data)
		{
			$this->startTimer(__FUNCTION__, $key);
			$result = parent::append($key, $data);
			$this->stopTimer(__FUNCTION__, $key);
			return $result;
		}
		
		public function decrement($key, $value)
		{
			$this->startTimer(__FUNCTION__, $key);
			$result = parent::decrement($key, $value);
			$this->stopTimer(__FUNCTION__, $key);
			return $result;
		}
		
		public function delete($index)
		{
			$this->startTimer(__FUNCTION__, $index);
			$result = parent::delete($index);
			$this->stopTimer(__FUNCTION__, $index);
			return $result;
		}
		
		public function get($index)
		{
			$this->startTimer(__FUNCTION__, $index);
			$result = parent::get($index);
			$this->stopTimer(__FUNCTION__, $index);
			return $result;
		}
		
		public function getc($index, &$cas)
		{
			$this->startTimer(__FUNCTION__, $index);
			$result = parent::getc($index, $cas);
			$this->stopTimer(__FUNCTION__, $index);
			return $result;
		}
		
		public function cas($key, $value, $expires = Cache::EXPIRES_MEDIUM, $cas)
		{
			$this->startTimer(__FUNCTION__, $key);
			$result = parent::cas($key, $value, $expires, $cas);
			$this->stopTimer(__FUNCTION__, $key);
			return $result;
		}
		
		public function getList($indexes)
		{
			$this->startTimer(__FUNCTION__);
			$result = parent::getList($indexes);
			$this->stopTimer(__FUNCTION__);
			
			return $result;
		}
		
		public function increment($key, $value)
		{
			$this->startTimer(__FUNCTION__, $key);
			$result = parent::increment($key, $value);
			$this->stopTimer(__FUNCTION__, $key);
			return $result;
		}
		
		protected function store(
			$action, $key, $value, $expires = Cache::EXPIRES_MEDIUM
		)
		{
			$this->startTimer(__FUNCTION__.$action, $key);
			$result = parent::store($action, $key, $value, $expires);
			$this->stopTimer(__FUNCTION__.$action, $key);
			return $result;
			
		}
		
		protected function connect()
		{
			$this->startTimer(__FUNCTION__);
			$result = parent::connect();
			$this->stopTimer(__FUNCTION__);
			return $result;
		}

		protected function startTimer($methodName, $key = '')
		{
			PinbaClient::me()->timerStart(
				'pecl_memcached_'.$this->host.'_'.$this->port.'_'.$methodName,
				array(
					'group'	=> 'memcache::'.$methodName,
					'host'	=> $this->host.':'.$this->port,
				)
			);
		}

		protected function stopTimer($methodName, $key = '')
		{
			PinbaClient::me()->timerStop(
				'pecl_memcached_'.$this->host.'_'.$this->port.'_'.$methodName
			);
		}
	}
