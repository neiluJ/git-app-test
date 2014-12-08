<?php
/**
 * User: neiluj
 * Date: 08/12/14
 * Time: 16:34
 */

namespace TestGit\Transactional;

class TrTest
{
    public $step = 0;
}

class TransactionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Transaction
     */
    private $object;

    public function testBasicTransactionGoesWell()
    {
        $trtest = new TrTest();
        $this->object = new Transaction();
        $this->object->add(function() use ($trtest) {
           $trtest->step++;
        }, function() use ($trtest) {
            $trtest->step--;
        });

        $this->assertEquals(0, $trtest->step);

        $this->object->start();
        $this->assertEquals(1, $trtest->step);
    }

    public function testBasicTransactionFuckUp()
    {
        $trtest = new TrTest();
        $this->object = new Transaction();
        $this->object->add(function() use ($trtest) {
            $trtest->step++;
            throw new \Exception('fails.');
        }, function() use ($trtest) {
            $trtest->step--;
        });

        $this->assertEquals(0, $trtest->step);
        $this->setExpectedException('TestGit\Transactional\TransactionException');
        $this->object->start();
    }

    public function testBasicTransactionFuckUpButRollback()
    {
        $trtest = new TrTest();
        $this->object = new Transaction();
        $this->object->add(function() use ($trtest) {
            $trtest->step++;
        }, function() use ($trtest) {
            $trtest->step--;
        });
        $this->object->add(function() use ($trtest) {
            $trtest->step++;
            throw new \Exception('fails.');
        }, function() use ($trtest) {
            $trtest->step--;
        });

        $this->assertEquals(0, $trtest->step);
        try {
            $this->object->start();
        } catch(TransactionException $exp) {
            $this->object->rollback();
        }
        $this->assertEquals(0, $trtest->step);
    }

    public function testTransactionRollbackAtCorrectIndex()
    {
        $trtest = new TrTest();
        $this->object = new Transaction();
        $this->object->add(function() use ($trtest) {
            $trtest->step++;
        }, function() use ($trtest) {
            $trtest->step--;
        });
        $this->object->add(function() use ($trtest) {
            $trtest->step++;
            throw new \Exception('fails.');
        }, function() use ($trtest) {
            $trtest->step--;
        });
        $this->object->add(function() use ($trtest) {
            $trtest->step++;
        }, function() use ($trtest) {
            $trtest->step--;
        });
        $this->object->add(function() use ($trtest) {
            $trtest->step++;
        }, function() use ($trtest) {
            $trtest->step--;
        });

        $this->assertEquals(0, $trtest->step);
        try {
            $this->object->start();
        } catch(TransactionException $exp) {
            $this->object->rollback();
        }
        $this->assertEquals(0, $trtest->step);
    }
}
 