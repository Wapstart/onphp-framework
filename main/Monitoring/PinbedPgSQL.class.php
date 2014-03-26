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
			$queryLabel = strtolower(substr($queryString, 0, 5));
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

		protected function startTimer($methodName, $queryString = '')
		{
			$tags = array(
				'group'	=> 'db::'.$methodName,
				'host'	=> $this->hostname.':'.$this->port,
			);

			if (in_array($methodName, array('inser', 'updat'))) {
				$tableName = 'unknown';
				$matches = array();
				if ($methodName == 'inser' && preg_match('/^insert[\s]+into[\s]+"?([a-zA-Z0-9_]+)/ui', $queryString, $matches)) {
					$tableName = $matches[1];
				} elseif ($methodName == 'updat' && preg_match('/^update[\s]+"?([a-zA-Z0-9_]+)/ui', $queryString, $matches)) {
					$tableName = $matches[1];
				}
				$tags['table'] = $tableName;
			}

			PinbaClient::me()->timerStart(
				'pg_sql_'.$this->hostname.'_'.$this->port.'_'.$methodName,
				$tags
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