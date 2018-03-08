<?php

namespace forms\core;

class Test
{
    protected $form;
    protected $impressions = 0;
    protected $submissions = 0;
    protected $submissionRate = 0;
    protected $formId = '';


    public function __construct($form)
    {
        $this->form = $form;
    }

    public function setFormId( $id )
    {
        $this->formId = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     * @return Test
     */
    public function setForm( $form )
    {
        $this->form = $form;
        $this->formId = $form->getId();
        return $this;
    }

    public function getFormId()
    {
        return $this->formId;

    }

    /**
     * @return mixed
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @param mixed $impressions
     * @return Test
     */
    public function setImpressions( $impressions )
    {
        $this->impressions = $impressions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubmissions()
    {
        return $this->submissions;
    }

    /**
     * @param mixed $submissions
     * @return Test
     */
    public function setSubmissions( $submissions )
    {
        $this->submissions = $submissions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubmissionRate()
    {
        if ($this->getImpressions() !== 0 && $this->getSubmissions() !== 0) {
            return  $this->getSubmissions() / $this->getImpressions();
        }
        return $this->submissionRate;
    }

    /**
     * @param mixed $submisionRate
     * @return Test
     */
    public function setSubmisionRate( $submissionRate )
    {
        $this->submissionRate = $submissionRate;
        return $this;
    }




}