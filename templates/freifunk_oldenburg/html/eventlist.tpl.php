<h1>Die letzten {$event_count} Events</h1>

<form action="./eventlist.php" method="POST" enctype="multipart/form-data">
<p>Die letzten <input name="event_count" type="text" size="1" value="{$event_count}"> Events anzeigen <input type="submit" value="aktualisieren"></p>
</form>

{if !empty($eventlist)}
	<ul>
		{foreach key=count item=event from=$eventlist}
			<li>
				<b><a href="event.php?event_id={$event->getEventId()}">{$event->getCreateDate()|date_format:"%e.%m.%Y %H:%M"}</a>:</b> 
				{if $event->getObject() == 'router'}
					{assign var="data" value=$event->getData()}
					<a href="./router_status.php?router_id={$event->getObjectId()}">{$data.hostname}</a> 
					{if $event->getAction() == 'status' AND $data.to == 'online'}
						geht <span style="color: #007B0F;">online</span>
					{/if}
					{if $event->getAction() == 'status' AND $data.to == 'offline'}
						geht <span style="color: #CB0000;">offline</span>
					{/if}
					{if $event->getAction() == 'status' AND $data.to == 'unknown'}
						erhält Status <span style="color: #F8C901;">pingbar</span>
					{/if}
					{if $event->getAction() == 'reboot'}
						wurde <span style="color: #000f9c;">Rebootet</span>
					{/if}
					{if $event->getAction() == 'new'}
						wurde Netmon hinzugefügt
					{/if}
					{if $event->getAction() == 'batman_advanced_version'}
						änderte Batman adv. Version von {$data.from} zu {$data.to}</span>
					{/if}
					{if $event->getAction() == 'firmware_version'}
						änderte Firmware Version von {$data.from} zu {$data.to}</span>
					{/if}
					{if $event->getAction() == 'nodewatcher_version'}
						änderte Nodewatcher Version von {$data.from} zu {$data.to}</span>
					{/if}
					{if $event->getAction() == 'distversion'}
						änderte Distversion Version von {$data.from} zu {$data.to}</span>
					{/if}
					{if $event->getAction() == 'distname'}
						änderte Distname Version von {$data.from} zu {$data.to}</span>
					{/if}
					{if $event->getAction() == 'hostname'}
						änderte Hostname von {$data.from} zu {$data.to}</span>
					{/if}
					{if $event->getAction() == 'chipset'}
						änderte Chipset von {$data.from} zu {$data.to}</span>
					{/if}
					{if $event->getAction() == 'watchdog_ath9k_bug'}
						<a href="./event.php?event_id={$event->getEventId()}">ATH9K Bug registriert</a>
					{/if}
				{/if}
				{if $event->getObject() == 'not_assigned_router'}
					{assign var="data" value=$event->getData()}
					{$data.router_auto_assign_login_string} 
					{if $event->getAction() == 'new'}
						 erscheint in der <a href="./routers_trying_to_assign.php">Liste der neuen, nicht zugewiesenen Router</a>
					{/if}
				{/if}
			</li>
		{/foreach}
	</ul>
{else}
<p>Es sind keine Events in der Datenbank gespeichert.</p>
{/if}