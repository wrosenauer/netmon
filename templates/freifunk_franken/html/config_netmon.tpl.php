<script type="text/javascript">
	document.body.id='tab2';
</script>

<ul id="tabnav">
	<li class="tab1"><a href="./config.php?section=edit">Datenbank</a></li>
	<li class="tab2"><a href="./config.php?section=edit_netmon">Netmon</a></li>
	<li class="tab3"><a href="./config.php?section=edit_community">Community</a></li>
	<li class="tab4"><a href="./config.php?section=edit_email">Email</a></li>
	<li class="tab5"><a href="./config.php?section=edit_jabber">Jabber</a></li>
	<li class="tab6"><a href="./config.php?section=edit_twitter">Twitter</a></li>
	<li class="tab7"><a href="./config.php?section=edit_hardware">Hardware</a></li>
</ul>

<h1>Netmon System Konfiguration</h1>
<form action="./config.php?section=insert_edit_netmon" method="POST">
	<p>Installationsroutine Gesperrt: <input name="installed" type="checkbox" value="true" {if $installed}checked{/if}></p>
	<p>URL zu Netmon:<br><input name="url_to_netmon" type="text" size="30" value="{$url_to_netmon}"></p>
	<p>Google Maps API Key:<br><input name="google_maps_api_key" type="text" size="30" value="{$google_maps_api_key}"></p>
	<p>Template Name:<br><input name="template" type="text" size="30" value="{$template_name}"></p>
	<p>Stunden nach denen Crawl Daten gelöscht werden sollen:<br><input name="hours_to_keep_mysql_crawl_data" type="text" size="30" value="{$hours_to_keep_mysql_crawl_data}"></p>
	<p>Stunden nach denen die History gelöscht werden soll:<br><input name="hours_to_keep_history_table" type="text" size="30" value="{$hours_to_keep_history_table}"></p>
	<p>Länge eines Crawl Zyklus in Minuten:<br><input name="crawl_cycle_length_in_minutes" type="text" size="30" value="{$crawl_cycle_length_in_minutes}"></p>

	<p><input type="submit" value="Absenden"></p>
</form>