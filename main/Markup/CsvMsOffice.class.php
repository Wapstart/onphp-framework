<?php
/***************************************************************************
 *   Copyright (C) 2013 by Artur Baltaev                                   *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	class CsvMsOffice extends Csv
	{
		const SEPARATOR = "\t";
		const CODE_PAGE_TO = 'UTF-16';
		const CODE_PAGE_FROM = 'UTF-8';

		private $codePageTo = self::CODE_PAGE_TO;
		private $codePageFrom = self::CODE_PAGE_FROM;

		public function render($forceQuotes = true)
		{
			return iconv(
				$this->codePageFrom,
				$this->codePageTo,
				parent::render($forceQuotes)
			);
		}

		public function setCodePageTo($codePageTo)
		{
			$this->codePageTo = $codePageTo;

			return $this;
		}

		public function getCodePageTo()
		{
			return $this->codePageTo;
		}

		public function setCodePageFrom($codePageFrom)
		{
			$this->codePageFrom = $codePageFrom;

			return $this;
		}

		public function getCodePageFrom()
		{
			return $this->codePageFrom;
		}
	}
