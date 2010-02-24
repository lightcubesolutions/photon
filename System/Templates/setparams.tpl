{foreach item=row from=$options name=row}
<option value="{$row.id}">{$row.param_name}</option>
{/foreach}
