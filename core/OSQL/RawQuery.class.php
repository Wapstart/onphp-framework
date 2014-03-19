<?php
/***************************************************************************
 *   Copyright (C) 2012, WapStart						                   *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	/**
	 * @ingroup OSQL
	 **/
	class RawQuery extends QueryIdentification
	{
		/**
		 * @var string
		 */
		private $rawQuery;

		public static function create($rawQuery)
		{
			return new self($rawQuery);
		}

		public function __construct($rawQuery)
		{
			$this->rawQuery = $rawQuery;
		}

		public function toDialectString(Dialect $dialect)
		{
			return $this->rawQuery;
		}
	}
