<?php

foreach( CampaignMonitorPluginInstance()->connection->get_clients() as $client ):
    
    foreach( CampaignMonitorPluginInstance()->connection->get_client_lists( $client->ClientID ) as $list ):

        $list = CampaignMonitorPluginInstance()->connection->get_list($list->ListID)->response;
        $fields = CampaignMonitorPluginInstance()->connection->get_list_fields($list->ListID)->response;
        
        $allFields = "";
        
        foreach( $fields as $field ){

            // Is Visible?
            if ($field->VisibleInPreferenceCenter == "1"){
                $is_visible = "checked";
            }else{
                $is_visible = "";
            }
            
            $key = str_replace( [ "\\'"], [""], $field->Key);
            $cleanKey = str_replace(array( '[', ']' ), '', $key);

            $allFields .= '<tr class="additional-field">
                <th class="check-column">
                    <input id="fields'.$key.'[enabled]" type="checkbox" name="fields'.$key.'[enabled]" value="1" '.$is_visible.'>
                </th>
                <td>
                    <label for="fields'.$key.'[enabled]" class="label-is-enabled">'.str_replace( "\\", "", $field->FieldName).'</label> 
                    <ul class="field-options"><li><a href="#" class="rename-field">'.__('Rename', 'campaign-monitor').'</a></li><li><a href="#TB_inline?width=300&height=130&inlineId=TB_confirm" class="delete-field thickbox" id="delete'.$key.'">'.__('Delete', 'campaign-monitor').'</a></li></ul>
                    <div class="fields-extra-config">
                        <input id="fields'.$key.'[label]" type="checkbox" name="fields'.$key.'[label]" value="1" checked> 
                        <label for="fields'.$key.'[label]">'.__('Show Label', 'campaign-monitor').'</label>
                        <input id="fields'.$key.'[required]" type="checkbox" name="fields'.$key.'[required]" value="1"> 
                        <label for="fields'.$key.'[required]">'.__('Required', 'campaign-monitor').'</label>
                        <input type="hidden" name="fields'.$key.'[DataType]" id="fields'.$key.'[DataType]" value="'. $field->DataType .'">
                    </div>
                    <div class="fields-rename-field">
                        <input type="text" name="fields'.$key.'[FieldName]" id="fields'.$key.'[FieldName]" value="'.str_replace( "\\", "", $field->FieldName).'" class="regular-text">
                        <input type="button" value="'.__('Done', 'campaign-monitor').'" class="button-secondary bt-change-name" id="bt-change-name">
                    </div>';
            
            if ( in_array( $field->DataType, ['Text', 'Number', 'Date'] ) ){
                    $allFields .= 
                    '<p class="description">'.__('Placeholder text (optional)', 'campaign-monitor').'</p>
                    <input type="text" name="fields'.$key.'[placeholder]" value="" class="regular-text">';
            }
            
            $allFields .= 
                    '<p class="description">'.__('Additional class (optional)', 'campaign-monitor').'</p>
                    <input type="text" name="fields'.$key.'[css_classes]" value="" class="regular-text">';
            
            
            if ( in_array( $field->DataType, ['MultiSelectOne', 'MultiSelectMany'] ) ){
                $allFields .= '<p class="description">'.__('Field Options (One per line)', 'campaign-monitor').'</p><textarea name="fields'.$field->Key.'[Options]" cols="30" rows="10" class="options-field required-options">';
                        
                foreach( $field->FieldOptions as $option){
                    $allFields .= CampaignMonitorPluginInstance()->clean_option($option) . "\n";
                }
                
                $allFields .= '</textarea>';
			 } 
       
    $allFields .= '</td>
                </tr>';

        }
        
        $allJsFields['list'.$list->ListID] = $allFields;
        
    endforeach;
endforeach; 

?>