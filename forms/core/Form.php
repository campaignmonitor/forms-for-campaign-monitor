<?php

namespace forms\core;

class Form
{
    protected $id="";
    protected $name="";
    protected $type="";

    protected $typeAr=array();
    protected $listName;
    protected $onPageAr=array();

    protected $createDate = "";
    protected $updateDate = "";

    protected $hasCaptcha = null;
    protected $isCaptchaEnabledOnSite = 0;

    protected $pageAr=array();
    protected $maxPageOnCount=10;
    protected $appearsLightbox="seconds";
    protected $lightboxSeconds=0;
    protected $lightboxScrollPercent=0;
    protected $formPlacement="topRight";
    protected $formPlacementBar="top";
    protected $header="";
    protected $subHeader="";
    protected $hasNameField=1;
    protected $hasDateOfBirthField=0;
    protected $hasOpenTextField=0;
    protected $openTextFieldLabel="Open text field";
    protected $hasGenderField=0;
    protected $hasCampMonLogo=1;

    protected $submitButtonBgHex="429BD0";
    protected $submitButtonTextHex="FFFFFF";
    protected $backgroundHex="FFFFFF";
    protected $textHex="000000";
    protected $submitButtonText="SUBSCRIBE";

    protected $isActive=1;

    protected static $orderBy="";
    protected static $ascOrDesc="";

    protected $campaignMonitorClientAr=array();
    protected $campaignMonitorListId="";
    protected $campaignMonitorClientId="";
    protected $successMessage = 'Your subscription has been confirmed. You\'ll hear from us soon.';
    protected $successMessageTitle = 'Thank you!';

    protected $buttonTypeText = '';

    protected $hasNameFieldLabel=1;
    protected $hasEmailFieldLabel=1;
    
    /**
     * @var forms\core\Font
     */
    protected $font;

    /**
     * @return forms\core\Font
     */
    public function getFont(){
       return $this->font;
    }

    public function setFont($font){
        $this->font = $font;
    }

    /**
     * @return int
     */
    public function getHasNameFieldLabel()
    {
        return $this->hasNameFieldLabel;
    }

    /**
     * @param int $hasNameFieldLabel
     */
    public function setHasNameFieldLabel($hasNameFieldLabel)
    {
        $this->hasNameFieldLabel = $hasNameFieldLabel;
    }

    /**
     * @return int
     */
    public function getHasEmailFieldLabel()
    {
        return $this->hasEmailFieldLabel;
    }

    /**
     * @param int $hasEmailFieldLabel
     */
    public function setHasEmailFieldLabel($hasEmailFieldLabel)
    {
        $this->hasEmailFieldLabel = $hasEmailFieldLabel;
    }

    /**
     * @return string
     */
    public function getButtonTypeText()
    {
        return $this->buttonTypeText;
    }

    /**
     * @param string $buttonTypeText
     * @return Form
     */
    public function setButtonTypeText( $buttonTypeText )
    {
        $this->buttonTypeText = $buttonTypeText;
        return $this;
    }

    /**
     * @return string
     */
    public function getSuccessMessageTitle()
    {
        return $this->successMessageTitle;
    }

