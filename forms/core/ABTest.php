<?php

namespace forms\core;

class ABTest
{
    const ENABLE_ON_RENDERED = 0;
    protected $name = '';
    protected $isActive = '';
    protected $createdAt = '';
    protected $modifiedAt = '';
    protected $id = '';
    protected $tests = array();
    protected $enableOn = ABTest::ENABLE_ON_RENDERED;

    /**
     * @return int
     */
    public function getEnableOn()
    {
        return $this->enableOn;
    }

    /**
     * @param int $enableOn
     * @return ABTest
     */
    public function setEnableOn( $enableOn )
    {
        $this->enableOn = $enableOn;
        return $this;
    }

    public function __construct($name)
    {
        $this->setName($name);
    }


    public function addTest( Test $test )
    {
        $this->tests[] = $test;
        return $this;
    }

    /**
     * @return array instanceof Test
     */
    public function getTests($index = null)
    {
        if (NULL !== $index) {

            if (isset( $this->tests[$index] )) {
                return $this->tests[$index];
            }
        }
        return $this->tests;
    }

    /**
     * @param mixed $id
     * @return ABTest
     */
    protected function setId($id)
    {
        $prefix = $id . '_';
        $this->id = uniqid($prefix, false);
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param string $isActive
     * @return ABTest
     */
    public function setIsActive( $isActive )
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ABTest
     */
    public function setName( $name )
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return ABTest
     */
    public function setCreatedAt( $createdAt )
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param string $modifiedAt
     * @return ABTest
     */
    public function setModifiedAt( $modifiedAt )
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }


    public function save($id = '')
    {
        if ($id === ''){
            $this->setId('cm');
            $id = $this->getId();
        }

        $tests = ABTest::get();

        $tests[$id] = $this;
        return Options::update('ab_tests', $tests);

    }

    /**
     * @param string $id
     * @return array|$this
     */
    public static function get($id = '')
    {
        $ab_tests = Options::get( 'ab_tests' );
        $abTests = empty($ab_tests) ? array() :  Options::get('ab_tests');

        if ($id === '') {
            return $abTests;
        }


        return isset( $abTests[$id] ) ? $abTests[$id] : null;

    }

    public static function remove( $testId )
    {
        if ($testId === '') {
            return;
        }

        $tests = ABTest::get();

        if (array_key_exists( $testId, $tests )) {
            unset( $tests[$testId] );
        }

        return Options::update('ab_tests', $tests);

    }


    public static function getByPost( $id )
    {
        $tests = ABTest::get();

        $testAr=array();


        //$currentTest = null;
        if ($tests !== null) {
            foreach ($tests as $test) {

                if ($test->getEnableOn() == $id || $test->getEnableOn() == -1) {
                    //$currentTest = $test;
                    $testAr[] = $test;
                }


            }

        }


        //return $currentTest;
        return $testAr;
    }


}