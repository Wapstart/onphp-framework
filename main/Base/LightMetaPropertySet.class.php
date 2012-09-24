<?php
	/**
	 * @author Alexander Zaytsev <a.zaytsev@co.wapstart.ru>
	 * @copyright Copyright (c) 2012, Wapstart
	 */
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
