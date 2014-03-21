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
	class PinbedPgSQL extends PgSQL
	{
		public function connect()
		{
			$this->startTimer(__FUNCTION__);
			try {
				$result = parent::connect();
			} catch (Exception $e) {
				$this->deleteTimer(__FUNCTION__);
				throw $e;
			}
			$this->stopTimer(__FUNCTION__);
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

		protected function startTimer($methodName)
		{
			PinbaClient::me()->timerStart(
				'pg_sql_'.$this->hostname.'_'.$this->port.'_'.$methodName,
				array(
					'group'	=> 'db::'.strtolower($methodName),
					'host'	=> $this->hostname.':'.$this->port,
				)
			);
		}

		protected function stopTimer($methodName)
		{
			PinbaClient::me()->timerStop(
				'pg_sql_'.$this->hostname.'_'.$this->port.'_'.$methodName
			);
		}

		protected function deleteTimer($methodName)
		{
			PinbaClient::me()->timerDelete(
				'pg_sql_'.$this->hostname.'_'.$this->port.'_'.$methodName
			);
		}
	}
?>