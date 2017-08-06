    <div id="content">
        {if ($Registry->controller eq 'home') and ($Registry->action eq index)}
        <div id="input">
            <form id="singleFile" action="#getFile">
                <input class="left" type="text" name="url" placeholder="Enter your file hosting download to generator premium link..." required />
                <input type="submit" value=" " />
            </form>
        </div>
            <div class="loader"><img src="{$AssetDir}images/loader/loader.gif" title="Your file is generating"/></div>
            <div id="feedback"></div>
        {/if}
        {$contents}
    </div>