{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{default attribute_base=ContentObjectAttribute}
<div class="block">
    <table>
    {section show=$attribute.contentclass_attribute.is_information_collector}
        <tr>
            <td>
            <label>{"Text"|i18n("design/standard/content/datatype")}</label>
            </td>
            <td>
            <label>{"Value (optional)"|i18n("design/standard/content/datatype")}</label>
            </td>
        </tr>
    {/section}
    {section var=MultiOptionList loop=$attribute.content.multioption_list sequence=array(bglight,bgdark)}

        <tr>
            <td>
            <label>{"Name"|i18n("design/standard/content/datatype")}</label><div class="labelbreak"></div>
            <input type="hidden" name="{$attribute_base}_data_multioption_id_{$attribute.id}[]" value="{$MultiOptionList.id}" />
            <input type="text" name = "{$attribute_base}_data_multioption_name_{$attribute.id}_{$MultiOptionList.id}" value="{$MultiOptionList.name}" />
            <label>{"Priority"|i18n("design/standard/content/datatype")}</label>
            <input type="text" size="3" name = "{$attribute_base}_data_multioption_priority_{$attribute.id}_{$MultiOptionList.id}" value="{sum($MultiOptionList.index,1)}" />
            <input type="checkbox" name="{$attribute_base}_data_multioption_remove_{$attribute.id}[]" value="{$MultiOptionList.id}" /><br>
            <label>{"Default"|i18n("design/standard/content/datatype")}</label>
           &nbsp <label>{"Options"|i18n("design/standard/content/datatype")}</label>
           &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 
            <label>{"Additional price"|i18n("design/standard/content/datatype")}</label>
            <div class="labelbreak"></div>
            </td>
        </tr>
        {section var=OptionList loop=$MultiOptionList.optionlist}
            <tr>
                <td>
                <input type="hidden" name="{$attribute_base}_data_option_id_{$attribute.id}_{$MultiOptionList.id}[]" value="{$OptionList.id}" />

                {section show=eq(sum($OptionList.index,1),$MultiOptionList.default_value)}
                    <input type="radio" name="{$attribute_base}_data_radio_checked_{$attribute.id}_{$MultiOptionList.id}"  value="{$OptionList.id}" checked="checked" />
                    {section-else}
                    <input type="radio" name="{$attribute_base}_data_radio_checked_{$attribute.id}_{$MultiOptionList.id}"  value="{$OptionList.id}" />
                {/section}
                &nbsp &nbsp &nbsp 
                <input type="text" name="{$attribute_base}_data_option_value_{$attribute.id}_{$MultiOptionList.id}[]" value="{$OptionList.value}" />
                <input type="text" name="{$attribute_base}_data_option_additional_price_{$attribute.id}_{$MultiOptionList.id}[]" value="{$OptionList.additional_price}" />
                <input type="checkbox" name="{$attribute_base}_data_option_remove_{$attribute.id}_{$MultiOptionList.id}[]" value="{$OptionList.id}" />

                </td>
            </tr>
        {/section}
        <tr>
            <td>
            <div class="buttonblock">
                <input class="smallbutton" type="submit" name="CustomActionButton[{$attribute.id}_new-option_{$MultiOptionList.id}]" value="{'New option'|i18n('design/standard/content/datatype')}" />
                <input class="smallbutton" type="submit" name="CustomActionButton[{$attribute.id}_remove-selected-option_{$MultiOptionList.id}]" value="{'Remove Selected'|i18n('design/standard/content/datatype')}" />

            </div>
            <br/>
            </td>
        </tr>
    {/section}
    </table>

<div class="buttonblock">
<input class="smallbutton" type="submit" name="CustomActionButton[{$attribute.id}_new_multioption]" value="{'New MultiOption'|i18n('design/standard/content/datatype')}" />
<input class="smallbutton" type="submit" name="CustomActionButton[{$attribute.id}_remove_selected_multioption]" value="{'Remove Selected MultiOption'|i18n('design/standard/content/datatype')}" />
</div>

{/default}

