<?php
/***************************************************************************
 *   Copyright (C) 2013 by Nikita V. Konstantinov                          *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	/**
	 * @ingroup Flow
	 **/
	class SimpleView implements View
	{
		/**
		 * @var null
		 */
		private $content = null;

		/**
		 * @var HttpStatus
		 */
		private $status = null;

		/**
		 * @var HttpHeaderCollection
		 */
		private $headerCollection = null;

		public function __construct(
			$content = null,
			HttpStatus $status = null,
			array $headers = array()
		)
		{
			$this->content          = $content;
			$this->status           = $status ?: new HttpStatus(HttpStatus::CODE_200);
			$this->headerCollection = new HttpHeaderCollection($headers);
		}

		/**
		 * @return HttpHeaderCollection
		 */
		public function getHeaderCollection()
		{
			return $this->headerCollection;
		}

		public function render($model = null)
		{
			header($this->status->toString());

			foreach ($this->headerCollection as $name => $valueList)
				foreach ($valueList as $value)
					header($name.': '.$value, true);

			if ($this->content !== null)
				echo $this->content;

			return $this;
		}
	}
?>