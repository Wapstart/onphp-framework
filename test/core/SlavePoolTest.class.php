<?php
	final class DBMock extends DB
	{
		private $connected = false;
		private $failConnect = false;
		
		public static function create()
		{
			return new self;
		}
		
		public static function getDialect()
		{
			return ImaginaryDialect::me();
		}
		
		public function __destruct()
		{
			
		}
		
		public function setFailConnect($isConnectFail)
		{
			$this->failConnect = ($isConnectFail === true);
			
			return $this;
		}
		
		public function setConnected($isConnected)
		{
			$this->connected = ($isConnected === true);
			
			return $this;
		}
		
		public function isConnected()
		{
			return $this->connected;
		}
		
		public function getHostName()
		{
			return $this->hostname;
		}
		
		public function connect()
		{
			if ($this->failConnect)
					{
						throw new DatabaseException('Connection failed');
					}
			
			$this->connected = true;
			
			return $this;
		}

		public function disconnect()
		{
			return $this;
		}

		public function getTableInfo($table)
		{
			return null;
		}

		public function queryRaw($queryString)
		{
			throw new DatabaseException($this->hostname);
		}

		public function queryRow(Query $query)
		{
			throw new DatabaseException($this->hostname);
		}

		public function querySet(Query $query)
		{
			throw new DatabaseException($this->hostname);
		}

		public function queryColumn(Query $query)
		{
			throw new DatabaseException($this->hostname);
		}

		public function queryCount(Query $query)
		{
			throw new DatabaseException($this->hostname);
		}

		public function setDbEncoding()
		{
			throw new DatabaseException($this->hostname);
		}
		
		public function obtainSequence($sequence)
		{
			return 1;
		}
	}

	final class FakeDAO extends StorableDAO
	{
		protected $useSlave = true;
		
		public function getLinkName()
		{
			return 'main';
		}
		
		public function getTable()
		{
			return 'test_table';
		}

		public function getObjectName()
		{
			return 'FakeObject';
		}
	}

	final class FakeObject extends IdentifiableObject implements Prototyped
	{
		public static function proto()
		{
			return Singleton::getInstance('ProtoFakeObject');
		}
		
		public function getId()
		{
			return 1;
		}
	}

	final class ProtoFakeObject extends AbstractProtoClass
	{
		protected function makePropertyList()
		{
			return array();
		}
	}

	final class FakeWorker extends BaseDaoWorker
	{
		public function getById($id)
		{
			return $this->fetchObject($this->getSimpleQuery());
		}

		public function getByLogic(LogicalObject $logic)
		{
			return $this->fetchObject($this->getSimpleQuery());
		}

		public function getByQuery(SelectQuery $query)
		{
			return $this->fetchObject($this->getSimpleQuery());
		}

		public function getCustom(SelectQuery $query)
		{
			return $this->fetchObject($this->getSimpleQuery());
		}

		public function getListByIds(array $ids)
		{
			return $this->fetchObject($this->getSimpleQuery());
		}

		public function getListByQuery(SelectQuery $query)
		{
			return $this->fetchObject($this->getSimpleQuery());
		}

		public function getListByLogic(LogicalObject $logic)
		{
			return $this->fetchList($this->getSimpleQuery());
		}

		public function getPlainList()
		{
			return $this->fetchObject($this->getSimpleQuery());
		}

		public function getCustomList(SelectQuery $query)
		{
			return $this->fetchList($this->getSimpleQuery());
		}

		public function getCustomRowList(SelectQuery $query)
		{
			return $this->fetchList($this->getSimpleQuery());
		}

		public function getQueryResult(SelectQuery $query)
		{
			return $this->fetchList($this->getSimpleQuery());
		}

		public function uncacheByIds($ids)
		{
			
		}

		public function uncacheLists()
		{
			
		}
		
		private function getSimpleQuery()
		{
			return OSQL::select()->from('test');
		}
	}

	final class SlavePoolTest extends TestCase
	{
		public function testSlavePoolDropLink()
		{			
			$slavePool =
				SlaveDBList::create()->
					addLink('test1', $this->getSlaveLink('test1'), 10)->
					addLink('test2', $this->getSlaveLink('test2'), 1);
			
			$slavePool->dropLink('test1');
			
			$link = $slavePool->getSlaveLink();
			
			$this->assertNotNull($link);
			$this->assertEquals('test2', $link->getHostName());
			
			$slavePool->dropLink('test2');
			
			$this->assertNull($slavePool->getSlaveLink());
			
			$slavePool =
				SlaveDBList::create()->
					addLink(
						'test', 
						$this->getSlaveLink('test')->setFailConnect(true),
						10
					);
			
			$this->assertNull($slavePool->getSlaveLink());
		}
		
		public function testSlavePoolAdd()
		{
			$slavePool = 
				SlaveDBList::create()->
				addLink('test', $this->getSlaveLink('test'), 10);
			
			try {
				$slavePool->addLink(
					'test',
					$this->getSlaveLink('test')
				);
				
				$this->fail('Dublicate slave link in slave pool');
			} catch (WrongArgumentException $e) {/* ok */}
			
			$dbPool = $this->makeDBPool();
			
			$dbPool->addSlaveList('main', $slavePool);
			
			try {
				$dbPool->addSlaveList('main', $slavePool);
				
				$this->fail('Dublicate slave pool');
			} catch (WrongStateException $e) {/* ok */}
			
			try {
				$dbPool->addSlaveList('no_such_link', $slavePool);
				
				$this->fail('Added slave pool for missing link');
			} catch (MissingElementException $e) {/* ok */}			
		}
		
		public function testSlaveChoose()
		{
			$pool = $this->makeDBPool();
			
			$pool->addSlaveList(
				'main', 
				SlaveDBList::create()->
					addLink(
						'slave1',
						$this->getSlaveLink('slave1'),
						1
					)
			);
			
			$this->assertEquals(
				'mainHost',
				$pool->getLink('main')->getHostName()
			);
			
			$this->assertEquals(
				'slave1',
				$pool->getLink('main', true)->getHostName()
			);
		}
		
		public function testSlaveForWorker()
		{
			$pool = $this->makeDBPool();
			
			$pool->
				addSlaveList(
					'main',
					SlaveDBList::create()->
						addLink('slave1', $this->getSlaveLink('slave1'))
				);
			
			try {
				Singleton::dropInstance('FakeDAO');
			} catch (MissingElementException $e) {}

			$dao = Singleton::getInstance('FakeDAO');
			$worker = new FakeWorker($dao);
			
			try {
				$worker->getById(1);
				
				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('slave1', $e->getMessage());
			}
			
			$dao->setUseSlave(false);

			try {
				$worker->getById(1);

				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('mainHost', $e->getMessage());
			}
			
			$dao->setUseSlave(true);

			try {
				$worker->getPlainList();

				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('slave1', $e->getMessage());
			}

			$dao->setUseSlave(false);

			try {
				$worker->getPlainList();

				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('mainHost', $e->getMessage());
			}
			
			$dao->setUseSlave(true);
			
			try {
				$worker->dropById(1);

				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('mainHost', $e->getMessage());
			}

			$dao->setUseSlave(false);

			try {
				$worker->dropById(1);

				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('mainHost', $e->getMessage());
			}

			$dao->setUseSlave(true);

			try {
				$worker->dropByIds(array(1, 2));

				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('mainHost', $e->getMessage());
			}

			$dao->setUseSlave(false);

			try {
				$worker->dropByIds(array(1, 2));

				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('mainHost', $e->getMessage());
			}
			
			$dao->setUseSlave(true);
			
			try {
				$dao->add(new FakeObject());

				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('mainHost', $e->getMessage());
			}
			
			$dao->setUseSlave(false);

			try {
				$dao->add(new FakeObject());

				$this->fail('No exception is strange');
			} catch (DatabaseException $e) {
				$this->assertEquals('mainHost', $e->getMessage());
			}
		}
		
		private function makeDBPool()
		{
			DBPool::dropInstance('DBPool');
			
			return
				DBPool::me()->
					addLink('main', DBMock::create()->setHostname('mainHost'));
		}

		/**
		 * @param $hostname
		 * @return DBMock
		 */
		private function getSlaveLink($hostname)
		{
			return
				DBMock::create()->
					setHostname($hostname);
		}
	}