    /**
     * @param string $successMessageTitle
     * @return Form
     */
    public function setSuccessMessageTitle( $successMessageTitle )
    {
        $this->successMessageTitle = $successMessageTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getSuccessMessage()
    {
        return $this->successMessage;
    }

    /**
     * @param string $successMessage
     * @return Form
     */
    public function setSuccessMessage( $successMessage )
    {
        $this->successMessage = $successMessage;
        return $this;
    }

    /**
     * @var array typeof FormField
     */
    protected $fields = array();

    /**
     * @return array typeof FormField
     */
    public function getFields($index = null)
    {
        if ($index !== null) {
            if (isset( $this->fields[$index] )) {
                return $this->fields[$index];
            }

            return null;
        }
        return $this->fields;
    }

    public function getFieldByKey($key)
    {
        $fields = $this->getFields();

        foreach ($fields as $field) {

            if ($field->getKey() === $key) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @param array $fields
     * @return Form
     */
    public function setFields( $fields )
    {
        $this->fields = $fields;
        return $this;
    }

    public function addField( $field )
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCampaignMonitorClientAr()
    {

        return $this->campaignMonitorClientAr;

    }

    /**
     * @param mixed $campaignMonitorClientAr
     * @return Form
     */
    public function setCampaignMonitorClientAr($campaignMonitorClientAr=null)
    {
        if (is_null($campaignMonitorClientAr))
        {
        	// TODO for refactoring
            $clients = Settings::get("campaign_monitor_clients");
            $campaignMonitorClientAr = array();
            foreach ($clients as $client)
            {
                $campaignMonitorClientAr[$client->ClientID] = filter_var($client->Name, FILTER_SANITIZE_STRING);
            }
        }
        $this->campaignMonitorClientAr = $campaignMonitorClientAr;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getCampaignMonitorListId()
    {
        return $this->campaignMonitorListId;
    }

    /**
     * @param mixed $campaignMonitorListId
     * @return Form
     */
    public function setCampaignMonitorListId($campaignMonitorListId)
    {
        if (empty($campaignMonitorListId))
        {
            $campaignMonitorListId = Settings::get("default_client");
        }
        $this->campaignMonitorListId = $campaignMonitorListId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCampaignMonitorClientId()
    {
        return $this->campaignMonitorClientId;
    }

    /**
     * @param mixed $campaignMonitorClientId
     * @return Form
     */
    public function setCampaignMonitorClientId($campaignMonitorClientId)
    {
        $this->campaignMonitorClientId = $campaignMonitorClientId;
        return $this;
    }


    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     * @return Form
     */
    public static function setOrderBy($orderBy)
    {
        $orderBy=$orderBy;
        if (empty($orderBy))
        {
            $orderBy="name";
        }
        self::$orderBy = $orderBy;
    }

    /**
     * @return string
     */
    public function getAscOrDesc()
    {
        return $this->ascOrDesc;
    }

    /**
     * @param string $ascOrDesc
     * @return Form
     */
    public static function setAscOrDesc($ascOrDesc)
    {
        $ascOrDesc=strtolower($ascOrDesc);
        if ($ascOrDesc!="asc" && $ascOrDesc!="desc")
        {
            $ascOrDesc="asc";
        }
        self::$ascOrDesc = $ascOrDesc;
    }

    /**
     * @param string $orderBy
     * @param string $ascOrDesc
     * @return array|mixed|void
     */
    public static function getAll($orderBy="", $ascOrDesc="", $searchStr="")
    {
        $forms = Options::get('forms');

        $forms = is_array($forms) && (count($forms) >  0) ? $forms : array();

        if (!empty($orderBy))
        {
            self::setOrderBy($orderBy);
            self::setAscOrDesc($ascOrDesc);
            if (is_array($forms) && count($forms)>0)
            {
                uasort($forms, array(__CLASS__,'sortFormsCmp'));
            }

        }

        if (!empty($searchStr))
        {
            foreach ($forms as $k=>$form)
            {
                if (stripos($form->name, $searchStr)===false)
                {
                    unset($forms[$k]);
                }
            }
        }

        if (!is_array($forms))
        {
            $forms=array();
        }

        return $forms;
    }

    /**
     * @param $id
     * @return self
     */
    public static function getOne($id)
    {

        $forms=self::getAll();
        if ( $id !== null && count($forms) > 0){

            if (array_key_exists($id, $forms)) {
                return $forms[$id];
            }
        }
        return null;
    }

    /*public static function sortForms($orderBy, $ascOrDesc)
    {

        self::get();

    }*/

    private static function sortFormsCmp($a, $b)
    {
        if (strtolower(self::$ascOrDesc)=="desc")
        {
            $val=-1;
        }
        else
        {
            $val=1;
        }

        $orderBy=self::$orderBy;

        if (isset($a->{$orderBy}) && isset($b->{$orderBy}))
        {
            if (strtolower($a->{$orderBy}) > strtolower($b->{$orderBy}))
            {
                return $val;
            }
            else if (strtolower($a->{$orderBy}) < strtolower($b->{$orderBy}))
            {
                return -$val;
            }
        }
        if (strtolower($a->name) > strtolower($b->name))
        {
            return $val;
        }
        else
        {
            return -$val;
        }

        return 0;
    }

    public static function getPageName($pageId)
    {
        if ($pageId=="-allPages-")
        {
            return "All Pages";
        }
        return get_the_title( $pageId );
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param boolean $isActive
     * @return Form
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    protected $embedCode='[cm_form %s]';

    public function __construct($formType="")
    {

        $this->setType($formType);
        $this->initializePageAr();

        if (strlen(Settings::get('recaptcha_key')) > 0)
        {
            $this->isCaptchaEnabledOnSite = 1;
        }
        else
        {
            $this->isCaptchaEnabledOnSite = 0;
        }

        /*if (empty($formId))
        {
            // add
        }
        else
        {
            //edit - get variables from database
        }*/
    }

    public function initializePageAr()
    {
        $ar = array( '' => '', -1 => "-- All pages --" );

        $types = array( 'page', 'post', );


        foreach ($types as $type) {

            $isDone = false;
            $i = 1;
            do {
                $args = array(
                    'sort_order' => 'asc',
                    'sort_column' => 'post_title',
                    'post_type' => $type,
                    'posts_per_page'=> 10,
                    'paged' => $i++,
                    'post_status' => 'publish' );
                $query =  new \WP_Query ( $args );

                $totalPosts = $query->max_num_pages;

                if ($query->have_posts()) {
                    while ( $query->have_posts() ) {
                        $post = $query->next_post();
                        $ar[$post->ID] = $post->post_title;
                    }
                    wp_reset_postdata();
                }

                $isDone =  $i > $totalPosts;

            } while(!$isDone);

        }

        asort( $ar  );

        $this->setPageAr( $ar );
    }



    public function save($id = ''){

        if ($id === ''){
            $this->setId('cm');
            $id = $this->getId();
        } else {
            $this->setId( $id , false);
        }


        $forms = Options::get('forms');
        $forms = (is_array($forms)) ? $forms : array();

//        Helper::display( $id );
//        Helper::display( $forms );
//        Helper::display( $this );
//        die();


        $forms["$id"] = $this;
        Options::update('forms', $forms);
        return $id;
    }

    public static function remove($id){

        $forms = Form::getAll();

        if (array_key_exists($id, $forms))
        {
            unset($forms[$id]);
        }
        return Options::update('forms', $forms);
    }

    private function getBoolVal($val)
    {
        if ($val)
        {
            return 1;
        }
        return 0;
    }

    private function getHexVal($val)
    {
        $val=trim(strtoupper($val),"#. ,!@#$%^&*()");
        $hexChars="0123456789ABCDEF";
        $str="";
        for ($x=1; $x<=6; $x++)
        {
            if (strlen($val)>=$x)
            {
                $char=substr($val,$x-1,1);
                if (strpos($hexChars, $char)!==false)
                {
                    $str.=$char;
                }
                else
                {
                    $str.="0";
                }
            }
            else
            {
                $str="0";
            }
        }

        return $str;
    }



    /**
     * @return string
     */
    public function getEmbedCode()
    {

        $formId = 'form_id=\'' . $this->getId() . '\'';
        return sprintf($this->embedCode, $formId);
    }

    /**
     * @param string $embedCode
     * @return Form
     */
    public function setEmbedCode($embedCode)
    {
        $this->embedCode = $embedCode;
        return $this;
    }




    /**
     * @return mixed
     */
    public function getTypeAr()
    {
        return $this->typeAr;
    }

    /**
     * @param mixed $typeAr
     * @return Form
     */
    public function setTypeAr($typeAr)
    {
        $this->typeAr = $typeAr;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getListName()
    {
        return $this->listName;
    }

    /**
     * @param mixed $listName
     * @return Form
     */
    public function setListName($listName)
    {
        $this->listName = $listName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnPageAr()
    {
        return $this->onPageAr;
    }

    /**
     * @param mixed $onPageAr
     * @return Form
     */
    public function setOnPageAr($onPageAr)
    {
        $this->onPageAr = $onPageAr;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPageAr()
    {
        return $this->pageAr;
    }

    /**
     * @param mixed $pageAr
     * @return Form
     */
    public function setPageAr($pageAr)
    {
        $this->pageAr = $pageAr;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxPageOnCount()
    {
        return $this->maxPageOnCount;
    }

    /**
     * @param mixed $maxPageOnCount
     * @return Form
     */
    public function setMaxPageOnCount($maxPageOnCount)
    {
        $this->maxPageOnCount = $maxPageOnCount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAppearsLightbox()
    {
        return $this->appearsLightbox;
    }

    /**
     * @param mixed $formAppearsLightbox
     * @return Form
     */
    public function setAppearsLightbox($formAppearsLightbox)
    {
        if ($this->type==FormType::LIGHTBOX)
        {
            $this->appearsLightbox = $formAppearsLightbox;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLightboxSeconds()
    {
        return $this->lightboxSeconds;
    }

    /**
     * @param mixed $lightboxSeconds
     * @return Form
     */
    public function setLightboxSeconds($lightboxSeconds)
    {
        if ($this->type=="lightbox")
        {
            $this->lightboxSeconds = $lightboxSeconds;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLightboxScrollPercent()
    {
        return $this->lightboxScrollPercent;
    }

    /**
     * @param mixed $lightboxScrollPercent
     * @return Form
     */
    public function setLightboxScrollPercent($lightboxScrollPercent)
    {
        if ($this->type=="lightbox")
        {
            $this->lightboxScrollPercent = $lightboxScrollPercent;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormPlacement()
    {
        return $this->formPlacement;
    }

    /**
     * @param mixed $formPlacement
     * @return Form
     */
    public function setFormPlacement($formPlacement)
    {
        //if ($this->type=="slideoutTab")
        //{
        $this->formPlacement = $formPlacement;
        //}
        //else
        //{
        //    $this->formPlacement = null;
        //}
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormPlacementBar()
    {
        return $this->formPlacementBar;
    }

    /**
     * @param mixed $formPlacementBar
     * @return Form
     */
    public function setFormPlacementBar($formPlacementBar)
    {
        $this->formPlacementBar = $formPlacementBar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     * @return Form
     */
    public function setheader($formHeader)
    {
        $this->header = $formHeader;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubHeader()
    {
        return $this->subHeader;
    }

    /**
     * @param mixed $formSubHeader
     * @return Form
     */
    public function setSubHeader($formSubHeader)
    {
        $this->subHeader = $formSubHeader;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHasNameField()
    {
        return $this->hasNameField;
    }

    /**
     * @param mixed $hasNameField
     * @return Form
     */
    public function setHasNameField($hasNameField)
    {
        $this->hasNameField = $this->getBoolVal($hasNameField);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHasDateOfBirthField()
    {
        return $this->hasDateOfBirthField;
    }

    /**
     * @param mixed $hasDateOfBirthField
     * @return Form
     */
    public function setHasDateOfBirthField($hasDateOfBirthField)
    {
        $this->hasDateOfBirthField = $this->getBoolVal($hasDateOfBirthField);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHasOpenTextField()
    {
        return $this->hasOpenTextField;
    }

    /**
     * @param mixed $hasOpenTextField
     * @return Form
     */
    public function setHasOpenTextField($hasOpenTextField)
    {
        $this->hasOpenTextField = $this->getBoolVal($hasOpenTextField);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpenTextFieldLabel()
    {
        return $this->openTextFieldLabel;
    }

    /**
     * @param mixed $openTextFieldLabel
     * @return Form
     */
    public function setOpenTextFieldLabel($openTextFieldLabel)
    {
        $this->openTextFieldLabel = $openTextFieldLabel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHasGenderField()
    {
        return $this->hasGenderField;
    }

    /**
     * @param mixed $hasGenderField
     * @return Form
     */
    public function setHasGenderField($hasGenderField)
    {
        $this->hasGenderField = $this->getBoolVal($hasGenderField);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHasCampMonLogo()
    {
        return $this->hasCampMonLogo;
    }

    /**
     * @param mixed $hasCampMonLogo
     * @return Form
     */
    public function setHasCampMonLogo($hasCampMonLogo)
    {
        $this->hasCampMonLogo = $this->getBoolVal($hasCampMonLogo);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubmitButtonBgHex()
    {
        return $this->submitButtonBgHex;
    }

    /**
     * @param mixed $submitButtonBgHex
     * @return Form
     */
    public function setSubmitButtonBgHex($submitButtonBgHex)
    {
        $this->submitButtonBgHex = $this->getHexVal($submitButtonBgHex);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubmitButtonTextHex()
    {
        return $this->submitButtonTextHex;
    }

    /**
     * @param mixed $submitButtonTextHex
     * @return Form
     */
    public function setSubmitButtonTextHex($submitButtonTextHex)
    {
        $this->submitButtonTextHex = $this->getHexVal($submitButtonTextHex);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBackgroundHex()
    {
        return $this->backgroundHex;
    }

    /**
     * @param mixed $backgroundHex
     * @return Form
     */
    public function setBackgroundHex($backgroundHex)
    {
        $this->backgroundHex = $this->getHexVal($backgroundHex);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTextHex()
    {
        return $this->textHex;
    }

    /**
     * @param mixed $textHex
     * @return Form
     */
    public function setTextHex($textHex)
    {
        $this->textHex = $this->getHexVal($textHex);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubmitButtonText()
    {
        return $this->submitButtonText;
    }

    /**
     * @param mixed $submitButtonText
     * @return Form
     */
    public function setSubmitButtonText($submitButtonText)
    {
        $this->submitButtonText = $submitButtonText;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormEmbedCode()
    {
        return $this->getEmbedCode();
    }

    /**
     * @param mixed $formEmbedCode
     * @return Form
     */
    public function setFormEmbedCode($formEmbedCode)
    {
        $this->formEmbedCode = $formEmbedCode;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Form
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Form
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * TODO optimize
     * @param mixed $id
     * @return Form
     */
    public function setId($id = '', $isNew=1)
    {
        if ($isNew || empty($id))
        {
            $this->id = $id . "_". uniqid();
        }
        else
        {
            $this->id = $id;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getCreateDate($isDisplay=0)
    {
        if ($isDisplay)
        {
            return $this->getDateDisplay($this->createDate);
        }
        return $this->createDate;
    }

    /**
     * @param string $createDate
     * @return Form
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdateDate($isDisplay=0)
    {
        if ($isDisplay)
        {
            return $this->getDateDisplay($this->updateDate);
        }
        return $this->updateDate;
    }

    private function getDateDisplay($d)
    {
        if (empty($d))
        {
            return "";
        }
        $ut=strtotime($d);
        if (empty($ut))
        {
            return "";
        }

        return date("Y/m/d", $ut);
    }

    /**
     * @param string $updateDate
     * @return Form
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getHasCaptcha()
    {
        return $this->hasCaptcha;
    }

    /**
     * @param int $hasCaptcha
     * @return Form
     */
    public function setHasCaptcha($hasCaptcha)
    {
        $this->hasCaptcha = $hasCaptcha;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsCaptchaEnabledOnSite()
    {
        return $this->isCaptchaEnabledOnSite;
    }

    /**
     * @param int $isCaptchaEnabledOnSite
     * @return Form
     */
    public function setIsCaptchaEnabledOnSite($isCaptchaEnabledOnSite)
    {
        $this->isCaptchaEnabledOnSite = $isCaptchaEnabledOnSite;
        return $this;
    }

}