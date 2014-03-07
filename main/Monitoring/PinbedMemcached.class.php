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
	final class PinbedMemcached extends Memcached
	{
		protected $host;
		protected $port;

		/**
		 * @return PinbedMemcached 
		**/
		public static function create(
			$host = Memcached::DEFAULT_HOST,
			$port = Memcached::DEFAULT_PORT,
			$buffer = Memcached::DEFAULT_BUFFER
		)
		{
			return new self($host, $port, $buffer);
		}
		
		public function __construct(
			$host = Memcached::DEFAULT_HOST,
			$port = Memcached::DEFAULT_PORT,
			$buffer = Memcached::DEFAULT_BUFFER
		)
		{
			$this->host = $host;
			$this->port = $port;

			$this->startTimer('connect');
			parent::__construct($host, $port, $buffer);
			$this->stopTimer('connect');
		}

		public function clean()
		{
			$this->startTimer(__METHOD__);
			$result = parent::clean();
			$this->stopTimer(__METHOD__);
			return $result;
		}

		public function getList($indexes)
		{
			$this->startTimer(__METHOD__);
			$result = parent::getList($indexes);
			$this->stopTimer(__METHOD__);
			return $result;
		}

		public function increment($key, $value)
		{
			$this->startTimer(__METHOD__);
			$result = parent::increment($key, $value);
			$this->stopTimer(__METHOD__);
			return $result;
		}

		public function decrement($key, $value)
		{
			$this->startTimer(__METHOD__);
			$result = parent::decrement($key, $value);
			$this->stopTimer(__METHOD__);
			return $result;
		}

		public function get($index)
		{
			$this->startTimer(__METHOD__);
			$result = parent::get($index);
			$this->stopTimer(__METHOD__);
			return $result;
		}

		public function delete($index, $time = null)
		{
			$this->startTimer(__METHOD__);
			$result = parent::delete($index, $time);
			$this->stopTimer(__METHOD__);
			return $result;
		}

		public function append($key, $data)
		{
			$this->startTimer(__METHOD__);
			$result = parent::append($key, $data);
			$this->stopTimer(__METHOD__);
			return $result;
		}

		protected function startTimer($method_name)
		{
			PinbaClient::me()->timerStart(
				'memcached_'.$this->host.'_'.$this->port.'_'.$method_name,
				array(
					'group'	=> 'memcache::'.$methodName,
					'host'	=> $this->host.':'.$this->port,
				)
			);
		}

		protected function stopTimer($method_name)
		{
			PinbaClient::me()->timerStop(
				'memcached_'.$this->host.'_'.$this->port.'_'.$method_name
			);
		}
	}
?>