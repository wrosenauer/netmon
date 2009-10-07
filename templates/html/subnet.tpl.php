<h1>Subnet {$net_prefix}.{$subnet.subnet_ip}.0/24, {$subnet.title}</h1>

<div style="float:left; width: 40%;">

<h2>Daten:</h2>

VPN-Server: {if !empty($subnet.vpn_server)}{$subnet.vpn_server} auf Port {$subnet.vpn_server_port}{else}Kein VPN-Server eingetragen{/if}<br>
Daten zu VPN: {if !empty($subnet.vpn_server)} Device {$subnet.vpn_server_device}, Protokoll {$subnet.vpn_server_proto}{else}Kein VPN-Server eingetragen{/if}<br>
<br>
Verantwortlicher: <a href="./user.php?id={$subnet.created_by}">{$subnet.nickname}</a><br>
Eingetragen seit: {$subnet.create_date}<br>

<h2>Beschreibung:</h2>
<p>{$subnet.description}</p>
</div>

<div style="padding-left: 50%;">
	<h2>Ort des Subnetzes<h2>
	<!--<small style="font-size: 9pt;">Map Data from <a href="http://openstreetmap.org">OpenStreetMap</a></small>-->

		<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
		<script type="text/javascript" src="./templates/js/OpenLayers.js"></script>
		<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
		<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
	<div id="map" style="height:300px; width:300px; border:solid 1px black;font-size:9pt;">
		<script type="text/javascript">
			var lon = {$subnet.longitude};
			var lat = {$subnet.latitude};
			var radius = {$subnet.radius}
			var zoom = 11;

			{literal}
				/* Initialize Map */
				init();

				/* Controls for the small map */
				MiniMapControls();

				/* Zoom to the subnet's center */
				point = new OpenLayers.LonLat(lon, lat);
				point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
				map.setCenter(point, zoom);

				/* Create the Subnet Layer */
				SubnetLayer("Subnet", lon, lat, radius);
			{/literal}
		</script>
	</div>
</div>

<h2>IP-Verteilung</h2>

<p>Rot "n" = Ip<br>
Orange "r" = Range<br>
Grün "f" = Frei</p>

<div style="width: 800px;">

{foreach key=key item=status from=$ipstatus}
	<div style="float:left;">

			{if $status.typ=="free"}
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-style: solid;">{$status.ip}</div>
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #6cff84;">f</div>
			{elseif $status.typ=="ip"}
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-style: solid;"><a href="./ip.php?id={$status.ip_id}">{$status.ip}</a></div>
		  		<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #ff0000;">n</div>
			{elseif $status.typ=="range"}
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-style: solid;"><a href="./ip.php?id={$status.belonging_ip_id}">{$status.range_ip}</a></div>
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #ff9448;">r</div>
			{/if}

	</div>
{/foreach}

</div>
<br style="clear:both;">

</div>
