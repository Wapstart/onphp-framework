<?php
/***************************************************************************
 *   Copyright (C) 2012 by Alexander A. Zaytsev                            *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	/**
	 * Container for passing array values into OSQL queries.
	 * 
	 * @ingroup OSQL
	 * @ingroup Module
	**/
	final class DBArray extends DBValue
	{
		/**
		 * @return DBArray
		**/
		public static function create(array $value)
		{
			return new self($value);
		}
		
		public function __construct(array $value)
		{
			parent::__construct($value);
		}
		
		public function toDialectString(Dialect $dialect)
		{
			$out = $dialect->quoteArray($this->getValue());
			
			return
				$this->cast
					? $dialect->toCasted($out, $this->cast)
					: $out;
		}
	}
?>
