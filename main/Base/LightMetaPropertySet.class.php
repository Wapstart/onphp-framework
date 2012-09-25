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
	 * @see LightMetaProperty
	 * 
	 * @ingroup Helpers
	**/
	final class LightMetaPropertySet extends LightMetaProperty
	{
		public function toValue(ProtoDAO $dao = null, $array, $prefix = null)
		{
			return
				$this->convertRawValue(
					$dao,
					DBPool::getByDao($dao)->getDialect()->
						unquoteArray($array[$prefix.$this->getColumnName()]),
					$prefix
				);
		}
		
		protected function convertRawValue(ProtoDAO $dao = null, $raw, $prefix)
		{
			if (is_array($raw)) {
				$returnList = array();
				
				foreach ($raw as $value)
					$returnList[] = $this->convertRawValue($dao, $value, $prefix);
				
				return $returnList;
			}
			
			return parent::convertRawValue($dao, $raw, $prefix);
		}
	}
?>
