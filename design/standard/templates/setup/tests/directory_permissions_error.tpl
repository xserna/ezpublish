{let file_results=$test_result[2]}

{* {$file_results.safe_mode} *}

<h3>{$result_number}. {"Insufficient directory permissions"|i18n("design/standard/setup/tests")}</h3>
<p>{"eZ publish cannot write to some important directories, without this the setup cannot finish and parts of eZ publish will fail."|i18n("design/standard/setup/tests")}</p>
<p>{"It's recommended that you fix this by running the commands below."|i18n("design/standard/setup/tests")}</p>

<p><b>{"Shell commands"|i18n("design/standard/setup/tests")}</b></p>
<pre class="example">cd {$file_results.current_path}<br/>
{section name=File loop=$file_results.result_elements}{section-exclude match=$:item.result}chmod -R a+rwx {$:item.file}<br/>{/section}</pre>

<p>
 If you know the user and group of the webserver it's recommended to use a different set of permissions.
 To do this you need to change all the <tt class="note">chmod</tt> commands.
</p>
<h3>Example</h3>
<pre class="example">chmod -R og+rwx var
chown -R nouser.nouser var</pre>

<blockquote class="note">
<p>
 <b>Note:</b> The <tt>nouser.nouser</tt> must be changed to your webserver username and groupname.
</p>
</blockquote>

{/let}
