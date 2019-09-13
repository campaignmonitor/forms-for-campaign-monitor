<?php

namespace forms\core;

class FormField {

    protected $_label = '';
    protected $_attributes = '';
    protected $_name = '';
    protected $_type = DataType::TEXT;
    protected $_enable = 1;
    protected $_isRequired = false;
    protected $_hasOptions = false;
    protected $_options = array();
    protected $_key = '';
    protected $_showLabel = false;
    protected $_html = '';


    public function __construct($name, $type = DataType::TEXT, $isRequired = false)
    {
        $this->setName( $name );
        $this->setType( $type );
        $this->setIsRequired( $isRequired );
    }

    /**
     * @return string
     */
    public function getHtml($class = '')
    {
        $fieldType = $this->getType();

        $class  .= ' campaign-monitor-custom-field';

        $name = $this->getName();
        $name = htmlDecodeEncode( $name );
        $formLabel = $this->getLabel();
        $formLabel = htmlDecodeEncode( $formLabel );
        $isRequired = $this->getIsRequired();

        if (empty($formLabel))
        {
            $formLabel=$name;
        }

        $label='';
        $placeholder = '';

        if ($this->isShowLabel() || $fieldType !== DataType::TEXT) {
            $label = '<label for="custom_fields['.str_replace(" ","_",$name).']">';
            $label .= $formLabel;
            if ($isRequired)
            {
                $label .= " *";
            }
            $label .= '</label>';
        }else {
            $placeholder = $name;
            if ($isRequired)
            {
                $placeholder .= " *";
            }
        }

        $optionIndex = 0;
        switch ($fieldType) {
            case DataType::TEXT :
                $this->_html = $label;
                $this->_html.= '<input type="text" placeholder="'.$placeholder.'" name="custom_fields['.$name.']" value="" class="' . $class . '" />';
            break;
            case DataType::NUMBER :
                $this->_html = $label;
                $this->_html.= '<input name="custom_fields['.$name.']" type="number" value="" class="' . $class . '" />';
            break;
            case DataType::DATE :
                $this->_html = $label;
                $this->_html.= '<input name="custom_fields['.$name.']" type="date" value="" class="' . $class . '">';
                break;
            case DataType::MULTI_SELECT_ONE :
                $this->_html = $label;
                $this->_html .= '<select placeholder="'.$placeholder.'" name="custom_fields['.$name.']" class="' . $class . '" />';
                $options = $this->getOptions();
                $this->_html .= '<option value="">';
                $this->_html .= '--- Select ---';
                $this->_html .= '</option>';
                foreach ($options as $option) {
                    $this->_html .= '<option value="'. htmlDecodeEncode($option) .'" data-index="'.$optionIndex.'">';
                    $this->_html .=  htmlDecodeEncode($option);
                    $this->_html .= '</option>';
                    $optionIndex++;
                }
                $this->_html .= '</select>';
                break;

            case DataType::MULTI_SELECT_MANY :

                $options = $this->getOptions();
                $this->_html = '<div class="'.$class.' cm-multi">';
                $this->_html .= '<div>';

                $this->_html .=  $label;
                $this->_html .= '<ul>';
                $x=0;
                $rand = rand(1,10000);
                foreach ($options as $option) {
                    $x++;
                    $this->_html .= '<li>';
                    $fieldId = 'op_'.$rand.'_'.$x;
                    //placeholder="'.$placeholder.'"
                    $this->_html .= '<input type="checkbox" data-index="'.$optionIndex.'" name="custom_fields['.$name.']['.$optionIndex.']" value="'.htmlDecodeEncode($option).'" id="'.$fieldId.'" />'.
                        ' <label for="'.$fieldId.'">' . htmlDecodeEncode($option).'</label>';
                    $this->_html .= '</li>';
                    $optionIndex++;

                }
                $this->_html .= '</ul>';
                $this->_html .= '</div>';
                $this->_html .= '</div>';
                break;

            default :
                $this->_html = $label;
                $this->_html .= '<input placeholder="'.$placeholder.'" name="custom_fields['.$name.']" type="text" value="' . $this->getName() . '" class="' . $class . '">';
                break;
        }

        $class="";
        if ( $this->_isRequired )
        {
            if ($fieldType == DataType::MULTI_SELECT_MANY)
            {
                $class=' class="cm-checkboxes-required"';
            }
            else
            {
                $class=' class="cm-required"';
            }
        }

        return "<div".$class.">".$this->_html."</div>";
    }

    /**
     * @param string $html
     */
    protected function setHtml( $html )
    {
        $this->_html = $html;
    }



    /**
     * @return boolean
     */
    public function isShowLabel()
    {
        return $this->_showLabel;
    }

    /**
     * @param boolean $showLabel
     * @return FormField
     */
    public function setShowLabel( $showLabel )
    {
        $this->_showLabel = $showLabel;
        return $this;
    }



    /**
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * @param string $key
     * @return FormField
     */
    public function setKey( $key )
    {
        $this->_key = htmlspecialchars($key);
        return $this;
    }


    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @param string $label
     * @return FormField
     */
    public function setLabel( $label )
    {
        $this->_label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * @param string $attributes
     * @return FormField
     */
    public function setAttributes( $attributes )
    {
        $this->_attributes = $attributes;
        return $this;
    }

    /**
     * @return string
     */
    public function getName($raw = false)
    {
        if ($raw) {
            return $this->_name;
        }
        return htmlspecialchars_decode( $this->_name, ENT_QUOTES );

    }

    /**
     * @param string $name
     * @return FormField
     */
    public function setName( $name )
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $type
     * @return FormField
     */
    public function setType( $type )
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getEnable()
    {
        return $this->_enable;
    }

    /**
     * @param int $enable
     * @return FormField
     */
    public function setEnable( $enable )
    {
        $this->_enable = $enable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsRequired()
    {
        return $this->_isRequired;
    }

    /**
     * @param boolean $isRequired
     * @return FormField
     */
    public function setIsRequired( $isRequired )
    {
        $this->_isRequired = $isRequired;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHasOptions()
    {
        return $this->_hasOptions;
    }

    /**
     * @param boolean $hasOptions
     * @return FormField
     */
    public function setHasOptions( $hasOptions )
    {
        $this->_hasOptions = $hasOptions;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param array $options
     * @return FormField
     */
    public function setOptions( $options )
    {
        if (is_array( $options )) {
            $this->_options = $options;

        } else {
            $this->_options = preg_split('/\r\n/', $options);
        }

        // TODO improve
        $this->_options = array_filter( $this->_options, 'strlen' );
        return $this;
    }

    public function addOption( $option )
    {
        $this->_options[] = $option;
        return $this;
    }

    //  FieldName, Key, DataType, FieldOptions (array of options for multiple choice)

}