<h1>Service der Ip {$net_prefix}.{$servicedata.subnet_ip}.{$servicedata.ip_ip} editieren</h1>

<form action="./serviceeditor.php?section=insert_edit&service_id={$servicedata.service_id}" method="POST">
  <p>
    <p>Typ: <input name="typ" type="text" size="5" maxlength="10" value="{$servicedata.typ}"><br>
     Hier wird angegeben was sich hinter der IP verbirgt. (node, vpn, client, service)</p>
  </p>

  <p>
    <p>Check: <input name="crawler" type="text" size="5" maxlength="10" value="{$servicedata.crawler}"><br>
    Hier wird angegeben wie die IP auf verfügbarkeit geprüft werden soll. Für node "json" eintragen, für vpn idR. auch json, für client "ping" und für service einen Port (bspw.: 21 oder 80), wenn nicht geprüft werden soll "no" eintragen!</p>
  </p>

  <p>
    <p>(Optional) Titel: <input name="title" type="text" size="40" maxlength="40" value="{$servicedata.title}"><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>

  <p>
    <p>(Optional) Beschreibung: <br><textarea name="description" cols="50" rows="10">{$servicedata.description}</textarea><br>
    Sinnvoll wenn Typ "service" sowie Port "80" ist und sich hinter der IP eine Website verbirgt. Oder ein VPN-Server, oder ein NFS-Downloadserver usw.</p>
  </p>
  
	<h2>Privatsphäre:</h2>
  <p>
    <p>Diesen Service nicht angemeldeten Benutzer zeigen: 
    <select name="visible" size="1">
      <option value="1" {if $servicedata.visible==1}selected{/if}>Ja</option>
      <option value="0" {if $servicedata.visible==0}selected{/if}>Nein</option>
    </select>
    <br>
    Alle Services sollten Sichbar sein. Wenn du aber einen Service anbietest bei dem es unter Umständen kritisch sein kann ihn öffentlich anzuzeigen, kannst du hier einstellen, dass nur angemeldete Personen den Service sehen können.</p>
  </p>

	<h2>Benachrichtigungen:</h2>
	Benachrichtige mich, wenn dieser Service länger als <input name="notification_wait" type="text" size="2" maxlength="2" value="{$servicedata.notification_wait}"> Crawldurchgänge nicht erreichbar ist
    <select name="notify" size="1">
      <option value="1" {if $servicedata.notify==1}selected{/if}>Ja</option>
      <option value="0" {if $servicedata.notify==0}selected{/if}>Nein</option>
    </select>
	</p>

  <p><input type="submit" value="Absenden"></p>
</form>


<form action="./serviceeditor.php?section=delete&service_id={$servicedata.service_id}" method="POST">
  <h2>Service Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <p><input type="submit" value="Löschen!"></p>
</form>