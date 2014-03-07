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
	 *  @ingroup DB
	**/
	final class PinbedPgSQL extends PgSQL
	{
		public function connect()
		{
			$this->startTimer(__METHOD__);
			try {
				$result = parent::connect();
			} catch (Exception $e) {
				$this->deleteTimer(__METHOD__);
				throw $e;
			}
			$this->stopTimer(__METHOD__);
			return $result;
		}

		public function queryRaw($queryString)
		{
			$queryLabel = substr($queryString, 0, 5);
			$this->startTimer($queryLabel);
			
			try {
				$result = parent::queryRaw($queryString);
			} catch (Exception $e) {
				$this->deleteTimer($queryLabel);
				throw $e;
			}

			$this->stopTimer($queryLabel);
			return $result;
		}

		protected function startTimer($method_name)
		{
			PinbaClient::me()->timerStart(
				'pg_sql_'.$this->hostname.'_'.$this->port.'_'.$method_name,
				array(
					'group'	=> 'db::'.$methodName,
					'host'	=> $this->hostname.':'.$this->port,
				)
			);
		}

		protected function stopTimer($method_name)
		{
			PinbaClient::me()->timerStop(
				'pg_sql_'.$this->hostname.'_'.$this->port.'_'.$method_name
			);
		}

		protected function deleteTimer($method_name)
		{
			PinbaClient::me()->timerDelete(
				'pg_sql_'.$this->hostname.'_'.$this->port.'_'.$method_name
			);
		}
	}
?>