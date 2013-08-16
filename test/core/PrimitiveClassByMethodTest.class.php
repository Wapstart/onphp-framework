<?php
	/* $Id$ */

	final class PrimitiveClassByMethodTest extends TestCase
	{
		public function testOf()
		{
			$prm = Primitive::classByMethod('form');

			try {
				$prm->of('InExIsNaNtClass');
				$this->fail('wrong parameter, should be ClassNotFoundException exception');
			} catch (ClassNotFoundException $e) {
				// pass
			}

			$this->assertFalse(
				$prm->
					of('MappedForm')->
					importValue(
						'form'
					)
			);

			$form = Form::create();
			$this->assertTrue(
				$prm->
					of('MappedForm')->
					importValue($form)
			);

			$this->assertFalse(
				$prm->
					of('Identifiable')->
					importValue(1)	//there is no method 'create'
			);
		}

		public function testSetMethodName()
		{
			$prm = Primitive::classByMethod('io');
			$testId = 1;

			try {
				$prm->setMethodName('::wrap');
				$this->fail('wrong parameter, should be ClassNotFoundException exception');
			} catch (ClassNotFoundException $e) {
				// pass
			}

			try {
				$prm->setMethodName('wrap');
				$this->fail('wrong parameter, should be WrongArgumentException exception');
			} catch (WrongArgumentException $e) {
				// pass
			}

			try {
				$prm->setMethodName('wrap::');
				$this->fail('wrong parameter, should be ClassNotFoundException exception');
			} catch (ClassNotFoundException $e) {
				// pass
			}

			$prm->
				of('IdentifiableObject')->
				setMethodName('wrap')->
				importValue($testId);

			$io = IdentifiableObject::wrap($testId);

			$this->assertEquals(
				$prm->getValue()->getId(),
				$io->getId()
			);

			$prm->
				setMethodName('IdentifiableObject::wrap')->
				importValue($testId);

			$this->assertEquals(
				$prm->getValue()->getId(),
				$io->getId()
			);
		}
	}
	?>