<?php

	final class PostgresDialectTest extends TestCase
	{
		public function testPrepareFullText()
		{
			$this->assertEquals(
				PostgresDialect::prepareFullText(
					array('Новый год', 'Снегурочка', 'ПрАзДнИк'),
					DB::FULL_TEXT_AND),
				"'новый год' & 'снегурочка' & 'праздник'"
			);
		}
		
		public function testQuoteArray()
		{
			$this->assertEquals(
				PostgresDialect::me()->quoteArray(
					array(
						array(1, 2, 3),
						array(4, 5, 6)
					)
				),
				'\'{{"1","2","3"},{"4","5","6"}}\''
			);
			
			$this->assertEquals(
				PostgresDialect::me()->quoteArray(
					array(
						'one """ string with "quotes"'
					)
				),
				'\'{"one \\\\"\\\\"\\\\" string with \\\\"quotes\\\\""}\''
			);
			
			$this->assertEquals(
				PostgresDialect::me()->quoteArray(
					array(
						'\'single\' quotes \n\n\n'
					)
				),
				'\'{"\\\\\'\'single\\\\\'\' quotes \\\\\\\\n\\\\\\\\n\\\\\\\\n"}\''
			);
			
			$this->assertEquals(
				PostgresDialect::me()->quoteArray(
					array(
						'"some" string with $var'
					)
				),
				'\'{"\\\\"some\\\\" string with $var"}\''
			);
			
			$this->assertEquals(
				PostgresDialect::me()->quoteArray(
					array()
				),
				'\'{}\''
			);
			
			$this->assertEquals(
				PostgresDialect::me()->quoteArray(
					array(
						array(''),
						array('')
					)
				),
				'\'{{""},{""}}\''
			);
			
			$this->assertEquals(
				PostgresDialect::me()->quoteArray(
					array(
						array(1, 2),
						array(3, 4)
					),
					';'
				),
				'\'{{"1";"2"};{"3";"4"}}\''
			);
			
			$this->assertEquals(
				PostgresDialect::me()->quoteArray(
					array(
						array(true, null),
						array(3, false)
					)
				),
				'\'{{'.Dialect::LITERAL_TRUE.','.Dialect::LITERAL_NULL.'},'
					.'{"3",'.Dialect::LITERAL_FALSE.'}}\''
			);
		}
		
		public function testUnquoteArray()
		{
			$this->assertEquals(
				PostgresDialect::me()->unquoteArray(
					'{"string with $var","\\"quoted\\" string"}'
				),
				array(
					'string with $var',
					'"quoted" string'
				)
			);
			
			$this->assertEquals(
				PostgresDialect::me()->unquoteArray(
					'{{{11,22},{"33",44}},{{55,66},{77,88}}}'
				),
				array(
					array(
						array('11', '22'),
						array('33', '44')
					),
					array(
						array('55', '66'),
						array('77', '88')
					)
				)
			);
			
			$this->assertEquals(
				PostgresDialect::me()->unquoteArray(
					'{{simpletext,"text with spaces"},{\'quoted\'text,"zlex,zlex"}}'
				),
				array(
					array('simpletext', 'text with spaces'),
					array('\'quoted\'text', 'zlex,zlex')
				)
			);
			
			$this->assertEquals(
				PostgresDialect::me()->unquoteArray(
					'{{1;2};{3;4}}',
					';'
				),
				array(
					array(1, 2),
					array(3, 4)
				)
			);
			
			$this->assertEquals(
				PostgresDialect::me()->unquoteArray(
					'{"",""}'
				),
				array('', '')
			);
			
			$this->assertEquals(
				PostgresDialect::me()->unquoteArray(
					'{}'
				),
				array()
			);
			
			$this->assertEquals(
				PostgresDialect::me()->unquoteArray(
					''
				),
				array()
			);
		}
	}
?>