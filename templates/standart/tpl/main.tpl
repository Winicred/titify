{include file="config.tpl"}
<!DOCTYPE html>
<html lang="ru">
{if($config->off == 1 && !is_admin())}
    {include file="off_site.tpl"}
{else}
    {content}
{/if}
</html>
